<?php

namespace AssistantEngine\Laravel\Components;

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use OpenAI\Client;

class VoiceRecorder extends Component
{
    public bool $isRecording = false;
    public string $transcription = '';
    protected Client $client;

    public function boot(Client $client): void
    {
        $this->client = $client;
    }

    public function toggleRecording(): void
    {
        $this->isRecording = !$this->isRecording;
    }

    public function transcribe(string $audioData): void
    {
        // Decode the base64 audio data
        $audioBinary = base64_decode($audioData);

        if ($audioBinary === false) {
            Log::error('VoiceRecorder: Failed to decode base64 audio data');
            return;
        }

        // Create a temporary file
        $tempFilePath = tempnam(sys_get_temp_dir(), 'audio_') . '.wav';

        try {
            // Save the audio data to the temporary file
            file_put_contents($tempFilePath, $audioBinary);

            // Open the file for reading
            $audioFile = fopen($tempFilePath, 'r');
            if ($audioFile === false) {
                throw new \RuntimeException('Failed to open temporary audio file');
            }

            // Call the OpenAI API for transcription
            $response = $this->client->audio()->transcribe([
                'model' => 'whisper-1',
                'file' => $audioFile,
                'response_format' => 'verbose_json',
                'language' => config("assistant-engine.chat.open-ai-recorder.language", "en")
            ]);

            // Store the transcription result
            $this->transcription = $response['text'];

            // Emit the event with the transcription
            $this->dispatch(ChatComponent::EVENT_PROCESS_MESSAGE, $this->transcription);
        } catch (\Exception $e) {
            Log::error('VoiceRecorder: Error during transcription', [
                'exception' => $e,
            ]);
            throw $e;
        } finally {
            // Ensure the temporary file is deleted
            if (file_exists($tempFilePath)) {
                unlink($tempFilePath);
            }
        }
    }

    public function render(): \Illuminate\View\View
    {
        return view('assistant-engine::voice-recorder');
    }
}
