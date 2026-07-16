<?php

namespace App\Filament\Resources\EntryBatches\Pages;

use App\Filament\Resources\EntryBatches\EntryBatchResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListEntryBatches extends ListRecords
{
    protected static string $resource = EntryBatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
