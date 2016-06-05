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
 * @see Zend_Db_Table_Row
 */
require_once('Zend/Db/Table/Row.php');

/**
 * Entity abstract for one row
 * 
 * @author Jeremy MOULIN
 * 
 * @package Marcel_Db
 * 
 * @subpackage Table
 * 
 * @uses Zend_Db_Table_Row
 *
 */
abstract class Marcel_Db_Table_Row_Abstract extends Zend_Db_Table_Row {
	/**
	 * Form class name for the object creation
	 * @var string
	 */
	protected $_formClassName = NULL;
	
	/**
	 * Instance of the created form
	 * @var Cartier_Form_Abstract
	 */
	protected $_instance = NULL;
		
	/**
	 * Retrieve the associated form
	 * 
	 * @param array $options Options for the associated form
	 * 
	 * @throws DomainException If form obejct does not exist
	 * @throws DomainException If form object is not instance of Marcel_Form_Abstract
	 * 
	 * @uses Marcel_Form_Abstract
	 * @uses Zend_Registry
	 * 
	 * @return Marcel_Form_Abstract
	 */
	public function getForm($options = NULL) {
		if ($this->_instance) {
			return $this->_instance;
		}
		require_once('Zend/Registry.php');
		$appname = ucfirst(Zend_Registry::getInstance()->config['appname']) . '_';
		$className = $appname . 'Form' . $this->getFormClassName();
		if (!@class_exists($className)) {
			throw new DomainException($className . ' should be an existing Form object', 1);
		}

		if (!isset($options['row'])) {
			$options['row'] = $this;
		}
		$this->_instance = new $className($options);
		require_once('Marcel/Form/Abstract.php');
		if (!$this->_instance instanceof Marcel_Form_Abstract) {
			throw new DomainException($className . ' should be an instance of Marcel_Form_Abstract', 2);
		}
		$data = $this->_data;
		foreach ($data as $key => $value) {
			if (empty($value)) {
				unset($data[$key]);
			}
		}
		
		if (!empty($data)) {
			$this->_instance->populate($this->_data);
		}
		return $this->_instance;
	}
	
	/**
	 * Define the form object class name
	 * 
	 * @param string|null $name Name of the class object (optional) (default : NULL)
	 * 
	 * @return Marcel_Form_Abstract
	 */
	public function setFormClassName($name = NULL) {
		$className = get_class($this);
		if (substr($className, -4) == '_Row') {
			$className = get_class($this->getTable());
		}
		$this->_formClassName = $name ? $name : strrchr($className, "_"); //find last Section of a class name with the first underscore
		if ($this->_formClassName{0} != '_') {
			$this->_formClassName = '_' . $this->_formClassName;
		}
		return $this;
	}
	
	/**
	 * Gives the current value for the form class (and set default if not defined)
	 * 
	 * @see Marcel_Db_Table_Row_Abstract::setFormClassName
	 * 
	 * @return string
	 */
	public function getFormClassName() {
		if (!$this->_formClassName) {
			$this->setFormClassName();
		}
		return $this->_formClassName;
	}
	
	/**
	 * Wake up if stored in cache
	 * 
	 * @see Zend_Db_Table_Row_Abstract::setTable
	 */
	public function __wakeup() {
		parent::__wakeup();
		
		$table = new $this->_tableClass;
		$this->setTable($table);
	}
	
	public function getPrimaryKey() {
		return $this->_getPrimaryKey();
	}

} 