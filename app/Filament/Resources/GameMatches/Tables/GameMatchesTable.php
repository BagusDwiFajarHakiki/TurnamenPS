<?php

namespace App\Filament\Resources\GameMatches\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use App\Services\TournamentService;
use Filament\Notifications\Notification;

class GameMatchesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->poll('3s')
            ->columns([
                TextColumn::make('stage.name')
                    ->label('Babak')
                    ->sortable(),

                TextColumn::make('bracket_position')
                    ->label('Posisi')
                    ->sortable(),

                TextColumn::make('match_details')
                    ->label('Pertandingan')
                    ->state(function ($record) {
                        $home = $record->participants->where('side', 'home')->first();
                        $away = $record->participants->where('side', 'away')->first();
                        
                        $homeName = $home?->entry?->display_name ?? 'TBD';
                        $homeClub = $home?->club?->name ? " ({$home->club->name})" : '';
                        
                        $awayName = $away?->entry?->display_name ?? 'TBD';
                        $awayClub = $away?->club?->name ? " ({$away->club->name})" : '';
                        
                        return "{$homeName}{$homeClub} VS {$awayName}{$awayClub}";
                    }),

                TextColumn::make('score')
                    ->label('Skor')
                    ->state(function ($record) {
                        if (in_array($record->status, ['pending', 'ready'])) {
                            return '-';
                        }
                        $home = $record->participants->where('side', 'home')->first();
                        $away = $record->participants->where('side', 'away')->first();
                        
                        $homeScore = $home?->goals_scored ?? 0;
                        $awayScore = $away?->goals_scored ?? 0;
                        
                        if ($record->status === 'walkover') {
                            return "WO ({$homeScore} - {$awayScore})";
                        }
                        
                        if ($record->decided_by_penalty) {
                            return "{$homeScore} - {$awayScore} (Pen: {$record->penalty_score_home} - {$record->penalty_score_away})";
                        }
                        
                        return "{$homeScore} - {$awayScore}";
                    }),

                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'gray',
                        'ready' => 'info',
                        'scheduled' => 'warning',
                        'ongoing' => 'primary',
                        'completed' => 'success',
                        'walkover' => 'danger',
                        default => 'gray',
                    })
                    ->description(function ($record) {
                        if (in_array($record->status, ['ready', 'scheduled', 'ongoing'])) {
                            $t = $record->stage?->tournament;
                            if ($t && $t->no_show_deadline_minutes) {
                                $limit = $t->no_show_deadline_minutes;
                                $elapsed = $record->updated_at->diffInMinutes(now());
                                if ($elapsed >= $limit) {
                                    return "LEWAT BATAS WAKTU ({$elapsed}m > {$limit}m)";
                                }
                            }
                        }
                        return null;
                    })
                    ->sortable(),

                TextColumn::make('finished_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('psUnit.name')
                    ->label('Console')
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('scheduled_at')
                    ->label('Jadwal')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'ready' => 'Siap',
                        'scheduled' => 'Terjadwal',
                        'ongoing' => 'Sedang Main',
                        'completed' => 'Selesai',
                        'walkover' => 'Walkover',
                    ]),
            ])
            ->actions([
                \Filament\Actions\Action::make('triggerQueue')
                    ->label('Jalankan Antrean')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->action(function () {
                        $service = app(TournamentService::class);
                        $service->processQueue();
                        Notification::make()
                            ->title('Proses antrean FIFO selesai dijalankan.')
                            ->success()
                            ->send();
                    })
                    ->button()
                    ->visible(fn ($record) => $record->status === 'ready'),
                \Filament\Actions\EditAction::make(),
                \Filament\Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
