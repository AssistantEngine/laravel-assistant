# Assistant Engine for Laravel

```php artisan vendor:publish --tag=assistant-config```

```
return [
    "api-url" => "https://api.assistant-engine.com/v1/",
    "api-key" => "",
    "llm-provider-key" => "",

    "chat" => [
        "render-assistant-message-as-markdown" => true,

        "disable-user-input" => false,

        "open-ai-recorder" => [
            "activate" => true,
            "open-ai-key" => "",
            "language" => "de"
        ]
    ]
];
```

```php artisan assistant-engine:chat {assistant} --recreate```

```
 <livewire:assistant-engine::chat-component :option="$option"/>
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
