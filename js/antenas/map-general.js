var map;
var cantidad_figuras = 0;
var figuras = {};
var markersArray = [];
var infowindow = new google.maps.InfoWindow();

function initialize() {
	var mapOptions = {
		zoom: 14,
		disableDoubleClickZoom: false,
		center: new google.maps.LatLng(-33.036658, -68.880687),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	map = new google.maps.Map(document.getElementById('map'), mapOptions);
	filtrarFiguras();
}

function extraePuntos(figura) {
	var puntos = {};
	for (i in figura) {
		puntos[i] = {lat: figura[i].lat(), lng: figura[i].lng()};
	}
	return puntos;
}

function loadFiguras(data_figura) {
	if (data_figura !== '') {
		var _figuras = data_figura;
		$.each(_figuras, function(key, value) {
			var index = cantidad_figuras;
			figuras[index] = value;
			if (value.tipo === 'marker') {
				var figura_overlay = new google.maps.Marker({
					position: new google.maps.LatLng(value.puntos[0]['lat'], value.puntos[0]['lng']),
					icon: value.option,
					clickable: true,
					draggable: false
				});
			}
			markersArray.push(figura_overlay);
			figura_overlay.setMap(map);

			if (value.tipo === 'marker') {
				google.maps.event.addListener(figura_overlay, 'click', function(e) {
					infowindow.setContent(value.tooltip);
					infowindow.setPosition(e.latLng);
					infowindow.open(map);
				});

				google.maps.event.addListener(figura_overlay, 'dragend', function() {
					figuras[index]['puntos'] = {0: {lat: this.getPosition().lat(), lng: this.getPosition().lng()}};
				});
			}
			cantidad_figuras++;
		});
	}
}

function filtrarFiguras() {
	var proveedor = $('#proveedor option:selected').val();
	var distrito = $('#distrito option:selected').val();
	deleteOverlays();
	$.ajax({
		type: "POST",
		url: 'antenas/mapa/getData',
		dataType: "json",
		data: {proveedor: proveedor, distrito: distrito, csrf_mlc2: csrfData},
		success: function(data, textStatus, jqXHR) {
			if (data !== null) {
				loadFiguras(data);
			}
		}
	});
}

function deleteOverlays() {
	if (markersArray) {
		for (i in markersArray) {
			markersArray[i].setMap(null);
		}
		markersArray.length = 0;
	}
}
google.maps.event.addDomListener(window, 'load', initialize);