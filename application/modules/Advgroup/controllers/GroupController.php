<?php
class Advgroup_GroupController extends Core_Controller_Action_Standard
{
    public function init()
    {
        
        if (0 !== ($group_id = (int)$this -> _getParam('group_id')) && null !== ($group = Engine_Api::_() -> getItem('group', $group_id)))
        {
            Engine_Api::_() -> core() -> setSubject($group);
        }
        $this -> _helper -> requireUser();
        $this -> _helper -> requireSubject('group');
    }
    
    
    public function editAction()
    {
        //Check authorizaion for editing.
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'edit') -> isValid())
        {
            return;
        }
        //Get group and officer list.
        $group = Engine_Api::_() -> core() -> getSubject();
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $officerList = $group -> getOfficerList();

        //Create edit form.
        $this -> view -> form = $form = new Advgroup_Form_Edit( array('item' => $group));
        if ($group -> is_subgroup)
        {
            $form -> removeElement('auth_sub_group');
        }
        
        $arrPlugins = array('ynwiki' => 'auth_wiki', 'ynevent' => 'auth_event', 'ynlistings' => 'auth_listing');
        foreach($arrPlugins as $key => $permission)
        {
            if(!Engine_Api::_()->advgroup()->checkYouNetPlugin($key)){
                $form->removeElement($permission);
            }
        }
        
        if(!Engine_Api::_()->advgroup()->checkYouNetPlugin('ynfilesharing')){
            $form->removeElement('auth_folder');
            $form->removeElement('auth_file_upload');
            $form->removeElement('auth_file_down');
        }

        $music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('music');
        $mp3music_enable = Engine_Api::_() -> advgroup() -> checkYouNetPlugin('mp3music');
		$socialmusic_enable = Engine_Api::_() -> hasModuleBootstrap('ynmusic');     
        if (!$music_enable && !$mp3music_enable && !$socialmusic_enable) {
            $form->removeElement('auth_music');
        }
        
        //Populate with categories
        $categories = Engine_Api::_() -> getDbtable('categories', 'advgroup') -> getAllCategoriesAssoc();
        $form -> category_id -> setMultiOptions($categories);

        if (count($form -> category_id -> getMultiOptions()) <= 1)
        {
            $form -> removeElement('category_id');
        }

        if (!$this -> getRequest() -> isPost())
        {
            // Populate auth
            $auth = Engine_Api::_() -> authorization() -> context;
            $roles = array(
                'owner',
                'officer',
                'member',
                'registered',
                'everyone'
            );
            $actions = array(
                'view',
                'comment',
                'invite',
                'photo',
                'event',
                'poll',
                'sub_group',
                'video',
                'wiki',
                'music',
                'folder',
                'file_upload',
                'file_down',
                'listing'
            );
            $perms = array();
            foreach ($roles as $roleString)
            {
                $role = $roleString;
                if ($role === 'officer')
                {
                    $role = $officerList;
                }
                foreach ($actions as $action)
                {
                    if ($auth -> isAllowed($group, $role, $action))
                    {
                        $perms['auth_' . $action] = $roleString;
                    }
                }
            }
            
            $form -> populate($group -> toArray());
            $form -> populate($perms);
            
            //parser location
            $location = Zend_Json::decode($group->location);
            $form -> populate(array('location_address' => $location['location']));
            $form -> populate(array('lat' => $location['latitude']));
            $form -> populate(array('long' => $location['longitude']));
            
            //Populate Tag
            $tagStr = '';
            foreach ($group->tags()->getTagMaps() as $tagMap)
            {
                $tag = $tagMap -> getTag();
                if (!isset($tag -> text))
                    continue;
                if ('' !== $tagStr)
                    $tagStr .= ', ';
                $tagStr .= $tag -> text;
            }
            $form -> populate(array('tags' => $tagStr, ));
            $this -> view -> tagNamePrepared = $tagStr;

            return;
        }

        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }
        
        // Process
        $db = Engine_Api::_() -> getItemTable('group') -> getAdapter();
        $db -> beginTransaction();

        try
        {
            $values = $form -> getValues();
            
            $values['location'] = Zend_Json::encode(array(
                    'location' => $values['location_address'],
                    'latitude' => $values['lat'],
                    'longitude' => $values['long'],
                
            )); 
            $values['latitude'] = $values['lat'];
            $values['longitude']  = $values['long'];
            // Set group info
            $group -> setFromArray($values);
            $group -> save();

            //Set custom fields
            $customfieldform = $form -> getSubForm('fields');
            $customfieldform -> setItem($group);
            $customfieldform -> saveValues();

            //Set photo
            if (!empty($values['photo']))
            {
                $group -> setPhoto($form -> photo);
            }
            
            // AddSetCover photo
            if (!empty($values['cover_thumb'])) {
                $group -> setCoverPhoto($form -> cover_thumb);
            }
            
            //Handle tags
            $tags = preg_split('/[,]+/', $values['tags']);
            $group -> tags() -> setTagMaps($viewer, $tags);

            $search_table = Engine_Api::_() -> getDbTable('search', 'core');
            $select = $search_table -> select() -> where('type = ?', 'group') -> where('id = ?', $group -> getIdentity());
            $row = $search_table -> fetchRow($select);
            if ($row)
            {
                $row -> keywords = $values['tags'];
                $row -> save();
            }

            // Process privacy
            $auth = Engine_Api::_() -> authorization() -> context;

            $roles = array(
                'owner',
                'officer',
                'member',
                'registered',
                'everyone'
            );

            if (empty($values['auth_view']))
            {
                $values['auth_view'] = 'everyone';
            }

            if (empty($values['auth_comment']))
            {
                $values['auth_comment'] = 'registered';
            }

            if (empty($values['auth_poll']))
            {
                $values['auth_poll'] = 'member';
            }

            if (empty($values['auth_event']))
            {
                $values['auth_event'] = 'registered';
            }

            if (empty($values['auth_sub_group']))
            {
                $values['auth_sub_group'] = 'member';
            }

            if (empty($values['auth_video']))
            {
                $values['auth_video'] = 'member';
            }

            if (empty($values['auth_wiki']))
            {
                $values['auth_wiki'] = 'member';
            }
            
            if( empty($values['auth_music'])){
                $values['auth_music'] = 'member';
              }
            
            if( empty($values['auth_listing'])){
                $values['auth_listing'] = 'member';
              }
                  
              if( empty($values['auth_folder'])){
                $values['auth_folder'] = 'member';
              }
        
              if( empty($values['auth_file_upload'])){
                $values['auth_file_upload'] = 'member';
              }
                
              if( empty($values['auth_file_down'])){
                $values['auth_file_down'] = 'member';
              }

            $viewMax = array_search($values['auth_view'], $roles);
            $commentMax = array_search($values['auth_comment'], $roles);
            $photoMax = array_search($values['auth_photo'], $roles);
            $eventMax = array_search($values['auth_event'], $roles);
            $pollMax = array_search($values['auth_poll'], $roles);
            $inviteMax = array_search($values['auth_invite'], $roles);
            $subGroupMax = array_search($values['auth_sub_group'], $roles);
            $videoMax = array_search($values['auth_video'], $roles);
            $wikiMax = array_search($values['auth_wiki'], $roles);
            $musicMax = array_search($values['auth_music'], $roles);
            $folderMax = array_search($values['auth_folder'], $roles);
            $fileuploadMax = array_search($values['auth_file_upload'], $roles);
            $filedownloadMax = array_search($values['auth_file_down'], $roles);
            $listingMax = array_search($values['auth_listing'], $roles);

            foreach ($roles as $i => $role)
            {
                if ($role === 'officer')
                {
                    $role = $officerList;
                }
                $auth -> setAllowed($group, $role, 'view', ($i <= $viewMax));
                $auth -> setAllowed($group, $role, 'comment', ($i <= $commentMax));
                $auth -> setAllowed($group, $role, 'photo', ($i <= $photoMax));
                $auth -> setAllowed($group, $role, 'event', ($i <= $eventMax));
                $auth -> setAllowed($group, $role, 'poll', ($i <= $pollMax));
                $auth -> setAllowed($group, $role, 'invite', ($i <= $inviteMax));
                $auth -> setAllowed($group, $role, 'sub_group', ($i <= $subGroupMax));
                $auth -> setAllowed($group, $role, 'video', ($i <= $videoMax));
                $auth -> setAllowed($group, $role, 'wiki', ($i <= $wikiMax));
                $auth->setAllowed($group, $role, 'music', ($i <= $musicMax));
                $auth->setAllowed($group, $role, 'folder', ($i <= $folderMax));
                $auth->setAllowed($group, $role, 'file_upload', ($i <= $fileuploadMax));
                $auth->setAllowed($group, $role, 'file_down', ($i <= $filedownloadMax));
                $auth->setAllowed($group, $role, 'listing', ($i <= $listingMax));
            }

            // Create some auth stuff for all officers
              $auth->setAllowed($group, $officerList, 'edit', 1);
              $auth->setAllowed($group, $officerList, 'style', 1);
              $auth->setAllowed($group, $officerList, 'photo.edit', 1);
              $auth->setAllowed($group, $officerList, 'announcement', 1);
              $auth->setAllowed($group, $officerList, 'member.edit', 1);
              $auth->setAllowed($group, $officerList, 'topic.edit', 1);
              $auth->setAllowed($group, $officerList, 'poll.edit', 1);
            // Add auth for invited users
            $auth -> setAllowed($group, 'member_requested', 'view', 1);

            // Commit
            $db -> commit();
        }
        catch( Engine_Image_Exception $e )
        {
            $db -> rollBack();
            $form -> addError(Zend_Registry::get('Zend_Translate') -> _('The image you selected was too large.'));
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }

        // Rebuild privacy
        $db -> beginTransaction();
        try
        {
            $actionTable = Engine_Api::_() -> getDbtable('actions', 'activity');
            foreach ($actionTable->getActionsByObject($group) as $action)
            {
                $actionTable -> resetActivityBindings($action);
            }
            $db -> commit();
        }
        catch( Exception $e )
        {
            $db -> rollBack();
            throw $e;
        }

        // Redirect
        if ($this -> _getParam('ref') === 'profile')
        {
            $this -> _redirectCustom($group);
        }
        else
        {
            $this -> _redirectCustom(array(
                'route' => 'group_general',
                'action' => 'manage'
            ));
        }
    }

    public function deleteAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();
        $group = Engine_Api::_() -> getItem('group', $this -> getRequest() -> getParam('group_id'));
        if (!$this -> _helper -> requireAuth() -> setAuthParams($group, null, 'delete') -> isValid())
            return;

        // In smoothbox
        $this -> _helper -> layout -> setLayout('default-simple');

        // Make form
        $this -> view -> form = $form = new Advgroup_Form_Delete();
        if (!$group -> is_subgroup)
        {
            $form -> setDescription("Are you sure you want to delete this group? All it's sub-group will also be deleted too and cannot be undone.");
        }
        if (!$group)
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _("Group doesn't exists or not authorized to delete");
            return;
        }

        if (!$this -> getRequest() -> isPost())
        {
            $this -> view -> status = false;
            $this -> view -> error = Zend_Registry::get('Zend_Translate') -> _('Invalid request method');
            return;
        }

        if (!$group -> is_subgroup && count($group -> getAllSubGroups()) > 0)
        {
            $db = $group -> getTable() -> getAdapter();
            $db -> beginTransaction();
            try
            {
                //Delete sub-groups
                foreach ($group->getAllSubgroups() as $sub_group)
                {
                    $sub_group -> delete();
                }
                //Delete parent group
                $group -> delete();
                $db -> commit();
            }
            catch (Exception $e)
            {
                $db -> rollback();
                throw $e;
            }
            $this -> view -> status = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The selected group and it\'s sub-groups have been deleted.');

            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'group_general', true),
                'messages' => Array($this -> view -> message)
            ));
        }
        else
        {
            $db = $group -> getTable() -> getAdapter();
            $db -> beginTransaction();

            try
            {
                $group -> delete();
                $db -> commit();
            }
            catch( Exception $e )
            {
                $db -> rollBack();
                throw $e;
            }

            $this -> view -> status = true;
            $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('The selected group has been deleted.');

            return $this -> _forward('success', 'utility', 'core', array(
                'parentRedirect' => Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'manage'), 'group_general', true),
                'messages' => Array($this -> view -> message)
            ));
        }
    }

    public function styleAction()
    {
        //Check authorizaion for stylist
        if (!$this -> _helper -> requireAuth() -> setAuthParams(null, null, 'style') -> isValid())
        {
            return;
        }
        $user = Engine_Api::_() -> user() -> getViewer();
        $group = Engine_Api::_() -> core() -> getSubject('group');

        // Make form
        $this -> view -> form = $form = new Advgroup_Form_Style();

        // Get current row
        $table = Engine_Api::_() -> getDbtable('styles', 'core');
        $select = $table -> select() -> where('type = ?', 'group') -> where('id = ?', $group -> getIdentity()) -> limit(1);

        $row = $table -> fetchRow($select);

        // Check post
        if (!$this -> getRequest() -> isPost())
        {
            $form -> populate(array('style' => (null === $row ? '' : $row -> style)));
            return;
        }

        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }

        // Cool! Process
        $style = $form -> getValue('style');

        // Save
        if (null == $row)
        {
            $row = $table -> createRow();
            $row -> type = 'group';
            $row -> id = $group -> getIdentity();
        }

        $row -> style = $style;
        $row -> save();

        $this -> view -> draft = true;
        $this -> view -> message = Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.');
        $this -> _forward('success', 'utility', 'core', array(
            'smoothboxClose' => true,
            'parentRefresh' => false,
            'messages' => array(Zend_Registry::get('Zend_Translate') -> _('Your changes have been saved.'))
        ));
    }

    public function featuredAction()
    {
        //Get data.
        $group_id = $this -> _getParam('group_id');
        $group_good = $this -> _getParam('good');

        //Begin transaction in database.
        $db = Engine_Db_Table::getDefaultAdapter();
        $db -> beginTransaction();

        $group = Engine_Api::_() -> getItem('group', $group_id);
        if (count($group) > 0)
        {
            $group -> featured = $group_good;
            $group -> save();
        }
        $db -> commit();
    }

    public function transferAction()
    {
        $viewer = Engine_Api::_() -> user() -> getViewer();

        $this -> view -> group_id = $group_id = $this -> _getParam('group_id');
        $group = Engine_Api::_() -> getItem('group', $group_id);

        if (!$group)
        {
            return $this -> _helper -> requireSubject -> forward();
        }

        if (!$viewer -> isAdmin() && !$group -> isOwner($viewer) && !$group -> isParentGroupOwner($viewer))
        {
            return $this -> _helper -> requireAuth -> forward();
        }

        $this -> view -> form = $form = new Advgroup_Form_Transfer;

        if (!$this -> getRequest() -> getPost())
        {
            return;
        }

        if (!$form -> isValid($this -> getRequest() -> getPost()))
        {
            return;
        }
        //Process
        $values = $form -> getValues();
        $db = Engine_Api::_() -> getDbtable('groups', 'advgroup') -> getAdapter();
        $db -> beginTransaction();
        $member = Engine_Api::_() -> user() -> getUser($values['toValues']);

        if (!$group -> membership() -> isMember($member))
        {
            return $this -> _forward('success', 'utility', 'core', array(
                'closeSmoothbox' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('That user is not a member of this group.')),
            ));
        }

        try
        {
            $group -> user_id = $values['toValues'];
            $list = $group -> getOfficerList();
            $list -> remove($member);
            $group -> save();

            // Add action
            $activityApi = Engine_Api::_() -> getDbtable('actions', 'activity');
            $action = $activityApi -> addActivity($member, $group, 'advgroup_transfer');

            //Add notification
            $notifyApi = Engine_Api::_() -> getDbtable('notifications', 'activity');
            $notifyApi -> addNotification($member, $viewer, $group, 'advgroup_transfer');

            $db -> commit();
        }
        catch(Exception $e)
        {
            $db -> rollback();
            throw $e;
        }
        $session = new Zend_Session_Namespace('mobile');
        if ($session -> mobile)
        {
            $callbackUrl = $this -> view -> url(array('id' => $group -> getIdentity()), 'group_profile', true);
            $this -> _forward('success', 'utility', 'core', array(
                'smoothboxClose' => true,
                'parentRedirect' => $callbackUrl,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The new group owner had been set.'))
            ));
        }
        else
        {
            return $this -> _forward('success', 'utility', 'core', array(
                'closeSmoothbox' => true,
                'parentRefresh' => true,
                'messages' => array(Zend_Registry::get('Zend_Translate') -> _('The new group owner had been set.')),
            ));
        }
    }

}
