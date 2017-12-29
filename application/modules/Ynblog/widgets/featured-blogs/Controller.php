<?php
class Ynblog_Widget_FeaturedBlogsController extends Engine_Content_Widget_Abstract {
	public function indexAction() {
		// Number of blog display
		if ($this -> _getParam('max') != '' && $this -> _getParam('max') >= 0) {
			$limitFblog = $this -> _getParam('max');
		} else {
			$limitFblog = 6;
		}

		// Get featured blogs
		$btable = Engine_Api::_() -> getItemTable('blog');
		$select = $btable -> select() -> where("search = 1") -> where("draft = 0") -> where("is_approved = 1") -> where("is_featured = 1") -> order('RAND()') -> limit($limitFblog);

		$this -> view -> blogs = $blog = $btable -> fetchAll($select);
		if(!count($blog))
		{
			return $this -> setNoRender();
		}
	}

}
