<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */

class Ynadvsearch_Form_FilesharingSearch extends Engine_Form {
	/* ----- Init Form Function ----- */
	public function init() {
		$this->setAttribs ( array (
			'id' => 'filter_form',
			'class' => 'global_form_box',
			'style' => 'margin-bottom: 15px',
			'method' => 'GET'
		));
		
		// Text filter element
		$this->addElement ( 'Text', 'search', array (
			'label' => 'Search',
		) );

		// Browse By Filter Element
		$this->addElement ( 'Select', 'type', array (
			'label' => 'Type',
			'multiOptions' => array (
				'all'	=> 'All',
				'file' => 'File',
				'folder' => 'Folder'
			),
		) );

		// Browse By Filter Element
		$this->addElement ( 'Select', 'orderby', array (
			'label' => 'List By',
			'multiOptions' => array (
				'creation_date' => 'Most Recent',
				'view_count' => 'Most Viewed',
				'download_count' => 'Most Downloaded'
			),
		) );

		$param = Zend_Controller_Front::getInstance()->getRequest()->getParam('type');
		if ($param == 'folder') {
			unset($this->orderby->options['download_count']);
		}

		$this->addElement('Hidden', 'tag', array(
			'order' => 101
		));
        $this->addElement('Button', 'submit_btn', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
	}
}