<?php

class Ultimatenews_Widget_SearchUltimatenewsController extends Engine_Content_Widget_Abstract
{

    public function indexAction()
    {
        $flag = 0;
        $this->view->form = $form = new Ultimatenews_Form_Commonsearch();
		$params = array('category_active' => 1);
		if ($_SESSION['keysearch']['category_parent'])
			$params['category_parent'] = $_SESSION['keysearch']['category_parent'];
		
        $categories = Engine_Api::_()->ultimatenews()->getAllCategories($params);
        $this->view->categories = $categories;
        $data = array();
		if($_SESSION['keysearch']['category'] == '-10')
		{
			$form->category->addMultiOption('-10', $this->view->translate("No Feed"));
		}
		else
		{
			$form->category->addMultiOption('0', $this->view->translate("All Feeds"));
		}
        foreach ($categories as $category)
        {
            $form->category->addMultiOption($category['category_id'], $category['category_name']);
            $data[] = $category['category_id'];
        }
       
        $this->view->categoryparents = $categoryparents = Engine_Api::_()->ultimatenews()->getAllCategoryparents(array('category_active' => 1));

        foreach ($categoryparents as $categoryparent)
        {
            $form->categoryparent->addMultiOption($categoryparent['category_id'], $categoryparent['category_name']);
        }

        $other_id = 0;
        $other_name = 'Other';
        $form->categoryparent->addMultiOption($other_id, $other_name);

        if (isset($_SESSION['keysearch']))
        {
            $ultimatenews_search_query = $_SESSION['keysearch']['searchText'];
            $page = $_SESSION['keysearch']['nextpage'];
        }
        else
        {
            $category = $this->_getParam('category');
            $page = "1";
            if (isset($_POST['nextpage']) && !(empty($_POST['nextpage'])))
            {
                $page = $_POST['nextpage'];
            }
            $searchText = $_POST['search'];

            $ultimatenews_search_arr = explode(" ", $searchText);

            $ultimatenews_searchs = array();
            foreach ($ultimatenews_search_arr as $item)
            {
                if ($item != "")
                {
                    $ultimatenews_searchs[] = $item;
                }
            }
            $ultimatenews_search_query = implode("%", $ultimatenews_searchs);
            $ultimatenews_search_query = "%" . $ultimatenews_search_query . "%";
            $_SESSION['category'] = $category;
            $_SESSION['searchText'] = $ultimatenews_search_query;
        }

        if (isset($_POST['start_date']) && !empty($_POST['start_date']))
        {
            
            $form->isValid($_POST);
            $values = $form->getValues();
            $start_date = $values['start_date'];
            $_SESSION['start_date'] = $start_date;
        }
        if (isset($_POST['end_date']) && !empty($_POST['end_date']))
        {
            $form->isValid($_POST);
            $values = $form->getValues();
            $end_date = $values['end_date'];
            $_SESSION['end_date'] = $end_date;

            if (strtotime($end_date) > 0 && strtotime($start_date) > 0 && strtotime($start_date) > strtotime($end_date))
            {
                $form->addError('End Date should be equal or greater than Start Date!');
                $flag = 1;
            }
        }

        if ($flag == 1)
        {
            return false;
        }

        if (isset($_SESSION['start_date']))
        {
            $start_date = $_SESSION['start_date'];
            $form->start_date->setValue($start_date);
        }
        if (isset($_SESSION['end_date']))
        {
            $end_date = $_SESSION['end_date'];
            $form->end_date->setValue($end_date);
        }
        if (isset($_SESSION['category']))
        {
            $category = $_SESSION['category'];
            $form->category->setValue($category);
        }
        if (isset($_SESSION['searchText']))
        {
            $searchText = $_SESSION['searchText'];
            $form->search->setValue($searchText);
        }
    }

}
