<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @subpackage Form
 * @author Jeremy MOULIN
 */

/**
 * @see Marcel_Form_Abstract
 */
require_once('Marcel/Form/Abstract.php');

/**
 * @see Marcel_Backoffice_Config
 */
require_once('Marcel/Backoffice/Config.php');


/**
 * Autodiscover form object to create a from dynamicly with table metadatas
 * 
 * @author Jeremy MOULIN
 * 
 * @uses Marcel_Form_Abstract
 * 
 * @package Marcel_Backoffice
 * @subpackage Form
 */
class Marcel_Backoffice_Form_Autodiscover extends Marcel_Form_Abstract {
	/**
	 * Construct form by retrieving metadatase from the table
	 * 
	 * @return array Properties for the form
	 */
	public function _defineOptions() {
		$options = array();
		$metadatas = $this->getRow()->getTable()->info('metadata');
		foreach($metadatas as $elmName => $struct) {
			$options['elements'][$elmName] = array(
				'type' => $this->_getElementFromType($struct),
				'options' => array(
					'label' => $elmName,
					'validators' => $this->_determineValidators($struct),
					'allowEmpty' => $struct['NULLABLE'],
					'required' => !$struct['NULLABLE'],
				),
			);
			$options['elements'][$elmName]['options'] = array_merge($options['elements'][$elmName]['options'], $this->_determineOptions($struct));
		}
		return $options;
	}
	
	/**
	 * Determine additional options for a given datatype
	 * 
	 * @param array $structure Structure for a datatype
	 *
	 * @return array
	 */
	protected function _determineOptions($structure) {
		$type = $structure['DATA_TYPE'];
		$options = array();
		if ($type == 'date' || $type == 'datetime') {
			$options = array(
				'jQueryParams' => array(
					'dateFormat' => 'yy-mm-dd',
				),
       		);
       	}
       	return $options;
	}
	
	/**
	 * Determine wich validators to add depending on a specified SQL field type
	 * 
	 * @param string $structure Field type
	 * 
	 * @return array Array of validators
	 */
	protected function _determineValidatorsByType($structure) {
		$type = $structure['DATA_TYPE'];
		$validators = array();
		if ($type == 'int' || $type == 'bigint') {
			$validators['int'] = array(
				'validator' => 'int',
			);
		}
		return $validators;
	}
	
	/**
	 * Determine wich validators to add depending on structure of the field
	 * 
	 * @param array $structure Structure of the field
	 * 
	 * @see Marcel_Backoffice_Form_Autodiscover::_getElementFromType()
	 * 
	 * @return array Array of validators
	 */
	protected function _determineValidatorsByStructure($structure) {
		$validators = array();
		if ($structure['LENGTH']) {
			$validators['stringLength'] = array(
				'validator' => 'stringLength',
				'options' => array(
					'min' => $structure['NULLABLE'] ? 0 : 1,
					'max' => (int) $structure['LENGTH'],
				),
			);
		}
		if ((stripos($structure['COLUMN_NAME'], 'mail') !== false) && (strpos($this->_getElementFromType($structure), 'text') === 0)) { //if column name contain mail and data type is text/textarea editable
			$validators['emailAddress'] = array(
				'validator' => 'emailAddress',
			);
		}
		if ($structure['DATA_TYPE'] == 'date' || $structure['DATA_TYPE'] == 'datetime') {
			$validators['date'] = array(
				'validator' => 'date',
			);
		}
		return $validators;
	}
	
	/**
	 * Determine wich validators to use for a given structure
	 * 
	 * @param array $structure Structure
	 * 
	 * @see Marcel_Backoffice_Form_Autodiscover::_determineValidatorsByStructure()
	 * @see Marcel_Backoffice_Form_Autodiscover::_determineValidatorsByType()
	 * 
	 * @return array Array of validators
	 */
	protected function _determineValidators($structure) {
		return array_merge(
			$this->_determineValidatorsByStructure($structure), 
			$this->_determineValidatorsByType($structure)
		);
	}
	
	/**
	 * Determine wich HTML element to create depending SQL datatype
	 * 
	 * @param array $structure Array of metadata for the row
	 * 
	 * @return string HTML Element type name
	 */
	protected function _getElementFromType($structure) {
		switch($structure['DATA_TYPE']) {
			case 'bool':
				$element = 'checkbox';
				break;
			case 'mediumtext':
			case 'longtext':
				$element = 'textarea';
				break;
			case 'date':
			case 'datetime':
				$element = 'datePicker';
				break;
			default:
				$element = 'text';
				break;
		}
		return $element;
	}
	
	/**
	 * Append rules at the end of the generation depending on the config file for the backoffice
	 * 
	 * @uses Marcel_Backoffice_Config
	 * 
	 * @see Marcel_Backoffice_Form_Autodiscover::_recursiveDelete()
	 * @see Marcel_Backoffice_Form_Autodiscover::_recursiveMerge()
	 * @see Zend_From::getRow()
	 * 
	 * @param array $options Array of options to append
	 */
	protected function _appendRules($options) {
		$tableName = $this->getRow()->getTable()->info('name');
		$config = Marcel_Backoffice_Config::getInstance()->getConfig();
		if (!isset($config['settings']['formbind']) || !$config['settings']['formbind']) {
			if (isset($config['forms'][$tableName]) || isset($config['forms'][get_class($this)])) {
				$options = isset($config['forms'][$tableName]) ? $config['forms'][$tableName] : $config['forms'][get_class($this)];
			} else {
				$options = array();
			}
			return $options;
		}
		if (isset($config['forms']['delete'][$tableName]) && is_array($config['forms']['delete'][$tableName])) {
			$options = $this->_recursiveDelete($options, $config['forms']['delete'][$tableName]);
		}
		if (isset($config['forms'][$tableName]) || isset($config['forms'][get_class($this)])) {
			$options = $this->_recursiveMerge($options, isset($config['forms'][get_class($this)]) ? $config['forms'][get_class($this)] : $config['forms'][$tableName]);
		}
		return $options;
	}
	
	/**
	 * Recursively delete properties from options
	 * 
	 * @param array $options Options to clean
	 * @param array $config  Backoffice configuration to determine wich options to keep
	 * 
	 * @see Marcel_Backoffice_Form_Autodiscover::_recursiveDelete()
	 * 
	 * @return array Cleaned options
	 */
	protected function _recursiveDelete($options, $config) {
		if (is_array($config)) {
			foreach ($config as $key => $rest) {
				if (!is_array($rest) && isset($options[$key])) {
					unset($options[$key]);
				} else if(is_array($rest) && isset($options[$key])) {
					$options[$key] = $this->_recursiveDelete($options[$key], $rest);
				}
			}
		}
		return $options;
	}
	
	/**
	 * Recursively merge datas from two arrays
	 * 
	 * @param array $Arr1 base array
	 * @param array $Arr2 array to merge into the first recursively
	 * 
	 * @return array Merged array
	 */
	protected function _recursiveMerge($Arr1, $Arr2) {
		foreach($Arr2 as $key => $Value) {
			$Arr1[$key] = (array_key_exists($key, $Arr1) && is_array($Value)) ? $this->_recursiveMerge($Arr1[$key], $Arr2[$key]) : $Value;
		}
		return $Arr1;
	}
}