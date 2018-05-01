# Kuaiapp/DB

Swoole\Coroutine\MySQL 的 PDO接口实现, 还未完成

## Inspire

世界上有太多太多的框架，每款框架都有自己特定的DB/ORM查询器. 而且他们绝大部分都不兼容。  
如果这些框架我们都要为他们单独集成一次 Swoole\Coroutine\MySQL 将是一个巨大的工作量。  

- 于是我就产生了再 `Swoole\Coroutine\MySQL` 基础上实现 `PDO` 的接口的想法.  
  其他框架的DB/ORM查询器都是兼容 `PDO` 的  
  只要此项目完成 其他框架的DB查询全部可以分分钟移植到 `Swoole` 的异步MYSQL客户端中

## Progress

- [x] PDO::__construct($dsn, $username = null, $passwd = null, $options = [])
- [x] PDO::prepare($statement, array $driver_options = array())
- [x] PDO::beginTransaction()
- [x] PDO::commit()
- [x] PDO::rollBack()
- [x] PDO::inTransaction()
- [ ] PDO::setAttribute($attribute, $value)
- [x] PDO::exec($statement)
- [ ] PDO::query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = array())
- [x] PDO::lastInsertId($name = null)
- [x] PDO::errorCode() 待校准
- [x] PDO::errorInfo() 待校准
- [ ] PDO::getAttribute($attribute)
- [ ] PDO::quote($string, $parameter_type = PDO::PARAM_STR)
- [ ] PDO::__wakeup()
- [ ] PDO::__sleep()
- [ ] PDOStatement::current()
- [ ] PDOStatement::key()
- [ ] PDOStatement::next()
- [ ] PDOStatement::rewind()
- [ ] PDOStatement::valid()
- [x] PDOStatement::execute($input_parameters = null)
- [ ] PDOStatement::fetch($fetch_style = null, $cursor_orientation = PDO::FETCH_ORI_NEXT, $cursor_offset = 0)
- [ ] PDOStatement::bindParam($parameter, &$variable, $data_type = PDO::PARAM_STR, $length = null, $driver_options = null)
- [ ] PDOStatement::bindColumn($column, &$param, $type = null, $maxlen = null, $driverdata = null)
- [ ] PDOStatement::bindValue($parameter, $value, $data_type = PDO::PARAM_STR)
- [ ] PDOStatement::rowCount()
- [ ] PDOStatement::fetchColumn($column_number = 0)
- [x] PDOStatement::fetchAll($fetch_style = null, $fetch_argument = null, array $ctor_args = array())
- [ ] PDOStatement::fetchObject($class_name = "stdClass", array $ctor_args = array())
- [ ] PDOStatement::errorCode()
- [ ] PDOStatement::errorInfo()
- [ ] PDOStatement::setAttribute($attribute, $value)
- [ ] PDOStatement::getAttribute($attribute)
- [ ] PDOStatement::columnCount()
- [ ] PDOStatement::getColumnMeta($column)
- [ ] PDOStatement::setFetchMode($mode, $classNameObject, array $ctorarfg)
- [ ] PDOStatement::nextRowset()
- [ ] PDOStatement::closeCursor()
- [ ] PDOStatement::debugDumpParams()
- [ ] PDOException::__construct()


## Install

Install by [Composer](https://getcomposer.org)  

```bash
composer require kuaiapp/db
```

## Require

- Swoole > 2.1.2, enable coroutine, enable mysqld

## Known Issue

- You can only use `Kuaiapp\Db\Pdo\PDO*` classes in SWOOLE `request` `receive` ... event or [`Coroutine`](https://github.com/swoole/swoole-src#coroutine) because of [swoole's limits](https://github.com/swoole/swoole-src/blob/37d1067687cff0b67fed978f9397ff72d76f6612/swoole_coroutine.c#L204).

## Credit

- [Swoole](https://github.com/swoole/swoole-src)

## LICENSE

Kuaiapp Database Component is open-sourced software licensed under the [MIT license](LICENSE).
