<?php

class Ynwiki_Form_Test_Diff extends Engine_Form
{

    public function init()
    {
        
        $this->addElement('textarea','from_text',array(
            'label'=>'from_text'
        ));
        
        $this->addElement('textarea','to_text',array(
            'label'=>'to_text'
        ));
        
        $this->addElement('Submit','submit');
    }

}
