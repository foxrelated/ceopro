<?php
class Advgroup_Form_Link_Delete extends Engine_Form
{
  public function init(){
		$this->setAttribs(array(
			'method' =>'post',
		))
		->setTitle('Delete Link?')
		->setDescription('Are you sure that you want to delete this link? It will not be recoverable after being deleted.');

    $this->addElement('Hidden','link_id');
    
    $this->addElement('Button', 'submit', array(
      'label' => 'Delete Link',
      'type' => 'submit',
      'ignore' => true,
      'decorators' => array('ViewHelper')
    ));

    $this->addElement('Cancel', 'cancel', array(
      'label' => 'cancel',
      'link' => true,
      'prependText' => ' or ',
      'href' => '',
      'onclick' => 'parent.Smoothbox.close();',
      'decorators' => array(
        'ViewHelper'
      )
    ));

    $this->addDisplayGroup(array('submit', 'cancel'), 'buttons');
  }
}
?>
