<?php

// config for AssistantEngine/Laravel
return [
    'api-url' => env('ASSISTANT_ENGINE_API', 'https://api.assistant-engine.com/v1/'),
    'api-key' => env('ASSISTANT_ENGINE_TOKEN'),
    'llm-provider-key' => env('OPENAI_API_KEY'),

    "chat" => [
        "render-assistant-message-as-markdown" => true,

        "disable-assistant-icon" => false,
        "disable-user-input" => false,

        "open-ai-recorder" => [
            "activate" => true,
            "open-ai-key" => env('OPENAI_API_KEY'),
            "language" => "en"
        ]
    ]
];
