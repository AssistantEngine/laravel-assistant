<?php

namespace AssistantEngine\Laravel\Commands;

use AssistantEngine\SDK\AssistantEngine;
use AssistantEngine\SDK\Models\Conversation\ConversationItem;
use AssistantEngine\SDK\Models\Conversation\ConversationItemAction;
use AssistantEngine\SDK\Models\Options\ConversationOption;
use AssistantEngine\SDK\Models\Options\MessageOption;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;

class LaravelAssistantChatCommand extends Command
{
    // Added the --recreate option
    protected $signature = 'assistant-engine:chat {assistant} {--recreate}';

    protected $description = 'Chat with an assistant using AssistantEngine SDK';

    public function handle(AssistantEngine $assistantEngine)
    {
        $assistantKey = $this->argument('assistant');
        $recreate = $this->option('recreate');

        // Create conversation options with the 'recreate' parameter
        $conversationOption = new ConversationOption($assistantKey, [
            'user_id' => 'console_user', // You can use any identifier
            'recreate' => $recreate,
        ]);

        $this->info('Conversation started. Type "exit" to quit.');

        try {
            // Find or create a conversation
            $conversation = $assistantEngine->findOrCreateConversation($conversationOption);

            // Process the conversation to ensure it's in a finite state
            $conversation = $this->processConversation($assistantEngine, $conversation);

            // Print existing conversation history with color coding
            if (!empty($conversation->history)) {
                foreach ($conversation->history as $item) {
                    $this->displayConversationItem($item);
                }
            }

            while (true) {
                // Prompt the user for input
                $userMessage = $this->ask('You');

                if (strtolower($userMessage) === 'exit') {
                    $this->info('Exiting the chat.');
                    break;
                }

                // Send the user message and process the assistant's response
                $conversation = $this->sendMessageAndProcessResponse($assistantEngine, $conversation, $userMessage);
            }

        } catch (GuzzleException $e) {
            $this->error('An error occurred: ' . $e->getMessage());
        }

        return 0;
    }

    /**
     * Processes the conversation to ensure it reaches a finite state.
     *
     * @param AssistantEngine $assistantEngine
     * @param $conversation
     * @return mixed
     */
    protected function processConversation(AssistantEngine $assistantEngine, $conversation)
    {
        // Initialize variable to track last displayed status
        $lastStatus = null;

        // Initialize variable to track if status was displayed
        $statusDisplayed = false;

        // Wait until the conversation is in a finite state
        do {
            // Sleep for a short period
            sleep(1);

            // Refresh the conversation status
            $conversation = $assistantEngine->getConversation($conversation->id);

            // If not in finite state, display last_run_status if it has changed
            if (!$conversation->isInFiniteState() && $conversation->last_run_status !== $lastStatus) {
                $lastStatus = $conversation->last_run_status;

                // Display status on the same line
                $this->output->write("\r<fg=yellow>Status: {$lastStatus}</>     ");
                $statusDisplayed = true;
            }
        } while (!$conversation->isInFiniteState());

        // Clear the status line if it was displayed
        if ($statusDisplayed) {
            // Move cursor to the beginning of the line and clear it
            $this->output->write("\r" . str_repeat(' ', 80) . "\r");
        }

        return $conversation;
    }

    /**
     * Sends a user message and processes the assistant's response.
     *
     * @param AssistantEngine $assistantEngine
     * @param $conversation
     * @param string $userMessage
     * @return mixed
     */
    protected function sendMessageAndProcessResponse(AssistantEngine $assistantEngine, $conversation, string $userMessage)
    {
        // Create a message option
        $messageOption = new MessageOption();
        $messageOption->message = $userMessage;

        // Send the message to the assistant
        $assistantEngine->createMessage($conversation->id, $messageOption);

        // Process the conversation to get assistant's response
        $conversation = $this->processConversation($assistantEngine, $conversation);

        // Get the last assistant message
        $lastAssistantItem = $conversation->getLastConversationItemByRole('assistant');

        if ($lastAssistantItem) {
            $this->displayConversationItem($lastAssistantItem);
        } else {
            $this->error('No response from assistant.');
        }

        return $conversation;
    }

    /**
     * Displays a conversation item (messages and actions) with appropriate styling.
     *
     * @param ConversationItem $item
     */
    protected function displayConversationItem(ConversationItem $item)
    {
        // Display actions
        foreach ($item->actions as $action) {
            $actionContent = $action->content;
            $actionStatus = $action->status;

            // Format the action output
            $actionOutput = "Action ({$action->role}): {$actionContent}";
            if ($actionStatus) {
                $actionOutput .= " [Status: {$actionStatus}]";
            }

            // Display actions with color based on status
            if ($actionStatus === ConversationItemAction::STATUS_SUCCESS) {
                $this->line("<fg=green>{$actionOutput}</>");
            } elseif ($actionStatus === ConversationItemAction::STATUS_ERROR) {
                $this->line("<fg=red>{$actionOutput}</>");
            } else {
                $this->line("<fg=yellow>{$actionOutput}</>");
            }
        }

        // Display messages
        foreach ($item->messages as $message) {
            $role = ucfirst($item->role);
            if (strtolower($role) === 'assistant') {
                // Assistant messages in blue
                $this->line("<fg=blue>{$role}: {$message->content}</>");
            } else {
                // User messages in default color
                $this->line(PHP_EOL . "{$role}: {$message->content}" . PHP_EOL);
            }
        }
    }
}
