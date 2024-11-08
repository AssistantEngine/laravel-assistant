<div>
    <!-- Collapsible Button -->
    <button
        type="button"
        class="{{ $animated ? 'animate-pulse' : '' }} inline-flex items-center gap-x-2 text-xs font-semibold rounded-lg text-gray-500 hover:text-gray-900 focus:outline-none focus:text-black-600 disabled:opacity-50 disabled:pointer-events-none dark:text-neutral-400 dark:hover:text-white dark:focus:text-white"
        wire:click="toggleCollapse">
        {{$title}}

        @if(count($items) > 0)
            <svg
                class="{{ $isCollapsed ? 'rotate-180' : '' }} shrink-0 size-3"
                xmlns="http://www.w3.org/2000/svg"
                width="24" height="24"
                viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round">
                <path d="m6 9 6 6 6-6"></path>
            </svg>
        @endif
    </button>

    <!-- Collapsible Content -->
    <div
        class="w-full overflow-hidden transition-[height] duration-300 {{ $isCollapsed ? '' : 'hidden' }}">

        <!-- Timeline -->
        <div class="mt-2">
            @foreach($items as $index => $item)
                <!-- Item -->
                <div class="flex gap-x-3">

                    <!-- Icon -->
                    <div class="relative {{ $loop->last ? 'after:hidden' : '' }} after:absolute after:top-7 after:bottom-0 after:start-3.5 after:w-px after:-translate-x-[0.5px] after:bg-gray-200 dark:after:bg-neutral-700">
                        <div class="relative z-10 size-7 flex justify-center items-center">
                            <div
                                @class([
                                   'size-2 rounded-full bg-gray-400 dark:bg-neutral-600',
                                   'bg-red-600' => isset($item['status']) && $item['status'] == 'error',
                                   'bg-green-600' => isset($item['status']) && $item['status'] == 'success',
                                   'bg-yellow-600' => isset($item['status']) && $item['status'] == 'pending',
                               ])
                            ></div>
                        </div>
                    </div>
                    <!-- End Icon -->

                    <!-- Right Content -->
                    <div class="grow pt-0.5 pb-4">
                        <h3
                            @class([
                                'flex gap-x-1.5 text-sm font-semibold text-gray-800 dark:text-white',
                                'text-red-600' => isset($item['status']) && $item['status'] == 'error',
                                'text-green-600' => isset($item['status']) && $item['status'] == 'success',
                                'text-yellow-600' => isset($item['status']) && $item['status'] == 'pending',
                            ])
                        >
                            {!! $item['content'] !!}
                        </h3>

                        @if(!empty($item['description']))
                            <p class="mt-1 text-xs text-gray-600 dark:text-neutral-400">
                                {!! $item['description'] !!}
                            </p>
                        @endif
                    </div>
                    <!-- End Right Content -->
                </div>
                <!-- End Item -->
            @endforeach
        </div>
        <!-- End Timeline -->
    </div>
</div>
