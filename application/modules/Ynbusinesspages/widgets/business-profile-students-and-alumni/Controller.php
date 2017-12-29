<?php

/**
 * Created by PhpStorm.
 * User: Nguyen Thanh
 * Date: 7/26/2016
 * Time: 4:13 PM
 */
class Ynbusinesspages_Widget_BusinessProfileStudentsAndAlumniController extends Engine_Content_Widget_Abstract
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

        $tableEdu = Engine_Api::_()->getDbTable('educations', 'ynresume');
        $resume_ids = $tableEdu
            ->select()
            ->distinct(true)
            ->from($tableEdu, 'resume_id')
            ->where('business_id = ?', $business->getIdentity())
            ->where('company_type = ?', $company_type)
            ->query()
            ->fetchAll(Zend_Db::FETCH_COLUMN);
        $resumes_tmp = Engine_Api::_()->getItemMulti('ynresume_resume', $resume_ids);

        foreach ($resumes_tmp as $resume)
            $resumes[] = $resume;
        $resumes = array_unique($resumes);
        $this->view->resumeIds = $resume_ids;
        $this->view->resumes = $resumes;
    }
    public function getChildCount() {
        return $this -> _childCount;
    }
}