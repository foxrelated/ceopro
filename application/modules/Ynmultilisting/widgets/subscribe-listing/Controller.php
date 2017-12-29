<?php
class Ynmultilisting_Widget_SubscribeListingController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		$this -> view -> form = $form = new Ynmultilisting_Form_Subscribe();
		
		$listingType = Engine_Api::_() -> ynmultilisting() -> getCurrentListingType();
		
		if(empty($listingType))
		{
			return $this -> setNoRender();
		}
		
		$categories = $listingType -> getCategories();
		
        unset($categories[0]);
        foreach ($categories as $category) {
            $form->category_id_subscribe->addMultiOption($category['category_id'], str_repeat("-- ", $category['level'] - 1).$category['title']);
        }
	}
}