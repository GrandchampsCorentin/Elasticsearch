<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\VilleIndexConfigurator;

class Ville extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'villes';
    protected $guarded 		  = [];
    protected $connection 	= 'web';
    public static $rules 	 = [
                                'libelle' => 'required|string|max:255',
                                'pays_id' => 'required|integer',
                              ];
    public static $rulesUp 	= [
                                'libelle' => 'sometimes|string|max:255',
                                'pays_id' => 'sometimes|integer',
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = VilleIndexConfigurator::class;

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

    public function pays()
    {
        return $this->belongsTo('App\Models\Web\Pays');
    }

    public function slProduits()
    {
        return $this->belongsToMany('App\Models\Web\SlProduit', 'sl_produits_villes')->whereNull('sl_produits_villes.deleted_at');
    }

    public function cmsNacelLayoutVille()
    {
        return $this->hasOne('App\Models\Web\CmsNacelLayoutVille');
    }
}
