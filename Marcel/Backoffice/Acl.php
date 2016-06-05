<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @author Jeremy MOULIN
 */

/**
 * @see Marcel_Acl
 */
require_once('Marcel/Acl.php');

/**
 * @see Zend_Auth_Adapter_Interface
 */
require_once('Zend/Auth/Adapter/Interface.php');

/**
 * Class to manage ACL for the backoffice
 * 
 * @package Marcel_Backoffice
 * 
 * @uses Marcel_Acl
 * @uses Zend_Auth_Adapter_Interface
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Backoffice_Acl extends Marcel_Acl implements Zend_Auth_Adapter_Interface {
	/**
	 * File to load for acl
	 * @var string
	 */
	protected $_filePath = "/application/backacl.ini";
	/**
	 * Current login
	 * @var string
	 */
	protected $_login = null;
	/**
	 * Current password
	 * @var string
	 */
	protected $_password = null;
	
	/**
	 * Retrieve the user for a given login/password
	 * 
	 * @param string $user     Login
	 * @param string $password Password
	 * 
	 * @throws Exception if user does not exists
	 * @throws Exception if password is not correct
	 * 
	 * @return array User informations
	 */
	public function getUser($user, $password) {
		if (!isset($this->_file['users'][$user]) || !isset($this->_file['users'][$user]['password']) || !isset($this->_file['users'][$user]['role'])) {
			throw new Exception('User does not exists', 1);
		}
		if ($this->_file['users'][$user]['password'] != $password) {
			throw new Exception('Password is not correct', 2);
		}
		return $this->_file['users'][$user];
	}
	
	/**
	 * Define user login/password
	 * 
	 * @param string $login Login
	 * @param string $pass  Password
	 * 
	 * @return Marcel_Backoffice_Acl
	 */
	public function login($login, $pass) {
		$this->_login = $login;
		$this->_password = $pass;
		return $this;
	}
	
	/**
	 * @see Zend_Auth_Adapter_Interface::authenticate()
	 */
	public function authenticate() {
		try {
			return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->getUser($this->_login, $this->_password));
		} catch(Exception $e) {
			return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, array());
		}
	}
}