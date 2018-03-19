<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\FormuleIndexConfigurator;

class Formule extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'formules';
    protected $guarded 		  = [];
    protected $connection 	= 'web';
    public static $rules 	 = [
                                'libelle' => 'required|string|max:255',
                                'categorie_id' => 'required|integer',
                              ];
    public static $rulesUp  = [
                                'libelle' => 'sometimes|string|max:255',
                                'categorie_id' => 'sometimes|integer',
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = FormuleIndexConfigurator::class;

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
        return $this->hasMany('App\Models\Web\SlProduit');
    }

    public function categorie()
    {
        return $this->belongsTo('App\Models\Web\Categorie');
    }
}
