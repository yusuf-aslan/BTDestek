@php
    $attachments = $getRecord()->attachments()->where('file_type', 'like', 'image/%')->get();
@endphp

<div x-data="{ open: false, activeImage: null }" class="w-full">
    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
        @foreach($attachments as $attachment)
            <button
                type="button"
                x-on:click="activeImage = @js(asset('storage/' . $attachment->file_path)); open = true"
                style="width: 112px; height: 80px; padding: 0; overflow: hidden; border: 1px solid #475569; border-radius: 8px; background: transparent; cursor: pointer;"
                title="Görseli büyüt"
            >
                <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->file_name }}" style="display: block; width: 112px; height: 80px; object-fit: cover;">
            </button>
        @endforeach
    </div>

    <div
        x-cloak
        x-bind:style="open ? 'position: fixed; inset: 0; z-index: 999999; display: flex; align-items: center; justify-content: center; padding: 24px; background: rgb(0 0 0 / 0.88);' : 'display: none;'"
        x-on:click="open = false"
        x-on:keydown.escape.window="open = false"
    >
        <button type="button" x-on:click="open = false" style="position: absolute; top: 20px; right: 20px; width: 40px; height: 40px; border: 0; border-radius: 9999px; background: rgb(255 255 255 / 0.15); color: white; font-size: 28px; line-height: 36px; cursor: pointer;">×</button>
        <img :src="activeImage" x-on:click.stop style="max-width: 92vw; max-height: 88vh; object-fit: contain; border-radius: 8px; box-shadow: 0 10px 40px rgb(0 0 0 / 0.5);">
    </div>
</div>
