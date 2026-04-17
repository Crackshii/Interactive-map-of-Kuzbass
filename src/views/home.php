<div class="app-container">
    <!-- Левый сайдбар -->
    <aside class="sidebar sidebar-left">
        <h3>Фильтры</h3>
        <p>Здесь будут фильтры</p>
    </aside>

    <!-- Центр (карта) --> 
   <div class="map-wrapper">
    <div id="map"></div>

    <div class="map-panel">
        <div>Широта: <span id="lat-value">—</span></div>
        <div>Долгота: <span id="lng-value">—</span></div>

        <form method="post" action="?page=points/store" id="point-form">
            <input type="hidden" name="x" id="point-x">
            <input type="hidden" name="y" id="point-y">
            <button type="submit">Добавить точку</button>
        </form>
    </div>
</div>

    <!-- Правый сайдбар -->
    <aside class="sidebar sidebar-right">
        <h3>Список точек</h3>
        <p>Здесь будет список точек</p>
    </aside>
</div>