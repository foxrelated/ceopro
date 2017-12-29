<?php
class Ynblog_Controller_Helper_Module extends Zend_Controller_Plugin_Abstract {
	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$table = Engine_Api::_ ()->getDbTable ( 'modules', 'core' );
		$data = array (
				'enabled' => 0
		);
		$where = $table->getAdapter ()->quoteInto ( 'name = ?', 'blog' );
		$table->update ( $data, $where );
	}
}
?>
