<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Form
 * @author Jay MOULIN
 */

/**
 * @see Zend_Form
 */
require_once('Zend/Form.php');

/**
 * @see ZendX_JQuery
 */
require_once('ZendX/JQuery.php');
/**
 * @see Zend_Registry
 */
require_once('Zend/Registry.php');
/**
 * @see Zend_Exception
 */
require_once('Zend/Exception.php');

/**
 * Abstract class for forms
 * 
 * @package Marcel_Form
 * 
 * @uses Zend_Form
 * 
 * @package Marcel_Form
 * 
 * @author Jeremy MOULIN
 *
 */
abstract class Marcel_Form_Abstract extends Zend_Form {

	/**
	 * Representztion for required fields
	 * @var string
	 */
	const REQUIRED_SUFFIX = " *";
	/**
	 * Disable the submit button
	 * @var bool
	 */
	protected $_disableSubmit = false;

	/**
	 * Instance for the parent Entity
	 * @var Marcel_Db_Table_Row_Abstract
	 */
	protected $_parent = NULL;

	/**
	 * Define Special for fields - Must be redefined
	 * 
	 * @return Array Elements definition for the form
	 */
	abstract protected function _defineOptions();

	/**
	 * Set the configuration for the form
	 * 
	 * @return Array Defaults params of this form
	 */
	protected function _getDefaultConfigs() {
		$config = array();
			
		if (!$this->_disableSubmit) {
			$config['elements']['submit'] = array(
        		'type' => 'submit' ,
        		'options' => array(
        			'label' => tr('Save'),
					'decorators'	=> array(
						'ViewHelper'	=> array(
							'decorator'	=> 'ViewHelper',
							'options'	=> array(),
						),
						'HtmlTag'	=> array(
							'decorator'	=> 'HtmlTag',
							'options'	=> array(
								'tag' => 'fieldset',
								'class' => 'fieldset_submit',
							),
						),
					),
				)
			);
		}

		$this->_attribs['id'] = get_class($this);
		
		return ($config);
	}
	/**
	 * Initializer
	 *    Adds the submit elements and prepare the use of curstom validators/elements/decorators
	 *    
	 * @uses ZendX_JQuery
	 * @uses Zend_Form_Element
	 * @uses Zend_Registry
	 * 
	 * @see Zend_Form_Abstract::addElementPrefixPath
	 * @see Zend_Form_Abstract::addPrefixPath
	 * @see Zend_Form_Abstract::setOptions
	 * @see Zend_Form_Abstract::setAction
	 * @see Zend_Form_Abstract::setEnctype
	 * @see Zend_Form_Abstract::setMethod
	 * @see Marcel_Form_Abstract::_getDefaultConfigs
	 * @see Marcel_Form_Abstract::_setRandomElementId
	 *  
	 * @return Marcel_Form_Abstract
	 */
	public function init() {
		ZendX_JQuery::enableForm($this);

		$options = $this->_defineOptions();
		
		$namespace = ucfirst(Zend_Registry::getInstance()->config['appname']);

		$this->addElementPrefixPath($namespace . '_Validate', $namespace . '/Validate', Zend_Form_Element::VALIDATE)
				->addElementPrefixPath('Marcel_Validate', 'Marcel/Validate', Zend_Form_Element::VALIDATE)
				->addElementPrefixPath($namespace . '_Filter', $namespace . '/Filter', Zend_Form_Element::FILTER)
				->addElementPrefixPath('Marcel_Filter', 'Marcel/Filter', Zend_Form_Element::FILTER)
				->addPrefixPath($namespace . '_Form', $namespace . '/Form')
				->addPrefixPath('Marcel_Form', 'Marcel/Form')
				->addPrefixPath('Marcel_Backoffice_Form', 'Marcel/Backoffice/Form');
				

				
		/* in order to add some validators in your forms, you have to create treeview as :
		 * <code>
		 *		'password' => array (
		 *			'type' => 'password',
		 *			'options' => array (
		 *				'label' => 'password',
		 * 				'required' => true,
		 * 				'validators' => array (
		 * 					'passwordConfirmation' => array (
		 * 						'validator' => 'passwordConfirmation',
		 * 						'options' => array(
		 * 							'compareFieldName' => 'test',
		 *						),
		 *					),
		 *				),
		 *			),
		 *		),
		 *		'test' => array (
		 *			'type' => 'password',
		 *			'options' => array (
		 *				'label' => 'password confirm',
		 *				'required' => true,
		 *			),
		 *		),
		 *		'selectbox' => array(
		 *			'type' => 'select',
		 *			'options' => array(
		 *				'label' => 'test',
		 *				'required' => true,
		 *				'multiOptions' => Publicis_Model::factory('job')->getPairs(),
		 *			),
		 *		),
		 * </code>
		 */

		$config = $this->_getDefaultConfigs();

		$config = $this->_appendRules(array_merge_recursive($options, $config));

		$this->setOptions($config)
				->setAction('#')
				->setEnctype('multipart/form-data')
				->setMethod('post')
				->_setRandomElementId();

				
		return ($this);
	}

	/**
	 * Define the parent row to link the form
	 *
	 * @param object $item Item to link form to that have the 'save' method
	 * 
	 * @throws Zend_Exception if item does not have save method
	 *
	 * @return Marcel_Form_Abstract
	 */
	public function setRow($item) {
		if (!method_exists($item, 'save')) {
			throw new Zend_Exception('Your item must have the "save" method');
		}
		$this->_parent = $item;
		return $this;
	}

	/**
	 * Retrieve the instance of the parent
	 *
	 * @return object
	 */
	public function getRow() {
		return $this->_parent;
	}
	
	/**
	 * Validate with optional params
	 *
	 * @param array $params Values to validate
	 *
	 * @return bool
	 */
	public function isValid($params) {
		return parent::isValid($params === NULL ? $this->getValues() : $params);
	}

	/**
	 * Save the values if everything is valid
	 *
	 * @param bool $suppressArrayNotation Suppress array notation if needed
	 * @param bool $saveEvenIfNotValid    Save datas even if form is not valid
	 *
	 * @see Zend_Form::getValues
	 * @see Zend_Form::isValid
	 * @see Marcel_Form_Abstract::getRow
	 * 
	 * @return mixed The primary key value(s), as an associative array if the
     *     key is compound, or a scalar if the key is single-column.
	 */
	public function save($suppressArrayNotation = false, $saveEvenIfNotValid = false) {
		$values = $this->getValues($suppressArrayNotation);
	    if (!$this->isValid($values)) {
	        if (!$saveEvenIfNotValid) {
				return false;
			}
	    }
	    $result = $this->getRow()
						->setFromArray($values)
						->save();
						
		return ($result);
	}

	/**
	 * Disable the submit button
	 * 
	 * @see Zend_Form::removeElement
	 * 
	 * @param bool $bool Delete the button ?
	 * 
	 * @return Marcel_Form_Abstract
	 */
	public function disableSubmit($bool = true) {
		$this->_disableSubmit = (bool) $bool;
		$this->removeElement('submit');
		return $this;
	}
	/**
	 * Check if submit button is disabled
	 * 
	 * @return bool
	 */
	public function isDisabledSubmit() {
		return $this->_disableSubmit;
	}


	/**
	 * Set the name of the form in a hidden field. Allows identification of forms when you have two or more forms in the same page.
	 * 
	 * @param string $name Name of this form.
	 * 
	 * @see Zend_Form::setName
	 * @see Zend_Form::addElement
	 * 
	 * @uses Zend_Form_Element_Hidden
	 * 
	 * @throws Exception if unable to add the formname hidden element
	 *
	 * @return Marcel_Form_Abstract this object
	 */
	public function setName($name)
	{
        parent::setName($name);
        require_once('Zend/Form/Element/Hidden.php');
        $this->addElement(new Zend_Form_Element_Hidden('formname', array('value' => $this->getName())));
        return ($this);
	}

	/**
	 * Define the required suffix
	 * 
	 * @see Zend_Form::getElements
	 * 
	 * @return Marcel_Form_Abstract
	 */
	protected function _setRequiredSuffix()
	{

		foreach ($this->getElements() as $element)
		{
			$decorator = $element->getDecorator("Label");
			if ($decorator) {
				$decorator->setRequiredSuffix(self::REQUIRED_SUFFIX);
			}
		}

		return ($this);
	}
	
	/**
	 * Define the random element id
	 * 
	 * @see Zend_Form::getElements
	 * 
	 * @return Marcel_Form_Abstract
	 */
	protected function _setRandomElementId() {
		foreach ($this->getElements() as $element) {
			$element->setAttrib('id', uniqid($element->getFullyQualifiedName()));
		}
		
		return ($this);
	}

	/**
	 * Apply some rules when the array definition is done
	 *
	 * @param array $config definition of the form
	 *
	 * @return array New form structure
	 */
	protected function _appendRules($config) {
		return $config;
	}
}