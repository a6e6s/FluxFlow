<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AttachmentsRelationManager extends RelationManager
{
    protected static string $relationship = 'attachments';

    protected static ?string $recordTitleAttribute = 'file_name';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('file_name')
                    ->label(__('admin.fields.file_name'))
                    ->searchable()
                    ->url(fn ($record): string => $record->url, shouldOpenInNewTab: true),
                TextColumn::make('formatted_size')
                    ->label(__('admin.fields.file_size')),
                TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->dateTime(),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}
