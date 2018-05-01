<?php
/**
 * Test script
 * 
 * @category Test
 * @package  Test
 * @author   xiaohui.lam <xiaohui.lam@icloud.com>
 * @license  MIT https://github.com/Kuaiapp/swoole-pdo-wrapper/blob/master/LICENSE
 * @link     https://github.com/Kuaiapp/swoole-pdo-wrapper/blob/master/example/example.php
 */
require_once __DIR__ . '/../vendor/autoload.php';

use Kuaiapp\Db\Pdo\PDO;

define('HOST', '127.0.0.1');
define('PORT', '9503');
define('REQUEST', 'request');

$http = new swoole_http_server(HOST, PORT);
$http->on(
    REQUEST,
    function ($request, $response) {
        $db = new PDO('mysql:host=127.0.0.1;dbname=crazygame', 'root', '');
        $db->beginTransaction();
        $stmt = $db->prepare('SELECT * FROM ask_users WHERE id!=?');
        $stmt->execute([1]);
        $result = $stmt->fetchAll();
        $response->end(json_encode($result));
        $db->commit();
    }
);
$http->start();
