<?php

namespace App\Filament\Resources\PsUnits\Pages;

use App\Filament\Resources\PsUnits\PsUnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPsUnits extends ListRecords
{
    protected static string $resource = PsUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
