<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon;
use CinetekHelper;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\SlProduitIndexConfigurator;

class SlProduit extends Modele
{
    use SoftDeletes;
    use Searchable;

    protected $table 		    = 'sl_produits';
    protected $guarded 		  = [];
    protected $connection 	= 'web';
    public static $rules 	 = [
                                'formule_id' => 'required|integer',
                                'langue_id' => 'required|integer',
                                'societe_id' => 'required|integer',
                              ];
    public static $rulesUp  = [
                                'formule_id' => 'sometimes|integer',
                                'langue_id' => 'sometimes|integer',
                                'societe_id' => 'sometimes|integer',
                              ];

    //Elasticsearch !!! Début
    protected $indexConfigurator = SlProduitIndexConfigurator::class;

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

    /**
     * renvoie si toutes les références regroupées sont complètes ou non
     *
     * @return boolean
     */
    public function complet()
    {
        $refs = $this->references;
        foreach ($refs as $ref) {
            $objProgramme = CinetekHelper::getObjProgrammeFromRef($ref->reference_id);
            if (!$objProgramme->isComplet()) {
                return false;
            }
        }

        return true;
    }

    //----------------------------------------------------------------------------------------------
    //region Relation avec les autres tables

    /**
     * lien avec les formules
     *
     * @return void
     */
    public function formule()
    {
        return $this->belongsTo('App\Models\Formule');
    }

    /**
     * lien avec les langues
     *
     * @return void
     */
    public function langue()
    {
        return $this->belongsTo('App\Models\Langue');
    }

    /**
     * lien avec les villes
     *
     * @return void
     */
    public function villes()
    {
        return $this->belongsToMany('App\Models\Ville', 'sl_produits_villes'); //->whereNull('sl_produits_villes.deleted_at'); // le create_at, updated_at, delete_at ne sont pas stockées sur table pivot.
    }

    /**
     * lien avec les hébergements
     *
     * @return void
     */
    public function hebergements()
    {
        return $this->belongsToMany('App\Models\Hebergement', 'sl_produits_hebergements');
    }

    /**
     * lien avec les transports
     *
     * @return void
     */
    public function modesTransports()
    {
        return $this->belongsToMany('App\Models\ModeTransport', 'sl_produits_modes_transports');
    }

    /**
     * lien avec les références
     *
     * @return void
     */
    public function references()
    {
        return $this->hasMany('App\Models\SlProduitReference', 'sl_produit_id');
    }

    /**
     * liens avec les dates
     * /!\ attention il ne faut pas utiliser 'dates' car mot cle réservé
     *
     * @return void
     */
    public function datesProduit()
    {
        return $this->hasMany('App\Models\SlProduitDate', 'sl_produit_id');
    }

    /**
     * Renvoie le cms nacel Layout produit
     *
     * @return void
     */
    public function cmsNacelLayoutProduit()
    {
        return $this->hasOne('App\Models\CmsNacelLayoutProduit');
    }

    /**
     * Renvoie le cms nacel Layout produit
     *
     * @return void
     */
    public function cmsClcLayoutProduitAncien()
    {
        return $this->hasOne('App\Models\CmsClcLayoutProduitAncien');
    }

    //endregion

    //----------------------------------------------------------------------------------------------
    //region scope

    /**
     * rajoute la recherche sur des slProduits de societe uniquement
     *
     * @param [type] $query
     * @param string $strDestinations
     * @return void
     */
    public function scopeSociete($query, int $societe_id)
    {
        // sous requete
        return $query->where('societe_id', $societe_id);
    }

    /**
     * rajoute la contraintesur les destinations pour le moteur de recherche
     *
     * @param [type] $query
     * @param string $strDestinations
     * @return void
     */
    public function scopeDestinations($query, string $strDestinations)
    {
        $arrDestinations = array_filter(explode(',', $strDestinations));

        // construction du tableau des libelle ou pays id
        $pays 	 = [];
        $villes = [];
        foreach ($arrDestinations as $destination) {
            if (is_numeric($destination)) {
                array_push($pays, $destination);
            } else {
                array_push($villes, $destination);
            }
        }

        // sous requete
        return $query->whereHas('villes', function ($q) use ($pays,$villes) {
            if (count($villes) > 0 && count($pays) > 0) {
                $q->whereIn('libelle', $villes)->orWhereIn('pays_id', $pays);
            } elseif (count($villes) > 0) {
                $q->whereIn('libelle', $villes);
            } elseif (count($pays) > 0) {
                $q->whereIn('pays_id', $pays);
            }
        });
    }

    /**
     * rajoute la contraintesur les catégories pour le moteur de recherche
     *
     * @param [type] $query
     * @param string $strCategories
     * @return void
     */
    public function scopeCategories($query, string $strCategories)
    {
        if ($strCategories == '') {
            return $query->whereHas('formule.categorie', function ($q) {
                $q->sansCertainesCategs();
            });
        } else {
            $arrCategories = array_filter(explode(',', $strCategories));

            return $query->whereHas('formule.categorie', function ($q) use ($arrCategories) {
                $q->whereIn('id', $arrCategories)->sansCertainesCategs();
            });
        }
    }

    /**
     * rajoute la contraintesur les catégories pour le moteur de recherche
     *
     * @param [type] $query
     * @param string $strHebergements
     * @return void
     */
    public function scopeHebergements($query, string $strHebergements)
    {
        $arrHebergements = array_filter(explode(',', $strHebergements));

        return $query->whereHas('hebergements', function ($q) use ($arrHebergements) {
            $q->whereIn('hebergements.id', $arrHebergements);
        });
    }

    /**
     * rajoute la contrainte sur les ages pour le moteur de recherche
     *
     * @param [type] $query
     * @param string $strAge
     * @return void
     */
    public function scopeAge($query, string $strAge)
    {
        return $query->where(function ($q) use ($strAge) {
            $q->where('ageMin', 0)->orWhere('ageMin', '<=', $strAge);
        })->where(function ($q2) use ($strAge) {
            $q2->where('ageMax', 0)->orWhere('ageMax', '>=', $strAge);
        });
    }

    /**
     * rajoute la contrainte sur les ages pour le moteur de recherche
     *
     * @param [type] $query
     * @param string $strType
     * @return void
     */
    public function scopeLangue($query, string $strLangue)
    {
        return $query->where('langue_id', $strLangue);
    }

    /**
     * rajoute la contrainte sur les ages pour le moteur de recherche
     *
     * @param [type] $query
     * @param string $strType
     * @return void
     */
    public function scopeType($query, string $strType)
    {
        return $query->whereHas('references', function ($q) use ($strType) {
            $q->where('reference_id', 'like', '%-' . strtoupper($strType));
        });
    }

    /**
     * rajoute la contrainte sur les ages pour le moteur de recherche
     *
     * @param [type] $query
     * @param string $strType
     * @return void
     */
    public function scopeDatesSejour($query, string $strDateDepart = '', string $strDateRetour = '')
    {
        $query->whereHas('datesProduit', function ($q2) use ($strDateDepart,$strDateRetour) {
            $strDateDepart = ($strDateDepart != '') ? Carbon::createFromFormat('d/m/Y', $strDateDepart)->format('Y-m-d') : '';
            $strDateRetour = ($strDateRetour != '') ? Carbon::createFromFormat('d/m/Y', $strDateRetour)->format('Y-m-d') : '';

            // dates libres
            $q2->where(function ($q3) {
                $q3->whereNull('dateDepart')->whereNull('dateRetour');
            });

            // si deux dates renseignées
            if ($strDateDepart != '' && $strDateRetour != '') {
                $q2->orWhere(function ($q3) use ($strDateDepart,$strDateRetour) {
                    $q3->where('dateDepart', '>=', $strDateDepart)
                            ->where('dateRetour', '<=', $strDateRetour);
                });
            }
            // si que date départ
            elseif ($strDateDepart != '') {
                $q2->orWhere(function ($q3) use ($strDateDepart) {
                    $q3->where('dateDepart', '>=', $strDateDepart);
                });
            }
            // si que date retour
            elseif ($strDateDepart != '') {
                $q2->orWhere(function ($q3) use ($strDateRetour) {
                    $q3->where('dateRetour', '<=', $strDateRetour);
                });
            }
        });

        return $query;
    }

    /**
     * Renvoie les séjours disponibles à la vente
     *
     * @param [type] $query
     * @return void
     */
    public function scopeIsNotComplet($query)
    {
        return $query->where('isComplet', 0);
    }

    /**
     * Renvoie les produits non CGOS
     *
     * @param [type] $query
     * @return void
     */
    public function scopeIsNotVisibleCGOS($query)
    {
        return $query->where('isCgos', 0);
    }

    //endregion

    //----------------------------------------------------------------------------------------------
    //region generation de listes deroulantes

    /**
     * renvoie un tableau de références concernant les référéneces du produit
     *
     * @param boolean $isLisible
     * @return void
     */
    public function getLibelleReferences($isLisible = false)
    {
        $arrRefsSelectionnees = [];
        $refs                 = $this->references;
        if (count($refs) > 0) {
            foreach ($refs as $ref) {
                array_push($arrRefsSelectionnees, $ref->reference_id);
            }

            if ($isLisible) {
                if (count($arrRefsSelectionnees) == 1) {
                    return '<strong>' . str_replace('_', '</strong> <small>(', $arrRefsSelectionnees[0]) . ')</small>';
                } else {
                    return '<strong>' . str_replace(
                                ['_', ','],
                                [' </strong><small>(', ')</small>,'],
                                implode('</strong>, <strong>', $arrRefsSelectionnees)
                            ) . ')</small>';
                }
            } else {
                return json_encode($arrRefsSelectionnees);
            }
        } elseif ($isLisible) {
            return '';
        } else {
            return json_encode([]);
        }
    }

    /**
     * renvoie une string lisible de toutes les dates gérées par le produit
     *
     * @return void
     */
    public function getDatesProduit()
    {
        $litDate = '';
        if (count($this->datesProduit) > 0) {
            foreach ($this->datesProduit as $date) {
                if ($date->dateDepart !== null && $date->dateRetour !== null) {
                    $dateD = Carbon::createFromFormat('Y-m-d', $date->dateDepart)->format('d/m/Y');
                    $dateR = Carbon::createFromFormat('Y-m-d', $date->dateRetour)->format('d/m/Y');
                } else {
                    $dateD = $dateR = '*';
                }

                $litDate .= '- du ' . $dateD . ' au ' . $dateR . "\r\n";
            }
        }

        return $litDate;
    }

    /**
     * renvoie un tableau contenant les villes
     *
     * @return void
     */
    public function getVilles()
    {
        $arrVillesSelectionnees = [];
        if (count($this->villes) > 0) {
            //dd($this->villes);
            foreach ($this->villes as $ville) {
                array_push($arrVillesSelectionnees, $ville->id);
            }

            return json_encode($arrVillesSelectionnees);
        } else {
            return json_encode([]);
        }
    }

    /**
     * renvoie un tableau d'hébergement
     *
     * @return void
     */
    public function getHebergements()
    {
        $arrhebSelect = [];
        if (count($this->hebergements) > 0) {
            foreach ($this->hebergements as $heb) {
                array_push($arrhebSelect, $heb->id);
            }

            return json_encode($arrhebSelect);
        } else {
            return json_encode([]);
        }
    }

    /**
     * renvoie un tableau contenant les listes de transports
     *
     * @return void
     */
    public function getModesTransports()
    {
        $arrModeTransport = [];
        if (count($this->modesTransports) > 0) {
            foreach ($this->modesTransports as $modeT) {
                array_push($arrModeTransport, $modeT->id);
            }

            return json_encode($arrModeTransport);
        } else {
            return json_encode([]);
        }
    }

    /**
     * met à jour dynamiquement les informations du produit avec les données en BDD chez cinetek
     *
     * @return void
     */
    public function miseAJourInfosDynamiques()
    {
        $ageMin 			        = null;
        $ageMax 			        = null;
        $pcMin 				        = null;
        $pcMax 				        = null;
        $nbJoursMin 		     = null;
        $nbJoursMax 		     = null;
        $nbHeuresCoursMin 	= null;
        $nbHeuresCoursMax 	= null;
        $prixAccroche 		   = null;
        $arrDates 			      = [];
        $isComplet 			     = true;
        $isCgos				        = true;

        //on parcourt les refs
        foreach ($this->references as $ref) {
            $objProgramme = CinetekHelper::getObjProgrammeFromRef($ref->reference_id);

            // si programme trouvé
            if ($objProgramme != null) {
                //debug($objProgramme->isComplet());
                // complet
                if (!$objProgramme->isComplet()) {
                    $isComplet = false;
                }

                // age Mini
                if ($objProgramme->ageMiniParticipant < $ageMin || $ageMin == null) {
                    $ageMin = $objProgramme->ageMiniParticipant;
                }

                // age Maxi
                if (($objProgramme->ageMaxiParticipant > $ageMax || $ageMax == null || $objProgramme->ageMaxiParticipant == 0) && $ageMax !== 0) {
                    $ageMax = $objProgramme->ageMaxiParticipant;
                }

                // nbHeuresCoursMin
                if ($objProgramme->siSe == 'SE') {
                    $nbHCours = $objProgramme->nombreHeuresCoursLangue + $objProgramme->nombreHeuresCoursAutre;
                } else {
                    $nbHCours = $objProgramme->nombreHeuresCoursLangue;
                }
                if ($nbHCours < $nbHeuresCoursMin || $nbHeuresCoursMin == null) {
                    $nbHeuresCoursMin = $nbHCours;
                }

                // nbHeuresCoursMax
                if ($nbHCours > $nbHeuresCoursMax || $nbHeuresCoursMax == null) {
                    $nbHeuresCoursMax = $nbHCours;
                }

                // pcMin && pcMax
                if ($objProgramme->nombrePC == 0 && $pcMin != 0 && $pcMax != 0) {
                    $pcMin = $pcMax = 0;
                } elseif ($pcMin != 0 || $pcMax != 0 || $pcMin == null || $pcMax == null) {
                    if ($objProgramme->nombrePC > $pcMax || $pcMax === null) {
                        $pcMax = $objProgramme->nombrePC;
                    }
                    if ($objProgramme->nombrePC < $pcMin || $pcMin === null) {
                        $pcMin = $objProgramme->nombrePC;
                    }
                }

                // CGOS
                if ($objProgramme->annule != 'C') {
                    $isCgos = false;
                }

                //nb jours
                if ($objProgramme->dateDepartFrance == '0000-00-00' && $objProgramme->dateRetourFrance == '0000-00-00') {
                    $nbJoursMin = $nbJoursMax = 0;
                } elseif ($nbJoursMin != 0 || $nbJoursMax != 0 || $nbJoursMin == null || $nbJoursMax == null) {
                    $dD                                                        = Carbon::createFromFormat('Y-m-d', $objProgramme->dateDepartFrance);
                    $dA                                                        = Carbon::createFromFormat('Y-m-d', $objProgramme->dateRetourFrance);
                    $diff                                                      = $dD->diffInDays($dA) + 1;
                    if ($diff < $nbJoursMin || $nbJoursMin == null) {
                        $nbJoursMin = $diff;
                    }
                    if ($diff > $nbJoursMax || $nbJoursMax == null) {
                        $nbJoursMax = $diff;
                    }
                }

                // dates
                $dateD = ($objProgramme->dateDepartFrance == '0000-00-00') ? null : $objProgramme->dateDepartFrance;
                $dateR = ($objProgramme->dateRetourFrance == '0000-00-00') ? null : $objProgramme->dateRetourFrance;
                $dates = ['dateDepart' => $dateD, 'dateRetour' => $dateR];
                if (!in_array($dates, $arrDates)) {
                    array_push($arrDates, $dates);
                }

                //prix d'accroche
                if ($objProgramme->prix < $prixAccroche || $prixAccroche == null) {
                    $prixAccroche = $objProgramme->prix;
                }
            }
        }

        // mise à jour des âges
        $this->update([
                        'ageMin' => $ageMin,
                        'ageMax' => $ageMax,
                        'nbHeuresCoursMin' => $nbHeuresCoursMin,
                        'nbHeuresCoursMax' => $nbHeuresCoursMax,
                        'nbJoursMin' => $nbJoursMin,
                        'nbJoursMax' => $nbJoursMax,
                        'pcMin' => $pcMin,
                        'pcMax' => $pcMax,
                        'prixAccroche' => $prixAccroche,
                        'isComplet' => $isComplet,
                        'isCgos' => $isCgos,
                    ]);

        // mise à jour des dates
        foreach ($this->datesProduit as $dateProduit) {
            // on supprime celles qui n'existent plus
            $arr       = ['dateDepart' => $dateProduit->dateDepart, 'dateRetour' => $dateProduit->dateRetour];
            $booExiste = false;
            foreach ($arrDates as $arrDate) {
                if ($arrDate['dateDepart'] == $dateProduit->dateDepart && $arrDate['dateRetour'] == $dateProduit->dateRetour) {
                    $booExiste = true;
                    break;
                }
            }
            if (!$booExiste) {
                $dateProduit->delete();
            }
        }
        // on crée  les dates
        foreach ($arrDates as $arrDate) {
            SlProduitDate::firstOrCreate([
                                        'sl_produit_id' => $this->id,
                                        'dateDepart' => $arrDate['dateDepart'],
                                        'dateRetour' => $arrDate['dateRetour'],
                ]);
        }
    }

    //endregion

    //----------------------------------------------------------------------------------------------
    //region toHuman

    public function isSE()
    {
        $isSE = true;

        $arrRef = $this->references;

        if (count($arrRef) > 0) {
            $arrR = explode('_', $arrRef[0]->reference_id);
            $isSE = (strpos($arrR[1], '-SI') === false);
        }

        return $isSE;
    }

    public function paysToHuman()
    {
        $villes = $this->villes;
        if (count($villes) > 0) {
            return $villes[0]->pays->libelle;
        } else {
            return 'Erreur Pays';
        }
    }

    public function formuleToHuman()
    {
        return $this->formule->libelle;
    }

    public function langueToHuman()
    {
        return $this->langue->libelle;
    }

    public function categorieToHuman()
    {
        return $this->formule->categorie->libelle;
    }

    public function villesToHuman()
    {
        // cas spécial pour les séjours lnogues durée
        if ($this->formule->categorie->id == 14) {
            return 'Plusieurs villes disponibles';
        } else {
            $libVilles 		= '';
            foreach ($this->villes as $ville) {
                $libVilles .= $ville->libelle . ' - ';
            }

            return substr($libVilles, 0, -3);
        }
    }

    public function agesToHuman()
    {
        if ($this->ageMin != $this->ageMax) {
            if ($this->ageMax == 0) {
                return 'à partir de ' . $this->ageMin . ' ans';
            } else {
                return $this->ageMin . ' à ' . $this->ageMax . ' ans';
            }
        } else {
            return $this->ageMin . ' ans';
        }
    }

    public function nbJoursToHuman()
    {
        if ($this->nbJoursMin != $this->nbJoursMax) {
            return 'de ' . $this->nbJoursMin . ' à ' . $this->nbJoursMax . ' jours';
        } else {
            if ($this->nbJoursMax == 0) {
                return '';
            } else {
                return $this->nbJoursMax . ' jours';
            }
        }
    }

    public function nbJoursSeToHuman()
    {
        $litTranche = '';
        foreach ($this->references as $ref) {
            $objProgramme = CinetekHelper::getObjProgrammeFromRef($ref->reference_id);
            if ($objProgramme->dateDepartFrance != '0000-00-00' && $objProgramme->dateRetourFrance != '0000-00-00' && $litTranche == '') {
                $dateDepart = Carbon::createFromFormat('Y-m-d', $objProgramme->dateDepartFrance)->format('d/m/Y');
                $dateRetour = Carbon::createFromFormat('Y-m-d', $objProgramme->dateRetourFrance)->format('d/m/Y');

                if ($objProgramme->periodeTrancheOuFixe == 'P') {
                    $litTranche = 'Entre le ' . $dateDepart . ' et le ' . $dateRetour;
                } else {
                    $litTranche = 'Du ' . $dateDepart . ' au ' . $dateRetour;
                }
            } else {
                $litTranche = 'au choix';
            }
        }

        return $litTranche;
    }

    public function nbPcToHuman()
    {
        $litRetour = '';
        if ($this->formule->categorie_id == 14) {
            $litRetour = '';
        } else {
            if ($this->pcMin == 0) {
                $litRetour = 'dates au choix';
            } else {
                $arrPC    = collect();
                $arrDP    = collect();
                $arrNuits = collect();

                // on collecte tous les nombres de PC différents
                foreach ($this->references as $ref) {
                    $objProg = CinetekHelper::getObjProgrammeFromRef($ref->reference_id);
                    if ($objProg->nombrePC > 0 && $this->formule->categorie_id != 14) {
                        if ($objProg->codePC == 'PC' && !$arrPC->contains($objProg->nombrePC)) {
                            $arrPC->push(intval($objProg->nombrePC));
                        } elseif ($objProg->codePC == 'DP' && !$arrDP->contains($objProg->nombrePC)) {
                            $arrDP->push(intval($objProg->nombrePC));
                        } elseif ($objProg->codePC == 'Nuits' && !$arrNuits->contains($objProg->nombrePC)) {
                            $arrNuits->push(intval($objProg->nombrePC));
                        }
                    }
                }
                // on les classes
                $arrPC    = $arrPC->sort();
                $arrDP    = $arrDP->sort();
                $arrNuits = $arrNuits->sort();

                // construction des chaines
                if ($arrPC->count() > 0) {
                    $litRetour = ($arrPC->count() == 1) ? $arrPC->first() . ' pensions complètes<br/>' : 'de ' . $arrPC->first() . ' à ' . $arrPC->last() . ' pensions complètes<br/>';
                }
                if ($arrDP->count() > 0) {
                    $litRetour = ($arrDP->count() == 1) ? $arrDP->first() . ' demi-pensions<br/>' : 'de ' . $arrDP->first() . ' à ' . $arrDP->last() . ' demi-pensions<br/>';
                }
                if ($arrNuits->count() > 0) {
                    $litRetour = ($arrNuits->count() == 1) ? $arrNuits->first() . ' nuits<br/>' : 'de ' . $arrNuits->first() . ' à ' . $arrNuits->last() . ' nuits<br/>';
                }
            }
        }

        //retour
        return $litRetour;
    }

    public function nbHeuresCoursToHuman()
    {
        if ($this->isSE()) {
            if ($this->nbHeuresCoursMin == $this->nbHeuresCoursMax) {
                return $this->nbHeuresCoursMin . ' cours / semaine';
            } else {
                return 'à partir de ' . $this->nbHeuresCoursMin . ' cours / semaine';
            }
        } else {
            if ($this->nbHeuresCoursMin == $this->nbHeuresCoursMax) {
                return $this->nbHeuresCoursMin . ' heures de cours';
            } else {
                return 'à partir de ' . $this->nbHeuresCoursMin . ' heures de cours';
            }
        }
    }

    //endregion
}
