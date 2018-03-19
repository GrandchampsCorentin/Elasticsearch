<?php

namespace App\Models\ES\Configurator;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class SlProduitIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $settings = [
        'index' => [
            'number_of_replicas' => 0,
            'number_of_shards' => 4,
        ],
    ];
}
