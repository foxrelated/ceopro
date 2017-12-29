<?php
class Ynmember_Form_Settings_Privacy extends Engine_Form
{
	public    $saveSuccessful  = FALSE;
	protected $_roles          = array('owner', 'member', 'network', 'registered', 'everyone');
	protected $_item;

	public function setItem(User_Model_User $item)
	{
		$this->_item = $item;
	}

	public function getItem()
	{
		if( null === $this->_item ) {
			throw new User_Model_Exception('No item set in ' . get_class($this));
		}
		return $this->_item;
	}

	public function init()
	{
		$auth = Engine_Api::_()->authorization()->context;
		$user = $this->getItem();

		$this->setTitle('Adv Member Privacy Settings')
		->setAction(Zend_Controller_Front::getInstance()->getRouter()->assemble(array()))
		;

		$availableLabels = array(
		      'owner'       => 'Only Me',
		      'member'      => 'Only My Friends',
		      'network'     => 'Friends & Networks',
		      'registered'  => 'All Registered Members',
		);

		// Init profile view
		$get_notification_options = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('ynmember_user', $user, 'auth_get_notification');
		$get_notification_options = array_intersect_key($availableLabels, array_flip($get_notification_options));

		$this->addElement('Radio', 'get_notification_privacy', array(
	      'label' => 'Get Notification Privacy',
	      'description' => 'YNMEMBER_GET_NOTIFICATION_PRIVACY',
	      'multiOptions' => $get_notification_options,
		));
		$this -> get_notification_privacy -> getDecorator("Description") -> setOption("placement", "prepend") -> setEscape(FALSE);
		
		foreach( $this->_roles as $role ) {
			if( 1 === $auth->isAllowed($user, $role, 'get_notification') ) {
				$this->get_notification_privacy->setValue($role);
			}
		}

		// Init submit
		$this->addElement('Button', 'submit', array(
	      'label' => 'Save Changes',
	      'type' => 'submit',
	      'ignore' => true
		));

		return $this;
	}

	public function save()
	{
		$auth = Engine_Api::_()->authorization()->context;
		$user = $this->getItem();

		// Process member profile getting notification privacy
		$notification_privacy_value = $this->getValue('get_notification_privacy');
		if( empty($notification_privacy_value) ) 
		{
			$privacy_setting = end(Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('user', $user, 'auth_get_notification'));
			// If admin did not choose any options, make it everyone.
			// If not, use the one option they have set since the only option may not aways be set to 'everyone'.
			$notification_privacy_value = empty($privacy_setting)
				? 'everyone'
				: $privacy_setting;
		}

		$privacy_max_role = array_search($notification_privacy_value, $this->_roles);
		foreach( $this->_roles as $i => $role )
		{
			$auth->setAllowed($user, $role, 'get_notification', ($i <= $privacy_max_role) );
		}
	}
} // end public function save()