

loader = {

    // Création du loader
    init: function() {

        // Je crée un loader
        this.overlay = $("<div>", {
            css: {
                backgroundImage: "url(../img/appearances/loader.gif)",
                backgroundRepeat: "no-repeat",
                backgroundPosition: "center center",
                position: "fixed",
                top: "50%",
                left: "50%",
                /*marginTop: -400,
                marginLeft: -400,
                height: 800,
                width: 800,*/
                display: "none"
            }
        })

        // J'ajoute au DOM
        $("body").append( this.overlay )

    },

    affiche: function() {
        this.overlay.css({display: "block"})
        //this.overlay.animate({display: "block"}, {duration: 400})

    },

    efface: function() {
        this.overlay.css({display: "none"})
    }
}


function init() {

}

function chargement() {

    // Ecouteurs d'évènement
    //$("#choixAbo input:radio").on("change", afficherPrix)
    $("#bdloc_appbundle_creditcard_abonnement input:radio").on("change", afficherPrix)
    $("#bdloc_appbundle_creditcard_submit").on("click", loader.affiche)

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
        $("#prix").html("12,00")
    }
    else if (choixAbo == "A") {
        $("#prix").html("120,00")
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