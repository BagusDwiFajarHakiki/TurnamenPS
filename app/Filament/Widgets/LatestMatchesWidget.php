<?php

namespace App\Filament\Widgets;

use App\Models\GameMatch;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestMatchesWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Pertandingan Terkini';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                GameMatch::query()
                    ->whereIn('status', ['ready', 'scheduled', 'ongoing', 'completed'])
                    ->with(['participants.entry.player', 'participants.club', 'psUnit', 'stage.tournament'])
                    ->orderByRaw("FIELD(status, 'ongoing', 'scheduled', 'ready', 'completed')")
                    ->orderByDesc('updated_at')
                    ->limit(8)
            )
            ->columns([
                Tables\Columns\TextColumn::make('stage.tournament.name')
                    ->label('Turnamen')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('bracket_position')
                    ->label('Babak / Pos')
                    ->formatStateUsing(function ($record) {
                        $maxRound = \App\Models\GameMatch::where('tournament_stage_id', $record->tournament_stage_id)->max('round_number') ?? 1;
                        $stagesLeft = $maxRound - $record->round_number;
                        if ($stagesLeft === 0) {
                            $roundLabel = 'Final';
                        } elseif ($stagesLeft === 1) {
                            $roundLabel = 'Semifinal';
                        } elseif ($stagesLeft === 2) {
                            $roundLabel = 'Perempat Final';
                        } else {
                            $teamsInRound = pow(2, $stagesLeft + 1);
                            $roundLabel = "Babak {$teamsInRound} Besar";
                        }
                        return "{$roundLabel} - M{$record->match_order}";
                    }),
                Tables\Columns\TextColumn::make('home')
                    ->label('Home')
                    ->getStateUsing(function ($record) {
                        $p = $record->participants->where('side', 'home')->first();
                        return $p?->entry?->display_name ?? 'TBD';
                    })
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('score')
                    ->label('Skor')
                    ->getStateUsing(function ($record) {
                        if (in_array($record->status, ['completed', 'walkover'])) {
                            $h = $record->participants->where('side', 'home')->first();
                            $a = $record->participants->where('side', 'away')->first();
                            return ($h?->goals_scored ?? 0) . ' - ' . ($a?->goals_scored ?? 0);
                        }
                        return 'vs';
                    })
                    ->alignCenter()
                    ->weight('bold')
                    ->color('primary'),
                Tables\Columns\TextColumn::make('away')
                    ->label('Away')
                    ->getStateUsing(function ($record) {
                        $p = $record->participants->where('side', 'away')->first();
                        return $p?->entry?->display_name ?? 'TBD';
                    })
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('psUnit.name')
                    ->label('Unit PS')
                    ->placeholder('Belum dijadwalkan')
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'ongoing'   => 'success',
                        'scheduled' => 'info',
                        'ready'     => 'warning',
                        'completed' => 'gray',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'scheduled' => 'Terjadwal',
                        'ready'     => 'Siap',
                        'ongoing'   => 'Sedang Berjalan',
                        'completed' => 'Selesai',
                        'pending'   => 'Pending',
                        default     => ucfirst($state),
                    }),
            ])
            ->emptyStateHeading('Belum ada pertandingan aktif')
            ->emptyStateDescription('Generate bracket turnamen untuk memulai.')
            ->emptyStateIcon('heroicon-o-rectangle-stack');
    }
}
