<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel
 * @author Jeremy MOULIN
 */

/**
 * @see Zend_Acl
 */
require_once('Zend/Acl.php');
/**
 * @see Zend_Registry
 */
require_once('Zend/Registry.php');
/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once('Zend/Controller/Action/HelperBroker.php');

/**
 * Management for users
 * 
 * @uses Zend_Acl
 * 
 * @package Marcel
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Acl extends Zend_Acl {
	/**
	 * Instances for ACL
	 * @var Marcel_Acl
	 */
	protected static $_instance = NULL;
	
	/**
	 * File to load for acl
	 * @var string
	 */
	protected $_filePath = "/application/acl.ini";
	
	/**
	 * Structure for ACL configuration
	 * @var array
	 */
	protected $_file = array();
	
	/**
	 * Loads ini file to determine wich profile can access wich action
	 * 
	 * @uses Zend_Registry
	 * @uses Zend_Config_Ini
	 * @uses Zend_Controller_Action_HelperBroker
	 * 
	 * @param string $filename Filename to load for ACL
	 *
	 * @see Marcel_Acl::_setRoles
	 * @see Marcel_Acl::_setResources
	 * @see Marcel_Acl::_setPrivileges
	 */
	public function __construct($filename = NULL) {
		$this->_file = NULL;
		$cacheKey = 'aclCacheFile';
		if (!is_array($filename) && $filename !== NULL) {
			$this->setFile($filename);
		} elseif (is_array($filename)) {
			$this->_file = $filename;
			if (Zend_Registry::getInstance()->config['cache']['active']) {
				$file = Zend_Controller_Action_HelperBroker::getStaticHelper('cache')->getCache('apc')->load($cacheKey);
			}
		}
		if (!$this->_file) {
			require_once('Zend/Config/Ini.php');
			$this->_file = new Zend_Config_Ini(ROOT_DIR . $this->getFilePath());
			$this->_file = $this->_file->toArray();
			if (isset(Zend_Registry::getInstance()->config['cache']['active']) && Zend_Registry::getInstance()->config['cache']['active']) {
				Zend_Controller_Action_HelperBroker::getStaticHelper('cache')->getCache('apc')->save($this->_file, $cacheKey);
			}
		}
		$roles = $this->_file['roles'];
		$this->_setRoles($roles);
		$this->_setResources($this->_file['resources']);

		foreach ($roles as $role => $parents) {
			$privileges = $this->_file[$role];
			$this->_setPrivileges($role, $privileges);
		}
	}
	
	/**
	 * Defines profiles
	 *
	 * @param $roles array Roles to define
	 * 
	 * @uses Zend_Acl_Role
	 *
	 * @return Marcel_Acl
	 */
	protected function _setRoles($roles) {
		require_once('Zend/Acl/Role.php');
		foreach ($roles as $role => $parents) {
			if (empty($parents))	{
				$parents = null ;
			} else {
				$parents = explode(',', $parents) ;
			}
			$this->addRole(new Zend_Acl_Role($role), $parents);
		}

		return $this ;
	}

	/**
	 * Define Resources
	 *
	 * @param $resources array Ressources to define
	 *
	 * @uses Zend_Acl_Resource
	 *
	 * @return Marcel_Acl
	 */
	protected function _setResources($resources) {
		require_once('Zend/Acl/Resource.php');
		foreach ($resources as $resource => $parents) {
			if (empty($parents))	{
				$parents = null ;
			} else {
				$parents = explode(',', $parents) ;
			}
			$this->add(new Zend_Acl_Resource($resource), $parents);
		}

		return $this ;
	}

	/**
	 * Define rights
	 *
	 * @param $roles array Rights to define
	 *
	 * @return Marcel_Acl
	 */
	protected function _setPrivileges($role, $privileges) {
		foreach ($privileges as $do => $resources) {
			foreach ($resources as $resource => $actions) {
				if (empty($actions)) {
					$actions = null ;
				} else {
					$actions = explode(',', $actions) ;
				}

				$this->{$do}($role, $resource, $actions);
			}
		}

		return $this ;
	}
	
	/**
	 * Retrieve filename for ACL
	 *
	 * @return string
	 */
	public function getFilePath() {
		return $this->_filePath;
	}
	
	/**
	 * Define the ACL file
	 *
	 * @param string $file Path to the ACL ini file
	 *
	 * @return Marcel_Acl
	 */
	public function setFilePath($file) {
		$this->_filePath = $file;
		return $this;
	}
	
	/**
	 * Retrieve the file informations
	 *
	 * @return array
	 */
	public function getFile() {
		return $this->_file;
	}
}