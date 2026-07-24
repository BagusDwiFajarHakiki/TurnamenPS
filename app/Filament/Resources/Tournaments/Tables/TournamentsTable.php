<?php

namespace App\Filament\Resources\Tournaments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\Tournaments\TournamentResource;
use Filament\Actions\Action;
use App\Models\TournamentStage;
use App\Services\TournamentService;
use Illuminate\Support\Facades\DB;

class TournamentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('price_per_slot')
                    ->label('Harga per Slot')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('slots_availability')
                    ->label('Slot Tersedia / Total')
                    ->state(function (\App\Models\Tournament $record): string {
                        $verified = $record->entries()->count();
                        $pending = \App\Models\EntryBatch::where('tournament_id', $record->id)
                            ->where('status', 'pending')
                            ->sum('slot_count');
                        $taken = $verified + $pending;
                        $available = max(0, $record->max_entries - $taken);
                        return "{$available} / {$record->max_entries}";
                    }),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'registration' => 'success',
                        'ongoing' => 'warning',
                        'completed' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('registration_start')
                    ->label('Pendaftaran Dibuka')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('tournament_start')
                    ->label('Turnamen Dimulai')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'registration' => 'Pendaftaran Buka',
                        'ongoing' => 'Turnamen Berjalan',
                        'completed' => 'Selesai',
                    ]),
            ])
            ->recordActions([
                static::makeGenerateBracketAction(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    protected static function makeGenerateBracketAction(): Action
    {
        return Action::make('generateBracket')
            ->icon('heroicon-o-sparkles')
            ->iconButton()
            ->color('success')
            ->requiresConfirmation()
            ->modalHeading('Buat Bagan Turnamen')
            ->visible(function (\App\Models\Tournament $record): bool {
                return !$record->stages()->exists()
                    && in_array($record->status, ['registration', 'ongoing'])
                    && $record->tournament_end > now()
                    && $record->registration_end <= now()
                    && $record->entries()->where('status', 'verified')->count() >= 2;
            })
            ->action(function (\App\Models\Tournament $record) {
                $verifiedCount = $record->entries()->where('status', 'verified')->count();

                DB::transaction(function () use ($record) {
                    $stage = TournamentStage::create([
                        'tournament_id' => $record->id,
                        'name' => 'Sistem Gugur',
                        'stage_order' => 1,
                        'format' => 'single_elimination',
                        'source_type' => 'registration',
                        'status' => 'pending',
                    ]);

                    app(TournamentService::class)->generateBracket($stage);

                    $record->update(['status' => 'ongoing']);

                    $verifiedEntries = $record->entries()->where('status', 'verified')->get();
                    $service = app(TournamentService::class);
                    foreach ($verifiedEntries as $entry) {
                        $service->fillSlotOnCheckIn($entry);
                    }
                });

                \Filament\Notifications\Notification::make()
                    ->success()
                    ->title('Bagan Berhasil Dibuat')
                    ->body("{$verifiedCount} slot telah masuk ke bagan.")
                    ->send();
            });
    }
}
