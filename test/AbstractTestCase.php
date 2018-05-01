<?php
namespace KuaiappTest\Db;

use PHPUnit\Framework\TestCase;
use Dotenv\Dotenv;

class AbstractTestCase extends TestCase
{
    public function __construct()
    {
        $dotenv = new Dotenv(__DIR__.'/../');
        $dotenv->load();
    }
}