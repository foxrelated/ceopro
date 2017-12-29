<?php

class Advgroup_Model_DbTable_Membership extends Core_Model_DbTable_Membership
{
    protected $_type = 'group';
    protected $_name = 'group_membership';

    // Configuration

    /**
     * Does membership require approval of the resource?
     *
     * @param Core_Model_Item_Abstract $resource
     * @return bool
     */
    public function isResourceApprovalRequired(Core_Model_Item_Abstract $resource = null)
    {
        return $resource->approval;
    }

    public function rejectedInvite(Core_Model_Item_Abstract $resource, User_Model_User $user)
    {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);

        if (null === $row) {
            throw new Core_Model_Exception("Membership does not exist");
        }
        if (!$row->rejected_ignored && !$row->active) {
            $row->rejected_ignored = true;
            $row->save();
        }
        return $this;
    }

    public function getInvitedMembers(Core_Model_Item_Abstract $resource)
    {
        $rejected_ignored = 1;
        $resource_approved = 1;
        $active = 0;
        $table = $this->getTable();
        $select = $table->select()
            ->where('resource_id = ?', $resource->getIdentity())
            ->where("active = $active AND (rejected_ignored = $rejected_ignored OR resource_approved = $resource_approved)");
        return $select;
    }

    /**
     * Reinvite a user has rejeted or ignored
     *
     * @param Core_Model_Item_Abstract $resource
     * @param User_Model_User $user
     * @return Core_Model_DbTable_Membership
     */
    public function setReinvite(Core_Model_Item_Abstract $resource, User_Model_User $user)
    {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);

        if (null === $row) {
            throw new Core_Model_Exception("Membership does not exist");
        }
        if ($row->rejected_ignored) {
            $row->rejected_ignored = false;
            $row->resource_approved = true;
            $row->user_approved = false;
            $row->save();
        }
        return $this;
    }

    /**
     * Reinvite a user has rejeted or ignored
     *
     * @param Core_Model_Item_Abstract $resource
     * @param User_Model_User $user
     * @return Core_Model_DbTable_Membership
     */
    public function requestAgain(Core_Model_Item_Abstract $resource, User_Model_User $user)
    {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);
        if (null === $row) {
            throw new Core_Model_Exception("Membership does not exist");
        }

        if (!$row->user_approved) {
            $row->user_approved = true;
            $row->resource_approved = false;
            $row->rejected_ignored = false;
            $row->save();
        }

        return $this;
    }

    /**
     * Set membership as being approved by the resource
     *
     * @param Core_Model_Item_Abstract $resource
     * @param User_Model_User $user
     * @return Core_Model_DbTable_Membership
     */
    public function setResourceApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
    {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);

        if (null === $row) {
            throw new Core_Model_Exception("Membership does not exist");
        }

        if (!$row->resource_approved) {
            $row->resource_approved = true;
            if ($row->resource_approved && $row->user_approved) {
                $row->active = true;
                $row->rejected_ignored = false;
                if (isset($resource->member_count)) {
                    $resource->member_count++;
                    $resource->save();
                }
            }
            $this->_checkActive($resource, $user);
            $row->save();
        }

        return $this;
    }

    /**
     * Set membership as being approved by the user
     *
     * @param Core_Model_Item_Abstract $resource
     * @param User_Model_User $user
     * @return Core_Model_DbTable_Membership
     */
    public function setUserApproved(Core_Model_Item_Abstract $resource, User_Model_User $user)
    {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);

        if (null === $row) {
            throw new Core_Model_Exception("Membership does not exist");
        }

        if (!$row->user_approved) {
            $row->user_approved = true;

            if ($row->resource_approved && $row->user_approved) {
                $row->active = true;
                $row->rejected_ignored = false;
                if (isset($resource->member_count)) {
                    $resource->member_count++;
                    $resource->save();
                }
            }
            $this->_checkActive($resource, $user);
            $row->save();
        }

        return $this;
    }

    /**
     * Checks if specified user ignored invite. Set $ignored to true/false
     * to check for approved status, or null for either.
     *
     * @param Core_Model_Item_Abstract $resource
     * @param User_Model_User $user
     * @param bool|null $active
     * @return bool
     */
    public function ignoredInvite(Core_Model_Item_Abstract $resource, User_Model_User $user, $ignored)
    {
        $this->_isSupportedType($resource);
        $row = $this->getRow($resource, $user);
        if ($row === null) {
            return true;
        }

        return ($ignored == $row->rejected_ignored);
    }

    /**
     * Gets members that belong to resource (overwrite the function from parent class)
     *
     * @param Core_Model_Item_Abstract $resource
     * @param bool|null $active
     * @return Engine_Db_Table_Rowset
     */
    public function getMembers(Core_Model_Item_Abstract $resource, $active = true)
    {
        $ids = array(0);
        foreach ($this->getMembersInfo($resource, $active) as $row) {
            $ids[] = $row->user_id;
        }
        $user_table = Engine_Api::_()->getItemTable('user');
        $select = $user_table->select()->where('user_id IN (?)', $ids)->order('displayname ASC');
        return $user_table->fetchAll($select);
    }

    public function getMembershipsOfSelect(User_Model_User $user, $active = true)
    {
        $itemTable = Engine_Api::_()->getItemTable($this->_type);
        $table = $this->getTable();
        $itName = $itemTable->info('name');
        if (Engine_Api::_()->hasModuleBootstrap('ynlocationbased')) {
            $select = Engine_Api::_()->ynlocationbased()->getLocationBasedSelect('advgroup', 'groups');
        } else {
            $select = $itemTable->select()->from("$itName", array("$itName.*"));
        }

        $mtName = $table->info('name');
        $col = current($itemTable->info('primary'));

        $select
            ->joinRight($mtName, "`{$mtName}`.`resource_id` = `{$itName}`.`{$col}`", null)
            ->where("`{$mtName}`.`user_id` = ?", $user->getIdentity());

        if ($active !== null) {
            $select->where("`{$mtName}`.`active` = ?", (bool)$active);
        }

        return $select;
    }

}