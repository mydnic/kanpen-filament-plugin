<?php

namespace Mydnic\KanpenFilamentPlugin\Resources\CampaignResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Mydnic\Kanpen\Actions\SendCampaignAction;
use Mydnic\Kanpen\Actions\SendTestCampaignAction;
use Mydnic\Kanpen\Enums\CampaignStatus;
use Mydnic\Kanpen\Models\Campaign;
use Mydnic\KanpenFilamentPlugin\Resources\CampaignResource;

class ViewCampaign extends ViewRecord
{
    protected static string $resource = CampaignResource::class;

    public function infolist(Schema $infolist): Schema
    {
        return $infolist->components([
            Section::make('Campaign')
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('subject'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (CampaignStatus $state): string => match ($state) {
                            CampaignStatus::Draft => 'gray',
                            CampaignStatus::Sending => 'warning',
                            CampaignStatus::Sent => 'success',
                            CampaignStatus::Cancelled => 'danger',
                        }),
                    TextEntry::make('scheduled_at')->dateTime(),
                    TextEntry::make('sent_at')->dateTime(),
                ])
                ->columns(3),

            Section::make('Sender')
                ->schema([
                    TextEntry::make('from_name')->label('From name'),
                    TextEntry::make('from_email')->label('From email'),
                    TextEntry::make('reply_to')->label('Reply-to'),
                ])
                ->columns(3),

            Section::make('Stats')
                ->schema([
                    TextEntry::make('deliveries_count')
                        ->label('Total sent')
                        ->state(fn (Campaign $record): int => $record->deliveries()->count()),
                    TextEntry::make('opens_count')
                        ->label('Unique opens')
                        ->state(fn (Campaign $record): int => $record->deliveries()->whereNotNull('opened_at')->count()),
                    TextEntry::make('clicks_count')
                        ->label('Total clicks')
                        ->state(fn (Campaign $record): int => $record->clicks()->count()),
                ])
                ->columns(3)
                ->visible(fn (Campaign $record): bool => $record->isSent() || $record->isSending()),

            Section::make('Content')
                ->schema([
                    TextEntry::make('content_html')
                        ->label('HTML Content')
                        ->html()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->visible(fn (Campaign $record): bool => $record->isDraft()),

            Action::make('send')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->visible(fn (Campaign $record): bool => $record->isDraft())
                ->requiresConfirmation()
                ->modalHeading('Send Campaign')
                ->modalDescription(fn (): string => "Are you sure you want to send \"{$this->record->name}\" to all subscribers?")
                ->action(function (): void {
                    try {
                        app(SendCampaignAction::class)->execute($this->record);
                        Notification::make()
                            ->title('Campaign queued for sending')
                            ->success()
                            ->send();
                        $this->refreshFormData(['status', 'sent_at']);
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Failed to send campaign')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Action::make('send_test')
                ->label('Send test')
                ->icon('heroicon-o-beaker')
                ->color('gray')
                ->visible(fn (Campaign $record): bool => $record->isDraft())
                ->form([
                    TextInput::make('email')
                        ->label('Recipient email')
                        ->email()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        app(SendTestCampaignAction::class)->execute($this->record, $data['email']);
                        Notification::make()
                            ->title('Test email sent to ' . $data['email'])
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Failed to send test email')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
