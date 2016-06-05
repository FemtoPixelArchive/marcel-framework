<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Plugin
 * @author Jeremy MOULIN
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once('Zend/Controller/Plugin/Abstract.php');

/**
 * Encoding Plugin - corrects encoding problem to convert every input to utf-8
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * 
 * @package Marcel_Plugin
 * 
 * @author Jeremy MOULIN 
 */

class Marcel_Plugin_Encoding extends Zend_Controller_Plugin_Abstract	{
	
	/**
	 * Will catch all input datas and force encoding to utf-8
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 * 
	 * @see Marcel_Plugin_Encoding::_recursiveTransform()
	 */
	public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
		$request->setParams($this->_recursiveTransform($request->getParams()));
	}
	
	/**
	 * Transform recursively all inputed datas to UTF-8
	 *
	 * @param array $param Array containing informations to convert
	 *
	 * @see Marcel_Plugin_Encoding::_recursiveTransform
	 * @see Marcel_Plugin_Encoding::_forceUtf8
	 *
	 * @return array converted data
	 */
	protected function _recursiveTransform($param) {
		foreach($param as $name => $else) {
			if (is_array($else)) {
				$param[$name] = $this->_recursiveTransform($else);
			} else {
				$param[$name] = $this->_forceUtf8($else);
			}
		}
		return $param;
	}
	
	/**
	 * Force input to utf8
	 *
	 * @param string $string String to encode
	 *
	 * @return string Encoded string
	 */
	protected function _forceUtf8($string) {
		return (utf8_encode(utf8_decode($string)) == $string ? $string : utf8_encode($string));
	}
}
