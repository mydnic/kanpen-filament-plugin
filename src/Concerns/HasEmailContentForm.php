<?php

namespace Mydnic\KanpenFilamentPlugin\Concerns;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Mail\Markdown;
use Mydnic\KanpenFilamentPlugin\Fields\UnlayerEditor;
use Mydnic\KanpenFilamentPlugin\Models\Template;

trait HasEmailContentForm
{
    protected static function contentSection(): Section
    {
        return Section::make('Content')
            ->schema([
                ToggleButtons::make('content_type')
                    ->label('Editor')
                    ->options([
                        'unlayer' => 'Drag & Drop',
                        'markdown' => 'Markdown',
                    ])
                    ->icons([
                        'unlayer' => 'heroicon-o-cursor-arrow-rays',
                        'markdown' => 'heroicon-o-code-bracket',
                    ])
                    ->default('unlayer')
                    ->inline()
                    ->live()
                    ->columnSpanFull(),

                // Unlayer path
                Select::make('_template_id')
                    ->label('Load from template')
                    ->placeholder('Select a template to start from...')
                    ->options(fn () => Template::where('content_type', 'unlayer')
                        ->orWhereNull('content_type')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->toArray())
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function (?int $state, Set $set): void {
                        if (! $state) {
                            return;
                        }
                        $template = Template::find($state);
                        if (! $template) {
                            return;
                        }
                        $set('design', $template->design);
                        $set('content_html', $template->content_html);
                    })
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('content_type') !== 'markdown'),

                Hidden::make('content_html'),

                UnlayerEditor::make('design')
                    ->label('')
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('content_type') !== 'markdown'),

                // Markdown path
                Textarea::make('content_markdown')
                    ->label('Content')
                    ->rows(20)
                    ->live(debounce: 500)
                    ->afterStateUpdated(function (?string $state, Set $set): void {
                        $set('content_html', $state ? (string) Markdown::parse($state) : null);
                    })
                    ->hint('Standard Markdown. Your app\'s default mail theme will be applied automatically.')
                    ->hintIcon('heroicon-o-information-circle')
                    ->columnSpanFull()
                    ->visible(fn (Get $get): bool => $get('content_type') === 'markdown'),
            ]);
    }
}
