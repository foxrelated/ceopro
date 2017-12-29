<?php
class Ynmember_Widget_MemberReviewForController extends Engine_Content_Widget_Abstract 
{
	public function indexAction()
	{
		if (!Engine_Api::_()->core()->hasSubject()) {
            return $this->setNoRender();
        }
		$this -> view -> currentReview = $review = Engine_Api::_()->core()->getSubject();
		if (!$review)
		{
			return $this->setNoRender();
		}
		$reviewTbl = Engine_Api::_()->getItemTable('ynmember_review');
		$this -> view -> reviews = $reviews = $reviewTbl -> getAllReviewsByUserId ($review->user_id);
		if (count($reviews) == 1)
		{
			return $this->setNoRender();
		}
	}
}