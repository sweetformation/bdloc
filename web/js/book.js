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
                //console.log($(server_response).find("#itemsNumber").html())
                //$("#itemsNumber").html( $(server_response).find("#itemsNumber").html() )
                //$("#itemsNumber").html( $(server_response).find("#itnum").html() )
                // Changement d'attributs du bouton...
                bouton.off("click")
                bouton.parent().attr("href", "")
                if ($("#itemsNumber").html() < 10) {
                    bouton.val("Dans votre panier")
                }
                // Maj du stock
                //console.log(bouton.parent().parent().find(".bookStock").html())
                bouton.parent().parent().find(".bookStock").html( $(server_response).find("#bookStock") )
                // Affichage des messages
                //console.log(bouton.parent().parent().parent().parent().parent().find("#flash-messages").html())
                bouton.parent().parent().parent().parent().parent().find("#flash-messages").html( $(server_response).find("#flash-messages").html() )
                url_items = $(server_response).find("#urlItemsNumber").html()
                $.ajax({
                   url: url_items, 
                   success: function(server_response) {
                       $("#itemsNumber").html( server_response )
                   }
                })
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
        $(document).on("click", ".listeBdCommandees", this.listingBd)
        

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
    },

    listingBd: function(e) {
        console.log("book.listingBd")
        var listing = $("#listing")
        //if (listing.css({display: "none"})) {
            popup.afficher( listing.html() )
        //}
        //if (listing.css({display: "block"})) {
        //    popup.fermer
        //}
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