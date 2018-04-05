<?php

namespace App\Models\Exemple;

use ScoutElastic\Searchable;
use Illuminate\Database\Eloquent\Model;

class SlProduitCMS extends Model
{
    use Searchable;

    protected $indexConfigurator = SlProduitCMSIndexConfigurator::class;

    protected $searchRules = [
        //
    ];

    protected $mapping = [
        'properties' => [
            'cms' => [
                'properties' => [
                    'langue' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'formule' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'categorie' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'hebergements' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'villes' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'pays' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'modes_transports' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'accroche' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                    'introduction' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                    ],
                ],
            ],
        ],
    ];
}
