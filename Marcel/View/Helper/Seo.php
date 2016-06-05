<?php
/**
 * Transform a product object to SEO friendly URL
 * 
 * @author Jeremy MOULIN
 * 
 * @uses Zend_View_Helper_Abstract
 * 
 * @package Marcel_View
 * 
 * @subpackage Helper
 *
 */
class Marcel_View_Helper_Seo extends Zend_View_Helper_Abstract {
	/**
	 * Transform product object to SEO friendly url
	 * 
	 * @param array $object       Object to translate
	 * @param bool  $removeAccent Should remove accent ? (optional / default : dalse)
	 * 
	 * @return string
	 */
	public function seo($text, $removeAccent = false) {
		$value = strip_tags($text);
		$value = strtr($value, ' ,\'', '---');
		$value = str_replace('#', '', $value);
		if ($removeAccent) {
			$table = array(
				'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj', 'Ž'=>'Z', 'ž'=>'z', 'C'=>'c', 'c'=>'c',
				'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
				'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O',
				'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss',
				'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e',
				'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o',
				'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b',
				'ÿ'=>'y', 'R'=>'R', 'r'=>'r',
			);
			$value = strtr($value, $table);
			$value = strtr($value, 
				"`’„‘’´“”\xE1\xE8\xEF\xEC\xE9\xED\xF2\xF3\xF8\x9A\x9D\xF9\xFA\xFD\x9E\xF4\xBC\xBE\xC1\xC8\xCF\xCC\xC9\xCD\xC2\xD3\xD8\x8A\x8D\xDA\xDD\x8E\xD2\xD9\xEF\xCF", 
				"'','''\"\"\x61\x63\x64\x65\x65\x69\x6E\x6F\x72\x73\x74\x75\x75\x79\x7A\x6F\x4C\x6C\x41\x43\x44\x45\x45\x49\x4E\x4F\x52\x53\x54\x55\x59\x5A\x4E\x55\x64\x44"
			);
		}
		$value = mb_strtolower($value);
		$value = preg_replace('~-+~', '-', $value);
		return $value;
	}
}