<?php
class Ynmember_Widget_GeneralRatingController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
		$this -> view -> resource = $resource = $user = Engine_Api::_()->core()->getSubject();
		$ratingTypeTbl = Engine_Api::_()->getItemTable("ynmember_ratingtype");
		$this -> view -> ratingTypes = $ratingTypeTbl -> getAllRatingTypes();
		if($user -> rating == 0)
		{
			return $this->setNoRender();
		}
	}
}