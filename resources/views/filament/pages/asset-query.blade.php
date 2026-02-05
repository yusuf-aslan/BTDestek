<x-filament-panels::page>
    <div class="space-y-6">
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
        @else
            <x-filament::section>
                <div class="text-center py-12">
                    <x-filament::icon 
                        icon="heroicon-o-information-circle" 
                        class="w-16 h-16 mx-auto text-gray-400 mb-4"
                    />
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
