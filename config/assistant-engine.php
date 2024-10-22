<?php

// config for AssistantEngine/Laravel
return [
    "api-url" => "https://api.assistant-engine.com/v1/",
    "api-key" => "",
    "llm-provider-key" => "",

    "chat" => [
        "render-assistant-message-as-markdown" => true,

        "disable-assistant-icon" => false,
        "disable-user-input" => false,

        "open-ai-recorder" => [
            "activate" => true,
            "open-ai-key" => "",
            "language" => "de"
        ]
    ]
];
