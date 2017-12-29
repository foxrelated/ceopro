<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Search
 *
 * @author dangth
 */
class Ynforum_Form_Forum_Search extends Engine_Form {
    //put your code here
    public function init() {
        // Element: title
        $this->addElement('Text', 'title');
//        $this->getElement('title')->clearDecorators();
    }
}
?>