<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\ModeTransportIndexConfigurator;

class ModeTransport extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $table 		    = 'modes_transports';
    protected $guarded 		  = [];
    protected $connection 	= 'web';
    public static $rules 	 = [
                                'libelle' => 'required|string|max:255',
                              ];
    public static $rulesUp  = [
                                'libelle' => 'sometimes|string|max:255',
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = ModeTransportIndexConfigurator::class;

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

    /*public function slSejour()
    {
        return $this->hasMany('App\Models\Web\SlSejour');
    } */
}
