<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.fields.name'))
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable()
                    ->copyable(),
                IconColumn::make('is_admin')
                    ->label(__('admin.fields.is_admin'))
                    ->boolean(),
                IconColumn::make('email_verified_at')
                    ->label(__('admin.fields.email_verified'))
                    ->boolean()
                    ->getStateUsing(fn (User $record): bool => $record->email_verified_at !== null),
                TextColumn::make('projects_count')
                    ->label(__('admin.fields.projects'))
                    ->counts('projects')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_admin')
                    ->label(__('admin.fields.is_admin'))
                    ->nullable()
                    ->queries(
                        true: fn (Builder $query): Builder => $query->where('is_admin', true),
                        false: fn (Builder $query): Builder => $query->where('is_admin', false),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
