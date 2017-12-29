<?php

class Ynadvsearch_Widget_YnEventResultController extends Engine_Content_Widget_Abstract {
    public function indexAction() {
    	$ynevent_enable = Engine_Api::_() -> ynadvsearch() -> checkYouNetPlugin('ynevent');
		if(!$ynevent_enable)
		{
			return $this -> setNoRender();
		}
		
		// Prepare
		$viewer = Engine_Api::_() -> user() -> getViewer();
		$this -> view -> canCreate = Engine_Api::_() -> authorization() -> isAllowed('event', null, 'create');
		$request = Zend_Controller_Front::getInstance()->getRequest();
		$filter = $request -> getParam('filter', 'future');
		if ($filter != 'past' && $filter != 'future')
			$filter = 'future';
		$this -> view -> filter = $filter;
		
		// Create form
		$this -> view -> formFilter = $formFilter = new Ynevent_Form_Filter_Browse();
		$formFilter -> getElement('is_search') -> setValue('1');

		$defaultValues = $formFilter -> getValues();

		if (!$viewer || !$viewer -> getIdentity())
		{
			$formFilter -> removeElement('view');
		}

		$val = $request -> getParams();
		
		// Populate form data
		if ($formFilter -> isValid($val))
		{
			$this -> view -> formValues = $values = $formFilter -> getValues();
		}
		else
		{
			$formFilter -> populate($defaultValues);
			$this -> view -> formValues = $values = array();
			$this -> view -> message = "The search value is not valid !";
		}

		// Prepare data
		$this -> view -> formValues = $values = array_merge($formFilter -> getValues(), $_GET);

		if ($viewer -> getIdentity() && @$values['view'] == 5)
		{
			$values['users'] = array();
			foreach ($viewer->membership()->getMembersInfo(true) as $memberinfo)
			{
				$values['users'][] = $memberinfo -> user_id;
			}
		}
		if ($viewer -> getIdentity() && @$values['view'] == 4)
		{
			$followTable = Engine_Api::_() -> getDbtable('follow', 'ynevent');
			$values['events'] = array();
			foreach ($followTable->getFollowEvents($viewer->user_id) as $event)
			{
				$values['events'][] = $event -> resource_id;
			}
		}
		else
		{
			if ($viewer -> getIdentity() && @$values['view'] != null)
			{
				$memberTable = Engine_Api::_() -> getDbtable('membership', 'ynevent');
				$values['events'] = array();
				foreach ($memberTable->getMemberEvents($viewer->user_id, $values['view']) as $event)
				{
					$values['events'][] = $event -> resource_id;
				}
			}
		}
		//search in sub categories
		if (!empty($values['category_id']) && $values['category_id'] > 0)
		{
			$parentCat = $values['category_id'];
			$parentNode = Engine_Api::_() -> getDbtable('categories', 'ynevent') -> getNode($parentCat);
			if ($parentNode)
			{
				$childsNode = $parentNode -> getAllChildrenIds();
				$values['arrayCat'] = $childsNode;
			}
		}

		if (!empty($values['start_date']))
		{
			$temp = explode("-", $values['start_date']);
			//Date format is Y-m-d;
			if (count($temp) == 3)
				$values['start_date'] = $temp[0] . "-" . $temp[1] . "-" . $temp[2];
		}
		if (!empty($values['end_date']))
		{
			$temp = explode("-", $values['end_date']);
			//Date format is Y-m-d;
			if (count($temp) == 3)
				$values['end_date'] = $temp[0] . "-" . $temp[1] . "-" . $temp[2];
		}

		$values['search'] = 1;
		if ($selected_day = $this -> _getParam('selected_day'))
		{
			$values['selected_day'] = $selected_day;
		}
		else
		{
			if ($filter == "past")
			{
				$values['past'] = 1;
			}
			else
			{
				$values['future'] = 1;
				$values['order'] = new Zend_Db_Expr("ABS(TIMESTAMPDIFF(SECOND,NOW(), starttime))");
				$values['direction'] = 'asc';
			}
		}
		// check to see if request is for specific user's listings
		if (($user_id = $this -> _getParam('user')))
		{
			$values['user_id'] = $user_id;
		}
		
		//request for selected day
		if ($selected_day = $this -> _getParam('selected_day'))
		{
			$values['selected_day'] = $selected_day;
		}

		//tag
		if ($tag_id = $this -> _getParam('tag'))
		{
			$values['tag'] = $tag_id;
		}
		
		//count result
		$this->view->total_content =  Engine_Api::_() -> getItemTable('event') -> getEventPaginator(array('search' => 1, 'future' => 1))->getTotalItemCount();
		$table = Engine_Api::_()->getItemTable('ynadvsearch_contenttype');
		$contentType = $table->getContentType('event');
		if ($contentType) $this->view->label_content = $contentType->title;
				
		$this -> view -> is_search = array_key_exists('keyword', $this -> _getAllParams());
		// Get paginator
		$this -> view -> paginator = $paginator = Engine_Api::_() -> getItemTable('event') -> getEventPaginator($values);
		
		$value_params = Zend_Controller_Front::getInstance()->getRequest()->getParams();
		$page = $value_params['page'];
		if(empty($page))
		{
			$page = 1;
		}
		$paginator -> setCurrentPageNumber($page);
        
		// Render
		
		$search = $this->_getParam('is_search');
		$selected_day =  $this->_getParam('selected_day');
		$tag =  $this->_getParam('tag');
    }

}
