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
 * @see Zend_Layout
 */
require_once('Zend/Layout.php');

/**
 * Layout Plugin - allow different layout for each module
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * 
 * @package Marcel_Plugin
 * 
 * @author Jeremy MOULIN
 * 
 */
class Marcel_Plugin_Layout extends Zend_Controller_Plugin_Abstract	{
	/**
	 * @var Zend_Layout instance 
	 */
	private $_layout;
			
	/**
	 * Constructor
	 * 
	 * @uses Zend_Layout
	 */
	public function __construct()	{
		$this->_layout = Zend_Layout::getMvcInstance();
	}
	
	/**
	 * Change default layout path for the module
	 * 
	 * @param Zend_Controller_Request_Abstract $request Request
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)	{
		$module = $request->getModuleName();
		$this->_layout->setLayoutPath(str_replace(':module', $module, $this->_layout->getLayoutPath()));
	}
}
