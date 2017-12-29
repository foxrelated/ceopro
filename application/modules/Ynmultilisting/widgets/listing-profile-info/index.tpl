<?php $this -> addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper'); ?>	

<div class="ynmultilisting-profile-detail-info">


<div class="rich_content_body">
<?php echo $this->translate($this->listing->description) ?>
</div>

<?php
	//get profile question
    $topStructure = Engine_Api::_() -> fields() -> getFieldStructureTop('ynmultilisting_listing');
    if (count($topStructure) == 1 && $topStructure[0] -> getChild() -> type == 'profile_type')
    {
       $profileTypeField = $topStructure[0] -> getChild();
       $topLevelId =  $profileTypeField -> field_id;
       $topLevelValue = $this -> listing -> getCategory() -> option_id;
    }
	
	$fieldIds = Engine_Api::_()->getApi('fields','ynmultilisting')->getFieldsIdsStructureFull($this->listing, $topLevelId, $topLevelValue);
	$valueTable = new Ynmultilisting_Model_DbTable_Values($this -> listing -> getType(), 'values');
	$values = $valueTable -> getValues($this->listing);
	$arrValue = array();
	foreach($values as $val)
	{
		$field = Engine_Api::_() -> getApi('fields','ynmultilisting') -> getFieldById($val -> field_id);
		if(isset($field) && $field -> type != "heading")
		{
			if(isset($arrValue[$val -> field_id])){
				$arrValue[$val -> field_id] .= ",".$val -> getValue();
			}else{
				$arrValue[$val -> field_id] = $val -> getValue();
			}
		}
	}
	if(!empty($arrValue))
	{
		echo "<div class='ynmultilisting-specifications'>";
		echo "<div class='ynmultilisting-specifications-title'><h2>".$this -> translate('listing specifications')."</h2></div>";
		$isOpen = false;
		foreach($fieldIds as $field_id)
		{	
			$field = Engine_Api::_() -> getApi('fields','ynmultilisting') -> getFieldById($field_id);			
			if($field -> type !="heading" && !$isOpen){
				echo "<ul>";
				$isOpen = true;
			}
			switch ($field -> type) {
				case 'multiselect':
				case 'multi_checkbox':
					echo "<li>";
					echo "<h5>".$field -> label."</h5>";
					echo "<p>";
					$ids = explode(",", $arrValue[$field_id]);
					$arrMultiValue = array();
					foreach($ids as $option_id){
						$option = Engine_Api::_() -> fields() -> getOption($option_id, 'ynmultilisting_listing');
						$arrMultiValue[] =  $option -> label;
					}
					echo implode(", ", $arrMultiValue);
					echo "</p>";
					echo "</li>";
					break;

				case 'heading':
					echo "</ul>";
					echo "<h4>".'<i class="fa fa-circle"></i>&nbsp;'.$field -> label."</h4>";
					$isOpen = false;
					break;
					
				default:
					echo "<li>";
					echo "<h5>".$field -> label."</h5>";
					echo "<p>";
					print_r($arrValue[$field_id]);
					echo "</p>";
					echo "</li>";
					break;
			}

		}

		echo "</div>";
	}
?>

	
</div>