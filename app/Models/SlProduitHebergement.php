<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\AssoProdHeberIndexConfigurator;

class SlProduitHebergement extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'sl_produits_hebergements';
    protected $guarded 		  = [];
    protected $connection 	= 'web';
    public static $rules 	 = [
                              ];
    public static $rulesUp  = [
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = AssoProdHeberIndexConfigurator::class;

    protected $searchRules = [
        //
    ];

    protected $mapping = [
        'properties' => [
            'id' => [
                'type' => 'integer',
            ],
        ],
    ];

    //Elasticsearch !!! Fin
}
