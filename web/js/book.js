/************************
 *      Modules         *
 ************************/

popup = {

    // Création de la popup
    chargement: function() {
        console.log("popup.chargement")
        // Je récupère la popup
        this.overlay = $("#popup")

        // On écoute le click sur croix fermeture
        $("#croixFermeture").on("click", this.fermer)

    },

    // Ajouter un contenu et affiche
    afficher: function(x) {
        //this.overlay.append(x).fadeIn()
        $("#popupContent").append(x)
        this.overlay.fadeIn()
    },

    fermer: function() {
        console.log("popup.fermer")
        $("#popup").fadeOut({
            duration: 1000,
            complete: function() {
                $("#popupContent").html("")  
            }
        })
    }

}


cart = {

    /* Chargement de la page */
    chargement: function(){
        console.log("cart.chargement")


        /* ******** On pose des écouteurs ********** */
        //$(document).on("click", ".commander", this.ajouteBd)
        if ($(".commander").val() == "Ajouter au panier !") {
            $(".commander").on("click", this.ajouteBd)
        }
        $(document).on("click", ".removeBDpanier", this.supprimeBd)

    },

    ajouteBd: function(e) {
        e.preventDefault()
        console.log("cart.ajouteBd")
        var bouton = $(this)
        //console.log(bouton)
        var url = bouton.parent().attr("href")
        $.ajax({
            url: url, 
            success: function(server_response) {
                // Maj du nb d'éléments en panier
                $("#itemsNumber").html( $(server_response).find("#itemsNumber") )
                // Changement d'attributs du bouton...
                bouton.val("Dans votre panier").off("click")
                bouton.parent().attr("href", "")
                // Maj du stock
                console.log(bouton.parent().parent().find(".bookStock").html())
                bouton.parent().parent().find(".bookStock").html( $(server_response).find("#bookStock") )
            },
            error: function() {
                console.log("erreur dans fonction cart.ajouteBd")
            }

        })
        // Prevent Default
        return false
    },

    supprimeBd: function() {
        console.log("cart.supprimeBd")
        var bouton = $(this)
        //console.log(bouton)
        var url = bouton.parent().attr("href")
        $.ajax({
            url: url, 
            success: function(server_response) {
                // Maj du nb d'éléments en panier
                $("#itemsNumber").html( $(server_response).find("#itemsNumber") )
                // Maj de l'affichage des éléments en panier
                $("#cart").html( $(server_response).find("#cart") )
            },
            error: function() {
                console.log("erreur dans fonction cart.supprimeBd")
            }

        })
        // Prevent Default
        return false
    }


}


/************************
 *   Objet principal    *
 ************************/

book = {
    
    
    /* Chargement du DOM */
    init: function() {
        console.log("book.init")

    },

    /* Chargement de la page */
    chargement: function(){
        console.log("book.chargement")

        /* ******** On pose des écouteurs ********** */
        $(document).on("click", ".details", this.detailsBd)

    },

    detailsBd: function(e) {
        e.preventDefault()
        console.log("book.detailsBd")
        var bouton = $(this)
        var url = bouton.parent().attr("href")
        $.ajax({
            url: url, 
            success: function(server_response) {
                popup.afficher($(server_response).find("#containerPostBd"))
            },
            error: function() {
                console.log("erreur dans fonction book.detailsBd")
            }

        })
    }



}



/*************************
 *  Chargement du DOM    *
 *  = chargement du html *
 *************************/

$(function() {
    console.log("chargement du dom")
    book.init()
})


/****************************
 *  Chargement de la page   *
 *  = chargement des assets *
 ****************************/

$(window).load(function() {
    console.log("chargement de la page")
    book.chargement()
    cart.chargement()
    popup.chargement()
});