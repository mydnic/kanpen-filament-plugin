<?php

namespace Mydnic\KanpenFilamentPlugin\Commands;

use Illuminate\Console\Command;

class KanpenFilamentPluginCommand extends Command
{
    public $signature = 'kanpen-filament-plugin';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
