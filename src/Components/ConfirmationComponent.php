<?php

namespace AssistantEngine\Laravel\Components;

use Livewire\Component;
use Illuminate\Support\Arr;

class ConfirmationComponent extends Component
{
    public $actionData = [];
    public $flattenedArray = [];
    public $userComment = '';

    public function mount($action)
    {
        $this->actionData = $action;
        $this->flattenedArray = Arr::dot($action['params']); // Flatten the array
    }

    public function confirm()
    {
        // Handle the confirm action
        $this->dispatch(ChatComponent::EVENT_PROCESS_APPROVAL, 'confirmed', $this->userComment, $this->actionData);
    }

    public function decline()
    {
        $this->dispatch(ChatComponent::EVENT_PROCESS_APPROVAL, 'declined', $this->userComment, $this->actionData);
    }

    public function render()
    {
        return view('assistant-engine::confirmation-component');
    }
}
