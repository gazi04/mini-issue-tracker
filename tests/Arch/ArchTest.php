<?php

use App\Http\Controllers\Controller;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

arch()->preset()->php();
arch()->preset()->security()
    // Non-crypto randomness: seeder demo data (rand) and quiz option
    // shuffling (shuffle) — not security concerns.
    ->ignoring(['Database\Seeders', 'App\Support\BlockSanitizer']);

arch('strict types')
    ->expect([
        'App\Http\Controllers',
        'App\Models',
        'App\Services',
        'App\Enums',
        'App\Providers',
    ])
    ->toUseStrictTypes();

arch('no debug statements')
    ->expect(['dd', 'dump', 'ray', 'var_dump', 'var_export', 'die', 'exit'])
    ->not->toBeUsed();

arch('models')
    ->expect('App\Models')
    ->toBeClasses()
    ->toExtend(Model::class);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->toHaveSuffix('Controller')
    ->toExtend(Controller::class)
    ->ignoring(Controller::class);

arch('services')
    ->expect('App\Services')
    ->toHaveSuffix('Service')
    // Command/validator/builder classes named for their role, not the Service suffix.
    ->ignoring([
        'App\Services\BlockValidator',
        'App\Services\AchievementAcknowledger',
        'App\Services\EquippedItemResolver',
        'App\Services\AchievementListBuilder',
    ]);

arch('enums')
    ->expect('App\Enums')
    ->toBeEnums();

arch('commands')
    ->expect('App\Console\Commands')
    ->toExtend(Command::class);

arch('models avoid http layer')
    ->expect('App\Models')
    ->not->toUse('App\Http');

arch('controllers do not query the database')
    ->expect('App\Http\Controllers')
    ->not->toUse([
        DB::class,
        Redis::class,
    ]);
