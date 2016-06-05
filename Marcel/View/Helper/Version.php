<?php
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
class Marcel_View_Helper_Version extends Zend_View_Helper_Abstract
{
	/**
	 * Display URL to the last version of requested resource
	 * 
	 * @param string $module    Module to the resource
	 * @param string $extension Extension to the resource
	 *
	 * @return string
	 */
	public function version($module, $extension) {
		$front = Zend_Controller_Front::getInstance();
		$cache = $front->getParam('bootstrap')->config['cache']['active'] ? $front->getParam('bootstrap')->getResource('cacheManager')->getCache('apc') : NULL;
		$cacheKey = str_replace('-', '', str_replace('.', '', 'version' . $module . $extension));
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
