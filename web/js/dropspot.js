

var infoWindow = []
var markers = []
var contentString = []
var geocoder
var map


function initialize() {
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(48.856614, 2.3522219000000177);  // coord de Paris
    var mapOptions = {
        zoom: 14,
        center: latlng
    }

    map = new google.maps.Map(document.getElementById('map-canvas'), mapOptions);

    var MARKER_PATH = 'https://maps.gstatic.com/intl/en_us/mapfiles/marker_green'
    // Create a marker for each adress, and assign a letter of the alphabetic to each marker icon.
    for (var i = 0; i < dropTab.length; i++) {
        // Contenu des infoWindows
        contentString[i] = '<div class="infoContent">'+
                                '<p class="infoName">'+dropTab[i].nom+'</p>'+
                                '<p class="infoAdresse">'+dropTab[i].add+' '+dropTab[i].zip+' Paris</p>' +
                                '<div class="infoChoix">Choisir</div>' +
                            '</div>';
        //var markerLetter = String.fromCharCode('A'.charCodeAt(0) + i);
        //var markerIcon = MARKER_PATH + markerLetter + '.png';
        var markerIcon = MARKER_PATH + '.png';
        infoWindow[i] = new google.maps.InfoWindow({
           content: contentString[i]
        });
        // Use marker animation to drop the icons incrementally on the map.
        markers[i] = new google.maps.Marker({
            position: {lat: dropTab[i].lat,lng: dropTab[i].lng},
            animation: google.maps.Animation.DROP,
            icon: markerIcon,
            title: ''+i
        });
        // If the user clicks a hotel marker, show the details in an info window.
        google.maps.event.addListener(markers[i], 'click', showInfoWindow)

        markers[i].setMap(map)
    }

    // Ajoute marker rouge pour l'adresse de l'utilisateur
    ajoutAddUser()

    $("#map-canvas").on("click", ".infoChoix", choisir)

}

function ajoutAddUser() {
    var address = add_user + ', Paris'
    geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            map.setCenter(results[0].geometry.location);
            var marker = new google.maps.Marker({
                map: map,
                position: results[0].geometry.location
            });
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    })
}

function showInfoWindow() {
    var marker = this;
    var i = marker.title
    infoWindow[i].open(map, marker)
}

function choisir() {
    //console.log("choisir")
    var choix = $(this).parent().find(".infoName").html()
    console.log(choix)
    var select = $("#bdloc_appbundle_dropspot_dropspot option")
    $("#bdloc_appbundle_dropspot_dropspot option:selected").attr("selected", null)

    select.each(function() {
        if( $(this).text() == choix ) {
            $(this).attr("selected", "selected")
        }
    })
}

google.maps.event.addDomListener(window, 'load', initialize)