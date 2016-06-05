<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @subpackage Db
 * @author Jeremy MOULIN
 */

/**
 * @see Marcel_Db_Table_Abstract 
 */
require_once('Marcel/Db/Table/Abstract.php');

/**
 * Autodiscover Model for a given table
 * 
 * @uses Marcel_Db_Table_Abstract
 * @uses Marcel_Backoffice_Db_Table_Row_Autodiscover
 * 
 * @package Marcel_Backoffice
 * @subpackage Db
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Backoffice_Db_Table_Autodiscover extends Marcel_Db_Table_Abstract {
	
	/**
	 * Must use a autodiscover Row
	 * @var string
	 */
	protected $_rowClass = 'Marcel_Backoffice_Db_Table_Row_Autodiscover';
	
	/**
	 * Retrieve available tables to use for the backoffice
	 * 
	 * @param array $config Configuration for the tables
	 * 
	 * @throws Exception in case of configuration not valid
	 * 
	 * @return array
	 */
	static public function getTableList($config = NULL) {
		if (!isset($config['settings'])) {
			throw new Exception('Config not valid');
		}
		$me = new self;
		if (isset($config['settings']['autodiscover']) && $config['settings']['autodiscover']) {
			$tables = $me->getAdapter()->fetchAll('SHOW tables');
			$returnTables = array();
			foreach ($tables as $table) {
				foreach ($table as $tableName) {
					$returnTables[$tableName] = isset($config['models'][$tableName]) ? $config['models'][$tableName] : $tableName;
				}
			}
		} else {
			$returnTables = $config['models'];
		}
		
		if (isset($config['deny']) && is_array($config['deny'])) {
			foreach ($config['deny'] as $model => $value) {
				unset($returnTables[$model]);
			}
		}
		
		return $returnTables;
	}
	
	/**
	 * Retrieve pairs for given key/value pair
	 * 
	 * @param string $keyField   field name in the table for the value
	 * @param string $valueField field name in the table for the label
	 * @param array  $where      Array containg condition + value indexes
	 * 
	 * @throws Exception if values are not correctly defined
	 * 
	 * @return Zend_Db_Table_Rowset
	 */
	public function getPairs($keyField, $valueField, $where = NULL) {
		if (!$keyField || !$valueField) {
			throw new Exception('Key and Value fields are required !');
		}
		$select = $this->getAdapter()
			->select()
			->from($this->_name, array($keyField, $valueField));
		
		if (is_array($where) && count($where)) {
			foreach ($where as $cond) {
				if (isset($cond['condition']) && isset($cond['value'])) {
					$select->where($cond['condition'], $cond['value']);
				}
			}
		}

		return $this->getAdapter()->fetchPairs($select);

	}
	
}