<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_View
 * @subpackage Helper
 * @author Edgar LACOUTURE
 */

/**
 * @see Zend_View_Helper_Abstract
 */
require_once('Zend/View/Helper/Abstract.php');

/**
 * Truncate a string
 * 
 * @uses Zend_View_Helper_Abstract
 * 
 * @package Marcel_View
 * @subpackage Helper
 * 
 * @author Edgar LACOUTURE
 *
 */
class Marcel_View_Helper_TruncateString extends Zend_View_Helper_Abstract
{
	/**
	 * Truncate a given text
	 * 
	 * @param string	$text 			Text to truncate
	 * @param int 		$start			The first position used in string
	 * @param int 		$maxSize		Maximum size of the string
	 * @param string 	$encoding 	Encoding of the string
	 * @param string 	$end 				Text to add at the end of the truncate string
	 *
	 * @return string
	 */
	public function truncateString($text, $start = 0, $maxSize = 50, $encoding = 'UTF-8', $end = '...') {
		if (strlen($text) > $maxSize) {
			return mb_substr($text, $start, $maxSize, $encoding) . $end;
		}
		return $text;
	}
}