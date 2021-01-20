<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <title>SportsRecruits Tournament Builder</title>

    </head>
    <body>
    <div class="container my-12 mx-auto px-4 md:px-12">
        <div class="flex flex-wrap -mx-1 lg:-mx-4">
            @foreach ($teams as $team)
                <div class="my-1 px-1 w-full md:w-1/2 lg:my-4 lg:px-4 lg:w-1/4">
                    <!-- Article -->
                    <article class="overflow-hidden rounded-lg shadow-lg">

                        <header class="bg-gradient-to-b from-cyan-400 to-light-blue-500 px-5 py-10 text-center">
                            <h1 class="text-xl font-black text-cyan-800">{{ $team->name }}</h1>
                        </header>

                        <header class="p-2 md:p-4">

                            <h2 class="text-lg font-bold">Player Count: {{ $team->player_count }}</h2>

                            <h2 class="text-lg font-bold mb-4">Average Ranking: {{ number_format($team->average_ranking, 2) }}</h2>
                            <ul>
                                @foreach ($team->players as $player)
                                    <li class="{{ $player->can_play_goalie ? 'font-medium text-cyan-800' : '' }}">{{ $player->full_name }} - {{ $player->ranking }}</li>
                                @endforeach
                            </ul>
                        </header>

                    </article>
                </div>
            @endforeach

        </div>

    </div>

    </body>
</html>
