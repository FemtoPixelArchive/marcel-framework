<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @author Jeremy MOULIN
 */

/**
 * @see Marcel_Backoffice_Db_Table_Autodiscover
 */
require_once('Marcel/Backoffice/Db/Table/Autodiscover.php');

/**
 * @see Zend_Registry
 */
require_once('Zend/Registry.php');

/**
 * Backoffice Model Factory - Factory design pattern
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Backoffice_Model {
	/**
	 * no constructor - Factory desgin pattern
	 */
	protected function __construct() {
	}
	/**
	 * No clone - factory
	 */
	protected function __clone() {
	}
	/**
	 * no sleep - factory
	 */
	protected function __sleep() {
	}
	/**
	 * no wakeup - factoru
	 */
	protected function __wakeup() {
	}
	
	/**
	 * Factory
	 * 
	 * @param string $model Model to construct
	 * 
	 * @uses Zend_Registry
	 * @uses Marcel_Backoffice_Db_Table_Autodiscover
	 * 
	 * @return mixed object created
	 */
	static public function factory($model) {
		$modelToCreate = ucfirst(Zend_Registry::getInstance()->config['appname']) . '_Model_' . $model;
		if (@class_exists($modelToCreate) && is_subclass_of($modelToCreate, 'Marcel_Db_Table_Abstract')) {
			$object = new $modelToCreate;
		} else {
			$object = new Marcel_Backoffice_Db_Table_Autodiscover(array('name' => $model));
		}
		return $object;
	}
}