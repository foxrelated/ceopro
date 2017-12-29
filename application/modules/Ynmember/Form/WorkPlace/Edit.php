<?php
class Ynmember_Form_WorkPlace_Edit extends Ynmember_Form_WorkPlace_Create
{
	protected $_location;
	
	public function getLocation()
	{
		return $this -> _location;
	}
	
	public function setLocation($location)
	{
		$this -> _location = $location;
	} 
	
	public function init()
	{
		parent::init();
		$this->setTitle('Edit Work Place');
		$this->addPlace->setLabel('Save Changes');
	}
}