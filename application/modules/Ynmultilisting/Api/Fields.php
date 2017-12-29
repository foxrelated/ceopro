<?php
class Ynmultilisting_Api_Fields extends  Fields_Api_Core {
   
   
   public function getFieldById($id) {
       $type = 'ynmultilisting_listing';
        return $this->getFieldsMeta($type)->getRowMatching('field_id', $id);
   }
   
   public function getFieldTypeStr($spec)
   {
   	 return $this->getFieldType($spec);
   }
   
   public function getFieldsIdsStructureFull($spec, $parent_field_id = null, $parent_option_id = null)
   {
    $type = $this->getFieldType($spec);

    $structure = array();
    foreach( $this->getFieldsMaps($type)->getRowsMatching('field_id', (int) $parent_field_id) as $map ) {
      // Skip maps that don't match parent_option_id (if provided)
      if( null !== $parent_option_id && $map->option_id != $parent_option_id ) {
        continue;
      }
      // Get child field
      $field = $this->getFieldsMeta($type)->getRowMatching('field_id', $map->child_id);
      if( empty($field) ) {
        continue;
      }
      // Add to structure
      $structure[] = $field -> field_id;
    }

     return $structure;
   }
   
  
}
