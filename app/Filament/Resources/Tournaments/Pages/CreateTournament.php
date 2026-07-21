<?php

namespace App\Filament\Resources\Tournaments\Pages;

use App\Filament\Resources\Tournaments\TournamentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTournament extends CreateRecord
{
    protected static string $resource = TournamentResource::class;

    protected function getCreateFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateFormAction()->label('Buat Turnamen');
    }

    protected function getCreateAnotherFormAction(): \Filament\Actions\Action
    {
        return parent::getCreateAnotherFormAction()->hidden();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
