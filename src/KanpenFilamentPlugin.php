<?php

namespace Mydnic\KanpenFilamentPlugin;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Mydnic\KanpenFilamentPlugin\Resources\CampaignResource;
use Mydnic\KanpenFilamentPlugin\Resources\SubscriberResource;
use Mydnic\KanpenFilamentPlugin\Resources\TemplateResource;

class KanpenFilamentPlugin implements Plugin
{
    public function getId(): string
    {
        return 'kanpen';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            CampaignResource::class,
            SubscriberResource::class,
            TemplateResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }
}
