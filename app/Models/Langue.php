<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\LangueIndexConfigurator;

class Langue extends Model
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'langues';
    protected $guarded 		  = [];
    protected $connection  = 'web';

    //Elasticsearch !!! Début
    protected $indexConfigurator = LangueIndexConfigurator::class;

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
}
