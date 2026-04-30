</main>

<script src="https://cdn.jsdelivr.net/npm/ol@v10.8.0/dist/ol.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const mapElement = document.getElementById('map');

    if (!mapElement) {
        return;
    }

    const mapPointsData = JSON.parse(mapElement.dataset.mapPoints || '[]');
    const selectedPointId = Number(mapElement.dataset.selectedPointId || 0);
    const emptyState = document.getElementById('point-details-empty');
    const detailsCard = document.getElementById('point-details-card');
    const newPointCard = document.getElementById('new-point-card');
    const photoBlock = document.getElementById('point-details-photo');
    const pointIdValue = document.getElementById('point-details-id');
    const usernameValue = document.getElementById('point-details-username');
    const userIdValue = document.getElementById('point-details-user-id');
    const statusValue = document.getElementById('point-details-status');
    const dateValue = document.getElementById('point-details-date');
    const coordinatesValue = document.getElementById('point-details-coordinates');
    const commentsList = document.getElementById('point-comments-list');
    const commentPointId = document.getElementById('comment-point-id');
    const pointForm = document.getElementById('point-form');
    const newPointCommentTitle = document.getElementById('new-point-comment-title');
    const newPointCommentText = document.getElementById('new-point-comment-text');
    const xInput = document.getElementById('point-x');
    const yInput = document.getElementById('point-y');
    const latValue = document.getElementById('lat-value');
    const lngValue = document.getElementById('lng-value');

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

    const pointStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 7,
            fill: new ol.style.Fill({
                color: '#2ea043'
            }),
            stroke: new ol.style.Stroke({
                color: '#ffffff',
                width: 2
            })
        })
    });
    const selectedPointStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 9,
            fill: new ol.style.Fill({
                color: '#58a6ff'
            }),
            stroke: new ol.style.Stroke({
                color: '#ffffff',
                width: 2
            })
        })
    });
    const markerStyle = new ol.style.Style({
        image: new ol.style.Circle({
            radius: 7,
            fill: new ol.style.Fill({
                color: '#f78166'
            }),
            stroke: new ol.style.Stroke({
                color: '#ffffff',
                width: 2
            })
        })
    });

    const markerSource = new ol.source.Vector();
    const pointsSource = new ol.source.Vector();
    const markerLayer = new ol.layer.Vector({
        source: markerSource,
        style: markerStyle
    });
    const pointsLayer = new ol.layer.Vector({
        source: pointsSource,
        style: pointStyle
    });

    map.addLayer(pointsLayer);
    map.addLayer(markerLayer);

    let markerFeature = null;
    let activePointFeature = null;

    function renderComments(comments) {
        if (!commentsList) {
            return;
        }

        commentsList.innerHTML = '';

        if (!comments.length) {
            const emptyComment = document.createElement('div');
            emptyComment.className = 'point-comment-empty';
            emptyComment.textContent = 'Комментариев пока нет';
            commentsList.appendChild(emptyComment);
            return;
        }

        comments.forEach(function (comment) {
            const item = document.createElement('div');
            item.className = 'point-comment-item';

            const title = document.createElement('div');
            title.className = 'point-comment-title';
            title.textContent = comment.title;

            const text = document.createElement('div');
            text.className = 'point-comment-text';
            text.textContent = comment.text;

            const author = document.createElement('div');
            author.className = 'point-comment-author';
            author.textContent = 'Автор: ' + (comment.username || ('ID ' + comment.user_id));

            item.appendChild(title);
            item.appendChild(text);
            item.appendChild(author);
            commentsList.appendChild(item);
        });
    }

    function hideAllPointPanels() {
        if (emptyState) {
            emptyState.classList.add('point-details-hidden');
        }

        if (detailsCard) {
            detailsCard.classList.add('point-details-hidden');
        }

        if (newPointCard) {
            newPointCard.classList.add('point-details-hidden');
        }
    }

    function showEmptyState() {
        hideAllPointPanels();

        if (emptyState) {
            emptyState.classList.remove('point-details-hidden');
        }
    }

    function resetNewPointForm() {
        if (newPointCommentTitle) {
            newPointCommentTitle.value = '';
        }

        if (newPointCommentText) {
            newPointCommentText.value = '';
        }
    }

    function resetPointSelection() {
        if (activePointFeature) {
            activePointFeature.setStyle(pointStyle);
            activePointFeature = null;
        }

        if (commentPointId) {
            commentPointId.value = '';
        }
    }

    function renderPointDetails(pointData, feature) {
        resetNewPointForm();
        resetPointSelection();

        activePointFeature = feature;
        activePointFeature.setStyle(selectedPointStyle);

        hideAllPointPanels();

        if (detailsCard) {
            detailsCard.classList.remove('point-details-hidden');
        }

        if (photoBlock) {
            if (pointData.photo) {
                photoBlock.classList.remove('point-details-photo-empty');
                photoBlock.innerHTML = '<img src="' + pointData.photo + '" alt="Фото точки">';
            } else {
                photoBlock.classList.add('point-details-photo-empty');
                photoBlock.textContent = 'Фото отсутствует';
            }
        }

        if (pointIdValue) {
            pointIdValue.textContent = pointData.id;
        }

        if (usernameValue) {
            usernameValue.textContent = pointData.username || '—';
        }

        if (userIdValue) {
            userIdValue.textContent = pointData.user_id || '—';
        }

        if (statusValue) {
            statusValue.textContent = pointData.status || 'Нет статуса';
        }

        if (dateValue) {
            dateValue.textContent = pointData.date || 'Нет даты';
        }

        if (coordinatesValue) {
            coordinatesValue.textContent = pointData.x + ', ' + pointData.y;
        }

        if (commentPointId) {
            commentPointId.value = pointData.id;
        }

        renderComments(pointData.comments || []);
    }

    function renderNewPointForm() {
        resetPointSelection();
        resetNewPointForm();
        hideAllPointPanels();

        if (newPointCard) {
            newPointCard.classList.remove('point-details-hidden');
        }
    }

    mapPointsData.forEach(function (pointData) {
        const feature = new ol.Feature({
            geometry: new ol.geom.Point(ol.proj.fromLonLat([Number(pointData.y), Number(pointData.x)]))
        });

        feature.set('pointData', pointData);
        pointsSource.addFeature(feature);

        if (selectedPointId && Number(pointData.id) === selectedPointId) {
            renderPointDetails(pointData, feature);
        }
    });

    if (!selectedPointId) {
        showEmptyState();
    }

    map.on('click', function (event) {
        const pointFeature = map.forEachFeatureAtPixel(event.pixel, function (feature, layer) {
            if (layer === pointsLayer) {
                return feature;
            }

            return null;
        });

        if (pointFeature) {
            if (markerFeature) {
                markerSource.removeFeature(markerFeature);
                markerFeature = null;
            }

            xInput.value = '';
            yInput.value = '';
            latValue.textContent = '—';
            lngValue.textContent = '—';
            renderPointDetails(pointFeature.get('pointData'), pointFeature);
            return;
        }

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

        if (newPointCommentTitle) {
            newPointCommentTitle.focus();
        }

        renderNewPointForm();
    });

    if (pointForm) {
        pointForm.addEventListener('submit', function (event) {
            const title = newPointCommentTitle ? newPointCommentTitle.value.trim() : '';
            const text = newPointCommentText ? newPointCommentText.value.trim() : '';

            if (!xInput.value || !yInput.value) {
                event.preventDefault();
                alert('Сначала выберите точку на карте');
                return;
            }

            if (title === '' || text === '') {
                event.preventDefault();
                alert('Заполните заголовок и текст комментария');
            }
        });
    }
});
</script>
</body>
</html>
