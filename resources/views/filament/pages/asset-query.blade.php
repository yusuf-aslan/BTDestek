<x-filament-panels::page>
    <div style="display: flex; flex-direction: column; gap: 10px;">
        {{-- Filtreleme Formu --}}
        <x-filament::section>
            <x-slot name="heading">
                Filtreleme Kriterleri
            </x-slot>
            <x-slot name="description">
                Varlıkları sorgulamak için ana birim ve isteğe bağlı olarak model seçin.
            </x-slot>
            
            <form wire:submit.prevent="$refresh">
                {{ $this->form }}
            </form>
        </x-filament::section>

        {{-- Sonuç Tablosu --}}
        @if (!empty($this->data['anabirim']))
            <div style="display: flex; flex-direction: column; gap: 10px;">
                <x-filament::section>
                    <x-slot name="heading">
                        Sorgu Sonuçları
                    </x-slot>
                    <x-slot name="description">
                        Ana Birim: <strong>{{ $this->data['anabirim'] }}</strong>
                        @if(!empty($this->data['model']) && $this->data['model'] !== 'all')
                            | Model: <strong>{{ $this->data['model'] }}</strong>
                        @else
                            | Model: <strong>Tümü</strong>
                        @endif
                    </x-slot>
                    
                    {{ $this->table }}
                </x-filament::section>

                <x-filament::section>
                    <div class="flex items-center justify-between gap-5">
                        <div class="flex-1">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                                Raporlama
                            </h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Listelenen sonuçları yazdırmak veya PDF olarak dışa aktarmak için tıklayın.
                            </p>
                        </div>
                        
                        <x-filament::button
                            tag="a"
                            href="{{ route('asset-query.print', [
                                'anabirim' => $this->data['anabirim'] ?? '',
                                'model' => $this->data['model'] ?? 'all',
                            ]) }}"
                            target="_blank"
                            color="primary"
                            icon="heroicon-o-printer"
                            size="md"
                        >
                            Yazdır / PDF
                        </x-filament::button>
                    </div>
                </x-filament::section>
            </div>
        @else
            <x-filament::section>
                <div class="text-center py-12">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                        Varlık Sorgusu
                    </h3>
                    <p class="text-gray-600 dark:text-gray-400">
                        Varlıkları sorgulamak için yukarıdaki formdan <strong>Ana Birim</strong> seçin ve isteğe bağlı olarak 
                        <strong>Model</strong> filtresi uygulayın.
                    </p>
                </div>
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
