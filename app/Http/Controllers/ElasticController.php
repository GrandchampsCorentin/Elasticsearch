<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller as Controller;
use App\Models\Categorie;
use App\Models\Formule;
use App\Models\Hebergement;
use App\Models\Langue;
use App\Models\ModeTransport;
use App\Models\Pays;
use App\Models\Ville;
use App\Models\SlProduit;

//Appel du Modèle "FormulaireComplementaireModel" renommé en FCM
/*use App\Models\IntranetWeb\FormulaireComplementaireModel as FCM;
use App\Models\IntranetWeb\Inscription as Inscr;*/

class ElasticController extends Controller
{
    public function search()
    {
        $categories        = Categorie::get()->pluck('libelle', 'id')->all();
        $formules          = Formule::get()->pluck('libelle', 'id')->all();
        $hebergements      = Hebergement::get()->pluck('libelle', 'id')->all();
        $langues           = Langue::get()->pluck('libelle', 'id')->all();
        $modeTransports    = ModeTransport::get()->pluck('libelle', 'id')->all();
        $pays              = Pays::get()->pluck('libelle', 'id')->all();
        $villes            = Ville::get()->pluck('libelle', 'id')->all();
        $slProduitPrix     = SlProduit::distinct()->select('prixAccroche')->orderBy('prixAccroche', 'asc')->get()->all();
        $slProduitHeureMin = SlProduit::distinct()->select('nbHeuresCoursMin')->orderBy('nbHeuresCoursMin', 'asc')->get()->all();
        $slProduitHeureMax = SlProduit::distinct()->select('nbHeuresCoursMax')->orderBy('nbHeuresCoursMax', 'asc')->get()->all();

        return view('test', compact('langues', 'categories', 'formules', 'hebergements', 'modeTransports', 'pays', 'villes', 'slProduitPrix', 'slProduitHeureMin', 'slProduitHeureMax'));
    }
}
