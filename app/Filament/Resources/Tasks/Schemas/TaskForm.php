<?php

namespace App\Filament\Resources\Tasks\Schemas;

use App\Actions\Tasks\SanitizeTaskDescription;
use App\Enums\Priority;
use App\Enums\TaskStatus;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TaskForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('project_id')
                    ->label(__('admin.fields.project'))
                    ->relationship('project', 'title')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('assigned_to')
                    ->label(__('admin.fields.assignee'))
                    ->relationship('assignee', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('title')
                    ->label(__('admin.fields.title'))
                    ->required()
                    ->maxLength(255),
                RichEditor::make('description')
                    ->label(__('admin.fields.description'))
                    ->toolbarButtons(['bold', 'italic', 'strike', 'underline', 'bulletList', 'orderedList', 'blockquote', 'codeBlock', 'link'])
                    ->dehydrateStateUsing(fn (?string $state): ?string => app(SanitizeTaskDescription::class)->handle($state ?? '') ?: null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(collect(TaskStatus::cases())->mapWithKeys(
                        fn (TaskStatus $status): array => [$status->value => $status->label()]
                    ))
                    ->required()
                    ->default(TaskStatus::Todo->value),
                Select::make('priority')
                    ->label(__('admin.fields.priority'))
                    ->options(collect(Priority::cases())->mapWithKeys(
                        fn (Priority $priority): array => [$priority->value => $priority->label()]
                    ))
                    ->required()
                    ->default(Priority::Medium->value),
                DatePicker::make('due_date')
                    ->label(__('admin.fields.due_date')),
                TextInput::make('effort_score')
                    ->label(__('admin.fields.effort_score'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10),
                TextInput::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }
}
