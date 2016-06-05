<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @author Jay MOULIN
 */
 
/**
 * @see Zend_Form_Element_Select
 */
require_once 'Zend/Form/Element/Select.php';

/**
 * @see Marcel_Backoffice_Model
 */
require_once('Marcel/Backoffice/Model.php');

/**
 * select component linked to another table
 * 
 * @author Romain VIAU
 *
 */
class Marcel_Backoffice_Form_Element_CustomSelect extends Zend_Form_Element_Select
{

	/**
	 * Table name to link to
	 * @var string
	 */
	protected $_tableName = null;
	
	/**
	 * field name for the value
	 * @var string
	 */
	protected $_keyField = null;
	
	/**
	 * Field name for the label
	 * @var string
	 */
	protected $_valueField = null;
	
	/**
	 * Where conditions
	 * @var array
	 */
	protected $_where = NULL;
	
	/**
	 * Define the table name
	 * 
	 * @param string $value  Table name to load
	 * 
	 * @return Marcel_Backoffice_Form_Element_CustomSelect
	 */
	public function setTableName($value) {
		$this->_tableName = $value;
		$this->_loadMultiOptions();
		return $this;
	}
	
	/**
	 * Retrieve the defined table name
	 * 
	 * @return string
	 */
	public function getTableName() {
		return $this->_tableName;
	}
	
	/**
	 * Define the field for the value
	 * 
	 * @param string $value field name
	 * 
	 * @return Marcel_Backoffice_Form_Element_CustomSelect
	 */
	public function setKeyField($value) {
		$this->_keyField = $value;
		$this->_loadMultiOptions();
		return $this;
	}
	
	/**
	 * Define the where conditions
	 *
	 * @param array $where Conditions (defined as associative with 'condition' and 'value' keys)
	 *
	 * @return Marcel_Backoffice_Form_Element_CustomSelect
	 */
	public function setWhere($where) {
		$this->_where = $where;
		$this->_loadMultiOptions();
		return $this;
	}
	
	/**
	 * Retrieve the defined where conditions
	 * 
	 * @return array|NULL
	 */
	public function getWhere() {
		return $this->_where;
	}
	
	/**
	 * Retrieve the field for the value
	 * 
	 * @return string
	 */
	public function getKeyField() {
		return $this->_keyField;
	}
	
	/**
	 * Define the field for the label
	 * 
	 * @param string $value field name
	 * 
	 * @return Marcel_Backoffice_Form_Element_CustomSelect
	 */
	public function setValueField($value) {
		$this->_valueField = $value;
		$this->_loadMultiOptions();
		return $this;
	}
	
	/**
	 * Retrieve the field for the label
	 * 
	 * @return string
	 */
	public function getValueField() {
		return $this->_valueField;
	}
	
	/**
	 * Load options
	 * 
	 * @uses Marcel_Backoffice_Model
	 * 
	 * @throws UnexpectedValueException id model has no getPairs method
	 * 
	 * @return Marcel_Backoffice_Form_Element_CustomSelect
	 */
	protected function _loadMultiOptions() {
		if ($this->_valueField && $this->_keyField && $this->_tableName) {
			$model = Marcel_Backoffice_Model::factory($this->_tableName);
			if (!method_exists($model, 'getPairs')) {
				throw new UnexpectedValueException('Model should have a getPairs method');
			}
			$this->addMultiOptions(array(0 => tr('Please select')));
			$this->addMultiOptions($model->getPairs($this->_keyField, $this->_valueField, $this->_where));
			
		}
		return $this;
	}
	
}