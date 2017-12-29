<?php

class Advmenusystem_View_Helper_ParseIcon extends Zend_View_Helper_HtmlElement
{
    public function parseIcon($src, $alt = "", $attribs = array())
    {
        $closingBracket = $this->getClosingBracket();
        // Allow passing an array
        if( is_array($src) )
        {
            $route = ( isset($src['route']) ? $src['route'] : 'default' );
            $reset = ( isset($src['reset']) ? $src['reset'] : false );
            unset($src['route']);
            unset($src['reset']);
            $src = $this->view->url($src, $route, $reset);
        } else {
            if (substr($src, 0, 3) == 'fa-') {
                return '<i class="fa ' . $src . '"></i>';
            } else if(!$src && $alt) {
                return '<i class="fa ' . $alt . '"></i>';
            }
        }

        // Merge data and type
        $attribs = array_merge(array(
            'src' => $src,
            'alt' => $alt), $attribs);


        return '<img'.$this->_htmlAttribs($attribs).$closingBracket;
    }
}
