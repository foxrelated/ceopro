<?php
class Ynmultilisting_Plugin_Task_SubsribeListings extends Core_Plugin_Task_Abstract
{
	public function execute()
	{
		Engine_Api::_() -> ynmultilisting() -> sendSubsribeMail();
	}
}
