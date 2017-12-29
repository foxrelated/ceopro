<?php
class Advalbum_AdminManageController extends Core_Controller_Action_Admin
{
	public function indexAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_admin_main', array(), 'advalbum_admin_main_manage');

		$this -> view -> form = $form = new Advalbum_Form_Admin_AlbumSearch();
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = array_filter($params);

		if (empty($params['orderby']))
			$params['orderby'] = 'album_id';
		if (empty($params['direction']))
			$params['direction'] = 'DESC';

		$this -> view -> formValues = $params;

		$page = $this -> _getParam('page', 1);
		$this -> view -> paginator = Engine_Api::_() -> advalbum() -> getAlbumPaginator($params);
		$this -> view -> paginator -> setItemCountPerPage(25);
		$this -> view -> paginator -> setCurrentPageNumber($page);
	}

	public function photosAction()
	{
		$this -> view -> navigation = $navigation = Engine_Api::_() -> getApi('menus', 'core') -> getNavigation('advalbum_admin_main', array(), 'advalbum_admin_main_photos');

		$form = new Advalbum_Form_Admin_PhotoSearch();

		$this -> view -> form = $form;
		$form -> isValid($this -> _getAllParams());
		$params = $form -> getValues();
		$this -> view -> formValues = array_filter($params);

		if (empty($params['orderby']))
			$params['orderby'] = 'photo_id';
		if (empty($params['direction']))
			$params['direction'] = 'DESC';

		$this -> view -> formValues = $params;

		$page = $this -> _getParam('page', 1);
		$paginator = Engine_Api::_() -> advalbum() -> getPhotoPaginator($params);
		$paginator -> setItemCountPerPage(25);
		$paginator -> setCurrentPageNumber($page);
		$this -> view -> paginator = $paginator;
	}

	public function featuredAction()
	{
		$photo_id = $this -> _getParam('photo_id');
		$photo_good = $this -> _getParam('good');
		$db = Engine_Db_Table::getDefaultAdapter();
		$db -> beginTransaction();
		$ftable = Engine_Api::_() -> getDbtable('features', 'advalbum');
		$fName = $ftable -> info('name');
		$select = $ftable -> select() -> from($fName) -> where("photo_id = ?", $photo_id);
		$features = $ftable -> fetchAll($select);
		if (count($features) > 0)
		{
			$feature_id = $features[0] -> feature_id;
			$feature = Engine_Api::_() -> getItem('advalbum_feature', $feature_id);
			$feature -> photo_good = $photo_good;
			$feature -> save();
		}
		else
		{
			$feature = Engine_Api::_() -> getDbtable('features', 'advalbum') -> createRow();
			$feature -> photo_id = $photo_id;
			$feature -> photo_good = $photo_good;
			$feature -> save();
		}
		$db -> commit();
	}

	public function deleteAction()
	{
		$id = $this -> _getParam('id');
		$this -> view -> photo_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				$ftable = Engine_Api::_() -> getDbtable('features', 'advalbum');
				$fName = $ftable -> info('name');
				$select = $ftable -> select() -> from($fName) -> where("photo_id = ?", $id);
				$features = $ftable -> fetchAll($select);
				if (count($features) > 0)
				{
					$feature_id = $features[0] -> feature_id;
					$feature = Engine_Api::_() -> getItem('advalbum_feature', $feature_id);
					$feature -> delete();
				}
				$blog = Engine_Api::_() -> getItem('advalbum_photo', $id);
				// delete the blog entry into the database
				$blog -> delete();
				$db -> commit();
			}

			catch ( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}

		// Output
		$this -> renderScript('admin-manage/delete.tpl');
	}

	public function deleteselectedAction()
	{
		$this->_helper->layout->setLayout('admin-simple');
		$type = $this -> _getParam('type', 'photo');
		$typeText = ($type == 'photo') ? $this->view->translate('photo(s)') : $this->view->translate('album(s)');
		$ids = $this -> _getParam('ids', null);
		$ids_array = explode(",", $ids);
//		$count = count($ids_array);
		$this -> view -> form = $form = new Advalbum_Form_Admin_DeletePhotos();
		$description = $this->view->translate('Are you sure that you want to delete the selected %s? It will not be recoverable after being deleted.', $typeText);
		$form -> setDescription($description);

		// Save values
		if ($this -> getRequest() -> isPost())
		{
			foreach ($ids_array as $id)
			{
				$item = Engine_Api::_() -> getItem('advalbum_' . $type, $id);
				if ($item)
				{
					if ($type == 'photo') {
						
					}
					$ftable = Engine_Api::_() -> getDbtable('features', 'advalbum');
					$fName = $ftable -> info('name');
					$select = $ftable -> select() -> from($fName) -> where("photo_id = ?", $id);
					$features = $ftable -> fetchAll($select);
					if (count($features) > 0)
					{
						$feature_id = $features[0] -> feature_id;
						$feature = Engine_Api::_() -> getItem('advalbum_feature', $feature_id);
						$feature -> delete();
					}
					$item -> delete();
				}
			}

			$this->view->status = 'deleted';

			return $this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => true,
				'parentRefresh' => 10,
				'messages' => Array('Success!')
			));
		}
	}

	public function deleteAlbumAction()
	{
		$id = $this -> _getParam('id');
		$this -> view -> album_id = $id;
		// Check post
		if ($this -> getRequest() -> isPost())
		{
			$db = Engine_Db_Table::getDefaultAdapter();
			$db -> beginTransaction();

			try
			{
				$album = Engine_Api::_() -> getItem('advalbum_album', $id);
				// delete the blog entry into the database
				$album -> delete();
				$db -> commit();
			}

			catch ( Exception $e )
			{
				$db -> rollBack();
				throw $e;
			}

			$this -> _forward('success', 'utility', 'core', array(
				'smoothboxClose' => 10,
				'parentRefresh' => 10,
				'messages' => array('')
			));
		}

		// Output
		$this -> renderScript('admin-manage/delete-album.tpl');
	}

	/* ----- Set Featured Album Function ----- */
	public function featureAction()
	{
		// Get params
		$id = $this -> _getParam('album_id');
		$is_featured = $this -> _getParam('status');

		// Get campaign need to set featured
		$table = Engine_Api::_() -> getDbTable('albums', 'advalbum');
		$select = $table -> select() -> where("album_id = ?", $id);
		$album = $table -> fetchRow($select);
		// Set featured/unfeatured
		if ($album)
		{
			$album -> featured = $is_featured;
			$album -> save();
		}
	}

}
