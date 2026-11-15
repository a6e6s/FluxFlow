<?php

namespace App\Filament\Resources\Tasks\RelationManagers;

use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvitationsRelationManager extends RelationManager
{
    protected static string $relationship = 'invitations';

    protected static ?string $recordTitleAttribute = 'email';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->label(__('admin.fields.email'))
                    ->searchable(),
                TextColumn::make('inviter.name')
                    ->label(__('admin.fields.invited_by')),
                TextColumn::make('status')
                    ->label(__('admin.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => __('admin.invitation_status.'.$state)),
                TextColumn::make('expires_at')
                    ->label(__('admin.fields.expires_at'))
                    ->dateTime()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->label(__('admin.fields.created_at'))
                    ->dateTime(),
            ])
            ->recordActions([
                DeleteAction::make()
                    ->label(__('admin.actions.revoke')),
            ]);
    }
}
