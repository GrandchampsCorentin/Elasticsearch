<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\AssoProdVilleIndexConfigurator;

class SlProduitVille extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table      = 'sl_produits_villes';
    protected $guarded    = [];
    protected $connection = 'web';
    public static $rules  = [
                                'ville_id' => 'required|integer',
                                'sl_produit_id' => 'required|integer',
                              ];
    public static $rulesUp = [
                                'ville_id' => 'sometimes|integer',
                                'sl_produit_id' => 'sometimes|integer',
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = AssoProdVilleIndexConfigurator::class;

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

    public function slProduit()
    {
        return $this->belongsTo('App\Models\Web\SlProduit');
    }
}
