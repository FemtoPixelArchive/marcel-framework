<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @subpackage Plugin
 * @author Jeremy MOULIN
 */

/**
 * @see Zend_Controller_Plugin_Abstract
 */
require_once('Zend/Controller/Plugin/Abstract.php');
/**
 * @see Zend_Auth
 */
require_once('Zend/Auth.php');
/**
 * @see Zend_Session
 */
require_once('Zend/Session.php');
/**
 * @see Zend_Controller_Front
 */
require_once('Zend/Controller/Front.php');
/**
 * @see Marcel_Backoffice_Acl
 */
require_once('Marcel/Backoffice/Acl.php');
/**
 * @see Marcel_Backoffice_Config
 */
require_once('Marcel/Backoffice/Config.php');


/**
 * Authentication plugin
 * 
 * @package Marcel_Backoffice
 *
 * @subpackage Plugin
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Backoffice_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
	/**
	 * @var Zend_Auth instance 
	 */
	private $_auth;
	
	/**
	 * @var Zend_Acl instance 
	 */
	private $_acl;
	
	/**
	 * (non-PHPdoc)
	 * @see library/Zend/Controller/Plugin/Zend_Controller_Plugin_Abstract#preDispatch()
	 * 
	 * @uses Zend_Session
	 * @uses Zend_Auth
	 * @uses Marcel_Acl
	 * @uses Zend_Controller_Front
	 * @uses Zend_Registry
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		if ($request->getModuleName() != 'back') {
			return;
		}
		if (isset($_COOKIE['Zend_Auth_RememberMe_Bo']) && $_COOKIE['Zend_Auth_RememberMe_Bo']) {
			setcookie("Zend_Auth_RememberMe_Bo", 0, time() - 1000, '/');
			Zend_Session::rememberMe(60 * 60 * 24 * 7); //7 days
		}
		
		$this->_auth = Zend_Auth::getInstance();
		$this->_acl  = new Marcel_Backoffice_Acl;
		$this->_config = Marcel_Backoffice_Config::getInstance()->getConfig();
		
		// is the user authenticated
		if ($this->_auth->hasIdentity()) {
		  // yes ! we get his role
		  $user = $this->_auth->getIdentity();
		  $role = $user['role'];
		} else {
		  // no = guest user
		  $role = 'guest';
		}
		
		$controller = $request->getControllerName() ;
		$action     = $request->getActionName() ;
		
		$front = Zend_Controller_Front::getInstance() ;
		$default = $front->getDefaultModule() ;
		
		$resource = $controller . '_' . $action;
		
    	// est-ce que la ressource existe ?
		if (!$this->_acl->has($resource)) {
			$request->setControllerName('index') ;
			$request->setActionName('login') ;
			return;
		}
		// controle si l'utilisateur est autoris�
		if (!$this->_acl->isAllowed($role, $resource, $request->getParam('model')) && $resource !== null) {
			$controller = 'index';
			$action = 'right';
		}

		$request->setControllerName($controller) ;
		$request->setActionName($action) ;
	}
	
	public function checkRight($array, $eraser = false) {
		// is the user authenticated
		if ($this->_auth->hasIdentity()) {
		  // yes ! we get his role
		  $user = $this->_auth->getIdentity();
		  $role = $user['role'];
		} else {
		  // no = guest user
		  $role = 'guest';
		}
		
		$controller = isset($array['controller']) ? $array['controller'] : ($eraser ? 'default' : $this->_request->getControllerName());
		$action     = isset($array['action']) ? $array['action'] : ($eraser ? 'index' : $this->_request->getActionName());
		$model = isset($array['model']) ? $array['model'] : ($eraser ? NULL : $this->_request->getParam('model'));
		
		$resource = $controller . '_' . $action;
    
    	// est-ce que la ressource existe ?
		if (!$this->_acl->has($resource)) {
			return false;
		}
		// controle si l'utilisateur est autoris�
		if (!$this->_acl->isAllowed($role, $resource, $model) && $resource !== null) {
			return false;
		}
		return true;
	}
}
