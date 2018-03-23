<?php

namespace App\Models;

use ScoutElastic\Searchable;
use Illuminate\Database\Eloquent\Model;
use App\Models\ES\Configurator\SlProduitESConfigurator;

class SlProduitES extends Model
{
    use Searchable;

    protected $indexConfigurator = SlProduitESConfigurator::class;

    protected $searchRules = [
        //
    ];

    protected $mapping = [
        'properties' => [
            'id' => [
                'type' => 'integer',
            ],
            'langue_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'formule_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'societe_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'ageMin' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'ageMax' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'pcMin' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'pcMax' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'nbJoursMin' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'nbJoursMax' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'nbHeuresCoursMin' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'nbHeuresCoursMax' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'prixAccroche' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'categorie_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'hebergements_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'villes_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'pays_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'modes_transports_id' => [
                'type' => 'integer',
                'boost' => 5,
            ],
            'cms' => [
                'properties' => [
                    'langue' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 3,
                    ],
                    'formule' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 3,
                    ],
                    'categorie' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 3,
                    ],
                    'hebergements' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 3,
                    ],
                    'villes' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 3,
                    ],
                    'pays' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 3,
                    ],
                    'modes_transports' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 3,
                    ],
                    'accroche' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 1,
                    ],
                    'introduction' => [
                        'type' => 'text',
                        'analyzer' => 'french',
                        'boost' => 1,
                    ],
                ],
            ],
        ],
    ];
}
