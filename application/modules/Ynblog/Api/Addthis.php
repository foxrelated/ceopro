<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
require_once(APPLICATION_PATH . '/application/modules/Ynblog/externals/lib/Service.class.php');
require_once(APPLICATION_PATH . '/application/modules/Ynblog/externals/lib/Request.class.php');
require_once(APPLICATION_PATH . '/application/modules/Ynblog/externals/lib/Authentication.class.php');
require_once(APPLICATION_PATH . '/application/modules/Ynblog/externals/lib/Metric.class.php');
require_once(APPLICATION_PATH . '/application/modules/Ynblog/externals/lib/Dimension.class.php');
require_once(APPLICATION_PATH . '/application/modules/Ynblog/externals/lib/QueryParameter.class.php');
class Ynblog_Api_Addthis extends Core_Api_Abstract
{
    /**
     * Main process of widget
     * 
     * @param array $args
     * @param array $instance
     */
    public function widget($metric,$url = null) {
        $username = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.username');
        $password = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.password');
        $dimension = 'url';
        $oRequest = new Request(
            new Authentication($username, $password),
            new Metric($metric),
            new Dimension($dimension),
            Ynblog_Api_Addthis::getServiceQuery()
        );
        $oService  = new Service($oRequest);
        $response  = $oService->getData();
        echo Ynblog_Api_Addthis::getContent($response,$url);
        return Ynblog_Api_Addthis::getContent($response,$url);

    }


    /**
     * Return the widgets content for given data
     * 
     * @param array $response
     * @return string
     */
    public function getContent($data,$url) {
        if(empty($data)) {
            return "0";
        }
        return Ynblog_Api_Addthis::getDataContent($data,$url);;
    }

    /**
     * Returns the content for the given analytics data
     * 
     * @param array $data
     * @return string
     */
    protected function getDataContent($data,$url) {
        $content = '';  
        foreach($data as $oData) {
            if($oData->url == $url)
            {
                foreach($oData as $key => $value) {
                    return $value;
                }
            }
        }
        return '<td>0</td>';
    }

    /**
     * Creates parameters to send to the API based on current
     * widget settings
     * 
     * @return array
     */
    protected function getServiceQuery() {
        $aQuery   = array();
        $pubid = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.pubid');
        $domain = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.domain');
        $period = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.period');
        $aQuery[] = new QueryParameter('pubid',$pubid);
        $aQuery[] = new QueryParameter('period', $period);
        $aQuery[] = new QueryParameter('domain', $domain);
        return $aQuery;
    }
}
?>
