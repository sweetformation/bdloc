/************************
 *      Modules         *
 ************************/




/************************
 *   Objet principal    *
 ************************/

app = {
    
    
    /* Chargement du DOM */
    init: function() {
        console.log("app.init")

    },

    /* Chargement de la page */
    chargement: function(){
        console.log("app.chargement")

        // Autocompletion d'address
        this.adresses()

    },

    adresses: function() {
        console.log("app.adresses")

        // On chope notre input
        var input = document.getElementById("bdloc_appbundle_register_address")
        //console.log(input)

        // Paramètres
        var options = {
            types: ['address'],
            componentRestrictions: {
                country: 'fr'
            }
        }

        // On ajoute l'autocomplete
        var autocomplete = new google.maps.places.Autocomplete(input, options)

        // lors de la sélection d'adresse, on récupère les coordonnées
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            // On récupère 
            var loc = autocomplete.getPlace().geometry.location

            // On traduit l'info dans un objet compréhensible par API OpenWeather
            var data = {
                lat: loc.lat(),
                lon: loc.lng()
            }

            // On affiche la météo avec les coordonnées
            //app.meteo(data)

        })

    },

    meteo: function(myData) {

        var myUrl = "http://api.openweathermap.org/data/2.5/weather"

        // On demande à l'API
        $.ajax({
            url: myUrl,
            data: myData,
            dataType: "json",
            success: function(res) {

                // On réucpère l'icône
                var icon = res.weather[0].icon

                // On crée l'image
                var img = $("<img>", {
                    src: "http://openweathermap.org/img/w/" + icon + ".png",
                    css: {
                        position: "absolute",
                        marginTop: -55,
                        marginLeft: 610
                    }
                })

                // On affiche l'image
                //$("#address").after(img)
                img.insertAfter("#address")
            }
        })
    },

    gpsCoordinates: function() {
        var geocoder = new google.maps.Geocoder()
        var inputAddress = document.getElementById("address").val()
        console.log(inputAddress)
        geocoder.geocode({
            "address": inputAddress
            }, function(results) {
            console.log(results[0].geometry.location); //LatLng
        })
    }

}



/*************************
 *  Chargement du DOM    *
 *  = chargement du html *
 *************************/

$(function() {
    console.log("chargement du dom")
    app.init()
})


/****************************
 *  Chargement de la page   *
 *  = chargement des assets *
 ****************************/

$(window).load(function() {
    console.log("chargement de la page")
    app.chargement()
});