<?php
/**
 * Test script
 *
 * @category Pdo
 * @package  Pdo
 * @author   xiaohui.lam <xiaohui.lam@icloud.com>
 * @license  MIT https://github.com/Kuaiapp/swoole-pdo-wrapper/blob/master/LICENSE
 * @link     https://github.com/Kuaiapp/swoole-pdo-wrapper/blob/master/src/Pdo/PDO.php
 */

namespace Kuaiapp\Db\Pdo;

use Swoole\Coroutine as co;
use PDO as NativePDO;

/**
 * 创建一个表示与数据库的连接的PDO实例
 *
 * @category Pdo
 * @package  Pdo
 * @author   xiaohui.lam <xiaohui.lam@icloud.com>
 * @license  MIT https://github.com/Kuaiapp/swoole-pdo-wrapper/blob/master/LICENSE
 * @link     https://github.com/Kuaiapp/swoole-pdo-wrapper/blob/master/src/Pdo/PDO.php
 */
class PDO extends NativePDO
{
    /**
     * 是否在事务内
     *
     * @var boolean
     */
    protected $in_transaction = false;

    /**
     * 协程MySQL客户端
     *
     * @var \Swoole\Coroutine\MySQL
     */
    public $connection = null;

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * 创建一个表示与数据库的连接的PDO实例
     * 
     * @link  http://php.net/manual/en/pdo.construct.php
     * @param string $dsn      连接字符 必传
     * @param string $username 用户名   可选
     * @param string $passwd   密码     可选
     * @param array  $options  选项     可选
     */
    public function __construct($dsn, $username = null, $passwd = null, $options = [])
    {
        $dsn = str_replace(';', '&', array_reverse(explode(':', $dsn))[0]);
        parse_str($dsn, $conf);
        if (!isset($conf['port']) || !$conf['port']) $conf['port'] = 3306;

        $this->connection = new co\MySQL();
        $this->connection->connect(
            [
                'host'     => $conf['host'],
                'port'     => $conf['port'],
                'user'     => $username,
                'password' => $passwd,
                'database' => $conf['dbname'],
            ]
        );
    }

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * 准备执行语句并返回一个语句对象
     * 
     * @link  http://php.net/manual/en/pdo.prepare.php
     * @param string $statement      查询预计SQL, 暂不支持:id类占位符, 只支持?占位符
     * @param array  $options        选传 该数组包含一个或多个 key=>value 对，以便为此方法返回的 PDOStatement 对象设置属性值. 您最常用的方法是将PDO::ATTR_CURSOR值设置为PDO::CURSOR_SCROLL以请求可滚动的游标. 某些驱动程序具有可能在准备时设置的驱动程序特定选项.
     * 
     * @return PDOStatement|bool 如果数据库服务器成功准备了该语句PDO::prepare 返回 a PDOStatement 对象. 如果数据库服务器无法成功准备语句，PDO::prepare 返回 FALSE 或 emits PDOException (取决于错误处理), 仿真准备语句不与数据库服务器通信，因此 PDO::prepare 不检查语句
     */
    public function prepare($statement, $options = null)
    {
        // TODO 将:placeholder替换成?占位
        $stmt = $this->connection->prepare($statement);
        if ($stmt == false) {
            throw new PDOException($this->connection->error, $this->connection->errno);
        }

        return PDOStatement::capture($stmt, $statement, $this);
    }

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Initiates a transaction
     * <p>
     * Turns off autocommit mode. While autocommit mode is turned off,
     * changes made to the database via the PDO object instance are not committed
     * until you end the transaction by calling {@link PDO::commit()}.
     * Calling {@link PDO::rollBack()} will roll back all changes to the database and
     * return the connection to autocommit mode.
     * </p>
     * <p>
     * Some databases, including MySQL, automatically issue an implicit COMMIT
     * when a database definition language (DDL) statement
     * such as DROP TABLE or CREATE TABLE is issued within a transaction.
     * The implicit COMMIT will prevent you from rolling back any other changes
     * within the transaction boundary.
     * </p>
     * @link http://php.net/manual/en/pdo.begintransaction.php
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     * @throws PDOException If there is already a transaction started or
     * the driver does not support transactions <br/>
     * <b>Note</b>: An exception is raised even when the <b>PDO::ATTR_ERRMODE</b>
     * attribute is not <b>PDO::ERRMODE_EXCEPTION</b>.
     */
    public function beginTransaction()
    {
        $this->exec('BEGIN');
        $this->in_transaction = true;
    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Commits a transaction
     * @link http://php.net/manual/en/pdo.commit.php
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function commit()
    {
        $this->exec('COMMIT');
        $this->in_transaction = false;

    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Rolls back a transaction
     * 
     * @link   http://php.net/manual/en/pdo.rollback.php
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function rollBack()
    {
        $this->exec('ROLLBACK');
        $this->in_transaction = false;

    }
    /**
     * (PHP 5 &gt;= 5.3.3, Bundled pdo_pgsql, PHP 7)<br/>
     * Checks if inside a transaction
     * 
     * @link   http://php.net/manual/en/pdo.intransaction.php
     * @return bool <b>TRUE</b> if a transaction is currently active, and <b>FALSE</b> if not.
     */
    public function inTransaction()
    {
        return !!$this->in_transaction;
    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Set an attribute
     * @link http://php.net/manual/en/pdo.setattribute.php
     * @param int $attribute
     * @param mixed $value
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    public function setAttribute($attribute, $value)
    {
    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Execute an SQL statement and return the number of affected rows
     * @link http://php.net/manual/en/pdo.exec.php
     * @param string $statement <p>
     * The SQL statement to prepare and execute.
     * </p>
     * <p>
     * Data inside the query should be properly escaped.
     * </p>
     * @return int <b>PDO::exec</b> returns the number of rows that were modified
     * or deleted by the SQL statement you issued. If no rows were affected,
     * <b>PDO::exec</b> returns 0.
     * </p>
     * This function may
     * return Boolean <b>FALSE</b>, but may also return a non-Boolean value which
     * evaluates to <b>FALSE</b>. Please read the section on Booleans for more
     * information. Use the ===
     * operator for testing the return value of this
     * function.
     * <p>
     * The following example incorrectly relies on the return value of
     * <b>PDO::exec</b>, wherein a statement that affected 0 rows
     * results in a call to <b>die</b>:
     * <code>
     * $db->exec() or die(print_r($db->errorInfo(), true));
     * </code>
     */
    public function exec($statement)
    {
        $this->connection->query($statement);
        return $this->connection->affected_rows;
    }

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.2.0)<br/>
     * Executes an SQL statement, returning a result set as a PDOStatement object
     * @link http://php.net/manual/en/pdo.query.php
     * @param string $statement <p>
     * The SQL statement to prepare and execute.
     * </p>
     * <p>
     * Data inside the query should be properly escaped.
     * </p>
     * @param int $mode <p>
     * The fetch mode must be one of the PDO::FETCH_* constants.
     * </p>
     * @param mixed $arg3 <p>
     * The second and following parameters are the same as the parameters for PDOStatement::setFetchMode.
     * </p>
     * @param array $ctorargs [optional] <p>
     * Arguments of custom class constructor when the <i>mode</i>
     * parameter is set to <b>PDO::FETCH_CLASS</b>.
     * </p>
     * @return PDOStatement|bool <b>PDO::query</b> returns a PDOStatement object, or <b>FALSE</b>
     * on failure.
     * @see PDOStatement::setFetchMode For a full description of the second and following parameters.
     */
    public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
    {
        // @todo 功能未實現
        $stmt = $this->connection->prepare($statement);
        if ($stmt == false) {
            throw new PDOException($this->connection->error, $this->connection->errno);
        }

        return PDOStatement::capture($stmt, $statement, $this);
    }

    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Returns the ID of the last inserted row or sequence value
     * @link http://php.net/manual/en/pdo.lastinsertid.php
     * @param string $name [optional] <p>
     * Name of the sequence object from which the ID should be returned.
     * </p>
     * @return string If a sequence name was not specified for the <i>name</i>
     * parameter, <b>PDO::lastInsertId</b> returns a
     * string representing the row ID of the last row that was inserted into
     * the database.
     * </p>
     * <p>
     * If a sequence name was specified for the <i>name</i>
     * parameter, <b>PDO::lastInsertId</b> returns a
     * string representing the last value retrieved from the specified sequence
     * object.
     * </p>
     * <p>
     * If the PDO driver does not support this capability,
     * <b>PDO::lastInsertId</b> triggers an
     * IM001 SQLSTATE.
     */
    public function lastInsertId($name = null)
    {
        return $this->connection->insert_id;
    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Fetch the SQLSTATE associated with the last operation on the database handle
     * @link http://php.net/manual/en/pdo.errorcode.php
     * @return mixed an SQLSTATE, a five characters alphanumeric identifier defined in
     * the ANSI SQL-92 standard. Briefly, an SQLSTATE consists of a
     * two characters class value followed by a three characters subclass value. A
     * class value of 01 indicates a warning and is accompanied by a return code
     * of SQL_SUCCESS_WITH_INFO. Class values other than '01', except for the
     * class 'IM', indicate an error. The class 'IM' is specific to warnings
     * and errors that derive from the implementation of PDO (or perhaps ODBC,
     * if you're using the ODBC driver) itself. The subclass value '000' in any
     * class indicates that there is no subclass for that SQLSTATE.
     * </p>
     * <p>
     * <b>PDO::errorCode</b> only retrieves error codes for operations
     * performed directly on the database handle. If you create a PDOStatement
     * object through <b>PDO::prepare</b> or
     * <b>PDO::query</b> and invoke an error on the statement
     * handle, <b>PDO::errorCode</b> will not reflect that error.
     * You must call <b>PDOStatement::errorCode</b> to return the error
     * code for an operation performed on a particular statement handle.
     * </p>
     * <p>
     * Returns <b>NULL</b> if no operation has been run on the database handle.
     */
    public function errorCode()
    {
        return $this->connection->errno;
    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.1.0)<br/>
     * Fetch extended error information associated with the last operation on the database handle
     * @link http://php.net/manual/en/pdo.errorinfo.php
     * @return array <b>PDO::errorInfo</b> returns an array of error information
     * about the last operation performed by this database handle. The array
     * consists of the following fields:
     * <tr valign="top">
     * <td>Element</td>
     * <td>Information</td>
     * </tr>
     * <tr valign="top">
     * <td>0</td>
     * <td>SQLSTATE error code (a five characters alphanumeric identifier defined
     * in the ANSI SQL standard).</td>
     * </tr>
     * <tr valign="top">
     * <td>1</td>
     * <td>Driver-specific error code.</td>
     * </tr>
     * <tr valign="top">
     * <td>2</td>
     * <td>Driver-specific error message.</td>
     * </tr>
     * </p>
     * <p>
     * If the SQLSTATE error code is not set or there is no driver-specific
     * error, the elements following element 0 will be set to <b>NULL</b>.
     * </p>
     * <p>
     * <b>PDO::errorInfo</b> only retrieves error information for
     * operations performed directly on the database handle. If you create a
     * PDOStatement object through <b>PDO::prepare</b> or
     * <b>PDO::query</b> and invoke an error on the statement
     * handle, <b>PDO::errorInfo</b> will not reflect the error
     * from the statement handle. You must call
     * <b>PDOStatement::errorInfo</b> to return the error
     * information for an operation performed on a particular statement handle.
     */
    public function errorInfo()
    {
        return $this->connection->error;
    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.2.0)<br/>
     * Retrieve a database connection attribute
     * @link http://php.net/manual/en/pdo.getattribute.php
     * @param int $attribute <p>
     * One of the PDO::ATTR_* constants. The constants that
     * apply to database connections are as follows:
     * PDO::ATTR_AUTOCOMMIT
     * PDO::ATTR_CASE
     * PDO::ATTR_CLIENT_VERSION
     * PDO::ATTR_CONNECTION_STATUS
     * PDO::ATTR_DRIVER_NAME
     * PDO::ATTR_ERRMODE
     * PDO::ATTR_ORACLE_NULLS
     * PDO::ATTR_PERSISTENT
     * PDO::ATTR_PREFETCH
     * PDO::ATTR_SERVER_INFO
     * PDO::ATTR_SERVER_VERSION
     * PDO::ATTR_TIMEOUT
     * </p>
     * @return mixed A successful call returns the value of the requested PDO attribute.
     * An unsuccessful call returns null.
     */
    public function getAttribute($attribute)
    {
    }
    /**
     * (PHP 5 &gt;= 5.1.0, PHP 7, PECL pdo &gt;= 0.2.1)<br/>
     * Quotes a string for use in a query.
     * @link http://php.net/manual/en/pdo.quote.php
     * @param string $string <p>
     * The string to be quoted.
     * </p>
     * @param int $parameter_type [optional] <p>
     * Provides a data type hint for drivers that have alternate quoting styles.
     * </p>
     * @return string a quoted string that is theoretically safe to pass into an
     * SQL statement. Returns <b>FALSE</b> if the driver does not support quoting in
     * this way.
     */
    public function quote($string, $parameter_type = PDO::PARAM_STR)
    {
    }

    /**
     * (PHP 5 &gt;= 5.1.3, PHP 7, PECL pdo &gt;= 1.0.3)<br/>
     * Return an array of available PDO drivers
     * @link http://php.net/manual/en/pdo.getavailabledrivers.php
     * @return array <b>PDO::getAvailableDrivers</b> returns an array of PDO driver names. If
     * no drivers are available, it returns an empty array.
     */
    public static function getAvailableDrivers()
    {
    }
}
