<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Team extends Model
{
    use HasFactory;

    private $name = null;
    private $team_strength = 0;

    /** @var Collection $players */
    private $_players;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->_players = new Collection();

    }
    public function getNameAttribute()
    {
        return $this->name;
    }

    public function setNameAttribute($name)
    {
        $this->name = $name;
    }

    public function getAverageRankingAttribute()
    {
        return $this->_players->average('ranking');
    }

    public function getPlayersAttribute()
    {
        return $this->_players;
    }

    public function getTeamStrengthAttribute()
    {
        return $this->team_strength;
    }

    public function addPlayer($player)
    {
        $this->_players->push($player);
        $this->team_strength += $player->ranking;

        return $this->_players;
    }

    public function getPlayerCountAttribute()
    {
        return $this->_players->count();
    }
}
