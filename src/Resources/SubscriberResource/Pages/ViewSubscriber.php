<?php

namespace Mydnic\KanpenFilamentPlugin\Resources\SubscriberResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;
use Mydnic\Kanpen\Models\Subscriber;
use Mydnic\KanpenFilamentPlugin\Resources\SubscriberResource;
use Mydnic\KanpenFilamentPlugin\Resources\SubscriberResource\RelationManagers\DeliveriesRelationManager;

class ViewSubscriber extends ViewRecord
{
    protected static string $resource = SubscriberResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make('Subscriber')
                ->schema([
                    TextEntry::make('email'),
                    IconEntry::make('email_verified_at')
                        ->label('Verified')
                        ->boolean()
                        ->getStateUsing(fn (Subscriber $record): bool => $record->hasVerifiedEmail()),
                    TextEntry::make('email_verified_at')
                        ->label('Verified at')
                        ->dateTime()
                        ->placeholder('Not verified'),
                    TextEntry::make('created_at')
                        ->label('Subscribed at')
                        ->dateTime(),
                ])
                ->columns(4),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    public function getRelationManagers(): array
    {
        return [
            DeliveriesRelationManager::class,
        ];
    }
}
