<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Backoffice
 * @subpackage Db
 * @author Jeremy MOULIN
 */

/**
 * @see Marcel_Db_Table_Row_Abstract
 */
require_once('Marcel/Db/Table/Row/Abstract.php');

/**
 * Instance of row when autodiscovering the table
 * 
 * @package Marcel_Backoffice
 * @subpackage Db
 * 
 * @author Jeremy MOULIN
 *
 * @uses Marcel_Db_Table_Row_Abstract
 */
class Marcel_Backoffice_Db_Table_Row_Autodiscover extends Marcel_Db_Table_Row_Abstract {
	/**
	 * Retrieve the associated form
	 * 
	 * @param array $options Options for the associated form
	 * 
	 * @throws DomainException If form object does not exist
	 * @throws DomainException If form object is not instance of Marcel_Form_Abstract
	 * 
	 * @uses Marcel_Form_Abstract
	 * @uses Marcel_Backoffice_Form_Autodiscover
	 * 
	 * @return Marcel_Form_Abstract
	 */
	public function getForm($options = NULL) {
		try {
			$this->_instance = parent::getForm($options);
		} catch (DomainException $e) {
			if ($e->getCode() == 1) {
				if (!$this->_instance) {
					require_once('Marcel/Backoffice/Form/Autodiscover.php');
					$this->_instance = new Marcel_Backoffice_Form_Autodiscover(array('row' => $this));
					$this->_instance->populate($this->_data);
				}
			} else {
				throw $e;
			}
		}
		return $this->_instance;
	}
	
}