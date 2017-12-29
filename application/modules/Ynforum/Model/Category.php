<?php

/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: Category.php 7820 2010-11-18 22:08:00Z steve $
 * @author     John
 */

/**
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 */
class Ynforum_Model_Category extends Core_Model_Item_Collection {

    protected $_children_types = array('ynforum_forum', 'ynforum_category');
    protected $_collectible_type = 'ynforum_forum';
    protected $_collection_column_name = 'category_id';

//    protected $_parent_type = 'ynforum_category';
//    protected $_owner_type = 'ynforum_category';

    public function getHref($params = array()) {
        $params = array_merge(array(
            'route' => 'ynforum_category',
            'reset' => true,
            'category_id' => $this->getIdentity(),
            'slug' => $this->getSlug(),
                ), $params);
        
        $route = $params['route'];
        $reset = $params['reset'];
        unset($params['route']);
        unset($params['reset']);
        return Zend_Controller_Front::getInstance()->getRouter()
                ->assemble($params, $route, $reset);
    }

    protected function getPrevCategory() {
        $table = Engine_Api::_()->getItemTable('ynforum_category');
        if (!in_array('order', $table->info('cols'))) {
            throw new Core_Model_Item_Exception('Unable to use order as order column doesn\'t exist');
        }


        $select = $table->select()->setIntegrityCheck(false)
                ->from($table->info('name'), 'MAX(`order`) AS max_order')
                ->where('`order` < ?', $this->order);

        $row = $select->query()->fetch();
        return $table->fetchAll($table->select()->where('`order` = ?', $row['max_order']))->current();
    }

    public function moveUp() {        
        $table = $this->getTable();
        $db = $table->getAdapter();
        $db->beginTransaction();
        try {
            $select = $table->select()->order('order ASC');
            $categories = $table->fetchAll($select);
            $newCatOrder = 1;
            for ($i = $this->order - 2; $i > 0; $i--) {
                if ($categories[$i]->level == $this->level) {
                    $newCatOrder = $categories[$i]->order;
                    break;
                }
            }

            $this->order = $newCatOrder;
            $this->save();

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function setPhoto($photo) {
        if ($photo instanceof Zend_Form_Element_File) {
            $file = $photo->getFileName();
        } else if (is_array($photo) && !empty($photo['tmp_name'])) {
            $file = $photo['tmp_name'];
        } else if (is_string($photo) && file_exists($photo)) {
            $file = $photo;
        } else {
            throw new Event_Model_Exception('invalid argument passed to setPhoto');
        }

        if ($this->photo_id) {
            $this->removeOldPhoto();
        }

        $name = basename($file);
        $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . 'temporary';
        $params = array(
            'parent_id' => $this->getIdentity(),
            'parent_type' => $this->getType()
        );

        // Save
        $storage = Engine_Api::_()->storage();

        // Resize image (main)
        $image = Engine_Image::factory();
        $image->open($file)
                ->resize(50, 50)
                ->write($path . '/m_' . $name)
                ->destroy();

        // Store
        $iMain = $storage->create($path . '/m_' . $name, $params);

        // Remove temp files
        @unlink($path . '/m_' . $name);

        // Update row
        $this->modified_date = date('Y-m-d H:i:s');
        $this->photo_id = $iMain->getIdentity();
        $this->save();

        return $this;
    }

    protected function _insert() {
        parent::_insert();

        // TODO [DangTH] : check again the SQL Builder here
        $table = Engine_Api::_()->getItemTable('ynforum_category');
        $table->update(array('order' => new Zend_DB_Expr('`order` + 1')), array('`order` >= ?' => $this->order));
    }

    public function moveCategoryWith($category) {
        $oldOrder = $this->order;
		$newOrder = $category->order;
		if($oldOrder == $newOrder)
		{
			$newOrder = $newOrder - 1;
		}
        $this->order = $newOrder;
        $category->order = $oldOrder;
        
        $category->save();
        $this->save();
    }
    
    protected function _update() {
        parent::_update();
        // check the update to avoid the looping situation when choosing an appropriate parent category
        if ($this->parent_category_id) {
            if (array_key_exists('parent_category_id', $this->_modifiedFields)) {
                $categoryTable = $this->getTable();
                $cat = $this;
                do {
                    $cat = $categoryTable->fetchRow($categoryTable->select()->where('category_id = ?', $cat->parent_category_id));
                    if ($cat->getIdentity() == $this->getIdentity()) {
                        throw new Ynforum_Model_Exception('The category hierachy is looped');
                    }
                } while ($cat->parent_category_id);
            }
        }
    }
    
//    protected function _update() {
//        parent::_update();
//
//        if ($this->_cleanData['order'] != $this->_data['order']) {
//            $originalOrder = $this->_cleanData['order'];
//            $table = Engine_Api::_()->getItemTable('ynforum_category');
//            if ($originalOrder > $this->order) {
//                $table->update(
//                        array('order' => new Zend_DB_Expr('`order` + 1')), array('category_id != ' . $this->getIdentity(),
//                    '`order` >= ' . $this->order,
//                    '`order` < ' . $originalOrder));
//            } else if ($originalOrder < $this->order) {
//                $table->update(
//                        array('order' => new Zend_DB_Expr('`order` - 1')), array('category_id != ' . $this->getIdentity(),
//                    '`order` > ' . $originalOrder,
//                    '`order` <= ' . $this->order));
//            }
//            $childrenCategories = $this->getChildrenCategory();
//            foreach ($childrenCategories as $key => $childCategory) {
//                $childCategory->order = $this->order + $key + 1;
//                $childCategory->save();
//            }
//        }
//    }

    protected function _delete() {
        parent::_delete();
        $table = Engine_Api::_()->getItemTable('ynforum_forum');
        foreach ($this->getChildren('ynforum_forum', array('order' => 'order')) as $index => $forum) {
            $forum->delete();
        }
        
        foreach($this->getChildrenCategory() as $category) {
            $category->delete();
        }
    }

    public function getModeratorList() {
        $table = Engine_Api::_()->getItemTable('ynforum_category_list');
        $select = $table->select()
                ->where('owner_id = ?', $this->getIdentity())
                ->limit(1);

        $list = $table->fetchRow($select);

        if (null === $list) {
            $list = $table->createRow();
            $list->setFromArray(array(
                'owner_id' => $this->getIdentity(),
            ));
            $list->save();
        }

        return $list;
    }

    public function isModerator($user) {
        $list = $this->getModeratorList();
        return $list->has($user);
    }

    /**
     * Remove the old thumbnail of a book. 
     * This is used in the case of updating book.
     */
    protected function removeOldPhoto() {
        if ($this->photo_id) {
            $item = Engine_Api::_()->storage()->get($this->photo_id);

            $table = Engine_Api::_()->getItemTable('storage_file');
            $select = $table->select()
                    ->where('parent_type = ?', $this->getType())
                    ->where('parent_id = ?', $this->getIdentity());

            foreach ($table->fetchAll($select) as $file) {
                try {
                    $file->delete();
                } catch (Exception $e) {
                    if (!($e instanceof Engine_Exception)) {
                        $log = Zend_Registry::get('Zend_Log');
                        $log->log($e->__toString(), Zend_Log::WARN);
                    }
                }
            }
        }
    }

    public function getParentCategory() {
        if ($this->parent_category_id) {
            return Engine_Api::_()->getItem('ynforum_category', $this->parent_category_id);
        }

        return null;
    }

    public function getChildrenCategory() {
        $table = $this->getTable();
        $select = $table->select()->where('parent_category_id = ?', $this->getIdentity())->order('order ASC');
        return $table->fetchAll($select);
    }

    public function removeMod($user, $removeModFromItsChildren = false) {
        $list = $this->getModeratorList();
        if ($list->has($user)) {
            $list->remove($user);
        }
        if ($removeModFromItsChildren) {
            foreach ($this->getChildrenCategory() as $childCategory) {
                $childCategory->removeMod($user, $removeModFromItsChildren);
                foreach ($childCategory->getCollectibles() as $subForum) {
                    $subForum->removeMod($user, $removeModFromItsChildren);
                }
            }
            foreach ($this->getCollectibles() as $forum) {
                $forum->removeMod($user, $removeModFromItsChildren);
            }
        }
    }

    public function addMod($user, $addModToItsChildren = false) {
        $list = $this->getModeratorList();
        if (!$list->has($user)) {
            $list->add($user);
        }
        if ($addModToItsChildren) {
            foreach ($this->getChildrenCategory() as $childCategory) {
                $childCategory->addMod($user, $addModToItsChildren);
                foreach ($childCategory->getCollectibles() as $subForum) {
                    $subForum->addMod($user, $addModToItsChildren);
                }
            }
            foreach ($this->getCollectibles() as $forum) {
                $forum->addMod($user, $addModToItsChildren);
            }
        }
    }
}