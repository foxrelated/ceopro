<?php
class Ynblog_Widget_UserBlogArchivesController extends Engine_Content_Widget_Abstract
{
	public function indexAction()
  {
    if(Engine_Api::_()->core()->hasSubject('user')){
      $user = Engine_Api::_()->core()->getSubject('user');
    }
    else if( Engine_Api::_()->core()->hasSubject('blog') ) {
      $blog = Engine_Api::_()->core()->getSubject('blog');
      $user = $blog->getOwner();
    }
    else {
          return $this->setNoRender();
    }
    $archiveList = Engine_Api::_()->ynblog()->getArchiveList($user->getIdentity());
    $this->view->archieve_list = $this->_handleArchiveList($archiveList);
  }

   protected function _handleArchiveList($results)
  {
    $localeObject = Zend_Registry::get('Locale');

    $blog_dates = array();
    foreach ($results as $result)
      $blog_dates[] = strtotime($result->creation_date);

    // GEN ARCHIVE LIST
    $time = time();
    $archive_list = array();

    foreach( $blog_dates as $blog_date )
    {
      $ltime = localtime($blog_date, TRUE);
      $ltime["tm_mon"] = $ltime["tm_mon"] + 1;
      $ltime["tm_year"] = $ltime["tm_year"] + 1900;

      // LESS THAN A YEAR AGO - MONTHS
      if( $blog_date+31536000>$time )
      {
        $date_start = mktime(0, 0, 0, $ltime["tm_mon"], 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, $ltime["tm_mon"]+1, 1, $ltime["tm_year"]);
        //$label = date('F Y', $blog_date);
        $type = 'month';

        $dateObject = new Zend_Date($blog_date);
        $format = $localeObject->getTranslation('MMMMd', 'dateitem', $localeObject);
        $label = $dateObject->toString($format, $localeObject);
      }

      // MORE THAN A YEAR AGO - YEARS
      else
      {
        $date_start = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]);
        $date_end = mktime(0, 0, 0, 1, 1, $ltime["tm_year"]+1);
        //$label = date('Y', $blog_date);
        $type = 'year';

        $dateObject = new Zend_Date($blog_date);
        $format = $localeObject->getTranslation('yyyy', 'dateitem', $localeObject);
        if( !$format ) {
          $format = $localeObject->getTranslation('y', 'dateitem', $localeObject);
        }
        $label = $dateObject->toString($format, $localeObject);
      }

      if( !isset($archive_list[$date_start]) )
      {
        $archive_list[$date_start] = array(
          'type' => $type,
          'label' => $label,
          'date_start' => $date_start,
          'date_end' => $date_end,
          'count' => 1
        );
      }
      else
      {
        $archive_list[$date_start]['count']++;
      }
    }

    return $archive_list;
  }
}