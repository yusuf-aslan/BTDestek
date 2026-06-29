<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    /**
     * Widget grid sütun sayısı — 1 yapınca tam genişlik (full-width) olur.
     */
    public function getColumns(): int|array
    {
        return 1;
    }
}
