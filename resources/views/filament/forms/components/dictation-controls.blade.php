<div x-data>
    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: -6px;">
        <x-filament::button
            type="button"
            size="sm"
            icon="heroicon-o-microphone"
            x-on:click="
                const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
                if (!SpeechRecognition) {
                    alert('Tarayıcınız sesle yazmayı desteklemiyor. Lütfen güncel Chrome veya Edge kullanın.');
                    return;
                }

                const editor = document.querySelector('[data-resolution-editor] [contenteditable]');
                if (!editor) {
                    alert('Çözüm notu editörü hazır değil. Lütfen yeniden deneyin.');
                    return;
                }

                if (window.ticketResolutionRecognition) window.ticketResolutionRecognition.stop();

                const recognition = new SpeechRecognition();
                window.ticketResolutionRecognition = recognition;
                recognition.lang = 'tr-TR';
                recognition.continuous = true;
                recognition.interimResults = false;

                recognition.onresult = (event) => {
                    let text = '';
                    for (let index = event.resultIndex; index < event.results.length; index++) {
                        if (event.results[index].isFinal) text += event.results[index][0].transcript;
                    }
                    if (text) {
                        editor.focus();
                        document.execCommand('insertText', false, text.trim() + ' ');
                    }
                };

                recognition.onerror = (event) => {
                    if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
                        alert('Mikrofon izni verilmedi. Tarayıcıdan mikrofon erişimine izin verin.');
                    }
                };

                recognition.onend = () => {
                    if (window.ticketResolutionRecognition === recognition) window.ticketResolutionRecognition = null;
                };

                recognition.start();
                setTimeout(() => {
                    if (window.ticketResolutionRecognition === recognition) recognition.stop();
                }, 60000);
            "
        >
            Mikrofonla Yaz
        </x-filament::button>

        <x-filament::button
            type="button"
            size="sm"
            color="danger"
            icon="heroicon-o-stop-circle"
            x-on:click="if (window.ticketResolutionRecognition) window.ticketResolutionRecognition.stop()"
        >
            Durdur
        </x-filament::button>

        <span style="font-size: 13px; color: #94a3b8;">Konuşma en fazla 60 saniye sürer.</span>
    </div>
</div>
