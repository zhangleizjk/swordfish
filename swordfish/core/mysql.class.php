<?php
// declare(strict_types = 1);
namespace Swordfish;

use PDO;
use PDOException;

class Mysql {
	/**
	 */
	const operate_read = 'read';
	const operate_write = 'write';
	const operate_both = 'both';
	protected $id;
	protected $sql;
	protected $error;
	protected $reader;
	protected $writer;
	protected $ds;
	protected $connectors = array();
	protected $errConnectors = array();
	protected $driverOptions = array(
			PDO::ATTR_CASE => PDO::CASE_LOWER, 
			PDO::ATTR_ERRMODE => PDO::ERRMODE_SILENT, 
			PDO::ATTR_ORACLE_NULLS => PDO::NULL_NATURAL, 
			PDO::ATTR_STRINGIFY_FETCHES => false);
	protected $sqlCmds = array('distinct', 'field', 'table', 'join', 'where', 'group', 'having', 'order', 'limit');
	protected $sqlDatas = array();
	
	/**
	 * void public function __construct(array $connectors)
	 */
	public function __construct(array $connectors) {
		foreach($connectors as $connector){
			if(!is_array($connector)) continue;
			elseif(is_database_connect_params($connector)) $this->connectors[] = $connector;
		}
	}
	
	/**
	 * void public function __destruct(void)
	 */
	public function __destruct() {
		$this->close();
	}
	
	/**
	 * ?string public function __get(string $cmd)
	 */
	public function __get(string $cmd): string {
		return $this->sqlDatas[$cmd] ?? null;
	}
	
	/**
	 * boolean public function data(string $cmd, string $data)
	 */
	public function data(string $cmd, string $data): bool {
		if(!in_array($cmd, $this->sqlCmds, true)) return false;
		$this->sqlDatas[$cmd] = $data;
		return true;
	}
	
	/**
	 * boolean public function clear(?string $cmd = null)
	 */
	public function clear(string $cmd = null): bool {
		if(is_null($cmd)) $this->sqlDatas = array();
		elseif(!in_array($cmd, $this->sqlCmds, true)) return false;
		else unset($this->sqlDatas[$cmd]);
		return true;
	}
	
	/**
	 * integer public function cmd(string $sql)
	 */
	public function cmd(string $sql): int {
		$this->sql = $sql;
		$this->error = null;
		if($this->link(self::operate_write)){
			$this->ds = $this->writer->prepare($this->sql);
			if($this->ds){
				if($this->ds->execute()) return $this->ds->rowCount();
				else $this->error = implode(':', $this->ds->errorInfo());
			}else
				$this->error = implode(':', $this->writer->errorInfo());
		}
		return -1;
	}
	
	/**
	 * array public function query(string $sql)
	 */
	public function query(string $sql): array {
		$this->sql = $sql;
		$this->error = null;
		if($this->link(self::operate_read)){
			$this->ds = $this->reader->prepare($this->sql);
			if($this->ds){
				if($this->ds->execute()) return $this->ds->fetchAll(PDO::FETCH_ASSOC);
				else $this->error = implode(':', $this->ds->errorInfo());
			}else
				$this->error = implode(':', $this->reader->errorInfo());
		}
		return array();
	}
	
	/**
	 * array public function select(void)
	 */
	public function select(): array {
		$sqls = array('select', $this->distinct, $this->field, 'from', $this->table, $this->join, $this->where, $this->group, $this->having, $this->order, $this->limit);
		$sql = implode(' ', array_filter($sqls, $this->isNoEmpty));
		return $this->query($sql);
	}
	
	/**
	 * integer public function insert(array $datas)
	 * @$datas = array(string $field => ?scalar $data,...)
	 */
	public function insert(array $datas): int {
		$datas = $this->filter($datas);
		$keyStr = implode(',', array_keys($datas));
		$valueStr = implode(',', array_values($datas));
		$sqls = array('insert into', $this->table . '(' . $keyStr . ')', 'values(' . $valueStr . ')');
		$sql = implode(' ', array_filter($sqls, $this->isNoEmpty));
		return $this->cmd($sql);
	}
	
	/**
	 * integer public function update(array $datas)
	 * @$datas = array(string $field => ?scalar $data,...)
	 */
	public function update(array $datas): int {
		$datas = $this->filter($datas);
		array_walk($datas, $this->change);
		$dataStr = implode(',', $datas);
		$sqls = array('update', $this->table, 'set', $dataStr, $this->where, $this->order, $this->limit);
		$sql = implode(' ', array_filter($sqls, $this->isNoEmpty));
		return $this->cmd($sql);
	}
	
	/**
	 * integer public function delete(void)
	 */
	public function delete(): int {
		$sqls = array('delete from', $this->table, $this->where, $this->order, $this->limit);
		$sql = implode(' ', array_filter($sqls, $this->isNoEmpty));
		return $this->cmd($sql);
	}
	
	/**
	 * boolean public function begin(void)
	 */
	public function begin(): bool {
		if(!$this->link(self::operate_write)) return false;
		elseif($this->writer->inTransaction()) return false;
		return $this->writer->beginTransaction();
	}
	
	/**
	 * boolean public function end(void)
	 */
	public function end(): bool {
		if(!$this->link(self::operate_write)) return false;
		elseif(!$this->writer->inTransaction()) return false;
		return $this->writer->commit();
	}
	
	/**
	 * boolean public function rollback(void)
	 */
	public function rollback(): bool {
		if(!$this->link(self::operate_write)) return false;
		elseif(!$this->writer->inTransaction()) return false;
		return $this->writer->rollBack();
	}
	
	/**
	 * ?string public function error(void)
	 */
	public function error(): string {
		return $this->error;
	}
	
	/**
	 * ?string public function sql(void)
	 */
	public function sql(): string {
		return $this->sql;
	}
	
	/**
	 * ?integer public function id(void)
	 */
	public function id(): int {
		return $this->id;
	}
	
	/**
	 * array public function fields(string $memory)
	 */
	public function fields(string $memory): array {
		if(!is_database_named_regular($memory)) return array();
		$sql = 'describe ' . database_backquote($memory);
		$datas = $this->query($sql);
		foreach($datas as $data){
			// list('field'=>$field, 'type'=>$type) = array_map('strtolower', $data);
			$pattern = '/^[a-z]+/';
			preg_match($pattern, $type, $matches);
			$type = $matches[0];
			$ends[$field] = $this->map($type);
		}
		return $ends ?? array();
	}
	
	/**
	 * string protected function map(string $type)
	 */
	public function map(string $type): string {
		$maps = array('integer' => array('tinyint', 'smallint', 'mediumint', 'int', 'bigint', 'bit'), 'float' => array('decimal', 'float', 'double'), 'string' => array('char', 'varchar', 'binary', 'varbinary', 'tinytext', 'text', 'mediumtext', 'longtext', 'tinyblob', 'blob', 'mediumblob', 'longblob', 'date', 'datetime', 'timestamp', 'time', 'year', 'enum', 'set'), 'boolean' => array('tinyint'));
		foreach($maps as $key => $map){
			if(in_array($type, $map, true)) return $key;
		}
		return 'null';
	}
	
	/**
	 * boolean protected function link(string $operate = operate_read|operate_write)
	 */
	protected function link(string $operate): bool {
		$maps = array(self::operate_read => 'reader', self::operate_write => 'writer');
		if(!in_array($operate, array(self::operate_read, self::operate_write), true)) return false;
		elseif($this->{$maps[$operate]}) return true;
		$params = $this->connector($operate);
		if($params) list($key, $dsn, $username, $password) = $params;
		else return false;
		try{
			$this->{$maps[$operate]} = new PDO($dsn, $username, $password, $this->driverOptions);
			$this->errConnectors = array();
			return true;
		}catch(PDOException $err){
			$this->errConnectors[] = $key;
			return $this->link($operate);
		}
	}
	
	/**
	 * array protected function connector(string $operate = operate_read|operate_write)
	 */
	protected function connector(string $operate): array {
		if(!in_array($operate, array(self::operate_read, self::operate_write), true)) return array();
		$connectors = array();
		foreach($this->connectors as $key => $connector){
			$yes = in_array($connector['operate'], array($operate, self::operate_both), true);
			$err = in_array($key, $this->errConnectors, true);
			if($yes && !$err) $connectors[$key] = $connector;
		}
		if($connectors){
			$key = array_rand($connectors);
			$params = $connectors[$key];
			// list('type'=>$type, 'host'=>$host, 'port'=>$port, 'dbname'=>$dbname, 'charset'=>$charset)=$params;
			// list('username'=>$username, 'pwssword'=>$password)=$params;
			$dsn = implode(';', array($type . ':host=' . $host, 'port=' . $port, 'dbname=' . $dbname, 'charset=' . $charset));
			return array($key, $dsn, $username, $password);
		}
		return array();
	}
	
	/**
	 * array protected function filter(array $datas = array(string $field => ?scalar $data,...))
	 */
	protected function filter(array $datas): array {
		foreach($datas as $field => $data){
			if(is_database_named_regular($field)) $key = database_backquote($field);
			else continue;
			if(is_integer($data) or is_float($data)) $data = (string)$data;
			elseif(is_string($data)) $data = "'" . $data . "'";
			elseif(is_bool($data)) $data = $data ? '1' : '0';
			elseif(is_null($data)) $data = 'null';
			else continue;
			$ends[$field] = $data;
		}
		return $ends ?? array();
	}
	
	/**
	 * boolean protexted function isNoEmpty(?string $data)
	 */
	protected function isNoEmpty(string $data): bool {
		return $data != '' ? true : false;
	}
	
	/**
	 * void protected function change(string &$data, string $key)
	 */
	protected function change(string &$data, string $key): void {
		$data = $key . '=' . $data;
	}
	
	/**
	 * void protected function close(void)
	 */
	protected function close(): void {
		$this->free();
		$this->reader = null;
		$this->writer = null;
	}
	
	/**
	 * void protected function free(void)
	 */
	protected function free(): void {
		$this->ds = null;
	}
	//
}