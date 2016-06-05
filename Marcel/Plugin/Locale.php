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
 * @see Zend_Registry
 */
require_once('Zend/Registry.php');
/**
 * @see Zend_Controller_Action_HelperBroker
 */
require_once('Zend/Controller/Action/HelperBroker.php');
/**
 * @see Zend_Translate
 */
require_once('Zend/Translate.php');
/**
 * @see Marcel_Translate
 */
require_once('Marcel/Translate.php');
/**
 * @see Zend_Validate_Abstract
 */
require_once('Zend/Validate/Abstract.php');


/**
 * Locale Plugin - define the site locale by getting lang param
 * 
 * @uses Zend_Controller_Plugin_Abstract
 * 
 * @package Marcel_Plugin
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Plugin_Locale extends Zend_Controller_Plugin_Abstract	{
	
	/**
	 * Check language requested and define for website
	 * 
	 * @param Zend_Controller_Request_Abstract $request Request
	 * 
	 * @uses Zend_Registry
	 * @uses Zend_Controller_Action_HelperBroker
	 * @uses Zend_Translate
	 * @uses Zend_Validate_Abstract
	 * @uses Marcel_Translate
	 */
	public function preDispatch(Zend_Controller_Request_Abstract $request) {
		$registry = Zend_Registry::getInstance();
		$config = Zend_Registry::getInstance()->config;
		
		if ($config['language']['active']) {
			/*if ($config['cache']['active']) {
				$cache = Zend_Controller_Action_HelperBroker::getStaticHelper('cache')->getCache('apc');
				if ($cache) {
					Zend_Translate::setCache ( $cache );
				}
			}*/

			$tr = new Zend_Translate('array', ROOT_DIR . '/library/resources/languages', 'en', array('scan' => Zend_Translate_Adapter::LOCALE_DIRECTORY));
			Zend_Validate_Abstract::setDefaultTranslator($tr);
			
			$tr = new Zend_Translate ( 'Zend_Translate_Adapter_Gettext', ROOT_DIR . '/application/languages/lang.en.mo', 'en' );
			foreach ($config['languages'] as $lang) {
				$tr->addTranslation ( ROOT_DIR . '/application/languages/lang.' . $lang . '.mo', $lang );
			}
			$registry->translate = $tr;
		} 
		if (!isset($registry->translate) || !$registry->translate) {
			$registry->translate = new Marcel_Translate;
		}

		Zend_Registry::getInstance()->locale = isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'auto';
		$config = Zend_Registry::getInstance()->config;
		$lang = $request->getParam('lang') ? $request->getParam('lang') : Zend_Registry::getInstance()->locale;
		//echo 'requested : ' . $lang;
		if ($lang === 'auto') {
			$lang = isset($_COOKIE['locale']) ? $_COOKIE['locale'] : 'auto';
		}
		if ($config['language']['active']) {
			if (!in_array($lang, $config['languages']) && $lang !== 'auto') {
				$lang = 'en';
			}
			$tr = Zend_Registry::getInstance()->translate;
			try {
			//	echo 'try to set : ' . $lang;
				$locale = new Zend_Locale ( $lang );
				//echo 'success';
			} catch ( Zend_Locale_Exception $e ) {
			//	echo 'failed to en';
				$locale = new Zend_Locale ( 'en' );
			}
			try {
				//echo "try to save " . $locale->getLanguage ();
				$tr->setLocale ( $locale->getLanguage () );
				//echo 'success';
			} catch ( Exception $e ) {
				$locale = new Zend_Locale ( 'en' );
				$tr->setLocale ( $locale->getLanguage ());
				//echo 'failed';
			}
			Zend_Registry::getInstance()->translate = $tr;
			Zend_Registry::getInstance()->locale = $locale->getLanguage ();
		} else {
			Zend_Registry::getInstance()->locale = 'en';
		}
		
		if (!isset($_COOKIE['locale'])) {
			$_COOKIE['locale'] = 'en';
		}
		if (Zend_Registry::getInstance()->locale !== 'auto' && $_COOKIE['locale'] != Zend_Registry::getInstance()->locale) {
			setcookie('locale', Zend_Registry::getInstance()->locale, time() + (60 * 60 * 24 * 7), '/');
		}
		//echo 'final choice : ' . Zend_Registry::getInstance()->locale;
	}
}

/**
 * Get the translation for an english string thanks to gettext
 * 
 * @param string $text English text to translate
 * 
 * @uses Zend_Registry
 * 
 * @return string translated text
 */
function tr($text) {
	$tr = Zend_Registry::getInstance ()->translate;
	return $tr->_( $text );
}