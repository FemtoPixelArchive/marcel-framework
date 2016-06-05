<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Form
 * @subpackage Element
 * @author Jay MOULIN
 */

/** 
 * @see Zend_Form_Element_File
 */
require_once 'Zend/Form/Element/File.php';

/**
 * File preview element to add a preview for an image for the file input
 * 
 * @uses Zend_Form_Decorator_File
 * 
 * @author Jeremy MOULIN
 */
class Marcel_Form_Element_FilePreview extends Zend_Form_Element_File {
	
	/**
	 * value that can be retrieved from database
	 * 
	 * @var string
	 */
    protected $_setValue = NULL;
    
    /**
     * Load default decorators
     *
     * @return Marcel_Form_Element_FilePreview
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }

        $decorators = $this->getDecorators();
        if (empty($decorators)) {
            $this->addDecorator('FilePreview')
                 ->addDecorator('Errors')
                 ->addDecorator('Description', array('tag' => 'p', 'class' => 'description'))
                 ->addDecorator('HtmlTag', array('tag' => 'dd'))
                 ->addDecorator('Label', array('tag' => 'dt'));
        }
        return $this;
    }
    
    /**
     * Allow to define a value
     * 
     * @param string $value Value to define
     * 
     * @return Marcel_Form_Element_FilePreview
     */
    public function setValue($value) {
		$this->_setValue = $value;
		if ($value) {
			$this->setRequired(false);
		}
		return $this;
    }
    
    /**
     * Retrieved the defined value
     * 
     * @return string
     */
    public function getValue() {
		$value = parent::getValue();
		return $value ? $value : $this->_setValue;
    }
}