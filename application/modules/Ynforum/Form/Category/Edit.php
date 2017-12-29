<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 7481 2010-09-27 08:41:01Z john $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Category_Edit extends Ynforum_Form_Category_Create {
    public function init() {
        parent::init();
        $this->setTitle('Edit Category');
    }
    
    protected function _fillDataInForm() {
        parent::_fillDataInForm();
        
        if ($this->_category) {
            // remove the category and its sub categories from the drop down list
            $parentCategoryEle = $this->getElement('parent_category_id');
            foreach($this->_orderCategories as $index => $orderCat) {
                if (isset ($beginPosition) && $orderCat->level <= $this->_category->level) {
                    $endPosition = $index;
                    break;
                } else {
                    if ($orderCat->getIdentity() == $this->_category->getIdentity()) {
                        $beginPosition = $index;
                    }       
                }
            }
            if (isset($beginPosition)) {
                $parentCategoryEle->removeMultiOption($this->_category->getIdentity());
                if (isset($endPosition)) {
                    $arr = array_slice($this->_orderCategories, $beginPosition + 1, $endPosition - $beginPosition - 1, true);
                    foreach($arr as $cat) {
                        $parentCategoryEle->removeMultiOption($cat->getIdentity());
                    }
                }
            }
        }        
    }
}