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

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()->label('Simpan');
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()->label('Batal');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['status']) && $data['status'] === 'verified' && $this->record->status !== 'verified') {
            $tournament = \App\Models\Tournament::find($this->record->tournament_id);
            
            if ($tournament && $tournament->max_slot_per_player > 0) {
                $existingCount = \App\Models\TournamentEntry::where('tournament_id', $tournament->id)
                    ->where('player_id', $this->record->player_id)
                    ->count();

                if (($existingCount + $this->record->slot_count) > $tournament->max_slot_per_player) {
                    \Filament\Notifications\Notification::make()
                        ->danger()
                        ->title('Gagal Disetujui')
                        ->body("Peserta ini sudah mencapai batas maksimal slot ({$tournament->max_slot_per_player} slot).")
                        ->send();

                    $this->halt();
                }
            }
            
            if ($tournament && $tournament->max_entries > 0) {
                $totalVerified = \App\Models\TournamentEntry::where('tournament_id', $tournament->id)->count();
                if (($totalVerified + $this->record->slot_count) > $tournament->max_entries) {
                    \Filament\Notifications\Notification::make()
                        ->danger()
                        ->title('Gagal Disetujui')
                        ->body('Total slot melebihi sisa kuota turnamen yang tersedia.')
                        ->send();

                    $this->halt();
                }
            }
        }

        return $data;
    }
}
