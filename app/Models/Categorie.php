<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Web\SlProduit;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\CategorieIndexConfigurator;

class Categorie extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'categories';
    protected $guarded 		  = [];
    protected $connection 	= 'web';

    public static $rules 	 = [
                                'libelle' => 'required|string|max:255',
                              ];
    public static $rulesUp  = [
                                'libelle' => 'sometimes|string|max:255',
                              ];

    public static $arrCategoriesSpeciales = [
                                                6, // Formation Pro
                                                13, // Glob-Explorer
                                                14, // Etudes à l'étranger
                                            ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = CategorieIndexConfigurator::class;

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

    public function formules()
    {
        return $this->hasMany('App\Models\Web\Formule');
    }

    public function cmsNacelLayoutCategorie()
    {
        return $this->hasOne('App\Models\Web\CmsNacelLayoutCategorie');
    }

    public function scopeSansCertainesCategs($query)
    {
        // on enlève les
        // - formations pro
        // - longues durées
        // - glob Explorer

        return $query->whereNotIn('id', self::$arrCategoriesSpeciales);
    }
}
