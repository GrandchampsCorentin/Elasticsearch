<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\HebergementIndexConfigurator;

class Hebergement extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'hebergements';
    protected $guarded 		  = [];
    protected $connection 	= 'web';
    public static $rules 	 = [
                                'libelle' => 'required|string|max:255',
                              ];
    public static $rulesUp  = [
                                'libelle' => 'sometimes|string|max:255',
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = HebergementIndexConfigurator::class;

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

    public function slProduits()
    {
        return $this->belongsToMany('App\Models\Web\SlProduit', 'sl_produits_hebergements')->whereNull('sl_produits_hebergements.deleted_at');
    }
}
