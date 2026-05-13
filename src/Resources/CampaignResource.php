<?php

namespace Mydnic\KanpenFilamentPlugin\Resources;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Mydnic\Kanpen\Actions\SendCampaignAction;
use Mydnic\Kanpen\Enums\CampaignStatus;
use Mydnic\Kanpen\Models\Campaign;
use Mydnic\KanpenFilamentPlugin\Resources\CampaignResource\Pages\CreateCampaign;
use Mydnic\KanpenFilamentPlugin\Resources\CampaignResource\Pages\EditCampaign;
use Mydnic\KanpenFilamentPlugin\Resources\CampaignResource\Pages\ListCampaigns;
use Mydnic\KanpenFilamentPlugin\Resources\CampaignResource\Pages\ViewCampaign;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationGroup = 'Kanpen';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Campaign Details')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(2),

            Section::make('Sender')
                ->schema([
                    TextInput::make('from_name')
                        ->label('From name')
                        ->maxLength(255)
                        ->placeholder(config('kanpen.campaigns.from.name')),
                    TextInput::make('from_email')
                        ->label('From email')
                        ->email()
                        ->maxLength(255)
                        ->placeholder(config('kanpen.campaigns.from.email')),
                    TextInput::make('reply_to')
                        ->label('Reply-to')
                        ->email()
                        ->maxLength(255),
                    DateTimePicker::make('scheduled_at')
                        ->label('Schedule at'),
                ])
                ->columns(2),

            Section::make('Content')
                ->schema([
                    RichEditor::make('content_html')
                        ->label('HTML Content')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(50),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (CampaignStatus $state): string => match ($state) {
                        CampaignStatus::Draft => 'gray',
                        CampaignStatus::Sending => 'warning',
                        CampaignStatus::Sent => 'success',
                        CampaignStatus::Cancelled => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('deliveries_count')
                    ->label('Sent to')
                    ->counts('deliveries')
                    ->sortable(),
                TextColumn::make('scheduled_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CampaignStatus::class),
                TrashedFilter::make(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make()
                    ->visible(fn (Campaign $record): bool => $record->isDraft()),
                Action::make('send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->visible(fn (Campaign $record): bool => $record->isDraft())
                    ->requiresConfirmation()
                    ->modalHeading('Send Campaign')
                    ->modalDescription(fn (Campaign $record): string => "Are you sure you want to send \"{$record->name}\" to all subscribers?")
                    ->action(function (Campaign $record): void {
                        try {
                            app(SendCampaignAction::class)->execute($record);
                            Notification::make()
                                ->title('Campaign queued for sending')
                                ->success()
                                ->send();
                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('Failed to send campaign')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                DeleteAction::make()
                    ->visible(fn (Campaign $record): bool => $record->isDraft()),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCampaigns::route('/'),
            'create' => CreateCampaign::route('/create'),
            'view' => ViewCampaign::route('/{record}'),
            'edit' => EditCampaign::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
