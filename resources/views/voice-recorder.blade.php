<div>
    <button type="button" class="mt-1" id="recordButton" wire:click="toggleRecording" aria-label="Toggle Recording">
        @if($isRecording)
            <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="shrink-0 size-5"  width="24" height="24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />
            </svg>
            <div wire:loading class="animate-spin inline-block size-4 border-[3px] border-current border-t-transparent text-blue-600 rounded-full dark:text-blue-500" role="status" aria-label="loading">
                <span class="sr-only">Loading...</span>
            </div>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="shrink-0 size-4"  width="24" height="24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M8.25 4.5a3.75 3.75 0 1 1 7.5 0v8.25a3.75 3.75 0 1 1-7.5 0V4.5Z" />
                <path d="M6 10.5a.75.75 0 0 1 .75.75v1.5a5.25 5.25 0 1 0 10.5 0v-1.5a.75.75 0 0 1 1.5 0v1.5a6.751 6.751 0 0 1-6 6.709v2.291h3a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1 0-1.5h3v-2.291a6.751 6.751 0 0 1-6-6.709v-1.5A.75.75 0 0 1 6 10.5Z" />
            </svg>
        @endif
    </button>
</div>

    @script
    <script>
        let wire = $wire;
        let mediaRecorder;
        let audioChunks = [];


        const recordButton = document.getElementById('recordButton');

        recordButton.addEventListener('click', function () {

            if (!wire.get('isRecording')) {
                startRecording();
            } else {
                stopRecording();
            }
        });

        async function startRecording() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    mediaRecorder = new MediaRecorder(stream);

                    mediaRecorder.ondataavailable = (event) => {
                        if (event.data.size > 0) {
                            audioChunks.push(event.data);
                        }
                    };

                    mediaRecorder.onstop = () => {
                        const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
                        audioChunks = [];
                        sendAudioBlob(audioBlob);
                    };

                    mediaRecorder.start();
                } catch (error) {
                    console.error('Error accessing microphone:', error);
                }
            } else {
                console.error('getUserMedia is not supported in this browser');
            }
        }

        function stopRecording() {
            if (mediaRecorder && mediaRecorder.state !== 'inactive') {
                mediaRecorder.stop();
                mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
        }

        function sendAudioBlob(audioBlob) {

            const reader = new FileReader();
            reader.onloadend = function () {
                const base64Audio = reader.result.split(',')[1];
                wire.call('transcribe', base64Audio);
            };
            reader.readAsDataURL(audioBlob);
        }

    </script>
    @endscript
