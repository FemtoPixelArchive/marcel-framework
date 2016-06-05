<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_View
 * @subpackage Helper
 * @author Jeremy MOULIN
 */

/**
 * @see Zend_View_Helper_Abstract
 */
require_once('Zend/View/Helper/FormFile.php');

/**
 * Display the correct URL to the last version of a compiled resource
 * 
 * @uses Zend_View_Helper_Abstract
 * 
 * @package Marcel_View
 * @subpackage Helper
 * 
 * @author Jay MOULIN
 *
 */
class Marcel_View_Helper_ResourceVersion extends Zend_View_Helper_Abstract
{
	/**
	 * Display URL to the last version of requested resource
	 * 
	 * @param string $module    Module to the resource
	 * @param string $extension Extension to the resource
	 *
	 * @return string
	 */
	public function resourceVersion($module, $extension) {
		$front = Zend_Controller_Front::getInstance();
		$cache = $front->getParam('bootstrap')->config['cache']['active'] ? $front->getParam('bootstrap')->getResource('cacheManager')->getCache('apc') : NULL;
		$cacheKey = 'version' . $module . $extension;
		$url = NULL;
		if ($cache) {
			$url = $cache->load($cacheKey);
		}
		if (!$url) {
			$version = 0;
			foreach (glob(ROOT_DIR . '/www/lib/' . $module . '/' . $extension . '/*.' . $extension) as $file) {
				$time = filemtime($file);
				$version = ($time >= $version) ? $time : $version;
			}
			$url = $this->view->baseUrl() . '/resource/' . $module . '/' . $version . '.' . $extension;
			if ($cache) {
				$cache->save($url, $cacheKey);
			}
		}
		return $url;
	}
}