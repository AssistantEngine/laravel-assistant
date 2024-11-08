<?php

namespace AssistantEngine\Laravel\Components;

use AssistantEngine\SDK\AssistantEngine;
use AssistantEngine\SDK\Models\Conversation\Conversation;
use AssistantEngine\SDK\Models\Conversation\ConversationItem;
use AssistantEngine\SDK\Models\Options\ConversationOption;
use AssistantEngine\SDK\Models\Options\MessageOption;
use Livewire\Attributes\On;
use Livewire\Component;

class ChatComponent extends Component
{
    const EVENT_CONVERSATION_RESET = 'assistant-engine:chat:conversationReset';
    const EVENT_RUN_FINISHED = 'assistant-engine:chat:runFinished';
    const EVENT_SHOULD_SCROLL = 'assistant-engine:chat:shouldScroll';
    const EVENT_CHANGE_CONVERSATION = 'assistant-engine:chat:changeConversation';
    const EVENT_PROCESS_MESSAGE = 'assistant-engine:chat:processMessage';

    protected AssistantEngine $assistantEngine;

    public $threadId;
    public $history = [];
    public $pendingUserItem = [];
    public $pendingAssistantItem = [];
    public $messageInput = '';

    public $maxHeight = 0;
    public $scrollAtBottom = true;
    public $lastRunStatus = '';
    public $lastRunInFiniteState = false;
    public $messageCount = 0;

    public function boot()
    {
        $this->assistantEngine = app(AssistantEngine::class);
    }

    /**
     * @param int|string $record
     * @return void
     */
    public function mount(?int $conversationId = null, $option = null): void
    {
        if ($option) {
            $thread = $this->assistantEngine->findOrCreateConversation($option);
        } else if ($conversationId) {
            $thread = $this->assistantEngine->getConversation($conversationId);
        } else {
            throw new \Exception("chat initialized with no parameters");
        }

        $this->initializeChat($thread);
    }

    /**
     * @param Conversation $thread
     *
     * @return void
     */
    protected function initializeChat(Conversation $thread): void
    {
        $this->threadId = $thread->id;
        $this->lastRunStatus = $thread->last_run_status;
        $this->lastRunInFiniteState = $thread->isInFiniteState();
        $this->history = $thread->historyAsArray();
        $this->pendingAssistantItem = [];
        $this->pendingUserItem = [];

        $pendingAssistantItem = $thread->getPendingItemByRole(ConversationItem::ROLE_ASSISTANT);

        if ($pendingAssistantItem) {
            $this->pendingAssistantItem = $pendingAssistantItem->toArray();
        }

        $pendingUserItem = $thread->getPendingItemByRole(ConversationItem::ROLE_USER);

        if ($pendingUserItem) {
            $this->pendingUserItem = $pendingUserItem->toArray();
        }
    }

    public function sendMessage()
    {
        if (!$this->lastRunInFiniteState) {
            return false;
        }

        $messageOption = new MessageOption();
        $messageOption->message = $this->messageInput;

        $this->assistantEngine->createMessage($this->threadId, $messageOption);

        $this->messageInput = '';

        $this->loadMessages(true);
        $this->dispatch(self::EVENT_SHOULD_SCROLL);
    }

    public function loadMessages($refresh = false)
    {
        if ($this->lastRunInFiniteState && !$refresh) {
            return;
        }

        $wasRunInFiniteState = $this->lastRunInFiniteState;

        $thread = $this->assistantEngine->getConversation($this->threadId);
        $this->initializeChat($thread);

        if ($this->messageCount !== $thread->countTotalMessages()) {
            if ($this->scrollAtBottom) {
                $this->dispatch(self::EVENT_SHOULD_SCROLL);
            }

            $this->messageCount = $thread->countTotalMessages();
        }

        if ($wasRunInFiniteState === false && $thread->isInFiniteState()) {
            $this->dispatch(ChatComponent::EVENT_RUN_FINISHED);
        }
    }

    public function resetThread()
    {

        $thread = $this->assistantEngine->getConversation($this->threadId);

        $conversationOption = new ConversationOption($thread->assistant_key);
        $conversationOption->user_id = $thread->user_id;
        $conversationOption->subject_id = $thread->subject_id;
        $conversationOption->title = $thread->title;
        $conversationOption->context = $thread->context;
        $conversationOption->additional_data = $thread->additional_data;
        $conversationOption->recreate = true;

        $newThread = $this->assistantEngine->findOrCreateConversation($conversationOption);

        $this->initializeChat($newThread);
        $this->loadMessages();

        $this->dispatch(self::EVENT_CONVERSATION_RESET, $newThread);
    }

    public function setMaxHeight(int $maxHeight)
    {
        $this->maxHeight = $maxHeight;
        $this->dispatch(self::EVENT_SHOULD_SCROLL);
    }

    public function cancelRun()
    {
        if ($this->lastRunInFiniteState) {
            return false;
        }

        $this->assistantEngine->cancelRun($this->threadId);
    }

    public function scrollDown()
    {
        $this->scrollAtBottom = true;
        $this->dispatch(self::EVENT_SHOULD_SCROLL);
    }

    #[On(self::EVENT_CHANGE_CONVERSATION)]
    public function changeConversation($conversationData)
    {
        $conversation = new Conversation($conversationData);

        $this->initializeChat($conversation);
        $this->loadMessages(true);
    }

    #[On(self::EVENT_PROCESS_MESSAGE)]
    public function processMessage($message)
    {
        $messageOption = new MessageOption();
        $messageOption->message = $message;

        $this->assistantEngine->createMessage($this->threadId, $messageOption);

        $this->loadMessages(true);
        $this->dispatch(self::EVENT_SHOULD_SCROLL);
    }

    public function render()
    {
        return view('assistant-engine::chat-component');
    }
}
