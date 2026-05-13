<?php

namespace Mydnic\KanpenFilamentPlugin;

use Filament\Support\Facades\FilamentAsset;
use Livewire\Features\SupportTesting\Testable;
use Mydnic\KanpenFilamentPlugin\Testing\TestsKanpenFilamentPlugin;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;


class KanpenFilamentPluginServiceProvider extends PackageServiceProvider
{
    public static string $name = 'kanpen-filament-plugin';

    public static string $viewNamespace = 'kanpen-filament-plugin';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name);
    }

    public function packageBooted(): void
    {
        FilamentAsset::register([], 'mydnic/kanpen-filament-plugin');

        Testable::mixin(new TestsKanpenFilamentPlugin);
    }
}
