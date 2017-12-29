<?php
class Ynmultilisting_Model_DbTable_Modules extends Engine_Db_Table {
    protected $_rowClass = 'Ynmultilisting_Model_Module';
    
    public function getAvailableModules() {
        return $this->fetchAll();
    }
}