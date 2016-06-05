<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Db
 * @subpackage Table
 * @author Jeremy MOULIN
 */

/**
 * @see Zend_Db_Table_Abstract
 */
require_once('Zend/Db/Table/Abstract.php');
/**
 * @see Marcel_Db_Table_Row
 */
require_once('Marcel/Db/Table/Row.php');

/**
 * @see Zend_Registry
 */
require_once('Zend/Registry.php');

/**
 * Table abstract driver
 * 
 * @author Jeremy MOULIN
 * 
 * @package Marcel_Db
 * 
 * @subpackage Table
 * 
 * @uses Zend_Db_Table_Abstract
 *
 */
abstract class Marcel_Db_Table_Abstract extends Zend_Db_Table_Abstract {

	/**
	 * Default entity used
	 * @var string
	 */
	protected $_rowClass = 'Marcel_Db_Table_Row';
	
	/**
	 * (non-PHPdoc)
	 * @see library/Marcel/Db/Table/Marcel_Db_Table_Abstract#__construct()
	 */
	public function __construct($config = array()) {
		$object = strrchr(get_class($this), "_"); //find last Section of a class name with the first underscore
		$appname = ucfirst(Zend_Registry::getInstance()->config['appname']) . '_';
    	$entity = $appname . 'Entity' . $object;
    	$this->_rowClass = ($this->_rowClass !== 'Marcel_Db_Table_Row') ? $this->_rowClass : (@class_exists($entity) ? $entity : 'Marcel_Db_Table_Row');
    	
    	parent::__construct($config);
    }
	
}