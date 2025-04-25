<?php

namespace App\Filament\Resources\GeoResource\Pages;

use App\Filament\Resources\GeoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGeo extends EditRecord
{
    protected static string $resource = GeoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
