<?php

namespace Mydnic\KanpenFilamentPlugin\Resources\SubscriberResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Mydnic\Kanpen\Models\CampaignDelivery;

class DeliveriesRelationManager extends RelationManager
{
    protected static string $relationship = 'deliveries';

    protected static ?string $title = 'Campaign History';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('campaign.name')
                    ->label('Campaign')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('campaign.subject')
                    ->label('Subject')
                    ->limit(50)
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->label('Sent at')
                    ->dateTime()
                    ->sortable(),
                IconColumn::make('opened_at')
                    ->label('Opened')
                    ->boolean()
                    ->getStateUsing(fn (CampaignDelivery $record): bool => $record->opened_at !== null)
                    ->trueIcon('heroicon-o-envelope-open')
                    ->falseIcon('heroicon-o-envelope'),
                TextColumn::make('open_count')
                    ->label('Opens')
                    ->sortable(),
                IconColumn::make('clicked_at')
                    ->label('Clicked')
                    ->boolean()
                    ->getStateUsing(fn (CampaignDelivery $record): bool => $record->clicked_at !== null)
                    ->trueIcon('heroicon-o-cursor-arrow-rays')
                    ->falseIcon('heroicon-o-minus'),
                TextColumn::make('clicks_count')
                    ->label('Clicks')
                    ->counts('clicks')
                    ->sortable(),
            ])
            ->defaultSort('sent_at', 'desc');
    }
}
