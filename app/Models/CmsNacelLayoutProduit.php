<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request as Request;
use App\Models\Web\CmsNacelLayoutCategorie;
use LayoutsHelper;
use NacelHelper;
//Elasticsearch
use ScoutElastic\Searchable;
use App\Models\ES\Configurator\CmsNacelLayoutProduitIndexConfigurator;

class CmsNacelLayoutProduit extends Modele
{
    use SoftDeletes;
    use Searchable;
    protected $guarded    = [];
    protected $connection = 'web';

    // définition des propriétés public
    public $table = 'cms_nacel_layouts_produits';

    // statiques
    public static $nom        = 'Page produit';
    public static $nomPluriel = 'Pages produit';
    public static $societe_id = 2;
    public static $route      = 'layoutProduit';
    public static $blade      = 'cms.layouts.nacel.layoutProduit';
    public static $rules      = [
    'nom' => 'required|string|max:255',
    'url' => 'sometimes|required|string|max:255',
    'title' => 'required|string|max:255',
    'metaDescription' => 'required|string|max:300',
    'h1' => 'sometimes|required|string|max:255',
];
    public static $storage = ''; // est redéfini dans le constructeur car impossible de le créer en static avec config

    //Elasticsearch !!! Début
    protected $indexConfigurator = CmsNacelLayoutProduitIndexConfigurator::class;

    protected $searchRules = [
        //
];

    protected $mapping = [
    'properties' => [
            'id' => [
                'type' => 'integer',
            ],
                'nom' => [
                'type' => 'text',
                'analyzer' => 'french',
                ],
            'url' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'title' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'metaDescription' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'h1' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'accroche' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'introduction' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'plusProduit1' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                'plusProduit2' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                'plusProduit3' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'ecoleNom' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'ecolePresentation' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                        'coursPresentation' => [
                'type' => 'text',
                'analyzer' => 'french',
            ],
                'hebergementPresentation' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'hebergementPresentationFamille' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'hebergementPresentationAppartement' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'hebergementPresentationResidence' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'activitesPresentation' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'liensUtiles' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'voyagePresentation' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'voyagePresentationAvion' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'voyagePresentationTrain' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'voyagePresentationAutocar' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'videos' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'longueDureePresentation' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'longueDureeTarifs' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
                'stagePresentation' => [
                    'type' => 'text',
                    'analyzer' => 'french',
                ],
            ],
        ];
    //Elasticsearch !!! Fin

    public function slProduit()
    {
        return $this->belongsTo('App\Models\Web\SlProduit', 'sl_produit_id', 'id');
    }

    /**
     * surcharge du constructeur parent pour spéficier le storage
     */
    public function __construct($attributes = [])
    {
        parent::__construct($attributes);

        $this::$storage = config('sites.nacel.storage') . 'produits/';
    }

    /**
     * Renvoie un tableau de datas de correspondance et de traitement des champs
     *
     * @param Request $request
     * @return array
     */
    public static function getArrAssign(Request $request)
    {
        $arrRetour = [
        'nom' => $request->input('nom'),
        'online' => $request->input('online') ?? 0,
        'title' => $request->input('title'),
        'metaDescription' => $request->input('metaDescription'),
        'sl_produit_id' => $request->input('sl_produit_id'),

        'h1' => $request->input('h1') ?? '',
        'accroche' => $request->input('accroche') ?? '',
        'introduction' => $request->input('introduction') ?? '',

        'plusProduit1' => $request->input('plusProduit1') ?? '',
        'plusProduit2' => $request->input('plusProduit2') ?? '',
        'plusProduit3' => $request->input('plusProduit3') ?? '',

        'ecoleNom' => $request->input('ecoleNom') ?? '',
        'ecolePresentation' => $request->input('ecolePresentation') ?? '',

        'coursPresentation' => $request->input('coursPresentation') ?? '',

        'hebergementPresentation' => $request->input('hebergementPresentation') ?? '',
        'hebergementPresentationFamille' => $request->input('hebergementPresentationFamille') ?? '',
        'hebergementPresentationAppartement' => $request->input('hebergementPresentationAppartement') ?? '',
        'hebergementPresentationResidence' => $request->input('hebergementPresentationResidence') ?? '',

        'activitesPresentation' => $request->input('activitesPresentation') ?? '',
        'liensUtiles' => $request->input('liensUtiles') ?? '',

        'voyagePresentation' => $request->input('voyagePresentation') ?? '',
        'voyagePresentationAvion' => $request->input('voyagePresentationAvion') ?? '',
        'voyagePresentationTrain' => $request->input('voyagePresentationTrain') ?? '',
        'voyagePresentationAutocar' => $request->input('voyagePresentationAutocar') ?? '',

        'videos' => $request->input('videos') ?? '',

        'longueDureePresentation' => $request->input('longueDureePresentation') ?? '',
        'longueDureeTarifs' => $request->input('longueDureeTarifs') ?? '',

        'stagePresentation' => $request->input('stagePresentation') ?? '',
    ];

        if ($request->has('url-produit-full')) {
            $arrRetour['url'] = $request->input('url-produit-full') . str_slug($request->input('url'));
        }

        return $arrRetour;
    }

    /**
     * Renvoie l'image principale
     *
     * @return void
     */
    public function getImagePrincipale()
    {
        $arrImage = LayoutsHelper::getVisuels($this, 'imagePrincipale');
        if (count($arrImage) > 0) {
            return NacelHelper::optimiseImageNacel(1920, 1440, $arrImage[0]);
        } else {
            return '';
        }
    }

    /**
     * Renvoie une miniature
     *
     * @return void
     */
    public function getImageThumb()
    {
        $arrImage = LayoutsHelper::getVisuels($this, 'imagePrincipale');
        if (count($arrImage) > 0) {
            return NacelHelper::optimiseImageNacel(300, 200, $arrImage[0]);
        } else {
            return '';
        }
    }

    /**
     * Renvoie les fichiers
     *
     * @return void
     */
    public function getProgrammesDetailles()
    {
        return NacelHelper::getUrlNameFichiers($this::$storage . $this->id . '/programmes');
    }

    /**
     * récupère le code HTML pour la galere d'images d'un produit
     *
     * @return void
     */
    public function getGalleryProduit()
    {
        $arrGallery = LayoutsHelper::getVisuels($this, 'visuels');
        $imgPrinc   = LayoutsHelper::getVisuels($this, 'imagePrincipale');

        $arrGalleryNacel = [];
        // on rajoute l'image principale au diaporama
        if (count($imgPrinc) > 0) {
            array_push($arrGalleryNacel, $imgPrinc[0]);
        }

        foreach ($arrGallery as $visuel) {
            array_push($arrGalleryNacel, $visuel);
        }

        return $arrGalleryNacel;
    }

    //region toHuman

    public function accompaniedOrAlone()
    {
        if ($this->slProduit == null) {
            return '';
        } else {
            return ($this->slProduit->isSE()) ? 'alone' : 'accompanied';
        }
    }

    public function pictoCategorie()
    {
        $cat = CmsNacelLayoutCategorie::where('categorie_id', $this->slProduit->formule->categorie->id)->first();
        if ($cat != null) {
            return $cat->picto;
        } else {
            return '';
        }
    }

    //region
}
