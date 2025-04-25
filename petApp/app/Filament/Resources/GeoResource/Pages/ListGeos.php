<?php

namespace App\Filament\Resources\GeoResource\Pages;

use App\Filament\Resources\GeoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGeos extends ListRecords
{
    protected static string $resource = GeoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
