<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel
 * @author Jeremy MOULIN
 */

/**
 * Translation class - Mock
 * 
 * @package Marcel
 * 
 * @author Jeremy MOULIN
 *
 */
class Marcel_Translate {
	/**
	 * Translate text
	 * 
	 * @param string $text English text to translate
	 * 
	 * @return string translated text
	 */
	public function _($text) {
		return $text;
	}
	
	/**
	 * Define locale to use
	 * 
	 * @param string $locale Locale to use
	 * 
	 * @return Marcel_Translate
	 */
	public function setLocale($locale = NULL) {
		return $this;
	} 
}