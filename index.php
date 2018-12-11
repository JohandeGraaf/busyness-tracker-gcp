<?php
require 'vendor/autoload.php';

use Google\Cloud\Datastore\DatastoreClient;

$projectId = getenv('GOOGLE_CLOUD_PROJECT');
$datastore = new DatastoreClient([
    'projectId' => $projectId
]);
$query = $datastore->query()->kind('constants');
$datastore_results = $datastore->runQuery($query);
foreach ($datastore_results as $entity) {
    define($entity['key'], $entity['value']);
}

switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':
        require 'homepage.php';
        break;
    case '/upload.php':
        require 'upload.php';
        break;
    case '/heatmap':
        require 'heatmap.php';
        break;
    default:
        http_response_code(404);
        exit('Not Found');
}
?>
