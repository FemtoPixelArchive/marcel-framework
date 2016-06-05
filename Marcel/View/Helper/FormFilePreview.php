<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_View
 * @subpackage Helper
 * @author Jeremy MOULIN
 */

/**
 * @see Zend_View_Helper_Abstract
 */
require_once('Zend/View/Helper/FormFile.php');

/**
 * File Preview view helper
 * 
 * @uses Zend_View_Helper_Abstract
 * 
 * @package Marcel_View
 * @subpackage Helper
 * 
 * @author Jay MOULIN
 *
 */
class Marcel_View_Helper_FormFilePreview extends Zend_View_Helper_FormFile
{
	/**
	 * Displays the file preview element
	 * 
	 * @param string                     $name    Name of the element
	 * @param Zend_Form_Element_Abstract $element Element to display
	 * @param array                      $attribs Attribute to configure the element
	 *
	 * @see Zend_View_Helper_FormFile
	 * 
	 * @return string
	 */
	public function formFilePreview($name, $element, $attribs = null) {
		$position = (isset($attribs['render']['placement']) && strtolower($attribs['render']['placement']) == 'prepend') ? 'prepend' : 'append';
		$imgSrc = ($element->getValue() ? (isset($attribs['render']['base']) && $attribs['render']['base'] ? $attribs['render']['base'] : $this->view->baseUrl()) . '/' . $element->getValue() : $this->view->baseUrl() . '/images/back/default.png');
		$preview = "<img src='$imgSrc' style='width:180px;height:75px;' " . $this->getClosingBracket();
		$deleteLink = '';
		$return = ($position == 'prepend' ? $preview : '') . $this->formFile($name, $attribs) . ($position == 'append' ? $preview : '');
		return $return;
	}
}