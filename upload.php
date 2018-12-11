<?php
use Google\Cloud\Datastore\DatastoreClient;
// https://googleapis.github.io/google-cloud-php/#/docs/google-cloud/v0.89.0/datastore/datastoreclient

$projectId = getenv('GOOGLE_CLOUD_PROJECT');
$datastore = new DatastoreClient([
	'projectId' => $projectId
]);
$query = $datastore->query()->kind('data');
$datastore_results = $datastore->runQuery($query);

foreach ($datastore_results as $entity) {
	//$entity['key']
	//$entity['value']
	var_dump($entity);
}


header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$data = json_decode(file_get_contents("php://input"));

$deviceName = $data->name;

$filtered5Min = $data->client_count->filtered_num_last_5_mins;
$filteredHour = $data->client_count->filtered_num_last_5_mins;
$all5Min = $data->client_count->num_clients_last_5_mins;
$allHour = $data->client_count->num_clients_last_hour;

$accessPoints = $data->ap;
$devices = $data->devices;

foreach ($accessPoints as $ap) {
	var_dump($ap->macAddress);
}

echo 'Hello world';

?>