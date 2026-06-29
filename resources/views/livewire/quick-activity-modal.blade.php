<div>
    <x-filament::modal
        id="quick-activity"
        width="lg"
        icon="heroicon-o-clipboard-document-list"
        icon-color="primary"
        heading="Hızlı Faaliyet Ekle"
        description="Bilet dışı yapılan faaliyet kaydı"
        :close-by-clicking-away="true"
    >
        <x-slot name="trigger">
            <x-filament::button
                icon="heroicon-o-plus-circle"
                color="primary"
                size="sm"
            >
                Faaliyet Ekle
            </x-filament::button>
        </x-slot>

        <form wire:submit.prevent="save">
            {{-- Faaliyet Türü --}}
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">
                    Faaliyet Türü
                </label>
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
                @error('activity_type') <p style="font-size:0.75rem; color:#ef4444; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Süre ve Tarih (yan yana) --}}
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">
                        Harcanan Süre (Dk)
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="number"
                            wire:model.defer="duration"
                            required
                            min="1"
                            placeholder="Örn: 15"
                        />
                    </x-filament::input.wrapper>
                    @error('duration') <p style="font-size:0.75rem; color:#ef4444; margin-top:0.25rem;">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">
                        Faaliyet Tarihi
                    </label>
                    <x-filament::input.wrapper>
                        <x-filament::input
                            type="date"
                            wire:model.defer="activity_date"
                            required
                        />
                    </x-filament::input.wrapper>
                    @error('activity_date') <p style="font-size:0.75rem; color:#ef4444; margin-top:0.25rem;">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Bölüm --}}
            <div style="margin-bottom: 1.25rem;">
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">
                    Bölüm
                </label>
                <x-filament::input.wrapper>
                    <x-filament::input
                        type="text"
                        wire:model.defer="department"
                        required
                        placeholder="Örn: Kardiyoloji Polikliniği 3. Oda"
                    />
                </x-filament::input.wrapper>
                @error('department') <p style="font-size:0.75rem; color:#ef4444; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Açıklama --}}
            <div style="margin-bottom: 1.5rem;">
                <label style="display:block; font-size:0.75rem; font-weight:600; color:#64748b; text-transform:uppercase; letter-spacing:0.05em; margin-bottom:0.5rem;">
                    Yapılan İşin Açıklaması
                </label>
                <x-filament::input.wrapper>
                    <textarea
                        wire:model.defer="description"
                        rows="4"
                        required
                        placeholder="Yapılan işlemi kısaca detaylandırın..."
                        style="display:block; width:100%; background:transparent; border:none; padding:0.5rem 0.75rem; font-size:0.875rem; line-height:1.5; color:inherit; outline:none; resize:vertical; box-sizing:border-box;"
                    ></textarea>
                </x-filament::input.wrapper>
                @error('description') <p style="font-size:0.75rem; color:#ef4444; margin-top:0.25rem;">{{ $message }}</p> @enderror
            </div>

            {{-- Butonlar --}}
            <div style="display:flex; justify-content:flex-end; align-items:center; gap:0.75rem; padding-top:1.25rem; margin-top:0.5rem; border-top:1px solid #e2e8f0;">
                <x-filament::button
                    color="gray"
                    type="button"
                    x-on:click="$dispatch('close-modal', { id: 'quick-activity' })"
                >
                    Vazgeç
                </x-filament::button>

                <x-filament::button
                    type="submit"
                    color="primary"
                    icon="heroicon-m-check"
                >
                    Kaydet
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>
</div>
