<?php

namespace App\Filament\Resources\EntryBatches\Pages;

use App\Filament\Resources\EntryBatches\EntryBatchResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEntryBatch extends EditRecord
{
    protected static string $resource = EntryBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
