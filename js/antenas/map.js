var map = '';
var marker = '';
var markersArray = [];

function initialize() {
	image = 'img/antenas/generales/antenas_48.png';
	shadow = 'img/antenas/generales/antenas_shadow_48.png';
	var mapOptions = {
		zoom: 14,
		disableDoubleClickZoom: false,
		center: new google.maps.LatLng(-33.036658, -68.880687),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	};

	map = new google.maps.Map(document.getElementById('map'), mapOptions);

	if (editable) {
		google.maps.event.addListener(map, 'click', function(event) {
			placeMarker(event.latLng);
			document.getElementById("latitud").value = event.latLng.lat();
			document.getElementById("longitud").value = event.latLng.lng();
		});
	}
	loadMarker();
}

function placeMarker(location) {
	deleteOverlays();
	marker = new google.maps.Marker({
		position: location,
		map: map,
		icon: image,
		shadow: shadow,
		draggable: editable,
		animation: google.maps.Animation.DROP
	});
	markersArray.push(marker);
	google.maps.event.addListener(marker, 'click', toggleBounce);
	google.maps.event.addListener(marker, 'dragend', function(event) {
		document.getElementById("latitud").value = event.latLng.lat();
		document.getElementById("longitud").value = event.latLng.lng();
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

function toggleBounce() {
	if (marker.getAnimation() !== null) {
		marker.setAnimation(null);
	} else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}

function loadMarker() {
	var input_lat = document.getElementById('latitud').value;
	var input_lng = document.getElementById('longitud').value;
	if (input_lat !== '' && input_lng !== '') {
		var ubicacion = new google.maps.LatLng(input_lat, input_lng);
		placeMarker(ubicacion);
		map.setCenter(ubicacion);
	}
}

google.maps.event.addDomListener(window, 'load', initialize);

var latlng = 0;
$(document).ready(function() {
	$('#btn_editar_lat').click(function(e) {
		Swal.fire({
			title: 'Modificar Latitud',
			type: 'info',
			html: '\n\
						<div class="form-horizontal">\n\
							<div class="row">\n\
								<div class="form-group">\n\
									<label for="decimal" class="col-sm-4 control-label">Decimal</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="decimal" value="" id="decimal" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="grados" class="col-sm-4 control-label">Grados</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="grados" value="" id="grados" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="minutos" class="col-sm-4 control-label">Minutos</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="minutos" value="" id="minutos" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="segundos" class="col-sm-4 control-label">Segundos</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="segundos" value="" id="segundos" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="direccion" class="col-sm-4 control-label">Dirección</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="direccion" value="" id="direccion" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
							</div>\n\
						</div>',
			onOpen: function() {
				$("#decimal").inputmask("decimal", {rightAlign: false});
				$("#grados").inputmask("decimal", {rightAlign: false});
				$("#minutos").inputmask("decimal", {rightAlign: false});
				$("#segundos").inputmask("decimal", {rightAlign: false});

				$("#decimal").keyup(function() {
					coordDec = $("#decimal").val();
					updateCoordGMT(coordDec);
				});
				$("#grados").keyup(function() {
					updateCoordDec();
				});
				$("#minutos").keyup(function() {
					updateCoordDec();
				});
				$("#segundos").keyup(function() {
					updateCoordDec();
				});
				$("#direccion").keyup(function() {
					updateCoordDec();
				});
				coordDec = $("#latitud").val();
				$("#decimal").val(coordDec);
				updateCoordGMT(coordDec);
			},
			showCloseButton: true,
			showCancelButton: true,
			focusCancel: true,
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			cancelButtonClass: 'btn btn-default',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar',
			customClass: 'sweetalert-lg'
		}).then((result) => {
			if (result.value) {
				$("#latitud").val($("#decimal").val());
				loadMarker();
			}
		});
	});
	$('#btn_editar_lng').click(function(e) {
		Swal.fire({
			title: 'Modificar Longitud',
			type: 'info',
			html: '\n\
						<div class="form-horizontal">\n\
							<div class="row">\n\
								<div class="form-group">\n\
									<label for="decimal" class="col-sm-4 control-label">Decimal</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="decimal" value="" id="decimal" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="grados" class="col-sm-4 control-label">Grados</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="grados" value="" id="grados" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="minutos" class="col-sm-4 control-label">Minutos</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="minutos" value="" id="minutos" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="segundos" class="col-sm-4 control-label">Segundos</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="segundos" value="" id="segundos" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
								<div class="form-group">\n\
									<label for="direccion" class="col-sm-4 control-label">Dirección</label> \n\
									<div class="col-sm-8">\n\
										<input type="text" name="direccion" value="" id="direccion" class="form-control" required="" autocomplete="off">\n\
									</div>\n\
								</div>\n\
							</div>\n\
						</div>',
			onOpen: function() {
				$("#decimal").inputmask("decimal", {rightAlign: false});
				$("#grados").inputmask("decimal", {rightAlign: false});
				$("#minutos").inputmask("decimal", {rightAlign: false});
				$("#segundos").inputmask("decimal", {rightAlign: false});

				$("#decimal").keyup(function() {
					coordDec = $("#decimal").val();
					updateCoordGMT(coordDec);
				});
				$("#grados").keyup(function() {
					updateCoordDec();
				});
				$("#minutos").keyup(function() {
					updateCoordDec();
				});
				$("#segundos").keyup(function() {
					updateCoordDec();
				});
				$("#direccion").keyup(function() {
					updateCoordDec();
				});
				coordDec = $("#longitud").val();
				$("#decimal").val(coordDec);
				updateCoordGMT(coordDec);
			},
			showCloseButton: true,
			showCancelButton: true,
			focusCancel: true,
			buttonsStyling: false,
			confirmButtonClass: 'btn btn-primary',
			cancelButtonClass: 'btn btn-default',
			confirmButtonText: 'Aceptar',
			cancelButtonText: 'Cancelar',
			customClass: 'sweetalert-lg'
		}).then((result) => {
			if (result.value) {
				$("#longitud").val($("#decimal").val());
				loadMarker();
			}
		});
	});
});

function updateCoordGMT(coordDec) {
	c = new Coordenada();
	r1 = c.dec2gms(coordDec, latlng);
	$("#grados").val(r1.grados);
	$("#minutos").val(r1.minutos);
	$("#segundos").val(r1.segundos);
	$("#direccion").val(r1.direccion);
}

function updateCoordDec() {
	grados = $("#grados").val();
	minutos = $("#minutos").val();
	segundos = $("#segundos").val();
	direccion = $("#direccion").val();
	c = new Coordenada();
	r2 = c.gms2dec(grados, minutos, segundos, direccion);
	$("#decimal").val(r2.decimal);
}

function Coordenada()
{
	/**
	 * @param Float Valor real de la coordenada.
	 * @param Int Tipo de la coordenada {Coordenada.LATITUD, Coordenada.LONGITUD}.
	 * @return Array ['grados', 'minutos', 'segundos', 'direccion', 'valor'].
	 */
	this.dec2gms = function(valor, tipo)
	{
		grados = Math.abs(parseInt(valor));
		minutos = (Math.abs(valor) - grados) * 60;
		segundos = minutos;
		minutos = Math.abs(parseInt(minutos));
		segundos = Math.round((segundos - minutos) * 60 * 10000000000) / 10000000000;
		signo = (valor < 0) ? -1 : 1;
		direccion = (tipo === 1) ? ((signo > 0) ? 'N' : 'S') : ((signo > 0) ? 'E' : 'O');
		if (isNaN(direccion))
			grados = grados * signo;
		return {
			'grados': grados,
			'minutos': minutos,
			'segundos': segundos,
			'direccion': direccion,
			'valor': grados + "\u00b0 " + minutos + "' " + segundos + "\"" + ((isNaN(direccion)) ? (' ' + direccion) : '')
		};
	};
	/**
	 * @param Float Grados de la coordenada.
	 * @param Float Minutos de la coordenada.
	 * @param Float Segundos de la coordenada.
	 * @param String Sentido de la coordenada {Coordenada.NORTE, Coordenada.SUR, Coordenada.ORIENTE, Coordenada.OCCIDENTE}
	 * @return Array ['decimal', 'valor'].
	 */
	this.gms2dec = function(grados, minutos, segundos, direccion)
	{
		if (direccion)
		{
			signo = (direccion.toLowerCase() === 'o' || direccion.toLowerCase() === 's') ? -1 : 1;
			direccion = (direccion.toLowerCase() === 'o' || direccion.toLowerCase() === 's' ||
							direccion.toLowerCase() === 'n' || direccion.toLowerCase() === 'e') ? direccion.toLowerCase() : '';
		} else
		{
			signo = (grados < 0) ? -1 : 1;
			direccion = '';
		}
		dec = Math.round((Math.abs(grados) + minutos / 60 + segundos / 3600) * 100000000000000) / 100000000000000;
		if (isNaN(direccion) || direccion === '')
			dec = dec * signo;
		return {
			'decimal': dec,
			'valor': dec + "\u00b0" + ((isNaN(direccion) || direccion === '') ? (' ' + direccion) : '')
		};
	};
}