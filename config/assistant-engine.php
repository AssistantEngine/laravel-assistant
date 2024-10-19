<?php

// config for AssistantEngine/Laravel
return [
    "api-url" => "https://api.assistant-engine.com/v1/",
    "api-key" => "",
    "llm-provider-key" => "",

    "chat" => [
        "render_assistant_message_as_markdown" => true,

        "disable_user_input" => false,

        "open-ai-recorder" => [
            "activate" => true,
            "open-ai-key" => "",
            "language" => "de"
        ]
    ]
];
