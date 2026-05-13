<?php

namespace Mydnic\KanpenFilamentPlugin\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Mydnic\KanpenFilamentPlugin\Concerns\HasEmailContentForm;
use Mydnic\KanpenFilamentPlugin\Models\Template;
use Mydnic\KanpenFilamentPlugin\Resources\TemplateResource\Pages\CreateTemplate;
use Mydnic\KanpenFilamentPlugin\Resources\TemplateResource\Pages\EditTemplate;
use Mydnic\KanpenFilamentPlugin\Resources\TemplateResource\Pages\ListTemplates;

class TemplateResource extends Resource
{
    use HasEmailContentForm;

    protected static ?string $model = Template::class;

    protected static string | null | \BackedEnum $navigationIcon = 'heroicon-o-document-text';

    protected static string | null | \UnitEnum $navigationGroup = 'Kanpen';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                ])->columns(4),

            static::contentSection()->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('content_type')
                    ->label('Editor')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'markdown' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'markdown' => 'Markdown',
                        default => 'Drag & Drop',
                    }),
                TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('updated_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTemplates::route('/'),
            'create' => CreateTemplate::route('/create'),
            'edit' => EditTemplate::route('/{record}/edit'),
        ];
    }
}
