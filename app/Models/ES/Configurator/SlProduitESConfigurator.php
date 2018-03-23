<?php

namespace App\Models\ES\Configurator;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class SlProduitESConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = 'sl_produit_es';

    protected $settings = [
        'index' => [
            'number_of_replicas' => 0,
            'number_of_shards' => 5,
        ],

        'analysis' => [
            'filter' => [
                'french_elision' => [
                    'type' => 'elision',
                    'articles_case' => true,
                    'articles' => [
                        'l', 'm', 't', 'qu', 'n', 's',
                        'j', 'd', 'c', 'jusqu', 'quoiqu',
                        'lorsqu', 'puisqu',
                    ],
                ],
                'french_stop' => [
                    'type' => 'stop',
                    'stopwords' => '_french_',
                ],
                'french_stemmer' => [
                    'type' => 'stemmer',
                    'language' => 'light_french',
                ],
            ],
            'analyzer' => [
                'french' => [
                    'tokenizer' => 'standard',
                    'filter' => [
                        'french_elision',
                        'lowercase',
                        'french_stop',
                        'french_stemmer',
                    ],
                ],
            ],
        ],
    ];
}
