<?php
class Ynblog_Api_Session extends Core_Api_Abstract{

   /*----- Store Data Into Session Function -----*/
  public function setSession($input = array(),$name = ''){
    $ynblog_search = new Zend_Session_Namespace($name);
    $ynblog_search->session_array   = $input;
  }

  /*----- Get Data From Session Function -----*/
  public function getSession($params = null,$name = null){
    $ynblog_search = new Zend_Session_Namespace($name);

    if(isset( $ynblog_search->session_array )) {
      $params = $ynblog_search->session_array;
    }
    return $params;
  }

  /*----- Unset Session Function -----*/
  public function unsetSession($name = null){
    $ynblog_search = new Zend_Session_Namespace($name);
    // Search field
    if(isset( $ynblog_search->session_array )) {
      $ynblog_search->__unset('session_array');
    }
  }
}
?>
