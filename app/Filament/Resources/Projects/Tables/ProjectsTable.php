<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Enums\Priority;
use App\Models\Project;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('icon')
                    ->label(__('admin.fields.icon')),
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('user.name')
                    ->label(__('admin.fields.owner'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('priority')
                    ->label(__('admin.fields.priority'))
                    ->badge()
                    ->formatStateUsing(fn (Priority $state): string => $state->label())
                    ->color(fn (Priority $state): string => match ($state) {
                        Priority::Low => 'success',
                        Priority::Medium => 'warning',
                        Priority::High => 'danger',
                    })
                    ->sortable(),
                TextColumn::make('tasks_count')
                    ->label(__('admin.fields.tasks'))
                    ->counts('tasks')
                    ->sortable(),
                ColorColumn::make('color')
                    ->label(__('admin.fields.color')),
                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->sortable(),
                TextColumn::make('archived_at')
                    ->label(__('admin.fields.archived_at'))
                    ->dateTime()
                    ->placeholder(__('admin.status.active'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('priority')
                    ->label(__('admin.fields.priority'))
                    ->options(collect(Priority::cases())->mapWithKeys(
                        fn (Priority $priority): array => [$priority->value => $priority->label()]
                    )),
                TernaryFilter::make('archived')
                    ->label(__('admin.status.archived'))
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query): Builder => $query->whereNotNull('archived_at'),
                        false: fn (Builder $query): Builder => $query->whereNull('archived_at'),
                    ),
                TrashedFilter::make(),
            ])
            ->recordActions([
                Action::make('archive')
                    ->label(__('admin.actions.archive'))
                    ->icon('heroicon-o-archive-box')
                    ->color('warning')
                    ->visible(fn (Project $record): bool => ! $record->isArchived())
                    ->requiresConfirmation()
                    ->action(fn (Project $record): bool => $record->update(['archived_at' => now()])),
                Action::make('unarchive')
                    ->label(__('admin.actions.unarchive'))
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('success')
                    ->visible(fn (Project $record): bool => $record->isArchived())
                    ->action(fn (Project $record): bool => $record->update(['archived_at' => null])),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ]);
    }
}
