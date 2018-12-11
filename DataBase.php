<?php
/**
 * Created by PhpStorm.
 * User: raoul
 * Date: 12/11/2018
 * Time: 4:32 PM
 */

use Google\Cloud\Datastore\DatastoreClient;

class DataBase {
    private static $datastore;

    private static function instance() {
        if (!self::$datastore){
            $projectId = getenv('GOOGLE_CLOUD_PROJECT');
            self::$datastore = new DatastoreClient([
                'projectId' => $projectId
            ]);
        }
        return self::$datastore;
    }

    public static function insert($key, $obj){
        $entity = self::instance()->entity($key, (array)$obj);
        self::instance()->insert($entity);
    }

    public static function batchInsert($key, $objects){
        $entities = array();
        foreach ($objects as $obj){
            $entities[] = self::instance()->entity($key, (array)$obj);
        }
        self::instance()->insertBatch($entities);
    }
}