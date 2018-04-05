<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"
        crossorigin="anonymous">
    <title>Elasticsearch</title>
    <style>
        .code {
            background-color: black;
            max-height: 100vh;
            overflow-x: scroll;
            font-size:12px;
        }
        #pre{
            white-space : wrap;

        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">

            <div class="col-6">
                <!-- formulaire global -->
                <form method="POST" action="{{route('toES')}}" id="formRecherche" class="form-group">
                    <div class='row'>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectCategories">Sélectionnez une ou plusieurs catégories :</label>
                                <select multiple class="form-control form-control-sm" id="selectCategories" name='selectCategories[]'>
                                    @foreach($categories as $id=>$libelle)
                                    <option value='{{$id}}'>{{$libelle}} - id : {{$id}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">

                            <div class="row form-group">
                                <label for="selectFormules">Sélectionnez une ou plusieurs formules :</label>
                                <select multiple class="form-control form-control-sm" id="selectFormules" name='selectFormules[]'>
                                    @foreach($formules as $id=>$libelle)
                                    <option value='{{$id}}'>{{$libelle}} - id : {{$id}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectHeberg">Sélectionnez un ou plusieurs hébergements :</label>
                                <select multiple class="form-control form-control-sm" id="selectHeberg" name='selectHeberg[]'>
                                    @foreach($hebergements as $id=>$libelle)
                                    <option value='{{$id}}'>{{$libelle}} - id : {{$id}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectLangues">Sélectionnez une ou plusieurs langues :</label>
                                <select multiple class="form-control form-control-sm" id="selectLangues" name='selectLangues[]'>
                                    @foreach($langues as $id=>$libelle)
                                    <option value='{{$id}}'>{{$libelle}} - id : {{$id}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectModeTransp">Sélectionnez un ou plusieurs modes de transports :</label>
                                <select multiple class="form-control form-control-sm" id="selectModeTransp" name='selectModeTransp[]'>
                                    @foreach($modeTransports as $id=>$libelle)
                                    <option value='{{$id}}'>{{$libelle}} - id : {{$id}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectPays">Sélectionnez un ou plusieurs pays :</label>
                                <select multiple class="form-control form-control-sm" id="selectPays" name='selectPays[]'>
                                    @foreach($pays as $id=>$libelle)
                                    <option value='{{$id}}'>{{$libelle}} - id : {{$id}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectVilles">Sélectionnez une ou plusieurs villes :</label>
                                <select multiple class="form-control form-control-sm" id="selectVilles" name='selectVilles[]'>
                                    @foreach($villes as $id=>$libelle)
                                    <option value='{{$id}}'>{{$libelle}} - id : {{$id}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectAgeMin">Sélectionnez un age minimum :</label>
                                <select class="form-control form-control-sm" id="selectAgeMin" name='selectAgeMin'>
                                    <option value='-1'>-</option>
                                    @for($i = 1; $i
                                    <36 ; $i++) <option value='{{$i}}'>{{$i}}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectAgeMax">Sélectionnez un age maximum :</label>
                                <select class="form-control form-control-sm" id="selectAgeMax" name='selectAgeMax'>
                                    <option value='-1'>-</option>
                                    @for($i = 1; $i
                                    <36 ; $i++) <option value='{{$i}}'>{{$i}}</option>
                                        @endfor
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectPrix">Sélectionnez un prix :</label>
                                <select class="form-control form-control-sm" id="selectPrix" name='selectPrix[]'>
                                    <option value='-1'>-</option>
                                    @foreach($slProduitPrix as $produit)
                                    <option value='{{$produit['prixAccroche']}}'>{{$produit['prixAccroche']}} €</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectHeureMin">Sélectionnez une heure minimum :</label>
                                <select class="form-control form-control-sm" id="selectHeureMin" name='selectHeureMin'>
                                    <option value='-1'>-</option>
                                    @foreach($slProduitHeureMin as $produit)
                                    <option value='{{$produit['nbHeuresCoursMin']}}'>{{$produit['nbHeuresCoursMin']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="row form-group">
                                <label for="selectHeureMax">Sélectionnez une heure maximum :</label>
                                <select class="form-control form-control-sm" id="selectHeureMax" name='selectHeureMax'>
                                    <option value='-1'>-</option>
                                    @foreach($slProduitHeureMax as $produit)
                                    <option value='{{$produit['nbHeuresCoursMax']}}'>{{$produit['nbHeuresCoursMax']}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-6">

                            <div class="form-group row">

                                <label for="queryRecherche">Faites votre recherche</label>
                                <input class="form-control form-control-sm" type="text" id="queryRecherche" placeholder="Tapez une phrase" name="queryRecherche">

                            </div>
                        </div>
                    </div>




                    <div class="form-group row">
                        <input type="submit" id="btnRecherche" value="Lancer la recherche">
                    </div>
                
                </form>

            </div>
            <div class="col-6 code">

        <pre id='pre'><code id="result" class="text-light">Résultats ici</code></pre>


    </div>
        </div>

    </div>
    

    <script type="text/javascript" src="/js/ajax.js"></script>
</body>

</html>