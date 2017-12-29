<?php
class Ynbusinesspages_ProfileController extends Core_Controller_Action_Standard
{
  	public function init()
  	{
  		$viewer = Engine_Api::_() -> user() -> getViewer();
  		$subject = null;
  		if( !Engine_Api::_()->core()->hasSubject() )
  		{
  			$id = $this->_getParam('id');
  			if( null !== $id )
  			{
  				$subject = Engine_Api::_()->getItem('ynbusinesspages_business', $id);
				if (!$subject || $subject->deleted) {
					return $this->_helper->requireSubject()->forward();
				}
				if ($subject->deleted)
				//check auth view if this business has owner
				if(!$subject -> is_claimed)
				{
	  				if( !$subject->isAllowed('view', $viewer) )
	  				{
	  					return $this -> _helper -> requireAuth() -> forward();
	  				}
				}
  				if( $subject && $subject->getIdentity() )
  				{
  					Engine_Api::_()->core()->setSubject($subject);
  				}
  				else 
  				{
	  				return;
  				}
  			}
  		}
  		$this->_helper->requireSubject('ynbusinesspages_business');
  	}
  
	public function indexAction()
	{
	    if(!Engine_Api::_()->core()->hasSubject()) 
		{
	    	return $this->_helper->requireSubject()->forward();
		}	
	    $subject = Engine_Api::_()->core()->getSubject();
		if (!$subject || $subject->deleted) {
			return $this->_helper->requireSubject()->forward();
		}
        // Check authorization to view business.
        if (!$subject->isViewable()) {
            return $this -> _helper -> requireAuth() -> forward();
        }
	    $viewer = Engine_Api::_()->user()->getViewer();
		if($subject -> status != 'published')
		{
			if(!$subject -> is_claimed)
			{
				if(!$viewer -> isAdmin() && !$viewer -> isSelf($subject -> getOwner()))
				{
					return $this -> _helper -> requireAuth() -> forward();
				}
			}
		}
		$subject -> view_count += 1;
		$subject -> save();

        $this->view->headScript()->appendFile(Zend_Registry::get('StaticBaseUrl') .
            'application/modules/Ynbusinesspages/externals/scripts/more-tabs.js');

        // Set meta
        $og = '<meta property="og:image" content="' . $this->finalizeUrl($subject->getPhotoUrl()) . '" />';
        $og .= '<meta property="og:image:width" content="640" />';
        $og .= '<meta property="og:image:height" content="442" />';
        $coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
        $covers = $coverTbl -> getCoverByBusiness($subject->getIdentity());
        foreach ($covers as $photo) {
            $og .= '<meta property="og:image" content="' . $this->finalizeUrl($photo->getPhotoUrl()) . '" />';
        }
        $og .= '<meta property="og:title" content="' . $subject->getTitle() . '" />';
        $og .= '<meta property="og:description" content="' . $this->view->string()->striptags($subject->getDescription()) . '" />';
        $og .= '<meta property="og:url" content="' . $this->finalizeUrl($subject->getHref()) . '" />';
        $og .= '<meta property="og:updated_time" content="' . strtotime($subject->modified_date) . '" />';
        $og .= '<meta property="og:type" content="website" />';
        $this->view->layout()->headIncludes .= $og;

	    // Render
	    $this->_helper->content
	        ->setNoRender()
	        ->setEnabled()
	        ;
  	}

  	private function finalizeUrl($url)
    {
        if ($url)
        {
            if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
            {
                $pageURL = 'http';
                if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
                {
                    $pageURL .= "s";
                }
                $pageURL .= "://";
                $pageURL .= $_SERVER["SERVER_NAME"];
                $url = $pageURL . '/'. ltrim( $url, '/');
            }
        }

        return $url;
    }
}
