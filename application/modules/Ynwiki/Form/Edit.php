<?php
/**
 * YouNet
 *
 * @category   Application_Extensions
 * @package    Auction
 * @copyright  Copyright 2011 YouNet Developments
 * @license    http://www.modules2buy.com/
 * @version    $Id: Edit.php
 * @author     Minh Nguyen
 */
class Ynwiki_Form_Edit extends Ynwiki_Form_Create
{
  public $_error = array();
  public function init()
  {
    parent::init();
    $this->setTitle('Edit Page')
         ->setDescription("Edit your page below, then click 'Save Changes' to save your page.");
    $this->submit->setLabel('Save Changes');
  }
}