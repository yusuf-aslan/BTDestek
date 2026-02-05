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

class AssetQuery extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-magnifying-glass';
    protected static ?string $title = 'Varlık Sorgulama';
    protected static ?string $navigationLabel = 'Varlık Sorgula';
    protected static ?int $navigationSort = 2;
    
    protected string $view = 'filament.pages.asset-query';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'anabirim' => null,
            'model' => 'all',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Filtreleme Kriterleri')
                    ->description('Varlıkları sorgulamak için ana birim ve isteğe bağlı olarak model seçin.')
                    ->schema([
                        Select::make('anabirim')
                            ->label('Ana Birim')
                            ->options(function () {
                                return Location::query()
                                    ->distinct()
                                    ->orderBy('anabirim')
                                    ->pluck('anabirim', 'anabirim');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                // Reset model when anabirim changes
                                $this->data['model'] = 'all';
                            }),
                        
                        Select::make('model')
                            ->label('Model')
                            ->options(function (Get $get) {
                                $anabirim = $get('anabirim');
                                
                                if (!$anabirim) {
                                    return ['all' => 'Tümü'];
                                }

                                $models = Asset::query()
                                    ->whereHas('location', function (Builder $query) use ($anabirim) {
                                        $query->where('anabirim', $anabirim);
                                    })
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
                            ->disabled(fn (Get $get): bool => !$get('anabirim')),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Varlık Yönetimi';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                $query = Asset::query()->with(['location', 'assignedUser']);

                if (!empty($this->data['anabirim'])) {
                    $query->whereHas('location', function (Builder $q) {
                        $q->where('anabirim', $this->data['anabirim']);
                    });
                }

                if (!empty($this->data['model']) && $this->data['model'] !== 'all') {
                    $query->where('model', $this->data['model']);
                }

                return $query;
            })
            ->columns([
                TextColumn::make('name')
                    ->label('Cihaz Adı')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('asset_tag')
                    ->label('Demirbaş No')
                    ->searchable()
                    ->copyable(),
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
                TextColumn::make('brand')
                    ->label('Marka')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('model')
                    ->label('Model')
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
                TextColumn::make('assignedUser.name')
                    ->label('Zimmetli Kişi')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Atanmamış'),
                TextColumn::make('status')
                    ->label('Durum')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'active' => 'Aktif',
                        'stock' => 'Depoda',
                        'maintenance' => 'Bakımda',
                        'retired' => 'Hurda',
                        'broken' => 'Arızalı',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'maintenance' => 'warning',
                        'broken', 'retired' => 'danger',
                        'stock' => 'gray',
                        default => 'info',
                    }),
            ])
            ->defaultSort('name', 'asc')
            ->emptyStateHeading('Varlık Bulunamadı')
            ->emptyStateDescription('Seçtiğiniz kriterlere uygun varlık bulunamadı. Lütfen filtreleme kriterlerinizi kontrol edin.')
            ->emptyStateIcon('heroicon-o-magnifying-glass');
    }
}
