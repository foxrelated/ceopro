<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Edit.php 7481 2010-09-27 08:41:01Z john $
 * @author     Jung
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Form_Forum_Edit extends Ynforum_Form_Forum_Create {
    private $_forum;
    
    public function __construct($options = null) {      
        if (is_array($options) && array_key_exists('forum', $options)) {
            $this->_forum = $options['forum'];
            unset ($options['forum']);
        }
        
        parent::__construct($options);
    }
    
    public function init() {
        parent::init();
        $this->setTitle('Edit Forum');
    }
    
    protected function _fillDataInForm() {
        parent::_fillDataInForm();
        
        $parentEle = $this->getElement('parent_category_forum');
        $cat = $this->_forum->category_id;
        if ($this->_forum) {            
            foreach($this->_orderForums[$this->_forum->category_id] as $index => $forum) {
                if (isset ($beginPosition) && $forum->level <= $this->_forum->level) {
                    $endPosition = $index;
                    break;
                } else {
                    if ($forum->getIdentity() == $this->_forum->getIdentity()) {
                        $beginPosition = $index;
                    }       
                }
            }
            if (isset($beginPosition)) {
                $parentEle->removeMultiOption('forum_id=' . $this->_forum->getIdentity());
                if (!isset($endPosition)) {
                    $endPosition = count($this->_orderForums[$this->_forum->category_id]) + 1;
                }
                
                $arr = array_slice($this->_orderForums[$this->_forum->category_id], $beginPosition + 1, $endPosition - $beginPosition - 1, true);
                foreach($arr as $forum) {
                    $parentEle->removeMultiOption('forum_id=' . $forum->getIdentity());
                }
                
            }
            
            if ($this->_forum->parent_forum_id) {
                $parentEle->setValue('forum_id=' . $this->_forum->parent_forum_id);
            } else {
                $parentEle->setValue('category_id=' . $this->_forum->category_id);
            }
        }        
    }
}