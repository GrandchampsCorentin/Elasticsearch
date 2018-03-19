<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Models\CmsNacelLayoutProduit;
use App\Models\Formule;
use App\Models\SlProduit;
use App\Models\SlProduitHebergement;
use App\Models\SlProduitModeTransport;
use App\Models\SlProduitVille;
use App\Models\Ville;

class AjaxController extends Controller
{
    public function getSerp(Request $request)
    {
        //Traitement de la sélection d'un pays
        if ($request->has('selectPays')) {
            $reqVille = Ville::search('*')
                        ->whereIn('pays_id', $request->selectPays)
                        ->take(1000)
                        ->get();
            foreach ($reqVille as $ville) {
                $idVille[] = $ville->id;
            }
            $reqEntreDeux = SlProduitVille::search('*')
                            ->whereIn('ville_id', $idVille)
                            ->take(1000)
                            ->get();

            foreach ($reqEntreDeux as $produit) {
                $idProduits[] = $produit->id;
            }
        }

        //Traitement de la sélection d'une ville
        if ($request->has('selectVilles')) {
            $reqProduit = SlProduitVille::search('*')
                ->whereIn('ville_id', $request->selectVilles)
                ->take(1000)
                ->get();

            foreach ($reqProduit as $produit) {
                $idProduits2[] = $produit->id;
            }
        }
        //Traitement de la sélection d'une catégorie
        if ($request->has('selectCategories')) {
            $reqFormule = Formule::search('*')
                        ->whereIn('categorie_id', $request->selectCategories)
                        ->take(1000)
                        ->get();
            foreach ($reqFormule as $formule) {
                $idFormules[] = $formule->id;
            }
        }
        //Traitement de la sélection d'un hébergement
        if ($request->has('selectHeberg')) {
            $reqProduit = SlProduitHebergement::search('*')
                ->whereIn('hebergement_id', $request->selectHeberg)
                ->take(1000)
                ->get();
            foreach ($reqProduit as $produit) {
                $idProduits3[] = $produit->id;
            }
        }
        //Traitement de la sélection d'un mode de transport
        if ($request->has('selectModeTransp')) {
            $reqProduit = SlProduitModeTransport::search('*')
                ->whereIn('mode_transport_id', $request->selectModeTransp)
                ->take(1000)
                ->get();
            foreach ($reqProduit as $produit) {
                $idProduits4[] = $produit->id;
            }
        }

        //Traitement de la recherche CMS
        if ($request->has('queryRecherche') && isset($request->queryRecherche)) {
            $reqCms = CmsNacelLayoutProduit::search($request->queryRecherche)
                ->take(1000)
                ->get();
            foreach ($reqCms as $cmsProduit) {
                if (isset($cmsProduit->sl_produit_id)) {
                    $idProduits5[] = $cmsProduit->sl_produit_id;
                }
            }
        }

        //Début de la requête par sélection
        $rq = SlProduit::search('*');
        if ($request->has('selectCategories')) { //Sélection d'une catégorie
            $rq->whereIn('formule_id', $idFormules);
        }
        if ($request->has('selectFormules')) { //Sélection d'une formule
            $rq->whereIn('formule_id', $request->selectFormules);
        }
        if ($request->has('selectLangues')) { //Sélection d'une langue
            $rq->whereIn('langue_id', $request->selectLangues);
        }
        if ($request->has('selectPays')) { //Sélection d'un pays
            $rq->whereIn('id', $idProduits);
        }
        if ($request->has('selectVilles')) { //Sélection d'une ville
            $rq->whereIn('id', $idProduits2);
        }
        if ($request->has('selectHeberg')) { //Sélection d'un hébergement
            $rq->whereIn('id', $idProduits3);
        }
        if ($request->has('selectModeTransp')) { //Sélection d'un mode de transport
            $rq->whereIn('id', $idProduits4);
        }
        if ($request->has('selectPrix') && $request->selectPrix[0] != -1) {
            $rq->where('prixAccroche', $request->selectPrix[0]);
        }
        if ($request->has('selectAgeMin') && $request->selectAgeMin != -1) {
            $rq->where('ageMin', '>=', $request->selectAgeMin);
        }
        if ($request->has('selectAgeMax') && $request->selectAgeMax != -1) {
            $rq->where('ageMax', '<=', $request->selectAgeMax);
        }
        if ($request->has('selectHeureMin') && $request->selectHeureMin != -1) {
            $rq->where('nbHeuresCoursMin', '>=', $request->selectHeureMin);
        }
        if ($request->has('selectHeureMax') && $request->selectHeureMax != -1) {
            $rq->where('nbHeuresCoursMax', '<=', $request->selectHeureMax);
        }
        if ($request->has('queryRecherche') && isset($request->queryRecherche)) {
            $rq->whereIn('id', $idProduits5);
        }
        $result = $rq->take(1000)->get()->toJson();

        return $result;
    }
}
