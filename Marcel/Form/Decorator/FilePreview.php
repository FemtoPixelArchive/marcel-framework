<?php
/**
 * Marcel Framework
 * 
 * @category   Marcel
 * @package    Marcel_Form
 * @subpackage Decorator
 * @author Jay MOULIN
 */

/** 
 * @see Zend_Form_Decorator_File
 */
require_once 'Zend/Form/Decorator/File.php';

/**
 * File preview decorator adds preview for an image for the file input
 * 
 * @uses Zend_Form_Decorator_File
 * 
 * @author Jeremy MOULIN
 */
class Marcel_Form_Decorator_FilePreview extends Zend_Form_Decorator_File
{

    /**
     * Render a form file
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        $element = $this->getElement();
        if (!$element instanceof Zend_Form_Element) {
            return $content;
        }

        $view = $element->getView();
        if (!$view instanceof Zend_View_Interface) {
            return $content;
        }

        $name      = $element->getName();
        $attribs   = $this->getAttribs();
        if (!array_key_exists('id', $attribs)) {
            $attribs['id'] = $name;
        }

        $separator = $this->getSeparator();
        $placement = $this->getPlacement();
        $markup    = array();
        $size      = $element->getMaxFileSize();
        if ($size > 0) {
            $element->setMaxFileSize(0);
            $markup[] = $view->formHidden('MAX_FILE_SIZE', $size);
        }

        if (Zend_File_Transfer_Adapter_Http::isApcAvailable()) {
            $markup[] = $view->formHidden(ini_get('apc.rfc1867_name'), uniqid(), array('id' => 'progress_key'));
        } else if (Zend_File_Transfer_Adapter_Http::isUploadProgressAvailable()) {
            $markup[] = $view->formHidden('UPLOAD_IDENTIFIER', uniqid(), array('id' => 'progress_key'));
        }

        if ($element->isArray()) {
            $name .= "[]";
            $count = $element->getMultiFile();
            for ($i = 0; $i < $count; ++$i) {
                $htmlAttribs        = $attribs;
                $htmlAttribs['id'] .= '-' . $i;
                $markup[] = $view->formFilePreview($name, $element, $htmlAttribs);
            }
        } else {
            $markup[] = $view->formFilePreview($name, $element, $attribs);
        }

        $markup = implode($separator, $markup);

        switch ($placement) {
            case self::PREPEND:
                return $markup . $separator . $content;
            case self::APPEND:
            default:
                return $content . $separator . $markup;
        }
    }
}
