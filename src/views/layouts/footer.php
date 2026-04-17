</main>

<script src="https://cdn.jsdelivr.net/npm/ol@v10.8.0/dist/ol.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');

    if (!mapElement) {
        return;
    }

    const map = new ol.Map({
        target: 'map',
        layers: [
            new ol.layer.Tile({
                source: new ol.source.OSM()
            })
        ],
        view: new ol.View({
            center: ol.proj.fromLonLat([86.087314, 55.354968]),
            zoom: 6
        })
    });

    const markerSource = new ol.source.Vector();

    const markerLayer = new ol.layer.Vector({
        source: markerSource
    });

    map.addLayer(markerLayer);

    const xInput = document.getElementById('point-x');
    const yInput = document.getElementById('point-y');
    const latValue = document.getElementById('lat-value');
    const lngValue = document.getElementById('lng-value');

    let markerFeature = null;

    map.on('click', function (event) {
        const coords = ol.proj.toLonLat(event.coordinate);
        const lng = coords[0].toFixed(7);
        const lat = coords[1].toFixed(7);

        if (markerFeature) {
            markerSource.removeFeature(markerFeature);
        }

        markerFeature = new ol.Feature({
            geometry: new ol.geom.Point(event.coordinate)
        });

        markerSource.addFeature(markerFeature);

        xInput.value = lat;
        yInput.value = lng;

        latValue.textContent = lat;
        lngValue.textContent = lng;
    });

    const pointForm = document.getElementById('point-form');

    if (pointForm) {
        pointForm.addEventListener('submit', function (event) {
            if (!xInput.value || !yInput.value) {
                event.preventDefault();
                alert('Сначала выберите точку на карте');
            }
        });
    }
});
</script>
</body>
</html>