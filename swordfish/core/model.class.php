<?php
// declare(strict_types = 1);
namespace Swordfish;

class Model {
	/**
	 * default_database_pass = array()
	 */
	protected $_memory;
	protected $_rules;
	protected $memory;
	protected $database;
	protected $datas = array();
	protected $fields = array();
	protected $messages = array();
	protected $rules = array();
	
	/**
	 * void public function __construct(?string $memory = null, ?string $pass = null)
	 */
	public function __construct(string $memory = null, string $pass = null) {
		$this->database($pass);
		if(is_string($this->_memory) && is_database_named_regular($this->_memory)) $this->memory = $this->_memory;
		elseif(is_string($memory) && is_database_named_regular($memory)) $this->memory = $memory;
		if(is_string($this->memory)){
			$this->fields($this->memory);
			$this->table($this->memory);
		}
		if(is_array($this->_rules)){
			foreach($this->_rules as $rule){
				if(is_array($rule) && is_data_validate_rule($rule)) $this->rules[] = $rule;
			}
		}
	}
	
	/**
	 * void public function __destruct(void)
	 */
	public function __destruct() {
		$this->clearDatabase();
	}
	
	/**
	 * ?scalar public function __get()
	 */
	public function __get(string $prop) {
		return $this->datas[$prop] ?? null;
	}
	
	/**
	 * void public function __set(string $prop, ?scalar $value)
	 */
	public function __set(string $prop, $value) {
		if(is_camel_named_regular($prop)){
			$datas[camel_to_database_named($prop)] = $value;
			$this->datas = array_merge($this->datas, $this->filter($datas));
		}
	}
	
	/**
	 * Model public function distinct(?boolean $data = null)
	 */
	public function distinct(bool $data = null): Model {
		if(is_null($data)) $this->database->clear('distinct');
		else $this->database->data('distinct', $data ? 'distinct' : 'all');
		return $this;
	}
	
	/**
	 * Model public function field(?string $data = null)
	 */
	public function field(string $data = null): Model {
		if(is_null($data)) $this->database->clear('field');
		else $this->database->data('field', $data);
		return $this;
	}
	
	/**
	 * Model public function table(?string $data = null)
	 */
	public function table(string $data = null): Model {
		if(is_null($data)) $this->database->clear('table');
		else $this->database->data('table', $data);
		return $this;
	}
	
	/**
	 * Model public function join(?string $data = null)
	 */
	public function join(string $data = null): Model {
		if(is_null($data)) $this->database->clear('join');
		else $this->database->data('join', $data);
		return $this;
	}
	
	/**
	 * Model public function where(?string $data = null)
	 */
	public function where(string $data = null): Model {
		if(is_null($data)) $this->database->clear('where');
		else $this->database->data('where', $data);
		return $this;
	}
	
	/**
	 * Model public function group(?string $data = null)
	 */
	public function group(string $data = null): Model {
		if(is_null($data)) $this->database->clear('group');
		else $this->database->data('group', $data);
		return $this;
	}
	
	/**
	 * Model public function having(?string $data = null)
	 */
	public function having(string $data = null): Model {
		if(is_null($data)) $this->database->clear('having');
		else $this->database->data('having', $data);
		return $this;
	}
	
	/**
	 * Model public function order(?string $data = null)
	 */
	public function order(string $data = null): Model {
		if(is_null($data)) $this->database->clear('order');
		else $this->database->data('order', $data);
		return $this;
	}
	
	/**
	 * Model public function limit(?int $num =null, int $offset =0)
	 */
	public function limit(int $num = null, int $offset = 0): Model {
		if(is_null($num)) $this->database->clear('limit');
		else $this->database->data('limit', 'limit ' . ($offset != 0 ? $offset . ',' . $num : $num));
		return $this;
	}
	
	/**
	 * Model public function release(void)
	 */
	public function release(): Model {
		if($this->database) $this->database->clear();
		return $this;
	}
	
	/**
	 * Model public function create(void)
	 */
	public function create(): Model {
		$this->clear();
		$this->datas = $this->filter(_i('post.*'));
		return $this;
	}
	
	/**
	 * Model public function data(array $datas)
	 * @$datas = array(string $field => ?scalar $value,...)
	 */
	public function data(array $datas): Model {
		$this->clear();
		$this->datas = $this->filter($datas);
		return $this;
	}
	
	/**
	 * Model public function clear(void)
	 */
	public function clear(): Model {
		$this->datas = array();
	}
	
	/**
	 * Model public function rule(array $datas)
	 * @$datas = array(string $field, string $regular, array $params, string $message)
	 */
	public function rule(array $datas): Model {
		if(is_data_validate_rule($datas)) $this->rules[] = $datas;
		return $this;
	}
	
	/**
	 * boolean public function validate(void)
	 */
	public function validate(): bool {
		$this->messages = array();
		$validator = new Validator();
		foreach($this->datas as $key => $value){
			foreach($this->rules as $rule){
				list($field, $regular, $params, $message) = $rule;
				if($key == $field){
					$method = new \ReflectionMethod($validator, $regular);
					array_unshift($params, $value);
					if(!$method->invokeArgs($validator, $params)){
						$this->messages[] = $message;
						continue 2;
					}
				}
			}
		}
		return $this->messages ? true : false;
	}
	
	/**
	 * array public function messages(void)
	 */
	public function messages(): array {
		return $this->messages;
	}
	
	/**
	 * integer public function cmd(string $sql)
	 */
	public function cmd(string $sql): int {
		return $this->database->cmd($sql);
	}
	
	/**
	 * array public function query(string $sql)
	 */
	public function query(string $sql): array {
		return $this->database->query($sql);
	}
	
	/**
	 * array public function select(void)
	 */
	public function select(): array {
		return $this->database->select();
	}
	
	/**
	 * integer public function add(void)
	 */
	public function add(): int {
		return $this->database->insert($this->datas);
	}
	
	/**
	 * integer public function save(void)
	 */
	public function save(): int {
		return $this->database->update($this->datas);
	}
	
	/**
	 * integer public function delete(void)
	 */
	public function delete(): int {
		return $this->database->delete();
	}
	
	/**
	 * boolean public function begin(void)
	 */
	public function begin(): bool {
		return $this->database->begin();
	}
	
	/**
	 * boolean public function end(void)
	 */
	public function end(): bool {
		return $this->database->end();
	}
	
	/**
	 * boolean public function rollback(void)
	 */
	public function rollback(): bool {
		return $this->database->rollback();
	}
	
	/**
	 * ?string public function sql(void)
	 */
	public function sql(): string {
		return $this->database->sql();
	}
	
	/**
	 * ?string public function error(void)
	 */
	public function error(): string {
		return $this->database->error();
	}
	
	/**
	 * ?integer public function id(void)
	 */
	public function id(): int {
		return $this->database->id();
	}
	
	/**
	 * void protected function database(?string $pass = null)
	 */
	protected function database(string $pass = null): void {
		$this->clearDatabase();
		$connectors = get_config($pass ?? 'default_database_pass', array());
		$this->database = new Mysql($connectors);
	}
	
	/**
	 * void protected function clearDatabase(void)
	 */
	protected function clearDatabase(): void {
		$this->database = null;
	}
	
	/**
	 * void protected function fields(string $memroy)
	 */
	protected function fields(string $memory): void {
		if(is_string($this->memory)) $this->fields = $this->database->fields($memory);
	}
	
	/**
	 * array protected function filter(array $datas)
	 * @$datas = array(string $key => ?scalar $value,...)
	 */
	protected function filter(array $datas): array {
		$func = function ($data, $key) {
			if(!is_string($key)) return false;
			elseif(!is_database_named_regular($key)) return false;
			elseif($this->fields && !in_array($key, array_keys($this->fields), true)) return false;
			elseif(!is_scalar($data) && !is_null($data)) return false;
			else return true;
		};
		$datas = array_filter($datas, $func, ARRAY_FILTER_USE_BOTH);
		if($this->fields){
			foreach($datas as $key => &$value){
				if(!is_null($value)) settype($value, $this->fields[$key]);
			}
		}
		return $datas;
	}
	//
}

