<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Models\Task;
use App\Models\Ticket;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->columnSpanFull()
                    ->schema([
                        Section::make('İş')
                            ->columnSpan(['default' => 3, 'lg' => 2])
                            ->schema([
                                TextInput::make('title')
                                    ->label('Başlık')
                                    ->placeholder('Örn. 3. kat yazıcılarının toner kontrolü')
                                    ->required()
                                    ->maxLength(200)
                                    ->autofocus(),

                                RichEditor::make('description')
                                    ->label('Ayrıntılar')
                                    ->placeholder('Yapılacakları, notları, bağlantıları yazın.')
                                    ->toolbarButtons([
                                        ['bold', 'italic', 'underline', 'strike', 'link'],
                                        ['h3', 'bulletList', 'orderedList', 'blockquote', 'codeBlock'],
                                        ['undo', 'redo'],
                                    ]),

                                Repeater::make('checklist')
                                    ->label('Alt Adımlar')
                                    ->helperText('İşi küçük adımlara bölün; listede kaçının bittiği görünür.')
                                    ->table([
                                        TableColumn::make('Tamam')->width('4rem'),
                                        TableColumn::make('Adım'),
                                    ])
                                    ->schema([
                                        Checkbox::make('done')->default(false),
                                        TextInput::make('text')->required()->maxLength(255)->placeholder('Örn. Yedek toner sipariş et'),
                                    ])
                                    ->addActionLabel('Adım Ekle')
                                    ->reorderable()
                                    ->defaultItems(0),
                            ]),

                        Grid::make(1)
                            ->columnSpan(['default' => 3, 'lg' => 1])
                            ->schema([
                                Section::make('Süre ve Hatırlatma')
                                    ->schema([
                                        DateTimePicker::make('due_at')
                                            ->label('Son Tarih')
                                            ->seconds(false)
                                            ->native(false)
                                            ->displayFormat('d.m.Y H:i')
                                            ->minutesStep(5)
                                            ->helperText('Saat seçilmezse 17:00 kabul edilir.')
                                            // Takvimden yalnızca gün seçilince saat 00:00 kalır; bu gece yarısı değil gün sonu demektir
                                            ->mutateDehydratedStateUsing(function ($state) {
                                                if (blank($state)) {
                                                    return $state;
                                                }

                                                $date = \Illuminate\Support\Carbon::parse($state);

                                                return ($date->format('H:i') === '00:00' ? $date->setTime(17, 0) : $date)->toDateTimeString();
                                            })
                                            ->live(),

                                        Select::make('remind_before_minutes')
                                            ->label('Hatırlatma')
                                            ->options(Task::REMINDERS)
                                            ->placeholder('Hatırlatma yok')
                                            ->default(60)
                                            ->helperText('Hatırlatma ve süre aşımı sağ üstteki bildirimlere düşer.')
                                            ->visible(fn (Get $get): bool => filled($get('due_at'))),

                                        Select::make('repeat')
                                            ->label('Tekrar')
                                            ->options(Task::REPEATS)
                                            ->placeholder('Tekrarlanmaz')
                                            ->helperText('Tamamlayınca bir sonraki tarih için yenisi oluşur.')
                                            ->visible(fn (Get $get): bool => filled($get('due_at'))),
                                    ]),

                                Section::make('Durum')
                                    ->schema([
                                        ToggleButtons::make('priority')
                                            ->label('Öncelik')
                                            ->options(Task::PRIORITIES)
                                            ->colors(Task::PRIORITY_COLORS)
                                            ->default('orta')
                                            ->inline()
                                            ->required(),

                                        Select::make('status')
                                            ->label('Durum')
                                            ->options(Task::STATUSES)
                                            ->default('bekliyor')
                                            ->required()
                                            ->native(false),

                                        Select::make('ticket_id')
                                            ->label('İlgili Talep')
                                            ->placeholder('Talep seçin (isteğe bağlı)')
                                            ->searchable()
                                            ->getSearchResultsUsing(fn (string $search): array => Ticket::query()
                                                ->visibleTo(Filament::auth()->user())
                                                ->where(fn ($query) => $query
                                                    ->where('tracking_number', 'like', "%{$search}%")
                                                    ->orWhere('subject', 'like', "%{$search}%"))
                                                ->latest()
                                                ->limit(20)
                                                ->get()
                                                ->mapWithKeys(fn (Ticket $ticket) => [$ticket->id => static::ticketLabel($ticket)])
                                                ->all())
                                            ->getOptionLabelUsing(fn ($value): ?string => ($ticket = Ticket::query()->visibleTo(Filament::auth()->user())->find($value))
                                                ? static::ticketLabel($ticket)
                                                : null)
                                            ->rule(fn () => function (string $attribute, $value, \Closure $fail) {
                                                if ($value && ! Ticket::query()->visibleTo(Filament::auth()->user())->whereKey($value)->exists()) {
                                                    $fail('Bu talebe erişiminiz yok.');
                                                }
                                            }),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    private static function ticketLabel(Ticket $ticket): string
    {
        return $ticket->tracking_number.' · '.str($ticket->subject)->limit(50);
    }
}
