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
require_once('Zend/View/Helper/Abstract.php');

/**
 * Replace URL in the text for a clickable URL
 * 
 * @uses Zend_View_Helper_Abstract
 * 
 * @package Marcel_View
 * @subpackage Helper
 * 
 * @author Jay MOULIN
 *
 */
class Marcel_View_Helper_ClickableUrl extends Zend_View_Helper_Abstract
{
	/**
	 * Transform URL from a given text to clickable urls
	 * 
	 * @param string $text Text to replace URL to clickable links
	 *
	 * @return string
	 */
	public function clickableUrl($text) {
		return preg_replace('~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~', '<a href="\\0">\\0</a>', $text);
	}
}