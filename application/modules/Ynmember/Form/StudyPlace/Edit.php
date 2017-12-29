<?php
class Ynmember_Form_StudyPlace_Edit extends Ynmember_Form_StudyPlace_Create
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
		$this->setTitle('Edit a school');
		$this->addPlace->setLabel('Save Changes');
	}
}