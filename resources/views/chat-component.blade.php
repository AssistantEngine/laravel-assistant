@php
    /** @var \AssistantEngine\SDK\Models\Conversation\ConversationItem[] $history */
    /** @var string $lastRunStatus */

    $disableAssistantIcon = config("assistant-engine.chat.disable-assistant-icon", false);
@endphp

<div class="flex flex-col pb-0 h-full relative dark:bg-gray-900 dark:text-neutral-100 ">
    <ul id="assistant-engine::chat-container" class="flex-1 overflow-y-auto pb-8 scrollbar-hide"
        @if($maxHeight !== 0) wire:poll.visible.1s="loadMessages" style="max-height: {{$maxHeight}}px" @endif
    >
        @foreach($history as $index => $conversationItem)
            @if($conversationItem['role'] === \AssistantEngine\SDK\Models\Conversation\ConversationItem::ROLE_ASSISTANT)
                <li class="max-w-lg flex gap-x-2 sm:gap-x-4 mb-4">
                    <div class="inline-block rounded-full {{$disableAssistantIcon ? 'hidden' : ''}}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mt-2 text-gray-500 dark:text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </div>

                    <div>
                        @if (!empty($conversationItem['actions']))
                            @php
                                if (count($conversationItem['actions']) > 1) {
                                    $title = count($conversationItem['actions']) . " actions executed";
                                } else {
                                    $title = "1 action executed";
                                }
                            @endphp

                            <div class="mt-1">
                                <livewire:assistant-engine::collapse-box key="conversation-history-item-{{$index}}" :title="$title" :items="$conversationItem['actions']" />
                            </div>
                        @endif

                        @foreach($conversationItem['messages'] as $assistantMessage)
                            <div class="mt-2 bg-white border border-gray-200 rounded-2xl p-4 text-sm space-y-3 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-100">
                                @if(config("assistant-engine.chat.render-assistant-message-as-markdown"))
                                    {!! \Illuminate\Support\Str::markdown($assistantMessage['content']) !!}
                                @else
                                    {{$assistantMessage['content']}}
                                @endif

                            </div>
                        @endforeach
                    </div>
                </li>
            @elseif($conversationItem['role'] === \AssistantEngine\SDK\Models\Conversation\ConversationItem::ROLE_USER)
                @foreach($conversationItem['messages'] as $userMessage)
                    <li class="flex ms-auto gap-x-2 sm:gap-x-4 {{$conversationItem['run_status'] === \AssistantEngine\SDK\Models\Conversation\Conversation::STATUS_CANCELLED ? 'mb-1' : 'mb-4'}}">
                        <div class="grow text-end space-y-3">
                            <div class="inline-flex flex-col justify-end">
                                <div class="inline-block bg-blue-600 rounded-2xl p-4 shadow-sm">
                                    <p class="text-sm text-white max-w-96">
                                        {{ $userMessage['content'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
                @if($conversationItem['run_status'] ===\AssistantEngine\SDK\Models\Conversation\Conversation::STATUS_CANCELLED)
                    <li class="flex mr-1 ms-auto mb-4 gap-x-2 sm:gap-x-4 ">
                        <span class="ms-auto flex items-center gap-x-1 text-xs text-yellow-600 dark:text-yellow-400">
                            <svg class="shrink-0 size-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                 stroke-linecap="round" stroke-linejoin="round">
                              <circle cx="12" cy="12" r="10"></circle>
                              <line x1="12" x2="12" y1="8" y2="12"></line>
                              <line x1="12" x2="12.01" y1="16" y2="16"></line>
                            </svg>
                            cancelled
                        </span>
                    </li>
                @endif
            @elseif($conversationItem['role'] === \AssistantEngine\SDK\Models\Conversation\ConversationItem::ROLE_ERROR)
                <li class="flex ms-auto gap-x-2 sm:gap-x-4 mb-4">
                    <div class="inline-block rounded-full {{$disableAssistantIcon ? 'hidden' : ''}}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mt-2 text-gray-500 dark:text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </div>
                    <div>
                        @foreach($conversationItem['messages'] as $errorMessage)
                            <div class="text-sm text-red-400 p-4 space-y-3 dark:bg-neutral-900 dark:border-neutral-700">
                                {{ $errorMessage['content'] }}
                            </div>
                        @endforeach
                    </div>
                </li>
            @endif
        @endforeach

        @if($lastRunInFiniteState === false)
            @if ($pendingUserItem)
                @foreach($pendingUserItem['messages'] as $userMessage)
                    <li class="flex ms-auto gap-x-2 mb-4  sm:gap-x-4">
                        <div class="grow text-end space-y-3">
                            <div class="inline-flex flex-col justify-end">
                                <div class="inline-block bg-blue-600 rounded-2xl p-4 shadow-sm">
                                    <p class="text-sm text-white">
                                        {{ $userMessage['content'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </li>
                @endforeach
            @endif
            @if ($pendingAssistantItem)
                <li class="max-w-lg flex gap-x-2 sm:gap-x-4 mb-4">
                    <div class="inline-block rounded-full {{$disableAssistantIcon ? 'hidden' : ''}}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 mt-2 text-gray-500 dark:text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                        </svg>
                    </div>

                    <div>
                        <div class="mb-2 mt-1">
                            @php
                                $title = $lastRunStatus;

                                foreach ($pendingAssistantItem['actions'] as $pendingAction) {
                                    if ($pendingAction['status'] === 'pending') {
                                        $title = "Calling: " . $pendingAction['content'];
                                    }
                                }
                            @endphp

                            <livewire:assistant-engine::collapse-box key="conversation-pending-assistant-item" animated="true" :title="$title" :items="$pendingAssistantItem['actions']" />
                        </div>

                        @foreach($pendingAssistantItem['messages'] as $pendingAssistantMessage)
                            <div class="text-sm bg-white border border-gray-200 rounded-2xl p-4 mb-2 space-y-3 dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-100">
                                @if(config("assistant-engine.chat.render-assistant-message-as-markdown"))
                                    {!! \Illuminate\Support\Str::markdown($pendingAssistantMessage['content']) !!}
                                @else
                                    {{$pendingAssistantMessage['content']}}
                                @endif
                            </div>
                        @endforeach

                        @if ($pendingAssistantItem['messages'])
                            <div class="mt-2">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gray-400 opacity-75 dark:bg-gray-500"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-gray-500 dark:bg-gray-400"></span>
                                </span>
                            </div>
                        @endif
                    </div>
                </li>
            @endif
        @endif

    </ul>

    @if(!$scrollAtBottom)
        <div id="scroll-to-bottom-btn" class="absolute bottom-24 mx-auto left-0 right-0 z-10 text-center">
            <button class="p-2 bg-gray-600 text-white rounded-full shadow-lg dark:bg-neutral-700 dark:text-neutral-200" wire:click="scrollDown">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
        </div>
    @endif

    @if(config("assistant-engine.chat.disable-user-input") !== true)
        <div class="pt-4 border-t flex flex-row dark:border-neutral-700">
            <div class="w-full">
                <div class="flex rounded-lg shadow-sm border border-gray-300 dark:border-neutral-700">
                    <button type="button" wire:click="resetThread" class="w-[2.875rem] h-[2.875rem] shrink-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-s-md border  border-white bg-white text-gray-600 hover:bg-gray-100 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:text-neutral-200 dark:border-neutral-700">
                        <svg class="shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                        </svg>
                    </button>

                    <form wire:submit.prevent="sendMessage" class="w-full">
                        <input type="text" wire:model.live="messageInput" placeholder="Type your message here..." class="py-3 px-4 block w-full border-white focus:border-white shadow-sm rounded-0 text-sm focus:z-10 focus:border-gray-200 focus:border-r-0 focus:outline-none focus:ring-0 disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-900 dark:border-neutral-700 dark:text-neutral-400 dark:placeholder-neutral-500 dark:focus:ring-neutral-600">
                    </form>

                    @if (!$lastRunInFiniteState)
                        <!-- Cancel Button -->
                        <button wire:click="cancelRun" type="button" class="w-[2.875rem] h-[2.875rem] shrink-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-e-md border border-white bg-white  text-gray-600 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:text-neutral-200 dark:border-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="shrink-0 size-6"  width="24" height="24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm6-2.438c0-.724.588-1.312 1.313-1.312h4.874c.725 0 1.313.588 1.313 1.313v4.874c0 .725-.588 1.313-1.313 1.313H9.564a1.312 1.312 0 0 1-1.313-1.313V9.564Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @elseif($messageInput)
                        <!-- Submit Button -->
                        <button wire:click="sendMessage" type="button" class="w-[2.875rem] h-[2.875rem] shrink-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-e-md border border-white bg-white  text-gray-600 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:text-neutral-200 dark:border-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="shrink-0 size-6"  width="24" height="24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.53 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v5.69a.75.75 0 0 0 1.5 0v-5.69l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @elseif (config("assistant-engine.chat.open-ai-recorder.activate"))
                        <!-- Voice Button -->
                        <span class="w-[2.875rem] h-[2.875rem] shrink-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-e-md border border-white bg-white  text-gray-600 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:text-neutral-200 dark:border-neutral-700">
                        <livewire:assistant-engine::voice-recorder wire:key="voice-recorder-modal"/>
                    </span>
                    @else
                        <button disabled type="button" class="w-[2.875rem] h-[2.875rem] shrink-0 inline-flex justify-center items-center gap-x-2 text-sm font-semibold rounded-e-md border  border-white bg-white   text-gray-600 focus:outline-none disabled:opacity-50 disabled:pointer-events-none dark:bg-neutral-800 dark:text-neutral-200 dark:border-neutral-700">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="shrink-0 size-6"  width="24" height="24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm.53 5.47a.75.75 0 0 0-1.06 0l-3 3a.75.75 0 1 0 1.06 1.06l1.72-1.72v5.69a.75.75 0 0 0 1.5 0v-5.69l1.72 1.72a.75.75 0 1 0 1.06-1.06l-3-3Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    @endif


    <style>
        /* For Webkit-based browsers (Chrome, Safari and Opera) */
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        /* For IE, Edge and Firefox */
        .scrollbar-hide {
            -ms-overflow-style: none;  /* IE and Edge */
            scrollbar-width: none;  /* Firefox */
        }
    </style>

    @script
    <script>
        let intervalId = setInterval(function () {
            let chatContainer = document.getElementById('assistant-engine::chat-container');

            if (chatContainer) {
                // set max chat height
                if ($wire.maxHeight === 0) {
                    $wire.setMaxHeight(chatContainer.scrollHeight);
                }

                chatContainer.addEventListener('scroll', function () {
                    let atBottom = chatContainer.scrollTop + chatContainer.clientHeight >= chatContainer.scrollHeight - 5;

                    if ($wire.scrollAtBottom !== atBottom) {
                        $wire.$set('scrollAtBottom', atBottom)
                    }
                });

                clearInterval(intervalId);
            }
        }, 100);

        function scrollToBottom() {
            let intervalIdScroll = setInterval(function () {
                let chatContainer = document.getElementById('assistant-engine::chat-container');

                if (chatContainer) {
                    chatContainer.scrollTop = chatContainer.scrollHeight;
                    clearInterval(intervalIdScroll);
                }

            }, 100);
        }

        $wire.on('{{\AssistantEngine\Laravel\Components\ChatComponent::EVENT_SHOULD_SCROLL}}', () => {
            scrollToBottom();
        });

    </script>
    @endscript
</div>
