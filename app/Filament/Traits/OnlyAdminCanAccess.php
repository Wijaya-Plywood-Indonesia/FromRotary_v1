<?php

namespace App\Filament\Traits;

trait OnlyAdminCanAccess
{
    public static function canAccess(): bool
    {
        // Hanya admin & super_admin yang boleh buka halaman ini
        return auth()->user()->hasRole(['admin', 'super_admin']);
    }
}