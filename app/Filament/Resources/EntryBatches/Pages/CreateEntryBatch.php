<?php

namespace App\Filament\Resources\EntryBatches\Pages;

use App\Filament\Resources\EntryBatches\EntryBatchResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEntryBatch extends CreateRecord
{
    protected static string $resource = EntryBatchResource::class;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Buat');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Batal');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
