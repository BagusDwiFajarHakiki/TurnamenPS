<?php

namespace App\Filament\Resources\PsUnits\Pages;

use App\Filament\Resources\PsUnits\PsUnitResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPsUnit extends EditRecord
{
    protected static string $resource = PsUnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
