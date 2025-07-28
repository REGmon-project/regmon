<?php // class db 

/**
 * Abstract database wrapper
 * Interact with databases with one line of code
 */
abstract class db {
	/**
	 * Stores the database name
	 * @var mixed
	 */
	protected $database;
	/**
	 * Stores the database connection
	 * @var mixed
	 */
	protected $connection;
	/**
	 * Stores the last query
	 * @var mixed
	 */
	protected $query;
	/**
	 * Stores the last query result
	 * @var mixed|mysqli_result
	 */
	protected $result;
	/**
	 * Stores the last Error
	 * @var mixed
	 */
	public $myError;
	/**
	 * Stores the logFile filename
	 * @var mixed
	 */
	protected $logFile;
	/**
	 * Log_Slow_DB_Query_Seconds is the time after which the query will logged as slow
	 * @var mixed
	 */
	protected $Log_Slow_DB_Query_Seconds = false;
	/**
	 * Enable/Disable the addition of Quotes when we prepare the query
	 * @var bool
	 */
	protected $addQuotes = true;
	/**
	 * Enable/Disable the filtering of Invalid Fields (fields not exist in DB)
	 * @var bool
	 */
	public $filterInvalidFields = true;
	/**
	 * Stores the DB schema
	 * @var mixed
	 */
	protected $schema = array();
	/**
	 * Stores the last created instance
	 * @var mixed
	 */
	public static $instance;

	/**
	 * abstract functions 
	 * 
	 * implement these methods when creating driver subclasses
	 * need to add _open() to the mix somehow
	 *  
	 */

	/**
	 * abstract function beginTransaction - Starts a transaction
	 * @return void
	 */
	public abstract function beginTransaction();
	/**
	 * abstract function commitTransaction - Commits the current transaction
	 * @return void
	 */
	public abstract function commitTransaction();
	/**
	 * abstract function rollbackTransaction - Rolls back current transaction
	 * @return void
	 */
	public abstract function rollbackTransaction();
	/**
	 * abstract function _query
	 * @return bool|mysqli_result
	 */
	protected abstract function _query(string $sql, bool $buffered = true);
	/**
	 * abstract function _fetchRow
	 * @return mixed
	 */
	protected abstract function _fetchRow();
	/**
	 * abstract function _fetch
	 * @return mixed
	 */
	protected abstract function _fetch();
	/**
	 * abstract function _fetchAll
	 * @return mixed
	 */
	protected abstract function _fetchAll();
	/**
	 * abstract function _fetchAllwithKey
	 * @return mixed
	 */
	protected abstract function _fetchAllwithKey(string $key);
	/**
	 * abstract function _fetchAllwithKey2
	 * @return mixed
	 */
	protected abstract function _fetchAllwithKey2(string $key, string $key2);
	/**
	 * abstract function _fetchAllwithKey3
	 * @return mixed
	 */
	protected abstract function _fetchAllwithKey3(string $key, string $key2, string $key3);
	/**
	 * abstract function close
	 * @return void
	 */
	public abstract function close();
	/**
	 * abstract function _error
	 * @return string
	 */
	protected abstract function _error();
	/**
	 * abstract function _numberRows - Gets the number of rows in the result set
	 * @return bool|int|string
	 */
	protected abstract function _numberRows();
	/**
	 * abstract function _affectedRows
	 * @return int|string
	 */
	protected abstract function _affectedRows();
	/**
	 * abstract function _lastID
	 * @return int|string
	 */
	protected abstract function _lastID();
	/**
	 * abstract function _free_result
	 * @return void
	 */
	protected abstract function _free_result();
	/**
	 * abstract function _escapeString
	 * @return string
	 */
	protected abstract function _escapeString(string $string);
	/**
	 * abstract function _fields
	 * @return mixed
	 */
	protected abstract function _fields(string $table);
	/**
	 * abstract function _quoteField
	 * @return string
	 */
	protected abstract function _quoteField(string $field);
	/**
	 * abstract function _quoteFields
	 * @return mixed
	 */
	protected abstract function _quoteFields(mixed $fields);

	/**
	 * Class init method
	 */
	public function __construct(mixed $handle = null) {
		$this->connection = $handle;
		$this->query = null;
		$this->result = null;
		$this->logFile = null;
		$this->schema = null;
		db::$instance = $this;
	}

	/**
	 * __destruct
	 * Close log file
	 */
	public function __destruct() {
		if($this->logFile) {
			fclose($this->logFile);
		}
	}
	
	/**
	 * logToFile - Open log file for append
	 * @return void
	 */
	public function logToFile(string $file, string $method = 'a+') {
		$this->logFile = fopen($file, $method);
	}

	/**
	 * Open a Connection with the Database
	 * @return mixed
	 */ 
	public static function open(mixed $type, mixed $database, string $user = '', string $password = '', string $host = 'localhost', int $Log_Slow_DB_Query_Seconds = 0) {
		$db_new = false;
		switch($type) {
			//case 'mysql':
			case 'mysqli':
				$name = 'db_' . $type;
				if(is_resource($database)) {
					$db_new = new $name($database);
				}
				if(is_string($database)) {
					$db_new = new $name();
					$db_new->_open($database, $user, $password, $host, $Log_Slow_DB_Query_Seconds);
				}
				break;
		}
		return $db_new;
	}

	/**
	 * execute - Performs a query using the given string
	 * Used by the other _query functions.
	 * @return bool
	 */
	public function execute(string $sql, mixed $parameters = array(), bool $buffered = true) {
		$time_start = microtime(true);

		$fullSql = $this->makeQuery($sql, $parameters);
		$this->query = $sql;
		$this->result = $this->_query($fullSql, $buffered); // sets $this->result

		$time_end = microtime(true);
		$seconds = number_format($time_end - $time_start, 8);
		//if slow (more than 10sec) write to error log
		if ($this->Log_Slow_DB_Query_Seconds AND $seconds > $this->Log_Slow_DB_Query_Seconds) {
			error_log(date("Y-m-d H:i:s")."\n".$fullSql."\n***************SLOW***************\n".$seconds." seconds\n\n");
		}
		if ($this->logFile) {
			fwrite($this->logFile, date("Y-m-d H:i:s")."\n".$fullSql."\n".$seconds." seconds\n\n");
		}

		$this->myError = false;
		if (!$this->result && (error_reporting() & 1)) {
			$this->myError = $this->_error(); 
		}

		if ($this->result) {
			return true;
		}
		return false;
	}
	
	/**
	 * insert - Insert data to DB table
	 * 
	 * Passed an array and a table name, it attempts to insert the data into the table.
	 * Returns the insert_id. If insert failed it will return false or the error
	 * 
	 * @return bool|int|string - false|insert_id|error
	 */
	public function insert(mixed $data, mixed $table) {
		if (is_string($data) && is_array($table)) {
			error_log(date("Y-m-d H:i:s").'db - Parameters passed to insert() were in reverse order');
		}
		// remove invalid fields
		if ($this->filterInvalidFields) {
			$data = $this->filterFields($data, $table);
		}
			
		// appropriately quote input data
		$sql = 'INSERT INTO ' . $table . ' (' . implode(',', (array)$this->_quoteFields(array_keys((array)$data))) . ') VALUES(' . implode(',', $this->placeHolders($data)) . ')';

		/**
		 * don't wrap single inserts in transactions 
		 * By forcing each insert to be in a transaction, 
		 * the user is denied control over transaction granularity. 
		 * As a side-note, users can now diagnose failed inserts, since the rollback cleared errors.
		 * 
		 * If you have rows that need to be inserted together and are dependent on each other, 
		 * those are the records you wrap in a transaction.
		 */
		// $this->beginTransaction();	
		$this->myError = false;
		if ($this->execute($sql, $data)) {
			$id = $this->_lastID();
			// $this->commitTransaction();
			return $id;
		} else {
			// $this->rollbackTransaction();
			//return false;
			return $this->myError; //false or error
		}
	}

	/**
	 * update - update date to DB table
	 * 
	 * Passed an array, table name, where clause 
	 * and placeholder parameters, it attempts to update a record.
	 * Returns the number of affected rows
	 * 
	 * @return int|string
	 */
	public function update(mixed $data, mixed $table, mixed $where = null, mixed $parameters = array()) {
		if (is_string($data) && is_array($table)) {
			error_log(date("Y-m-d H:i:s").'db - Parameters passed to update() were in reverse order');
		}
		
		// remove invalid fields
		if ($this->filterInvalidFields) {
			$data = (array)$this->filterFields($data, $table);
		}

		$sql = 'UPDATE ' . $table . ' SET ';
		// merge field placeholders with actual $parameters
		foreach((array)$data as $key => $value) {
			// wrap quotes around keys
			$sql .= $this->_quoteField($key) . '=:' . $key . ',';
		}
		$sql = substr($sql, 0, -1); // strip off last comma

		if ($where) {
			$sql .= ' WHERE ' . $where;
			$data = array_merge((array)$data, (array)$parameters);
		}

		$this->execute($sql, $data);

		return $this->_affectedRows();
	}

	/**
	 * delete - DELETE query
	 * @return int|string
	 */
	public function delete(string $table, mixed $where = null, mixed $parameters = array()) {
		$sql = 'DELETE FROM ' . $table;
		if($where) {
			$sql .= ' WHERE ' . $where;
		}
		$this->execute($sql, $parameters);

		return $this->_affectedRows();
	}

	/**
	 * fetchAll - Fetches all of the rows where each is an associative array.
	 * @return mixed
	 */
	public function fetchAll(string $sql, mixed $parameters = array()) {
		$this->execute($sql, $parameters, false);
		if ($this->result) {
			return $this->_fetchAll();
		}
		return array();
	}
	
	/**
	 * fetchAllwithKey - The same as fetchAll but grouped by key
	 * @return mixed
	 */
	public function fetchAllwithKey(string $sql, mixed $parameters = array(), string $key = '') {
		$this->execute($sql, $parameters, false);
		if ($this->result) {
			return $this->_fetchAllwithKey($key);
		}
		return array();
	}

	/**
	 * fetchAllwithKey2 - The same as fetchAll but grouped by 2 keys
	 * @return mixed
	 */
	public function fetchAllwithKey2(string $sql, mixed $parameters = array(), string $key = '', string $key2 = '') {
		$this->execute($sql, $parameters, false);
		if ($this->result) {
			return $this->_fetchAllwithKey2($key, $key2);
		}
		return array();
	}

	/**
	 * fetchAllwithKey3 - The same as fetchAll but grouped by 3 keys
	 * @return mixed
	 */
	public function fetchAllwithKey3(string $sql, mixed $parameters = array(), string $key = '', string $key2 = '', string $key3 = '') {
		$this->execute($sql, $parameters, false);
		if ($this->result) {
			return $this->_fetchAllwithKey3($key, $key2, $key3);
		}
		return array();
	}

	/**
	 * fetch
	 * This is intended to be the method used for large result sets.
	 * It is intended to return an iterator, and act upon buffered data.
	 * @return mixed
	 */
	public function fetch(string $sql, mixed $parameters = array()) {
		$this->execute($sql, $parameters);
		return $this->_fetch();
	}

	/**
	 * fetchRow - Fetch just 1 row
	 * @return mixed
	 */
	public function fetchRow(string $sql = null, mixed $parameters = array()) {
		if($sql != null) {
			$this->execute($sql, $parameters);
		}
		if($this->result) {
			return $this->_fetchRow();
		}
		return array();
	}

	/**
	 * fetchCell - Fetches the first cell from the first row returned by the query
	 * @return mixed
	 */
	public function fetchCell(string $sql, mixed $parameters = array()) {
		if($this->execute($sql, $parameters)) {
			$Row = (array)$this->_fetchRow();
			return array_shift($Row); // shift first field off first row
		}
		return null;
	}

	/**
	 * fetchColumn
	 * This method is quite different from fetchCell(), actually
	 * it fetches one cell from each row and places all the values in 1 array
	 * @return mixed
	 */
	public function fetchColumn(string $sql, mixed $parameters = array()) {
		if($this->execute($sql, $parameters)) {
			$cells = array();
			foreach((array)$this->_fetchAll() as $row) {
				$row = (array)$row;
				$cells[] = array_shift($row);
			}
			return $cells;
		} else {
			return array();
		}
	}

	/**
	 * makeQuery
	 * This combines a query and parameter array into a final query string for execution
	 * PDO drivers don't need to use this
	 */
	protected function makeQuery(string $sql, mixed $parameters):string {
		// bypass extra logic if we have no parameters
		if(sizeof((array)$parameters) == 0) {
			return $sql;
		}
		
		$parameters = (array)$this->prepareData($parameters);
		// separate the two types of parameters for easier handling
		$questionParams = array();
		$namedParams = array();
		foreach($parameters as $key => $value) {
			if(is_numeric($key)) {
				$questionParams[] = $value;
			} else {
				$namedParams[ ':' . $key ] = $value;
			}
		}
		// sort namedParams in reverse to stop substring squashing
		krsort($namedParams);
		
		// split on question-mark and named placeholders
		$result = preg_split('/(\?|:[a-zA-Z0-9_-]+)/', $sql, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		
		// every-other item in $result will be the placeholder that was found
		
		$query = '';
		
		if (is_array($result)) {
			for($i = 0; $i < sizeof($result); $i+=2) {
				$query .= $result[ $i ];
				
				$j = $i+1;
				if (array_key_exists($j, $result)) {
					$test = $result[ $j ];
					if ($test == '?') {
						$query .= array_shift($questionParams);
					}
					else {
						$query .= $namedParams[ $test ]; 
					}
				}
			}
		}

		return $query;
	}

	/**
	 * debugging
	 * Return query and other debugging data if error_reporting to right settings
	 * @return mixed|null
	 */
	/*private function debugging() {
		if (in_array(error_reporting(), array(E_ALL))) {
			return $this->query;
		}
	}*/

	/**
	 * numberRows - Returns the number of rows of the last query
	 * @return bool|int|string
	 */
	public function numberRows() {
		return $this->_numberRows();
	}

	/**
	 * free_result - Frees the memory associated with a result
	 * @return void
	 */
	public function free_result() {
		$this->_free_result(); 
	}
	
	/**
	 * filterFields
	 * Used by insert() and update() to filter invalid fields from a data array
	 * @return mixed
	 */
	private function filterFields(mixed $data, mixed $table) {
		$this->buildSchema($table); // builds if not previously built
		$fields = $this->schema[ $table ]['fields'];
		foreach((array)$data as $field => $value) {
			if(!array_key_exists((string)$field, (array)$fields))
				unset($data[ $field ]);
		}
		return $data;
	}
	
	/**
	 * prepareData
	 * This should be protected and over loadable by driver classes
	 * @return mixed
	 */
	private function prepareData(mixed $data) {
		$values = array();
		foreach((array)$data as $key => $value) {
			$escape = true;
			// don't quote or esc if value is an array, we treat it as a "decorator" 
			// that tells us not to escape the value contained in the array
			if(is_array($value) && !is_object($value)) {
				$escape = false;
				$value = array_shift($value);
			}
			// it's not right to worry about invalid fields in this method because we may be operating 
			// on fields that are aliases, or part of other tables through joins 
			// if(!in_array($key, $columns)) { continue; } // skip invalid fields
			if ($escape) {
				if ($this->addQuotes) {
					if (is_null($value)) {
						$values[$key] = "NULL";
					}
					else {
						$values[$key] = "'" . $this->_escapeString($value.'') . "'";
					}
				}
				else {
					$values[$key] = $this->_escapeString($value.'');
				}
			}
			else {
				$values[$key] = $value;
			}
		}
		return $values;
	}

	/**
	 * placeHolders
	 * Given a data array, this returns an array of placeholders
	 * These may be question marks "?", or ":email" type
	 * @return array<string>
	 */
	private function placeHolders(mixed $values) {
		$data = array();
		foreach((array)$values as $key => $value) {
			if(is_numeric($key))
				$data[] = '?';
			else
				$data[] = ':' . $key;
		}
		return $data;
	}
	
	
	/**
	 * buildSchema
	 * We build the DB Table Schema so we can use it later to filter the fields
	 * @return void
	 */
	public function buildSchema(mixed $table) {
		if (isset($this->schema[ $table ]) AND $this->schema[ $table ] != null) {
			return;
		}
		$schema = $this->schema;
		$schema[ $table ] = array(
			'fields' => array(),
			'primaryKey' => null
		);

		$fields = $this->_fields($table.'');
		$schema[ $table ]['fields'] = $fields;
		foreach((array)$fields as $name => $field) {
			if($field['primaryKey']) {
				$schema[ $table ]['primaryKey'] = $name;
			}
		}
		$this->schema = $schema;
	}
}


/**
 * db_mysqli
 */
class db_mysqli extends db {

	/**
	 * beginTransaction - Starts a transaction
	 * @return void
	 */
	public function beginTransaction() {
		if ($this->connection instanceof mysqli) {
			// Turn autocommit off
			mysqli_autocommit($this->connection, false);
		}
	}

	/**
	 * commitTransaction - Commits the current transaction
	 * @return void
	 */
	public function commitTransaction() {
		if ($this->connection instanceof mysqli) {
			// Commit transaction
			mysqli_commit($this->connection);
			// Turn autocommit on
			mysqli_autocommit($this->connection, true);
		}
	}

	/**
	 * rollbackTransaction - Rolls back current transaction
	 * @return void
	 */
	public function rollbackTransaction() {
		if ($this->connection instanceof mysqli) {
			// Rollback transaction
			mysqli_rollback($this->connection);
			// Turn autocommit on
			mysqli_autocommit($this->connection, true);
		}
	}

	/**
	 * _open - Open a new connection to the MySQL server
	 * also set the encoding to UTF8
	 * @return bool|mysqli
	 */
	protected function _open(string $database, string $user, string $password, string $host, int $Log_Slow_DB_Query_Seconds) {
		$this->database = $database;
		$this->connection = mysqli_connect($host, $user, $password, $database);
		$this->Log_Slow_DB_Query_Seconds = $Log_Slow_DB_Query_Seconds;
		// Check connection
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			exit();
		}
		if ($this->connection) {
			mysqli_query($this->connection, "SET NAMES 'UTF8'");
			return $this->connection;
		} else {
			return false;
		}
	}


	/**
	 * _query - Performs a query on the database
	 * @return bool|mysqli_result
	 */
	protected function _query(string $sql, bool $buffered = true) {
		if ($this->connection instanceof mysqli) {
			if ($buffered) {
				return mysqli_query($this->connection, $sql);
			}
			// This tries to use unbuffered queries to cut down on execution time and memory usage,
			// but you'll only see a benefit with extremely large result sets.
			return mysqli_query($this->connection, $sql, MYSQLI_USE_RESULT);
		}
		return false;
	}

	/**
	 * _fetchRow
	 * Fetch the next row of a result set as an associative array
	 * @return mixed
	 */
	protected function _fetchRow() {
		if (!$this->result) {
			$this->_log_error('_fetchRow', $this->_error());
			return false;
		}
		if ($this->result instanceof mysqli_result ) {
			return mysqli_fetch_assoc($this->result);
		}
		return false;
	}

	/**
	 * _fetch - alias for _fetchAll
	 * @return mixed
	 */
	protected function _fetch() {
		return $this->_fetchAll();
	}

	/**
	 * _fetchAll - Get all rows
	 * @return mixed
	 */
	protected function _fetchAll() {
		$data = array();
		if (!$this->result) {
			$this->_log_error('_fetchAll', $this->_error());
			return array();
		}
		else {
			if ($this->result instanceof mysqli_result) {
				while ($row = mysqli_fetch_assoc($this->result)) {
					$data[] = $row;
				}
			}
		}
		return $data;
	}
	
	/**
	 * _fetchAllwithKey
	 * Get all rows in a multidimensional array grouped by $key field
	 * @return mixed
	 */
	protected function _fetchAllwithKey(string $key='') {
		$data = array();
		if (!$this->result) {
			$this->_log_error('_fetchAllwithKey', $this->_error());
			return array();
		}
		else {
			if ($this->result instanceof mysqli_result) {
				while ($row = mysqli_fetch_assoc($this->result)) {
					$data[$row[$key]] = $row;
				}
			}
		}
		return $data;
	}

	/**
	 * _fetchAllwithKey2
	 * Get all rows in a multidimensional array grouped by $key, $key2 fields
	 * @return mixed
	 */
	protected function _fetchAllwithKey2(string $key='', string $key2='') {
		$data = array();
		if (!$this->result) {
			$this->_log_error('_fetchAllwithKey2', $this->_error());
			return array();
		}
		else {
			if ($this->result instanceof mysqli_result) {
				while ($row = mysqli_fetch_assoc($this->result)) {
					$data[$row[$key]][$row[$key2]] = $row;
				}
			}
		}
		return $data;
	}

	/**
	 * _fetchAllwithKey3
	 * Get all rows in a multidimensional array grouped by $key, $key2, $key3 fields
	 * @return mixed
	 */
	protected function _fetchAllwithKey3(string $key='', string $key2='', string $key3='') {
		$data = array();
		if (!$this->result) {
			$this->_log_error('_fetchAllwithKey3', $this->_error());
			return array();
		}
		else {
			if ($this->result instanceof mysqli_result) {
				while ($row = mysqli_fetch_assoc($this->result)) {
					$data[$row[$key]][$row[$key2]][$row[$key3]] = $row;
				}
			}
		}
		return $data;
	}

	/**
	 * close - Closes a previously opened database connection
	 * @return void
	 */
	public function close() {
		if ($this->connection instanceof mysqli) {
			mysqli_close($this->connection);
		}
	}

	/**
	 * _error - Returns a string description of the last error
	 * @return string
	 */
	public function _error() {
		if ($this->connection instanceof mysqli) {
			return mysqli_error($this->connection);
		}
		return '';
	}

	/**
	 * _escapeString - Escapes special characters in a string for use in an SQL statement, 
	 * taking into account the current charset of the connection
	 * @return string
	 */
	protected function _escapeString(mixed $string) {
		if ($this->connection instanceof mysqli) {
			return mysqli_real_escape_string($this->connection, $string.'');
		}
		return '';
	}

	/**
	 * _free_result - Frees the memory associated with a result
	 * @return void
	 */
	protected function _free_result() {
		//if ($this->result) { 
		//if (is_resource($this->result)) { 
		if ($this->result instanceof mysqli_result) {
			mysqli_free_result($this->result); 
		} 
		$this->result = 0; 
	}
	
	/**
	 * _numberRows - Gets the number of rows in the result set
	 * @return bool|int|string
	 */
	protected function _numberRows() {
		if (!$this->result) {
			$this->_log_error('_numberRows', $this->_error());
			return false;
		}
		if ($this->result instanceof mysqli_result) {
			return mysqli_num_rows($this->result);
		}
		return false;

		/*
		if(mysqli_affected_rows($this->connection)) { // for insert, update, delete
			$this->numberRecords = mysqli_affected_rows($this->connection);
		} elseif(!is_bool($this->result)) { // for selects
			$this->numberRecords = mysqli_num_rows($this->result);
		} else { // will be boolean for create, drop, and other
			$this->numberRecords = 0;
		}
		*/
	}

	/**
	 * _affectedRows - Gets the number of affected rows in a previous MySQL operation
	 * @return int|string
	 */
	protected function _affectedRows() {
		if ($this->connection instanceof mysqli) {
			return mysqli_affected_rows($this->connection);
		}
		return 0;
	}

	/**
	 * _lastID - Returns the insert_id
	 * Returns the value generated for an AUTO_INCREMENT column by the last query
	 * @return int|string
	 */
	protected function _lastID() {
		if ($this->connection instanceof mysqli) {
			return mysqli_insert_id($this->connection);
		}
		return 0;
	}
	
	/**
	 * _quoteField - Add quotes to a field
	 * @return string
	 */
	protected function _quoteField(string $field) {
		return '`' . $field . '`';
	}

	/**
	 * _quoteFields - Add quotes to an array of fields
	 * @return mixed
	 */
	protected function _quoteFields(mixed $fields) {
		return array_map(array($this, '_quoteField'), (array)$fields);
	}

	/**
	 * _fields - Get the fields of a table
	 * @return mixed
	 */
	protected function _fields(string $table) {
		$fields = array();
		$this->execute('describe ' . $table, array(), false);
		$rows = $this->_fetchAll();
		foreach((array)$rows as $row) {
			$type = strtolower(preg_replace('/\(.*\)/', '', $row['Type'].'').''); // remove size specifier
			$name = $row['Field'];
			$fields[ $name ] = array('type' => $type, 'primaryKey' => ($row['Key'] == 'PRI'));
		}
		return $fields;
	}
	
	/**
	 * _log_error - Logs the error to the error_log
	 * @return void
	 */
	protected function _log_error(string $method, string $error) {
		error_log('MySQL Error ('.$method.') : ' . $error. ' ... '. $this->query);
	}
}
?>