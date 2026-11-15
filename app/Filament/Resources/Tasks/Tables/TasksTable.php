<?php

namespace App\Filament\Resources\Tasks\Tables;

use App\Enums\Priority;
use App\Enums\TaskStatus;
use App\Models\Task;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TasksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label(__('admin.fields.title'))
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('project.title')
                    ->label(__('admin.fields.project'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label(__('admin.fields.assignee'))
                    ->placeholder(__('admin.status.unassigned'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (TaskStatus $state): string => $state->label())
                    ->color(fn (TaskStatus $state): string => match ($state) {
                        TaskStatus::Todo => 'gray',
                        TaskStatus::Doing => 'info',
                        TaskStatus::Review => 'warning',
                        TaskStatus::Done => 'success',
                    })
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
                TextColumn::make('due_date')
                    ->label(__('admin.fields.due_date'))
                    ->date()
                    ->color(fn (Task $record): ?string => $record->isOverdue() ? 'danger' : null)
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('effort_score')
                    ->label(__('admin.fields.effort_score'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sort_order')
                    ->label(__('admin.fields.sort_order'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('admin.fields.status'))
                    ->options(collect(TaskStatus::cases())->mapWithKeys(
                        fn (TaskStatus $status): array => [$status->value => $status->label()]
                    )),
                SelectFilter::make('priority')
                    ->label(__('admin.fields.priority'))
                    ->options(collect(Priority::cases())->mapWithKeys(
                        fn (Priority $priority): array => [$priority->value => $priority->label()]
                    )),
                TernaryFilter::make('overdue')
                    ->label(__('admin.status.overdue'))
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query): Builder => $query->overdue(),
                        false: fn (Builder $query): Builder => $query->whereNot(
                            fn (Builder $overdueQuery): Builder => $overdueQuery->overdue()
                        ),
                    ),
                TrashedFilter::make(),
            ])
            ->recordActions([
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
