var vectorLayer;
var iconGeometry;

function createMap(divId, lat, lon, nav) {
    map = new ol.Map({
        target: divId,
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([-68.8418, -33.0677]),
            zoom: 11
        })
    });

    var zoomslider = new ol.control.ZoomSlider();
    map.addControl(zoomslider);

    if (lat && lon) {
        iconGeometry = new ol.geom.Point(ol.proj.fromLonLat([lon, lat]));
        var iconFeature = new ol.Feature({
            geometry: iconGeometry,
        });
        var iconStyle = new ol.style.Style({
            image: new ol.style.Icon({
                opacity: 0.75,
                src: 'img/lujan_pass/gps.png'

            })
        });
        iconFeature.setStyle(iconStyle);
        var vectorSource = new ol.source.Vector({
            features: [iconFeature]
        });
        vectorLayer = new ol.layer.Vector({
            source: vectorSource
        });
        map.addLayer(vectorLayer);
    }

    if (nav) {
        map.on("singleclick", function(evt) {
            setPinOnMap(evt);
        });
    }
}

function setPinOnMap(evt)
{
    var lonLat = ol.proj.toLonLat(evt.coordinate);
    var lat = lonLat[1];
    var lon = lonLat[0];

    if (vectorLayer !== undefined) {
        iconGeometry.setCoordinates(evt.coordinate);
    } else {
        iconGeometry = new ol.geom.Point(evt.coordinate);
        var iconFeature = new ol.Feature({
            geometry: iconGeometry,
        });
        var iconStyle = new ol.style.Style({
            image: new ol.style.Icon({
                opacity: 0.75,
                src: 'img/lujan_pass/gps.png'

            })
        });
        iconFeature.setStyle(iconStyle);
        var vectorSource = new ol.source.Vector({
            features: [iconFeature]
        });
        vectorLayer = new ol.layer.Vector({
            source: vectorSource
        });
        map.addLayer(vectorLayer);
    }
    document.getElementById("latitud").value = lat.toPrecision(10);
    document.getElementById("longitud").value = lon.toPrecision(10);
}

function updatePinOnMap(lon, lat)
{
    var lonLat = ol.proj.fromLonLat([lon, lat]);
    if (vectorLayer !== undefined) {
        iconGeometry.setCoordinates(lonLat);
    } else {
        iconGeometry = new ol.geom.Point(lonLat);
        var iconFeature = new ol.Feature({
            geometry: iconGeometry,
        });
        var iconStyle = new ol.style.Style({
            image: new ol.style.Icon({
                opacity: 0.75,
                src: 'img/lujan_pass/gps.png'
            })
        });
        iconFeature.setStyle(iconStyle);
        var vectorSource = new ol.source.Vector({
            features: [iconFeature]
        });
        vectorLayer = new ol.layer.Vector({
            source: vectorSource
        });
        map.addLayer(vectorLayer);
    }
}

function removePinOnMap()
{
    if (vectorLayer !== undefined) {
        map.removeLayer(vectorLayer);
        vectorLayer = undefined;
        iconGeometry = undefined;
    }
}
