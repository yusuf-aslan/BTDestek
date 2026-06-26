<div>
    <x-filament::modal 
        id="quick-activity" 
        width="lg" 
        icon="heroicon-o-clipboard-document-list"
        icon-color="primary"
        heading="Hızlı Faaliyet Ekle"
        description="Bilet dışı yapılan faaliyet kaydı"
        :close-by-clicking-away="true"
        wire:submit.prevent="save"
    >
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

        <!-- Form Content (Inside Modal Body) -->
        <div class="grid grid-cols-2 gap-4 py-2">
            <!-- Activity Type -->
            <div class="col-span-2 space-y-1.5 text-left">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Faaliyet Türü</label>
                <x-filament::input.wrapper>
                    <x-filament::input.select wire:model.defer="activity_type" required>
                        <option value="Telefon Desteği">Telefon Desteği</option>
                        <option value="Saha Çalışması">Saha Çalışması</option>
                        <option value="Toplantı">Toplantı</option>
                        <option value="Rutin Bakım/Onarım">Rutin Bakım/Onarım</option>
                        <option value="Sistem/Altyapı Çalışması">Sistem/Altyapı Çalışması</option>
                        <option value="Diğer">Diğer</option>
                    </x-filament::input.select>
                </x-filament::input.wrapper>
                @error('activity_type') <p class="text-xs text-red-500 mt-1 ml-0.5">{{ $message }}</p> @enderror
            </div>

            <!-- Duration -->
            <div class="col-span-1 space-y-1.5 text-left">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Harcanan Süre (Dk)</label>
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="number"
                        wire:model.defer="duration"
                        required
                        min="1"
                        placeholder="Örn: 15"
                    />
                </x-filament::input.wrapper>
                @error('duration') <p class="text-xs text-red-500 mt-1 ml-0.5">{{ $message }}</p> @enderror
            </div>

            <!-- Date -->
            <div class="col-span-1 space-y-1.5 text-left">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Faaliyet Tarihi</label>
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="date"
                        wire:model.defer="activity_date"
                        required
                    />
                </x-filament::input.wrapper>
                @error('activity_date') <p class="text-xs text-red-500 mt-1 ml-0.5">{{ $message }}</p> @enderror
            </div>

            <!-- Department -->
            <div class="col-span-2 space-y-1.5 text-left">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-0.5">Bölüm</label>
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        wire:model.defer="department"
                        required
                        placeholder="Örn: Kardiyoloji Polikliniği 3. Oda"
                    />
                </x-filament::input.wrapper>
                @error('department') <p class="text-xs text-red-500 mt-1 ml-0.5">{{ $message }}</p> @enderror
            </div>

            <!-- Description -->
            <div class="col-span-2 space-y-1.5 text-left">
                <label class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider ml-1">Yapılan İşin Açıklaması</label>
                <x-filament::input.wrapper>
                    <textarea
                        wire:model.defer="description"
                        rows="3"
                        required
                        class="fi-input block w-full border-0 bg-transparent px-3 py-1.5 text-gray-900 focus:ring-0 dark:text-white sm:text-sm sm:leading-6 outline-none"
                        placeholder="Yapılan işlemi kısaca detaylandırın..."
                    ></textarea>
                </x-filament::input.wrapper>
                @error('description') <p class="text-xs text-red-500 mt-1 ml-0.5">{{ $message }}</p> @enderror
            </div>
        </div>

        <!-- Footer Slot (Native Filament Modal Footer) -->
        <x-slot name="footer">
            <div class="flex justify-end gap-3 w-full">
                <x-filament::button color="gray" x-on:click="close" type="button">
                    Vazgeç
                </x-filament::button>
                <x-filament::button type="submit" color="primary" icon="heroicon-m-check">
                    Kaydet
                </x-filament::button>
            </div>
        </x-slot>
    </x-filament::modal>
</div>
