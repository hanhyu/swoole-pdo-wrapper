<?php
namespace Kuaiapp\Db\Pdo;

use RuntimeException;
/**
 * Represents an error raised by PDO. You should not throw a <b>PDOException</b> from your own code.
 * 
 * @link http://php.net/manual/en/class.pdoexception.php
 * @see  http://php.net/manual/en/language.exceptions.php Exceptions in PHP
 */
class PDOException extends RuntimeException
{
    public $errorInfo;
}
