<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller as Controller;
use App\Models\SlProduitES;

class AjaxController extends Controller
{
    public function getSerp(Request $request)
    {
        $tbFields      = []; //Tableau des champs à traiter dans l'input TEXTUEL du /search
        $tbSelects     = []; //Tableau des selects à traiter du /search

        //Catégorie
        //Si il y a sélection on veut que la requête s'oriente sur l'ID de la catégorie sélectionnée dans le /search
        if ($request->has('selectCategories')) {
            array_push($tbSelects, ['terms' => ['categorie_id' => $request->selectCategories], ]);
        }
        //Sinon la requête s'orientera sur une recherche dans l'input TEXTUEL du /search
        else {
            array_push($tbFields, 'cms.categorie');
        }

        //Formules
        if ($request->has('selectFormules')) {
            array_push($tbSelects, ['terms' => ['formule_id' => $request->selectFormules], ]);
        } else {
            array_push($tbFields, 'cms.formule');
        }

        //Langues
        if ($request->has('selectLangues')) {
            array_push($tbSelects, ['terms' => ['langue_id' => $request->selectLangues], ]);
        } else {
            array_push($tbFields, 'cms.langue');
        }

        //Pays
        if ($request->has('selectPays')) {
            array_push($tbSelects, ['terms' => ['pays_id' => $request->selectPays], ]);
        } else {
            array_push($tbFields, 'cms.pays');
        }

        //Villes
        if ($request->has('selectVilles')) {
            array_push($tbSelects, ['terms' => ['villes_id' => $request->selectVilles], ]);
        } else {
            array_push($tbFields, 'cms.villes');
        }

        //Hébergements
        if ($request->has('selectHeberg')) {
            array_push($tbSelects, ['terms' => ['hebergements_id' => $request->selectHeberg], ]);
        } else {
            array_push($tbFields, 'cms.hebergements');
        }

        //Modes de transports
        if ($request->has('selectModeTransp')) {
            array_push($tbSelects, ['terms' => ['modes_transports_id' => $request->selectModeTransp], ]);
        } else {
            array_push($tbFields, 'cms.modes_transports');
        }

        //Prix
        if ($request->has('selectPrix') && $request->selectPrix[0] != -1) {
            array_push($tbSelects, ['match' => ['prixAccroche' => $request->selectPrix[0]], ]);
        } else {
            array_push($tbFields, 'prixAccroche');
        }

        //Age minimum
        if ($request->has('selectAgeMin') && $request->selectAgeMin != -1) {
            array_push($tbSelects, ['range' => ['ageMin' => ['gte' => $request->selectAgeMin]], ]);
        } else {
            array_push($tbFields, 'ageMin');
        }

        //Age maximum
        if ($request->has('selectAgeMax') && $request->selectAgeMax != -1) {
            array_push($tbSelects, ['range' => ['ageMax' => ['lte' => $request->selectAgeMax]], ]);
        } else {
            array_push($tbFields, 'ageMax');
        }

        //Heures minimum de cours
        if ($request->has('selectHeureMin') && $request->selectHeureMin != -1) {
            array_push($tbSelects, ['range' => ['nbHeuresCoursMin' => ['gte' => $request->selectHeureMin]], ]);
        } else {
            array_push($tbFields, 'nbHeuresCoursMin');
        }

        //Heures maximum de cours
        if ($request->has('selectHeureMax') && $request->selectHeureMax != -1) {
            array_push($tbSelects, ['range' => ['nbHeuresCoursMax' => ['lte' => $request->selectHeureMax]], ]);
        } else {
            array_push($tbFields, 'nbHeuresCoursMax');
        }

        //Recherche par l'input TEXTUEL du /search
        //Si il existe une valeur dans l'input et qu'elle est différente de null
        if ($request->has('queryRecherche') && $request->queryRecherche !== null) {
            /*Rappels
            /- MUST   => Elasticsearch DOIT trouver les valeurs passées dans le MUST pour créer un score/document et renvoyer des résultats
            /- SHOULD => Elasticsearch PEUT trouver les valeurs passées dans le SHOULD pour améliorer le score des résultats trouvés par le MUST
            /- Attention ! SHOULD peut servir de MUST lorsque celui-ci n'existe pas !
            /- Les documents les plus pertinents seront ceux qui matcheront avec le MUST ET le SHOULD, ensuite viendront ceux qui matcheront avec MUST, puis ceux avec SHOULD
            */

            //$shouldMatch fait de la recherche textuelle sur les champs introduits dans $tbFields
            $shouldMatch = [
                   'multi_match' => [ //Permet de passer plusieurs champs en propriétés
                       'query' => $request->queryRecherche, //Requête textuelle
                       'fields' => $tbFields, //Les champs concernés par la recherche textuelle
                       'type' => 'cross_fields', //Oblige la requête à matcher avec le plus de champs possible parmi les champs ci dessus
                       'analyzer' => 'french',
                   ],
               ];
            //$shouldQuery fait de la recherche textuelle sur les champs à grands textes
            $shouldQuery =
                   [
                   'query_string' => [ //Découpe une chaine de caractères en token qui seront analysés puis comparés aux tokens des champs textuels visés
                       'query' => $request->queryRecherche, //Requête textuelle
                       'fields' => ['cms.accroche', 'cms.introduction'], //Les champs concernés par la recherche textuelle
                       'type' => 'best_fields', //On récupère le score du champ qui matche le plus avec la recherche textuelle proposée
                       'analyzer' => 'french',
                   ],
               ];
        }

        //Construction de la requête booléenne
        //Si il n'existe uniquement que la requête de l'input TEXTUEL on utilise que les éléments SHOULD
        if ($tbSelects === []) {
            $rqBooleenne =
            ['bool' => [
                'should' => [
                    $shouldMatch,
                    $shouldQuery,
                    ],
                ],
            ];
        } else {
            //Sinon, si il n'existe uniquement que des sélections par ID (balises Select) on utilise que l'élément MUST
            if ($request->queryRecherche === null) {
                $rqBooleenne =
            ['bool' => [
                'must' => $tbSelects,
                ],
            ];
            } else {
                //Sinon, si il n'existe et des sélections par ID (balises Select) ET une requête de l'input TEXTUEL on utilise que l'élément MUST et SHOULD
                $rqBooleenne =
                ['bool' => [
                    'must' => $tbSelects,

                    'should' => [
                        $shouldMatch,
                        $shouldQuery,
                        ],
                    ],
                ];
            }
        }

        //Requête complête
        //searchRaw permet de crééer des requête dans un format proche du JSON, puis traduit la requête en JSON
        $rq = SlProduitES::searchRaw([
        //'size' => 1000, //Permet de choisir le nombre de résultats à afficher de base il en affiche 10
            'query' => $rqBooleenne,
        ]);

        return $rq;
    }

    /* EN DESSOUS ANCIENNES VERSIONS DU CODE, INCOMPLETES OU PEU PERTINENTES

    public function getSerp3(Request $request)
    {
        //Début de la requête par sélection
        if ($request->has('queryRecherche') && isset($request->queryRecherche)) {
            $rq = SlProduitES::search($request->queryRecherche);
        } else {
            $rq = SlProduitES::search('*');
        }

        if ($request->has('selectCategories')) { //Sélection d'une catégorie
            $rq->whereIn('categorie_id', $request->selectCategories);
        }

        if ($request->has('selectFormules')) { //Sélection d'une formule
            $rq->whereIn('formule_id', $request->selectFormules);
        }
        if ($request->has('selectLangues')) { //Sélection d'une langue
            $rq->whereIn('langue_id', $request->selectLangues);
        }
        if ($request->has('selectPays')) { //Sélection d'un pays
            $rq->whereIn('pays_id', $request->selectPays);
        }
        if ($request->has('selectVilles')) { //Sélection d'une ville
            $rq->whereIn('ville_id', $request->selectVilles);
        }
        if ($request->has('selectHeberg')) { //Sélection d'un hébergement
            $rq->whereIn('hebergements_id', $request->selectHeberg);
        }
        if ($request->has('selectModeTransp')) { //Sélection d'un mode de transport
            $rq->whereIn('modes_transports_id', $request->selectModeTransp);
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
        dd($rq);
        //$result = $rq->take(1000)->get()->toJson();
        $result = json_encode($rq->take(1000)->explain());

        return $result;
    }

    public function getSerp2(Request $request)
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
        //$result = $rq->take(1000)->get()->toJson();
        $result = json_encode($rq->take(1000)->explain());

        return $result;
    }*/
}
