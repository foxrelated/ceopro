<?php

class Ynwiki_Widget_Element_TextYn extends Engine_Form_Element_Text
{
    /**
     * Use formTextarea view helper by default
     * @var string
     */
    public $helper = 'formTextYounet';

    public function loadDefaultDecorators()
    {
        
      if( $this->loadDefaultDecoratorsIsDisabled() )
      {

        return;
      }
      $decorators = $this->getDecorators();
      if( empty($decorators) )
      {
        $this->addDecorator('ViewHelper');
        Engine_Form::addDefaultDecorators($this);
      }
    }
    
    public function setView(Zend_View_Interface $view = null)
    {
        $view->addHelperPath(APPLICATION_PATH .'/application/modules/Ynwiki/widgets/group-profile-pages/View/Helper', 'Ynwiki_Widget_View_Helper_');
        return parent::setView($view);
    }
}

