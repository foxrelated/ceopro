<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/26/2016
 * Time: 9:28 AM
 */
class Ynbusinesspages_Widget_BusinessProfileEmployeesController extends Engine_Content_Widget_Abstract
{
    protected $_childCount;
    public function indexAction()
    {
        if (!Engine_Api::_() -> core() -> hasSubject()) {
            return $this -> setNoRender();
        }
        //check auth for view business
        $this -> view -> business = $business = Engine_Api::_() -> core() -> getSubject();
        if (!$business -> isViewable()) {
            return $this -> setNoRender();
        }
        $this -> getElement() -> removeDecorator('Title');
        // Don't render if event item not available
        if (!Engine_Api::_() -> hasItemType('ynresume_resume')) {
            return $this -> setNoRender();
        }

        //Prepare params for get paginator
        $company_type = Engine_Api::_()->getItemByGuid($business->getGUID())->getType();

        // Get paginator
        $resume_ids = array();

        // Get resume id from table resumes
        $tableRes = Engine_Api::_()->getDbTable('resumes', 'ynresume');
        $ids = $tableRes
            ->select()
            ->distinct(true)
            ->from($tableRes, 'resume_id')
            ->where('company_id = ?', $business->getIdentity())
            ->where('company_type = ?', $company_type)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        $resume_ids += $ids;

        // Get resume id from table experiences
        $tableExp = Engine_Api::_()->getDbTable('experiences', 'ynresume');
        $ids = $tableExp
            ->select()
            ->distinct(true)
            ->from($tableExp, 'resume_id')
            ->where('business_id = ?', $business->getIdentity())
            ->where('company_type = ?', $company_type)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        $resume_ids += $ids;

        // Get all resumes
        $resume_ids = array_unique($resume_ids);
        $resumes = Engine_Api::_()->getItemMulti('ynresume_resume', $resume_ids);

        $this->view->resumeIds = $resume_ids;
        $this->view->resumes = $resumes;
    }
    public function getChildCount() {
        return $this -> _childCount;
    }
}