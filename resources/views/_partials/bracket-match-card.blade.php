@php
    $activeEntryIntIds = $activeEntryIds ?? [];
    $isThirdPlace = ($match['bracket_position'] ?? '') === '3rd_place';
    $homeEmpty = $home && !($home['tournament_entry_id'] ?? null) && !$match['is_bye'];
    $awayEmpty = $away && !($away['tournament_entry_id'] ?? null) && !$match['is_bye'];
    $homeTbd = $isThirdPlace ? (!($home['tournament_entry_id'] ?? null)) : (!$match['is_bye'] && (!$home || !($home['tournament_entry_id'] ?? null)));
    $awayTbd = $isThirdPlace ? (!($away['tournament_entry_id'] ?? null)) : (!$match['is_bye'] && (!$away || !($away['tournament_entry_id'] ?? null)));
@endphp

<div class="text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-800/60 border-b border-gray-200 dark:border-gray-700/50" style="font-size: 0.65rem; font-weight: 700; padding: 0.25rem 0.5rem; display: flex; justify-content: space-between;">
    <span>Pos: {{ $match['bracket_position'] }}</span>
    @if($match['status'] === 'completed' && ($match['is_bye'] ?? false) && $isThirdPlace)
        <span class="text-amber-500" style="font-weight: 800;">MENUNGGU</span>
    @elseif($match['status'] === 'completed')
        <span class="text-emerald-500" style="font-weight: 800;">SELESAI</span>
    @elseif($match['status'] === 'walkover')
        <span class="text-red-500" style="font-weight: 800;">WALKOVER</span>
    @else
        <span class="text-gray-400 dark:text-gray-500" style="font-weight: 800;">PENDING</span>
    @endif
</div>

@php
    $homeId = ($home['tournament_entry_id'] ?? 0);
    $awayId = ($away['tournament_entry_id'] ?? 0);
    $isHomeActive = in_array($homeId, $activeEntryIntIds);
    $isAwayActive = in_array($awayId, $activeEntryIntIds);
@endphp

<div class="bracket-player border-b border-gray-200 dark:border-gray-700/50" style="display: flex; justify-content: space-between; align-items: center; padding: 0.55rem 0.75rem; font-size: 0.82rem; background: {{ ($home && $home['is_winner']) ? 'rgba(16, 185, 129, 0.08)' : ($isHomeActive ? 'rgba(16, 185, 129, 0.05)' : '') }}">
    <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1; min-width: 0;">
        <span class="text-gray-900 dark:text-white" style="font-weight: {{ $isHomeActive ? '800' : '700' }}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: {{ $homeEmpty ? 'var(--text-muted)' : ($isHomeActive ? 'var(--primary)' : 'inherit') }}; font-style: {{ $homeEmpty ? 'italic' : 'normal' }};">
            {{ $homeEmpty ? 'Menunggu Check-in' : ($homeTbd ? 'TBD' : ($home['player_name'] ?? 'BYE')) }}
        </span>
        @if($home && ($home['club_name'] ?? null))
            <span class="text-gray-500 dark:text-gray-400" style="font-size: 0.7rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $home['club_name'] }}</span>
        @endif
    </div>
    <span style="font-weight: 850; font-size: 0.9rem; margin-left: 0.5rem; {{ ($home && $home['is_winner']) ? 'color: #10b981;' : 'color: inherit;' }}">
        {{ $match['is_bye'] ? '-' : ($home['goals_scored'] ?? 0) }}@if($match['decided_by_penalty'] && isset($match['penalty_score_home']) && $match['status'] === 'completed' && !$match['is_bye']) <span style="font-size:0.7rem; font-weight:700; color: var(--text-muted);">({{ $match['penalty_score_home'] }})</span>@endif
    </span>
</div>

<div class="bracket-player" style="display: flex; justify-content: space-between; align-items: center; padding: 0.55rem 0.75rem; font-size: 0.82rem; background: {{ ($away && $away['is_winner']) ? 'rgba(16, 185, 129, 0.08)' : ($isAwayActive ? 'rgba(16, 185, 129, 0.05)' : '') }}">
    <div style="display: flex; flex-direction: column; overflow: hidden; flex: 1; min-width: 0;">
        <span class="text-gray-900 dark:text-white" style="font-weight: {{ $isAwayActive ? '800' : '700' }}; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: {{ $awayEmpty ? 'var(--text-muted)' : ($isAwayActive ? 'var(--primary)' : 'inherit') }}; font-style: {{ $awayEmpty ? 'italic' : 'normal' }};">
            {{ $awayEmpty ? 'Menunggu Check-in' : ($awayTbd ? 'TBD' : ($away['player_name'] ?? 'BYE')) }}
        </span>
        @if($away && ($away['club_name'] ?? null))
            <span class="text-gray-500 dark:text-gray-400" style="font-size: 0.7rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $away['club_name'] }}</span>
        @endif
    </div>
    <span style="font-weight: 850; font-size: 0.9rem; margin-left: 0.5rem; {{ ($away && $away['is_winner']) ? 'color: #10b981;' : 'color: inherit;' }}">
        {{ $match['is_bye'] ? '-' : ($away['goals_scored'] ?? 0) }}@if($match['decided_by_penalty'] && isset($match['penalty_score_away']) && $match['status'] === 'completed' && !$match['is_bye']) <span style="font-size:0.7rem; font-weight:700; color: var(--text-muted);">({{ $match['penalty_score_away'] }})</span>@endif
    </span>
</div>
