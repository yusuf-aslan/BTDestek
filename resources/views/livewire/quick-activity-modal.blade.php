<div class="relative shrink-0 flex items-center">
    <!-- Quick Add Button -->
    <button wire:click="openModal" 
            type="button" 
            title="Hızlı Faaliyet Ekle"
            class="flex items-center justify-center w-9 h-9 rounded-lg bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300 transition shadow-sm hover:scale-105 duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
    </button>

    <!-- Modal Backdrop & Window -->
    <div x-data="{ show: @entangle('isOpen') }" 
         x-show="show" 
         x-on:keydown.escape.window="show = false"
         class="fixed inset-0 z-[1000] flex items-center justify-center p-4 sm:p-6"
         style="display: none;">
        
        <!-- Backdrop -->
        <div x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
             @click="show = false"></div>

        <!-- Modal Box -->
        <div x-show="show" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-8"
             class="relative bg-white dark:bg-slate-900 w-full max-w-lg rounded-2xl shadow-2xl overflow-hidden border border-slate-200 dark:border-slate-800">
            
            <!-- Header -->
            <div class="px-6 py-5 border-b border-slate-100 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/30 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white leading-tight">Hızlı Faaliyet Ekle</h3>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Bilet dışı yapılan faaliyet kaydı</p>
                    </div>
                </div>
                <button @click="show = false" type="button" class="text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Form -->
            <form wire:submit.prevent="save" class="p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <!-- Activity Type -->
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Faaliyet Türü</label>
                        <select wire:model.defer="activity_type" required class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white appearance-none">
                            <option value="Telefon Desteği">Telefon Desteği</option>
                            <option value="Saha Çalışması">Saha Çalışması</option>
                            <option value="Toplantı">Toplantı</option>
                            <option value="Rutin Bakım/Onarım">Rutin Bakım/Onarım</option>
                            <option value="Sistem/Altyapı Çalışması">Sistem/Altyapı Çalışması</option>
                            <option value="Diğer">Diğer</option>
                        </select>
                        @error('activity_type') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                    </div>

                    <!-- Duration -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Harcanan Süre (Dk)</label>
                        <input type="number" wire:model.defer="duration" required min="1" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white" placeholder="Örn: 15">
                        @error('duration') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                    </div>

                    <!-- Date -->
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Faaliyet Tarihi</label>
                        <input type="date" wire:model.defer="activity_date" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white">
                        @error('activity_date') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                    </div>

                    <!-- Department -->
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Bölüm/Oda</label>
                        <input type="text" wire:model.defer="department" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white" placeholder="Örn: Kardiyoloji Polikliniği 3. Oda">
                        @error('department') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                    </div>

                    <!-- Description -->
                    <div class="space-y-1.5 col-span-2">
                        <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Yapılan İşin Açıklaması</label>
                        <textarea wire:model.defer="description" rows="3" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white" placeholder="Yapılan işlemi kısaca detaylandırın..."></textarea>
                        @error('description') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Footer / Action Buttons -->
                <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                    <button @click="show = false" type="button" class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                        Vazgeç
                    </button>
                    <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold transition flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
