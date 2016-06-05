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
 * Staticfier Plugin - move resources to a static URL to gain for performances
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * 
 * @package Marcel_Plugin
 * 
 * @author Jeremy MOULIN 
 */

class Marcel_Plugin_Staticfier extends Zend_Controller_Plugin_Abstract	{
	
	/**
	 * Check if plugin is enabled
	 * @var bool
	 */
	protected $_enabled = true;
	
	/**
	 * Number of static instances
	 * @var bool
	 */
	protected $_statics = 3;
	
	/**
	 * Prefix of static instances
	 * @var bool
	 */
	protected $_staticPrefix = 'static';
	/**
	 * Suffix of static instances
	 * @var bool
	 */
	protected $_staticSuffix = '';
	/**
	 * Subdomain to replace
	 * @var string
	 */
	protected $_subdomain = 'www';
	/**
	 * Domain to replace
	 * @var string
	 */
	protected $_domain = NULL;
	
	/**
	 * Current asset
	 * @var int
	 */
	protected $_currentAsset = 1;
	
	/**
	 * Will catch all resources URL
	 * 
	 * @see Marcel_Plugin_Staticfier::_computeAsset
	 * 
	 * @param Zend_Controller_Request_Abstract $request
	 */
	public function dispatchLoopShutdown(Zend_Controller_Request_Abstract $request = NULL) {
		if ($this->_domain === NULL) {
			$this->_domain = 'http://' . $_SERVER['HTTP_HOST'];
		}
		$isJson = false;
		if ($this->_enabled) {
			$response = $this->getResponse()->getBody();
			$oldresponse = $response;
			foreach ($this->getResponse()->getHeaders() as $item) {
				if ($item['name'] == 'Content-Type' && $item['value'] == 'application/json') {
					$isJson = true;
				}
			}
			if ($isJson) {
				$response = stripslashes($response);
			}
			preg_match_all('~src="?\'?([^"\']+)"?\'?~', $response, $globb);
			$values = array();
			foreach ($globb[1] as $key => $string) {
				$values[crc32($string)] = $string;
			}
			$response = $oldresponse;
			foreach ($values as $value) {
				$response = str_replace($value, $this->_computeAsset($value, $isJson), $response);
			}
			$this->getResponse()->setBody($response);
		}
	}
	
	/**
	 * Compute wich asset for a path and return the full path
	 * 
	 * @param string $path
	 * @param bool   $isJson Check if url should be encoded
	 * 
	 * @return string
	 */
	protected function _computeAsset($path, $isJson = false) {
		if (strpos(strtolower($path), 'http://') === false) {
			$path = $this->_domain . $path;
		} else {
			return $path;
		}
		$path = str_replace($this->_subdomain, $this->_staticPrefix . $this->_currentAsset++ . $this->_staticSuffix, $path);
		if ($this->_currentAsset > $this->_statics) {
			$this->_currentAsset = 1;
		}
		if ($isJson) {
			$path = str_replace('/', '\/', $path);
		}
		return $path;
	}
	
	/**
	 * Disable plugin if needed
	 *
	 * @return Marcel_Plugin_Staticfier
	 */
	public function disablePlugin($bool = true) {
		$this->_enabled = !$bool;
		return $this;
	}
}
