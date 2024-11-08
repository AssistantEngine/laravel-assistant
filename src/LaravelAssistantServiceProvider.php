<?php

namespace AssistantEngine\Laravel;

use AssistantEngine\Laravel\Components\ChatComponent;
use AssistantEngine\Laravel\Components\CollapseBox;
use AssistantEngine\Laravel\Components\VoiceRecorder;
use AssistantEngine\SDK\AssistantEngine;
use Illuminate\Foundation\Application;
use Livewire\Livewire;
use OpenAI;
use OpenAI\Client;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use AssistantEngine\Laravel\Commands\LaravelAssistantChatCommand;

class LaravelAssistantServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-assistant')
            ->hasConfigFile("assistant-engine")
            ->hasViews('assistant-engine')
            ->hasCommand(LaravelAssistantChatCommand::class);
    }

    public function bootingPackage()
    {
        $this->app->singleton(AssistantEngine::class, function (Application $app) {
            return new AssistantEngine(
                config('assistant-engine.api-url'),
                config('assistant-engine.api-key'),
                config('assistant-engine.llm-provider-key'),
                config('assistant-engine.basic-auth')
            );
        });

        if (config("assistant-engine.chat.open-ai-recorder.activate") && config("assistant-engine.chat.open-ai-recorder.open-ai-key")) {
            $this->app->singleton(Client::class, function (Application $app) {
                $apiKey = config("assistant-engine.chat.open-ai-recorder.open-ai-key");

                return OpenAI::factory()
                    ->withApiKey($apiKey)
                    ->withHttpHeader('OpenAI-Beta', 'assistants=v2')
                    ->make();
            });
        }

        Livewire::component('assistant-engine::voice-recorder', VoiceRecorder::class);
        Livewire::component('assistant-engine::collapse-box', CollapseBox::class);
        Livewire::component('assistant-engine::chat-component', ChatComponent::class);
    }
}
