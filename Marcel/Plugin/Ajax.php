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
 * @see Zend_Controller_Action_HelperBroker
 */
require_once('Zend/Controller/Action/HelperBroker.php');

/**
 * Ajax Plugin - hide layout if needed
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * 
 * @package Marcel_Plugin
 * 
 * @author Jeremy MOULIN 
 */

class Marcel_Plugin_Ajax extends Zend_Controller_Plugin_Abstract	{
		
	/**
	 * Must render or not the view
	 * @var bool
	 */
	protected $_noRender = false;
	/**
	 * Must render or not the layout
	 * @var bool
	 */
	protected $_ajaxRender = false;	
	
	/**
	 * Check if request is ajax or requested as ajax
	 * 
	 * @param Zend_Controller_Request_Abstract $request Request
	 * 
	 * @uses Zend_Controller_Action_HelperBroker
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request)	{
		if (($request->isXmlHttpRequest() || $request->getParam('format', false) == 'ajax') && $request->getParam('format', false) != 'customjson') {
			$this->_ajaxRender = $request->isXmlHttpRequest() ? 'xml' : 'ajax';
			//disable layout
			Zend_Controller_Action_HelperBroker::getStaticHelper('layout')->disableLayout();
			
			//act like context switcher but wrap to avoid context declaration in each controller
			if ($request->getParam('format', false) == 'json') {
				$this->_noRender = true;
				Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->setNoRender(true);
			}
			$request->setParam('format', false);
		}
	}
	
	/**
	 * When dispatched treat the layout/view hide if needed
	 * 
	 * @param Zend_Controller_Request_Abstract $request Request
	 * 
	 * @uses Zend_Controller_Action_HelperBroker
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request)	{
		if ($this->_ajaxRender) { //if ajax request
			Zend_Controller_Action_HelperBroker::getStaticHelper('layout')->disableLayout(true);
			if ($this->_ajaxRender == 'ajax') {
				$this->_response->setBody(Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view->headTitle() . $this->_response->getBody()); 
			}
			if ($this->_noRender) { //if must display json rendering
				$this->_response
					->setHeader('Content-Type', 'application/json')
					->setBody(Zend_Json::encode(Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view));
			}
		}
	}
	
	/**
	 * Return true if json is output
	 *
	 * @return bool
	 */
	public function isJson() {
		return $this->_noRender ? true : false;
	}
}
