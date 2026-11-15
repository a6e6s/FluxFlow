<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Only users with `is_admin` can reach the admin panel (see User::canAccessPanel),
 * so resources open all actions here instead of going through the app policies.
 */
trait GrantsAdminPanelAccess
{
    public static function can(string|UnitEnum $action, ?Model $record = null): bool
    {
        return true;
    }

    public static function canAccess(): bool
    {
        return true;
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canView(Model $record): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canEdit(Model $record): bool
    {
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }

    public static function canRestore(Model $record): bool
    {
        return true;
    }

    public static function canRestoreAny(): bool
    {
        return true;
    }

    public static function canForceDelete(Model $record): bool
    {
        return true;
    }

    public static function canForceDeleteAny(): bool
    {
        return true;
    }
}
