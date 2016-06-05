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
 * @see Zend_Controller_Front
 */
require_once('Zend/Controller/Front.php');
/**
 * @see Zend_Session
 */
require_once('Zend/Session.php');
/**
 * @see Zend_Auth
 */
require_once('Zend/Auth.php');
/**
 * @see Marcel_Acl
 */
require_once('Marcel/Acl.php');
/**
 * @see Zend_Registry
 */
require_once('Zend/Registry.php');


/**
 * Authentication plugin
 * 
 * @package Marcel_Plugin
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Plugin_Auth extends Zend_Controller_Plugin_Abstract {
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
		if (isset($_COOKIE['Zend_Auth_RememberMe']) && $_COOKIE['Zend_Auth_RememberMe']) {
			setcookie("Zend_Auth_RememberMe", 0, time() - 1000, '/');
			Zend_Session::rememberMe(60 * 60 * 24 * 7); //7 days
		}
		
		$this->_auth = Zend_Auth::getInstance();
		$this->_acl = new Marcel_Acl;
		
				// is the user authenticated
		if ($this->_auth->hasIdentity()) {
		  // yes ! we get his role
		  $user = $this->_auth->getIdentity();
		  $role = $user->usr_privilege;
		} else {
		  // no = guest user
		  $role = 'guest';
		}
		
		$module 	= $request->getModuleName() ;
		$controller = $request->getControllerName() ;
		$action     = $request->getActionName() ;
		
		$front = Zend_Controller_Front::getInstance() ;
		$default = $front->getDefaultModule() ;
		
		// compose le nom de la ressource
		if ($module == $default)	{
			$resource = $controller;
		} else {
			$resource = $module.'_'.$controller ;
		}
    
    	// est-ce que la ressource existe ?
		if (!$this->_acl->has($resource)) {
			return;
		}
		
		// controle si l'utilisateur est autoris�
		if (!$this->_acl->isAllowed($role, $resource, $action) && $resource !== null) {
			// l'utilisateur n'est pas autoris� � acceder � cette ressource
			// on va le rediriger
			$authModuleLogin = Zend_Registry::getInstance()->config['auth'];
			if (isset(Zend_Registry::getInstance()->config[$module]['auth']['login'])) {
				$authModuleLogin = Zend_Registry::getInstance()->config[$module]['auth']['login'];
			}
			$authModuleRights = Zend_Registry::getInstance()->config['auth'];
			if (isset(Zend_Registry::getInstance()->config[$module]['auth']['rights'])) {
				$authModuleRights = Zend_Registry::getInstance()->config[$module]['auth']['rights'];
			}
			
			if (!$this->_auth->hasIdentity()) {
				// il n'est pas identifi� -> module de login
				$module = $authModuleLogin['module'];
				$controller = $authModuleLogin['controller'];
				$action = $authModuleLogin['action'];
			} else {
				// il est identifi� -> error de privil�ges
				$module = $authModuleRights['module'];
				$controller = $authModuleRights['controller'];
				$action = $authModuleRights['action'];
			}
		}

		$request->setModuleName($module) ;
		$request->setControllerName($controller) ;
		$request->setActionName($action) ;
	}
}
