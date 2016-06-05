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
 * Expire Plugin
 * Custom expire header from config file
 *
 * @package Marcel_Plugin
 * 
 * @uses Zend_Controller_Plugin_Abstract
 *
 * @author Jeremy MOULIN
 *
 */
class Marcel_Plugin_Expire extends Zend_Controller_Plugin_Abstract	{

	/**
	 * Singleton instance
	 * @var Marcel_Plugin_Expire
	 */
	protected static $_instance = null;
	
	/**
	 * Configuration
	 * @var array|null
	 */
	protected $_config = null;
	/**
	 * Default value for cache expire
	 * @var int
	 */
	protected $_default = 7200;	// Valeur par défaut : 2 heures
	
	/**
	 * Define configuration
	 * 
	 * @param array|Zend_Config_Ini $config configuration
	 * 
	 * @throws Exception if config is not ok
	 * 
	 * @return Marcel_Plugin_Expire
	 */
	public function setConfig($config = array()) {
		if ($config instanceof Zend_Config_Ini) {
			$config = $config->toArray();
		}
		if (!is_array($config)) {
			throw new Exception("Config must be an array.");
		}
		if (array_key_exists('common', $config)) {
			$this->_default = $config['common'];
		}
		if (array_key_exists('expiretime', $config)) {
			$this->_config = $config['expiretime'];
		}		
		return ($this);
	}
	
	/**
	 * Retrieve instance of Plugin
	 * 
	 * @return Marcel_Plugin_Expire
	 */
	public static function getInstance() {
		if (!self::$_instance) {
			self::$_instance = new self;
		}
		return (self::$_instance);
	}
	
	/**
	 * Define the default configuration
	 * 
	 * @uses Zend_Config_Ini
	 * 
	 * @see Marcel_Plugin_Expire::setConfig
	 * 
	 * @return Marcel_Plugin_Expire
	 */
	protected function _defaultConfig() {
		$file = ROOT_DIR . "/application/expire.ini";
		require_once('Zend/Config/Ini.php');
		$expiration = new Zend_Config_Ini($file, 'expire');
		$this->setConfig($expiration->toArray());
		return $this;
	}
	
	/**
	 * Return the expiration time for requested action
	 * 
	 * @param string $module     Module of requested action
	 * @param string $controller Controller of requested action
	 * @param string $action     Action of requested action
	 * 
	 * @see Marcel_Plugin_Expire::_defaultConfig
	 * 
	 * @return int
	 */
	protected function _getExpirationTime($module, $controller, $action) {
		if (!$this->_config) {
			$this->_defaultConfig();
		}
		if (isset($this->_config[$module][$controller][$action]['value'])) {
			return ($this->_config[$module][$controller][$action]['value']);
		}
		if (isset($this->_config[$module][$controller]['value'])) {
			return ($this->_config[$module][$controller]['value']);
		}
		if (isset($this->_config[$module]['value'])) {
			return ($this->_config[$module]['value']);
		}
		return ($this->_default);
	}
	
	/**
	 * Define headers (can be overridden in action if needed)
	 * 
	 * @param Zend_Controller_Request_Abstract $request Request
	 * 
	 * @see Marcel_Plugin_Expire::_getExpirationTime
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$time = $this->_getExpirationTime($request->getModuleName(), $request->getControllerName(), $request->getActionName());
		$this->_response
				->setHeader('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + $time), true)
				->setHeader('Pragma', $time ? 'public' : 'no-cache, no-store, must-revalidate', true)
				->setHeader('Cache-Control', $time ? 'maxage=' . $time : 'no-cache, no-store, must-revalidate, maxage=0', true);
	}
	
	/**
	 * Send header if can send
	 * 
	 * @param Zend_Controller_Request_Abstract $request Request
	 */
	public function postDispatch(Zend_Controller_Request_Abstract $request) {
		if (!$this->_response->canSendHeaders(false)) {
			$this->_response->clearAllHeaders();
		}
	}
}