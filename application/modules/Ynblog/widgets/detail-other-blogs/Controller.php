<?php
class Ynblog_Widget_DetailOtherBlogsController extends Engine_Content_Widget_Abstract {
	public function indexAction()
	{
		if( !Engine_Api::_()->core()->hasSubject('blog') ) {
			return $this -> setNoRender();
		}
		$blog = Engine_Api::_()->core()->getSubject('blog');
		//Get number of blogs display
		if ($this -> _getParam('max') != '' && $this -> _getParam('max') >= 0) {
			$limitMVblog = $this -> _getParam('max');
		} else {
			$limitMVblog = 4;
		}

		//Select glogs
		$table = Engine_Api::_() -> getItemTable('blog');
		$name = $table -> info('name');
		$select = $table -> select() -> from($name)
			-> order('view_count DESC')
			-> where("search = 1")
			-> where("owner_id = ?", $blog -> owner_id)
			-> where("draft = 0")
			-> where('is_approved = 1')
			-> where("blog_id <> ?", $blog -> blog_id)
			-> limit($limitMVblog);

		$this -> view -> blogs = $blogs = $table -> fetchAll($select);
		if(!count($blogs))
		{
			return $this -> setNoRender();
		}
	}

}
