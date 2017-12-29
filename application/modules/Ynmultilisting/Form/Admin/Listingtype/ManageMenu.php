<?php
class Ynmultilisting_Form_Admin_Listingtype_ManageMenu extends Engine_Form {
    
    protected $_params = array();
    protected $_listingtype = null;
    
    public function getParams() {
        return $this -> _params;
    }
    
    public function setParams($params) {
        $this -> _params = $params;
    }
    
    public function getListingtype() {
        return $this -> _listingtype;
    }
    
    public function setListingtype($listingtype) {
        $this -> _listingtype = $listingtype;
    }
    
    public function init() {
        $params = $this->getParams();
        $listingtype = $this->getListingtype();
        $promotion = $listingtype->getPromotion();
        $categories = Engine_Api::_() -> getDbTable('categories', 'ynmultilisting') -> getListingTypeCategories($listingtype->getIdentity());
        unset($categories[0]);
        
        $this -> addElement('Dummy', 'top_categories', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_manage_top_categories.tpl',
                    'params' => $this->getParams(),
                    'listingtype' => $this->getListingtype(),
                    'categories' => $categories
                )
            )), 
        ));
        
        $this -> addElement('Dummy', 'more_categories', array(
            'decorators' => array( array(
                'ViewScript',
                array(
                    'viewScript' => '_manage_more_categories.tpl',
                    'params' => $this->getParams(),
                    'listingtype' => $this->getListingtype(),
                    'categories' => $categories
                )
            )), 
        ));
        
        $this->addElement('Heading', 'promotion_place_section', array(
            'label' => 'Promotion Section',
        ));
        
        $this->addElement('Text', 'title', array(
            'label' => 'Promotion title',
            'description' => 'Maximum 30 characters',
            'validators' => array(
                array('StringLength', false, array(0, 30)),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
		$this -> title -> getDecorator("Description") -> setOption("placement", "append");
        
        $this->addElement('Textarea', 'content', array(
            'label' => 'Promotion content',
            'description' => 'Maximum 150 characters',
            'validators' => array(
                array('StringLength', false, array(0, 150)),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));
		$this -> content -> getDecorator("Description") -> setOption("placement", "append");
                
        $this->addElement('File', 'photo', array(
            'label' => 'Cover photo',
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        
        $text_color = '#FFFFFF';
        if ($promotion) {
            $text_color = $promotion->text_color;
        }
        if (isset($params['text_color'])) {
            $text_color = $params['text_color'];
        }
            
        $this->addElement('Heading', 'text_color', array(
            'label' => 'Text color',
            'value' => '<input value="'.$text_color.'" type="color" id="text_color" name="text_color"/>'
        ));
        
        $text_background_color = '#FB8905';
        if ($promotion) {
            $text_background_color = $promotion->text_background_color;
        }
        if (isset($params['text_background_color'])) {
            $text_background_color = $params['text_background_color'];
        }
        $this->addElement('Heading', 'text_background_color', array(
            'label' => 'Text background color',
            'value' => '<input value="'.$text_background_color.'" type="color" id="text_background_color" name="text_background_color"/>'
        ));
        
        $this->addElement('Text', 'link', array(
            'label' => 'Link',
        ));
        
        $this->addElement('Button', 'submit_btn', array(
            'type' => 'submit',
            'label' => 'Save Change',
            'ignore' => true,
        ));
    }
}