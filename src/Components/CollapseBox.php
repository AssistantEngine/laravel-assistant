<?php

namespace AssistantEngine\Laravel\Components;

use Livewire\Attributes\Reactive;
use Livewire\Component;

class CollapseBox extends Component
{
    #[Reactive]
    public $title;
    public $isCollapsed = false;
    #[Reactive]
    public $animated = false;

    #[Reactive]
    public $items = [];

    public function mount($title, $items = [], $animated = false)
    {
        $this->items = $items;
        $this->title = $title;
        $this->animated = $animated;
    }

    public function toggleCollapse()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function render()
    {
        return view('assistant-engine::collapse-box');
    }
}
