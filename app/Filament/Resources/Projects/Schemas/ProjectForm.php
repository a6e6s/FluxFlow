<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Enums\Priority;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('admin.fields.owner'))
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('title')
                    ->label(__('admin.fields.title'))
                    ->required()
                    ->maxLength(255),
                TextInput::make('icon')
                    ->label(__('admin.fields.icon'))
                    ->maxLength(16),
                ColorPicker::make('color')
                    ->label(__('admin.fields.color')),
                Select::make('priority')
                    ->label(__('admin.fields.priority'))
                    ->options(collect(Priority::cases())->mapWithKeys(
                        fn (Priority $priority): array => [$priority->value => $priority->label()]
                    ))
                    ->required()
                    ->default(Priority::Medium->value),
                TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
                    ->default(0)
                    ->required(),
                DateTimePicker::make('archived_at')
                    ->label(__('admin.fields.archived_at')),
            ]);
    }
}
