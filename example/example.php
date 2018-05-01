<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Kuaiapp\Db\Pdo\PDO;

define('HOST',      '127.0.0.1');
define('PORT',      '9503');
define('REQUEST',   'request');

$http = new swoole_http_server(HOST, PORT);
$http->on(
    REQUEST,
    function ($request, $response) {
        $db = new PDO('mysql:host=127.0.0.1;dbname=crazygame', 'root', '');
        $stmt = $db->prepare('SELECT * FROM ask_users WHERE id=?');
        $result = $stmt->execute([1]);
        $response->end(json_encode($result));
    }
);
$http->start();
