<div>
    <x-filament::modal id="quick-activity" width="lg" :close-by-clicking-away="true">
        <x-slot name="trigger">
            <!-- Icon Button matching Filament Topbar -->
            <x-filament::icon-button
                icon="heroicon-o-plus-circle"
                color="primary"
                size="lg"
                title="Hızlı Faaliyet Ekle"
                class="hover:scale-110 transition duration-200"
            />
        </x-slot>

        <x-slot name="heading">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg bg-primary-500/10 text-primary-600 dark:text-primary-400 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div class="text-left">
                    <h3 class="font-bold text-slate-900 dark:text-white leading-tight">Hızlı Faaliyet Ekle</h3>
                    <p class="text-[10px] text-slate-400 dark:text-slate-500 font-medium">Bilet dışı yapılan faaliyet kaydı</p>
                </div>
            </div>
        </x-slot>

        <!-- Form -->
        <form wire:submit.prevent="save" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <!-- Activity Type -->
                <div class="space-y-1.5 col-span-2 text-left">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Faaliyet Türü</label>
                    <select wire:model.defer="activity_type" required class="w-full px-3 py-2.5 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white appearance-none">
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
                <div class="space-y-1.5 text-left">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Harcanan Süre (Dk)</label>
                    <input type="number" wire:model.defer="duration" required min="1" class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white" placeholder="Örn: 15">
                    @error('duration') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                </div>

                <!-- Date -->
                <div class="space-y-1.5 text-left">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Faaliyet Tarihi</label>
                    <input type="date" wire:model.defer="activity_date" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white">
                    @error('activity_date') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                </div>

                <!-- Department -->
                <div class="space-y-1.5 col-span-2 text-left">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Bölüm</label>
                    <input type="text" wire:model.defer="department" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white" placeholder="Örn: Kardiyoloji Polikliniği 3. Oda">
                    @error('department') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                </div>

                <!-- Description -->
                <div class="space-y-1.5 col-span-2 text-left">
                    <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Yapılan İşin Açıklaması</label>
                    <textarea wire:model.defer="description" rows="3" required class="w-full px-3 py-2 border border-slate-200 dark:border-slate-800 rounded-xl focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 outline-none transition bg-slate-50/50 dark:bg-slate-950/50 text-sm dark:text-white" placeholder="Yapılan işlemi kısaca detaylandırın..."></textarea>
                    @error('description') <p class="text-xs text-red-500 mt-0.5 ml-0.5">{{ $message }}</p> @enderror
                </div>
            </div>

            <!-- Footer / Action Buttons -->
            <div class="pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                <x-filament::button color="gray" x-on:click="close" type="button">
                    Vazgeç
                </x-filament::button>
                <x-filament::button type="submit" color="primary" icon="heroicon-m-check">
                    Kaydet
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>
</div>
