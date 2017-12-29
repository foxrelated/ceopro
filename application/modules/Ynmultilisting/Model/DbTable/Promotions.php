<?php
class Ynmultilisting_Model_DbTable_Promotions extends Engine_Db_Table {
    protected $_name = 'ynmultilisting_promotions';
    protected $_rowClass = 'Ynmultilisting_Model_Promotion';
    
    public function getListingTypePromotion($listingtype_id) {
        $select = $this->select()->where('listingtype_id = ?', $listingtype_id);
        $promotion = $this->fetchRow($select);
        return $promotion;
    }
}