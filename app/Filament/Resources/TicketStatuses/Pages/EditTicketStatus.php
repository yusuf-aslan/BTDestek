<?php
namespace App\Filament\Resources\TicketStatuses\Pages;
use App\Filament\Resources\TicketStatuses\TicketStatusResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
class EditTicketStatus extends EditRecord { protected static string $resource=TicketStatusResource::class; protected function getRedirectUrl():string{return $this->getResource()::getUrl('index');} protected function getHeaderActions():array{return [DeleteAction::make()->visible(fn():bool=>!\App\Models\Ticket::where('status',$this->record->key)->exists())];} }
