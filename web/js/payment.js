
function init() {

}

function chargement() {

    // Ecouteurs d'évènement
    //$("#choixAbo input:radio").on("change", afficherPrix)
    $("#bdloc_appbundle_creditcard_abonnement input:radio").on("change", afficherPrix)

}

function afficherPrix() {

    //var choixAbo = $("#choixAbo input:radio:checked").val()
    //if (choixAbo == "mensuel") {
    //    $("#prix").html(12)
    //}
    //else if (choixAbo == "annuel") {
    //    $("#prix").html(120)
    //}

    var choixAbo = $("#bdloc_appbundle_creditcard_abonnement input:radio:checked").val()
    if (choixAbo == "M") {
        $("#prix").html("12.00")
    }
    else if (choixAbo == "A") {
        $("#prix").html("120.00")
    }
}



/*************************
 *  Chargement du DOM    *
 *  = chargement du html *
 *************************/

$(function() {
    console.log("payment.js chargement du dom")
    init()
})


/****************************
 *  Chargement de la page   *
 *  = chargement des assets *
 ****************************/

$(window).load(function() {
    console.log("payment.js chargement de la page")
    chargement()
})