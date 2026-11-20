{{--
    İş formunda Ctrl+V ile ekran görüntüsü yapıştırma.
    Filament'in yükleme alanı (FilePond) yapıştırmayı zaten destekler, ama imleç bir metin
    kutusundayken (başlık, ayrıntılar vb.) yapıştırılan resmi almaz. Panoda resim varsa
    olayı yakalayıp yükleme alanına yönlendiriyoruz; düz metin yapıştırma etkilenmez.
--}}
<script>
    (() => {
        if (window.__taskPhotoPaste) return;
        window.__taskPhotoPaste = true;

        document.addEventListener('paste', (event) => {
            if (event.__forwarded || ! event.clipboardData) return;

            const images = [...event.clipboardData.files].filter((file) => file.type.startsWith('image/'));
            const active = document.activeElement;
            const inTextField = active && (/^(input|textarea)$/i.test(active.nodeName) || active.isContentEditable);

            // Yükleme alanı odaktaysa ya da resim yoksa FilePond / tarayıcı kendisi halleder
            if (! images.length || ! inTextField || active.closest('.filepond--root')) return;
            if (! document.querySelector('.filepond--root')) return;

            event.preventDefault();
            event.stopImmediatePropagation();

            const transfer = new DataTransfer();
            images.forEach((file) => transfer.items.add(file));

            active.blur();
            const forwarded = new ClipboardEvent('paste', { clipboardData: transfer, bubbles: true });
            forwarded.__forwarded = true;
            document.dispatchEvent(forwarded);
            active.focus({ preventScroll: true });
        }, true);
    })();
</script>
