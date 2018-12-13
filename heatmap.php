<?php
/**
 * Created by PhpStorm.
 * User: raoul
 * Date: 12/11/2018
 * Time: 8:26 PM
 */

require 'DataBase.php';

//$dataTypes = ['all5Min', 'allHour', 'filtered5Min', 'filteredHour'];
$dataTypes = ['filtered5Min', 'filteredHour'];

$data = DataBase::get('DevicesInArea');
$entries = array();
foreach ($data as $d){
    if($d['lat'] == null || $d['long'] == null){
        continue;
    }
    $entry = new stdClass();
    $entry->deviceName = $d['deviceName'];
    $entry->all5Min = $d['all5Min'];
    $entry->allHour = $d['allHour'];
    $entry->filtered5Min = $d['filtered5Min'];
    $entry->filteredHour = $d['filteredHour'];
    $entry->lat = $d['lat'];
    $entry->long = $d['long'];
    $entry->datetime = $d['datetime'];
    $entries[] = $entry;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Heatmaps</title>
    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #map {
            height: 100%;
        }
        /* Optional: Makes the sample page fill the window. */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #floating-panel {
            position: absolute;
            top: 10px;
            left: 25%;
            z-index: 5;
            background-color: #fff;
            padding: 5px;
            border: 1px solid #999;
            text-align: center;
            font-family: 'Roboto','sans-serif';
            line-height: 30px;
            padding-left: 10px;
        }
        #floating-panel {
            background-color: #fff;
            border: 1px solid #999;
            left: 25%;
            padding: 5px;
            position: absolute;
            top: 10px;
            z-index: 5;
        }
    </style>
</head>

<body>
<div id="floating-panel">
    <button onclick="toggleHeatmap()">Toggle Heatmap</button>
    <button onclick="changeGradient()">Change gradient</button>
    <button onclick="changeRadius()">Change radius</button>
    <button onclick="changeOpacity()">Change opacity</button>
    <select id="typeSelect" onchange="changeType()">
<!--        <option value="all5Min" selected>all5Min</option>-->
<!--        <option value="allHour">allHour</option>-->
        <option value="filtered5Min" selected>filtered5Min</option>
        <option value="filteredHour">filteredHour</option>
    </select>

</div>
<div id="map"></div>
<script>
    let map;
    let heatmaps = {};
    let currentType = 'filtered5Min';

    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 13,
            center: {lat: 52.0835029, lng: 5.1675215},
            mapTypeId: 'satellite'
        });

        <?php foreach ($dataTypes as $type){
            $dataStr = '';
            foreach ($entries as $entity){
                for ($i = 0; $i < $entity->$type; $i++) {
                    $dataStr = $dataStr . 'new google.maps.LatLng('.$entity->lat.', '.$entity->long.'),';
                }
            }
            echo 'heatmaps["'.$type.'"] = new google.maps.visualization.HeatmapLayer({
                data: ['.$dataStr.'],
                map: map
            });';
        }
        ?>
        for (let heatmap in heatmaps) {
            if(currentType !== heatmap){
                heatmaps[heatmap].setMap(null);
            }
        }
    }

    function toggleHeatmap() {
        heatmaps[currentType].setMap(heatmaps[currentType].getMap() ? null : map);
    }

    function changeGradient() {
        let gradient = [
            'rgba(0, 255, 255, 0)',
            'rgba(0, 255, 255, 1)',
            'rgba(0, 191, 255, 1)',
            'rgba(0, 127, 255, 1)',
            'rgba(0, 63, 255, 1)',
            'rgba(0, 0, 255, 1)',
            'rgba(0, 0, 223, 1)',
            'rgba(0, 0, 191, 1)',
            'rgba(0, 0, 159, 1)',
            'rgba(0, 0, 127, 1)',
            'rgba(63, 0, 91, 1)',
            'rgba(127, 0, 63, 1)',
            'rgba(191, 0, 31, 1)',
            'rgba(255, 0, 0, 1)'
        ];
        heatmaps[currentType].set('gradient', heatmaps[currentType].get('gradient') ? null : gradient);
    }

    function changeRadius() {
        heatmaps[currentType].set('radius', heatmaps[currentType].get('radius') ? null : 20);
    }

    function changeOpacity() {
        heatmaps[currentType].set('opacity', heatmaps[currentType].get('opacity') ? null : 0.2);
    }

    function changeType() {
        heatmaps[currentType].setMap(null);
        currentType = document.getElementById("typeSelect").value;
        heatmaps[currentType].setMap(map);
    }
</script>
<script async defer src="<?php echo 'https://maps.googleapis.com/maps/api/js?key='.GOOGLE_API_KEY.'&libraries=visualization&callback=initMap'?>">
</script>
</body>
</html>
