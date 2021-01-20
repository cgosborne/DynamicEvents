<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class TournamentsController extends Controller
{
    private $minPlayers = 18;
    private $maxPlayers = 22;

    public function teams()
    {
        $playerCount = User::where('user_type', 'player')->count();
        $goalieCount = User::where('user_type', 'player')->where('can_play_goalie', true)->count();

        // A minimum of 36 players needed for every two teams
        $teamCount = floor($playerCount / ($this->minPlayers * 2)) * 2;

        // In the case that there are not enough goalies for each team, the team count will be the nearest even number
        // of possible goalies.
        if ($teamCount > $goalieCount) {
            $teamCount = floor($goalieCount / 2) * 2;
        }

        // Select a random number of goalies based on the team count.
        $goalies = User::where('user_type', 'player')
            ->where('can_play_goalie', true)
            ->inRandomOrder()
            ->limit($teamCount)
            ->get();

        // Ranking the goalies by rank so the distribution is cleaner to start with.
        $goaliesByRank = $goalies->sortBy('ranking')->values();

        $teams = new Collection();

        // Add a goalie to each team.
        for ($i = 0; $i < $teamCount; $i++) {
            // Create a new team using Team factory.
            $team = Team::factory()->make();
            $team->addPlayer($goaliesByRank[$i]);
            $teams->push($team);
        }

        // Select all additional players that aren't already pre-defined goalies, with a limit of 21 per team
        $additionalPlayers = User::where('user_type', 'player')
            ->whereNotIn('id', $goalies->pluck('id'))
            ->limit($teamCount * 21)
            ->inRandomOrder()
            ->get();

        // Now sort the randomized list of players by ranking from highest to lowest
        /** @var Collection $playersByRanking */
        $playersByRanking = $additionalPlayers->sortByDesc('ranking')->values();

        /* Serpentining through the ordered players by ranking seems the be the best way to balance teams.*/
        $index = 0;

        // Strongest team gets updated as the teams get built with 18 (or min players) per team.
        $strongestTeam = 0;

        // Add 17 players more players to each team so that each team has 18
        for ($i = 0; $i < (($this->minPlayers - 1) * $teamCount); $i++) {
            // Only change the index if $i mod $teamCount is not 0. This will create the serpentine pattern. ie for 4
            // teams 0 0 1 2 3 3 2 1 0 0 1 2 3 3, etc.
            if ($i % $teamCount !== 0) {
                $index += floor($i / $teamCount) % 2 === 0 ? 1 : -1;
            }
            $player = $playersByRanking[$i];
            $teams[$index]->addPlayer($player);
            if ($teams[$index]->team_strength > $strongestTeam) {
                $strongestTeam = $teams[$index]->team_strength;
            }
        }


        $remainingPlayers = $playersByRanking->slice(($this->minPlayers - 1) * $teamCount)
            ->groupBy('ranking');

        foreach ($teams as $team) {
            $modified = true;
            // Attempt to balance the team until all choices are exhausted.
            while ($team->team_strength !== $strongestTeam && $modified && $team->player_count <= $this->maxPlayers) {
                $modified = false;
                foreach ($remainingPlayers as $key => $remainingPlayer) {
                    $difference = $strongestTeam - $team->team_strength;
                    if ($key <= $difference && $remainingPlayer->count() > 0) {
                        $modified = true;
                        $player = $remainingPlayer->pop();
                        $team->addPlayer($player);
                    }
                }
            }
        }

        return view('teams', compact('teams'));

    }
}
