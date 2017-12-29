<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Wiki
 * @copyright  Copyright 2011 YouNet Company
 * @license    http://www.modules2buy.com/
 * @version    $Id: Core.php
 * @author     Minh Nguyen
 */
class Ynwiki_Plugin_Core
{
  public function onStatistics($event)
  {
    $table  = Engine_Api::_()->getDbTable('pages', 'ynwiki');
    $select = new Zend_Db_Select($table->getAdapter());
    $select->from($table->info('name'), 'COUNT(*) AS count');
    $event->addResponse($select->query()->fetchColumn(0), 'pages');
  }
}