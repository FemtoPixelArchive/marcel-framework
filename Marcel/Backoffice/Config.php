<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @author Jeremy MOULIN
 */

/**
 * Singleton for backoffice configuration
 * 
 * @package Marcel_Backoffice
 * 
 * @author Jeremy MOULIN
 *
 */
final class Marcel_Backoffice_Config {
	/**
	 * Default path to the file
	 * @var string
	 */
	protected $_filePath = '/application/backdefinition.ini';
	/**
	 * Configuration retrieved by the file to array
	 * @var array
	 */
	protected $_file = array();
	/**
	 * Current instance - singleton
	 * @var Marcel_Backoffice_Config
	 */
	static protected $_instance = NULL;
	/**
	 * protected constructor - Singleton design pattern
	 * Loads the configuration
	 * 
	 * @uses Zend_Config_Ini
	 */
	protected function __construct() {
		require_once('Zend/Config/Ini.php');
		$file = new Zend_Config_Ini(ROOT_DIR . $this->_filePath);
		$this->_file = $file->toArray();
	}
	/**
	 * No sleep - Singleton
	 */
	protected function __sleep() {
	}
	/**
	 * No wakeup - Singleton
	 */
	protected function __wakeup() {
	}
	/**
	 * no clone - Singleton
	 */
	protected function __clone() {
	}
	/**
	 * Retrive the specified instance - singleton
	 */
	static public function getInstance() {
		if (!self::$_instance instanceof self) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}
	
	/**
	 * Retrieve the configuration as array
	 * @return array
	 */
	public function getConfig() {
		return $this->_file;
	}
}