<?php

namespace Mydnic\KanpenFilamentPlugin;

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
        $package
            ->name(static::$name)
            ->hasViews(static::$viewNamespace)
            ->hasMigrations([
                'create_kanpen_templates_table',
                'add_design_to_kanpen_campaigns_table',
            ]);
    }

    public function packageBooted(): void
    {
        Testable::mixin(new TestsKanpenFilamentPlugin);
    }
}
