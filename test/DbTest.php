<?php
namespace KuaiappTest\Db;

use Swoole\Coroutine as co;
use Kuaiapp\Db\Pdo\PDO as PDO;

class DbTest extends AbstractTestCase
{
    public function testDb()
    {
        co::create(
            function () {
                $db = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME').'', getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
                $stmt = $db->prepare('SELECT 1');
                $stmt->execute([]);
                $result = $stmt->fetchAll();
                $this->assertEquals(1, $result[0][1]);
            }
        );
    }
}