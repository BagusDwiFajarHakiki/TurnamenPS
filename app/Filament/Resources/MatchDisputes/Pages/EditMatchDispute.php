<?php

namespace App\Filament\Resources\MatchDisputes\Pages;

use App\Filament\Resources\MatchDisputes\MatchDisputeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditMatchDispute extends EditRecord
{
    protected static string $resource = MatchDisputeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
