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
use App\Models\SlProduitES;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Artisan;

class ElasticController extends Controller
{
    public function getIndex()
    {
        //On supprime l'index qui est lié à l'indexConfigurator passé en paramètre
        Artisan::call('elastic:drop-index', [
            'index-configurator' => 'App\Models\ES\Configurator\SlProduitESConfigurator',
        ]);
        //On créé un nouvel index pour ce même indexConfigurator
        Artisan::call('elastic:create-index', [
            'index-configurator' => 'App\Models\ES\Configurator\SlProduitESConfigurator',
        ]);
        //On indexe nos données dans l'index créé
        $colProduits  = SlProduit::with('langue', 'formule.categorie', 'villes.pays', 'cmsNacelLayoutProduit', 'hebergements', 'modesTransports')->get(); //On récupère tous les produits
        $colProduitEs = new Collection(); //On créé une nouvelle collection
        foreach ($colProduits as $sl) { //Pour chaque produit on instancie les champs et leurs valeurs
            $slEs                      = new SlProduitES();
            $slEs->id                  = $sl->id;
            $slEs->langue_id           = $sl->langue_id;
            $slEs->formule_id          = $sl->formule_id;
            $slEs->societe_id          = $sl->societe_id;
            $slEs->ageMin              = $sl->ageMin;
            $slEs->ageMax              = $sl->ageMax;
            $slEs->pcMin               = $sl->pcMin;
            $slEs->pcMax               = $sl->pcMax;
            $slEs->nbJoursMin          = $sl->nbJoursMin;
            $slEs->nbJoursMax          = $sl->nbJoursMax;
            $slEs->nbHeuresCoursMin    = $sl->nbHeuresCoursMin;
            $slEs->nbHeuresCoursMax    = $sl->nbHeuresCoursMax;
            $slEs->prixAccroche        = $sl->prixAccroche;
            $slEs->categorie_id        = $sl->formule->categorie->id;
            $slEs->hebergements_id     = $sl->hebergements->pluck('id')->all();
            $slEs->villes_id           = $sl->villes->pluck('id')->all();
            $slEs->pays_id             = $sl->villes->pluck('pays_id')->unique()->all();
            $slEs->modes_transports_id = $sl->modesTransports->pluck('id')->all();

            if ($sl->cmsNacelLayoutProduit != null) { //Certains produits ne sont pas liés à une page CMS !
                $slEs->cms = [
                    'langue' => $sl->langue->libelle,
                    'formule' => $sl->formule->libelle,
                    'categorie' => $sl->formule->categorie->libelle,
                    'hebergements' => $sl->hebergements->map(function ($h) {
                        return $h->libelle;
                    })->toArray(),
                    'villes' => $sl->villes->map(function ($v) {
                        return $v->libelle;
                    })->toArray(),
                    'pays' => $sl->villes->map(function ($v) {
                        return $v->pays->libelle;
                    })->unique()->toArray(),
                    'modes_transports' => $sl->modesTransports->map(function ($m) {
                        return $m->libelle;
                    })->toArray(),
                    'accroche' => $sl->cmsNacelLayoutProduit->accroche,
                    'introduction' => strip_tags($sl->cmsNacelLayoutProduit->introduction),
                ];
            }

            $colProduitEs->push($slEs); //On complête la collection à chaque fin de traitement pour un produit
        }
        $colProduitEs->searchable(); //On permet à notre index de faire ses recherches avec la collection fraichement créée

        dump($colProduitEs); //Retour visuel
    }

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
