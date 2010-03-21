var current_marker = null;
var map = null;


function initialize() {
    if (GBrowserIsCompatible()) {
        map = new GMap2(document.getElementById("map"));
        
        var point = getPointFromForm();
        if (point != null) {
            map.setCenter(point, 13);
            map.setUIToDefault();
            setMarkerOnMap(point);
        
            GEvent.addListener(map, 'click', function(overlay, point) { 
                setMarkerOnMap(point);
            });
        }
    }
}

function getPointFromForm()
{
    var latitude = parseFloat(document.getElementById('latitude').value);
    var longitude = parseFloat(document.getElementById('longitude').value);
    if (isNaN(latitude) || isNaN(longitude)) {
        return null;
    }
    var point = new GLatLng(latitude, longitude);
    return point;
}

function setFormValues(point)
{
    var latitude = document.getElementById('latitude');
    var longitude = document.getElementById('longitude'); 
    longitude.value = point.x;
    latitude.value = point.y; 
}

function setMarkerFromForm()
{
    var point = getPointFromForm();
    if (point != null) {
        setMarkerOnMap(point);
        var zoomLevel = 13;
        if (point.x == 0 || point.y == 0) {
            zoomLevel = 0;
        }
        map.setCenter(point, zoomLevel);
    }
}

function setMarkerOnMap(point) 
{
    var marker = new GMarker(point, {draggable: true});
    GEvent.addListener(marker, "dragend", function(point) {
        setMarkerOnMap(point);
        });
    map.addOverlay(marker);
    
    if (current_marker) { 
        map.removeOverlay(current_marker); 
        current_marker = null; 
    } 
    current_marker = marker;
    setFormValues(point);
    
}
