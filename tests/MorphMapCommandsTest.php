<?php

use function Pest\Laravel\artisan;

use Spatie\LaravelMorphMapGenerator\Cache\FilesystemMorphMapCacheDriver;
use Spatie\LaravelMorphMapGenerator\Cache\MorphMapCacheDriver;
use Spatie\LaravelMorphMapGenerator\Commands\CacheMorphMapCommand;
use Spatie\LaravelMorphMapGenerator\Commands\ClearMorphMapCommand;
use Spatie\TemporaryDirectory\TemporaryDirectory;

beforeEach(function () {
    $this->temporaryDirectory = (new TemporaryDirectory())->create();

    $this->app->extend(MorphMapCacheDriver::class, fn () => resolve(FilesystemMorphMapCacheDriver::class, [
        'config' => [
            'type' => FilesystemMorphMapCacheDriver::class,
            'path' => $this->temporaryDirectory->path('cached'),
        ],
    ]));
});

it('can cache a morph map', function () {
    artisan(CacheMorphMapCommand::class)
        ->assertExitCode(0)
        ->run();

    expect(file_exists($this->temporaryDirectory->path('cached/morph-map.php')))
        ->toBeTrue();
});

it('can remove a cached morph map', function () {
    artisan(CacheMorphMapCommand::class)
        ->assertExitCode(0)
        ->run();
    artisan(ClearMorphMapCommand::class)
        ->assertExitCode(0)
        ->run();

    expect(file_exists($this->temporaryDirectory->path('cached/morph-map.php')))
        ->toBeFalse();
});
