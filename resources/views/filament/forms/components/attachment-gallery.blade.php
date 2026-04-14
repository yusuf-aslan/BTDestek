@php
    $attachments = $getRecord()->attachments()->where('file_type', 'like', 'image/%')->get();
@endphp

<div x-data="{ 
    open: false, 
    activeImage: null 
}" class="w-full py-4">
    <!-- Thumbnail Grid -->
    <div style="display: flex; flex-wrap: wrap; gap: 12px;">
        @foreach($attachments as $attachment)
            <div class="relative group cursor-pointer overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 shadow-sm"
                 style="width: 100px; height: 100px; position: relative;"
                 @click="activeImage = '{{ asset('storage/' . $attachment->file_path) }}'; open = true">
                
                <img src="{{ asset('storage/' . $attachment->file_path) }}" 
                     alt="{{ $attachment->file_name }}"
                     style="width: 100%; height: 100%; object-fit: cover;"
                     class="transition duration-300 group-hover:scale-110">
                
                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                    <div class="bg-white/90 rounded-full p-1 shadow-sm opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg style="width: 16px; height: 16px; color: #374151;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                        </svg>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Lightbox Modal (Pure CSS Fixed) -->
    <div x-show="open" 
         x-cloak
         style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; background-color: rgba(0,0,0,0.9) !important; z-index: 999999 !important; display: flex !important; align-items: center !important; justify-content: center !important; padding: 20px !important;"
         @click="open = false"
         @keydown.escape.window="open = false">
        
        <!-- Close Button -->
        <button @click="open = false" 
                style="position: absolute; top: 20px; right: 20px; background: rgba(255,255,255,0.1); border: none; color: white; width: 44px; height: 44px; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 1000000;">
            <svg style="width: 24px; height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <!-- Big Image -->
        <div style="max-width: 90%; max-height: 90vh; display: flex; align-items: center; justify-content: center;">
            <img :src="activeImage" 
                 @click.stop
                 style="max-width: 100%; max-height: 90vh; object-fit: contain; box-shadow: 0 0 50px rgba(0,0,0,0.5); border-radius: 4px; border: 2px solid rgba(255,255,255,0.1);">
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
