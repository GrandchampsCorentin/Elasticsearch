$(document).ready(function () {

    "use strict";

    $('#btnRecherche').on('click', function (e) {

        e.preventDefault();

        // init des variables:
        var divResult = $('#result'); // div dans lequel tu vas afficher les résultats
        var maRoute = 'getSerp'; // route déclarée dans web.php en POST
        var monForm = $('#formRecherche'); // form qui contient tous tes select/input

        console.log(divResult.attr('id'));
        // appel
        $.ajax({
            type: 'POST',
            url: maRoute,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // chouette, on a déjà mis la meta
            },
            dataType: "json",
            data: monForm.serialize(),
            success: function (data) {
                divResult.html(JSON.stringify(data, null,2));
            },
            error: function (data) {
                divResult.html(data);
            }
        });
    });
});
