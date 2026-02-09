<?php

namespace App\Filament\Pages;

use App\Models\Asset;
use App\Models\Location;
use Filament\Pages\Page;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;


class AssetQuery extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $title = 'Envanter Sorgulama';
    protected static ?string $navigationLabel = 'Envanter Sorgula';
    protected static ?int $navigationSort = 2;
    
    protected string $view = 'filament.pages.asset-query';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'anabirim' => 'all',
            'model' => 'all',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Filtreleme Kriterleri')
                    ->description('Envanterleri sorgulamak için ana birim ve isteğe bağlı olarak model seçin.')
                    ->schema([
                        Select::make('anabirim')
                            ->label('Ana Birim')
                            ->options(function () {
                                $anabirims = Location::query()
                                    ->distinct()
                                    ->orderBy('anabirim')
                                    ->pluck('anabirim', 'anabirim')
                                    ->toArray();
                                return ['all' => 'Tüm Bölümler'] + $anabirims;
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                // Reset model when anabirim changes
                                if ($state === 'all') {
                                    $set('model', 'all');
                                }
                            }),
                        
                        Select::make('model')
                            ->label('Model')
                            ->options(function (Get $get) {
                                $anabirim = $get('anabirim');
                                
                                $query = Asset::query();

                                if ($anabirim && $anabirim !== 'all') {
                                    $query->whereHas('location', function (Builder $q) use ($anabirim) {
                                        $q->where('anabirim', $anabirim);
                                    });
                                }

                                $models = $query
                                    ->whereNotNull('model')
                                    ->where('model', '!=', '')
                                    ->distinct()
                                    ->orderBy('model')
                                    ->pluck('model', 'model')
                                    ->toArray();

                                return ['all' => 'Tümü'] + $models;
                            })
                            ->default('all')
                            ->searchable()
                            ->live()

                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Envanter Yönetimi';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Asset::query()->with(['location']);

                // Apply Anabirim filter
                if (!empty($this->data['anabirim']) && $this->data['anabirim'] !== 'all') {
                    $query->whereHas('location', function (Builder $q) {
                        $q->where('anabirim', $this->data['anabirim']);
                    });
                }

                // Apply Model filter
                if (!empty($this->data['model']) && $this->data['model'] !== 'all') {
                    $query->where('model', $this->data['model']);
                }

                // Special case: If "Tüm Bölümler" is selected for anabirim AND no specific model is chosen,
                // then default to active computers.
                if ($this->data['anabirim'] === 'all' && ($this->data['model'] === 'all' || empty($this->data['model']))) {
                    $query->where('status', 'active')->where('type', 'computer');
                }

                return $query;
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Cihaz Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('type')
                    ->label('Tür')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'computer' => 'Bilgisayar',
                        'printer' => 'Yazıcı',
                        'network' => 'Ağ Cihazı',
                        'monitor' => 'Monitör',
                        'tablet' => 'Tablet',
                        'medical' => 'Tıbbi Cihaz',
                        'other' => 'Diğer',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'computer' => 'info',
                        'printer' => 'warning',
                        'network' => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('model')
                    ->label('Marka Model')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('location.anabirim')
                    ->label('Ana Birim')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('location.altbirim')
                    ->label('Alt Birim')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'retired' => 'Hurda',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'retired' => 'danger',
                        default => 'info',
                    }),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('Envanter Bulunamadı')
            ->emptyStateDescription('Seçtiğiniz kriterlere uygun envanter bulunamadı. Lütfen filtreleme kriterlerinizi kontrol edin.');
    }
}
