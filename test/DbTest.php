<?php
namespace KuaiappTest\Db;

use Swoole\Coroutine as co;
use Kuaiapp\Db\Pdo\PDO as PDO;
use KuaiappTest\Db\AbstractTestCase;

class DbTest extends AbstractTestCase
{
    public function test__PDO_prepare_and_PDOStatement_fetchAll()
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

    public function test__PDO_query()
    {
        co::create(
            function () {
                $db = new PDO('mysql:host='.getenv('DB_HOST').';dbname='.getenv('DB_NAME').'', getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
                foreach ($db->query('SELECT 1') as $row) {
                    print_r($row);
                }
            }
        );
    }
}