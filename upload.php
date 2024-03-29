<?php

include_once 'DataBase.php';

header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


$data = json_decode(file_get_contents("php://input"));

$dataObj = new stdClass();
$dataObj->deviceName = $data->name;
$dataObj->filtered5Min = $data->client_count->filtered_num_last_5_mins;
$dataObj->filteredHour = $data->client_count->filtered_num_last_5_mins;
$dataObj->all5Min = $data->client_count->num_clients_last_5_mins;
$dataObj->allHour = $data->client_count->num_clients_last_hour;

$curl = curl_init();
curl_setopt($curl, CURLOPT_POST, 1);
curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode(array('wifiAccessPoints' => $data->ap)));
curl_setopt($curl, CURLOPT_URL, 'https://www.googleapis.com/geolocation/v1/geolocate?key=' . GOOGLE_API_KEY);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
$result = curl_exec($curl);
curl_close($curl);
$resultObj = json_decode($result);

$dataObj->lat = $resultObj->location->lat;
$dataObj->long = $resultObj->location->lng;
$dataObj->accuracy = $resultObj->accuracy;

$date = new DateTime();
$dataObj->datetime = $date->format('Y-m-d H:i:s');

DataBase::insert('DevicesInArea', $dataObj);



//DataBase::batchInsert('AccessPoints', $data->ap);
//DataBase::batchInsert('Devices', $data->devices);

http_response_code(202);

?>