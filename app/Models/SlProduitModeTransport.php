<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\AssoProdTranspIndexConfigurator;

class SlProduitModeTransport extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'sl_produits_modes_transports';
    protected $guarded 		  = [];
    protected $connection 	= 'web';
    public static $rules 	 = [
                              ];
    public static $rulesUp  = [
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = AssoProdTranspIndexConfigurator::class;

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
