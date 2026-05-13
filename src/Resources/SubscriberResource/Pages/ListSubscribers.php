<?php

namespace Mydnic\KanpenFilamentPlugin\Resources\SubscriberResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Mydnic\KanpenFilamentPlugin\Resources\SubscriberResource;

class ListSubscribers extends ListRecords
{
    protected static string $resource = SubscriberResource::class;
}
