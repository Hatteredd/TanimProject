<?php

return [
    'driver' => env('SCOUT_DRIVER', 'database'),

    'prefix' => env('SCOUT_PREFIX', ''),

    'queue' => env('SCOUT_QUEUE', false),

    'after_commit' => false,

    'soft_delete' => false,

    'identify' => env('SCOUT_IDENTIFY', false),

    'database' => [
        'collection' => env('SCOUT_DATABASE_COLLECTION', 'scout.index'),
    ],
];
