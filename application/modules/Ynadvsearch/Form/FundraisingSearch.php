<?php
class Ynadvsearch_Form_FundraisingSearch extends Engine_Form
{
/*----- Init Form Function -----*/
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      	'style' => 'margin-bottom: 15px',
        'method' => 'GET',
      ));
    //Text filter element
    $this->addElement('Text', 'search', array(
      'label' => 'Search',
    ));

    //Browse By Filter Element
    $this->addElement('Select', 'show', array(
      'label' => 'View',
      'multiOptions' => array(
      	''  => 'All',
        '1' => 'My Own Campaigns',
        '2' => 'My Donated Campaigns',
      ),
      'value' => '1',
    ));

	//Type Filter
    if(Engine_Api::_ ()->getApi ( 'core', 'ynfundraising' )->checkIdeaboxPlugin ())
	{
	    $this->addElement('Select', 'type', array(
	      'label' => 'Type',
	      'multiOptions' => array(
	        ''  => 'All',
	        'idea' => 'Idea',
	        'trophy' => 'Trophy',
	        'user' => 'User',
	    ),
	      'value' => '',
	    ));
	}

    //Campaign Search - Status Filter
    $this->addElement('Select', 'status', array(
      'label' => 'Status',
      'multiOptions' => array(
        ''  => 'All',
        'ongoing' => 'Ongoing',
        'closed' => 'Closed',
        'reached' => 'Reached',
        'expired' => 'Expired',
    ),
      'value' => '',
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

    $this->addElement('Hidden', 'tag', array(
    		'order' => 101
    ));

    // Element: order
    $this->addElement ( 'Hidden', 'orderby', array (
    		'order' => 102,
    		'value' => 'campaign_id'
    ) );

    // Element: direction
    $this->addElement ( 'Hidden', 'direction', array (
    		'order' => 103,
    		'value' => 'DESC'
    ) );

    $this->addElement('Button', 'submit_btn', array(
        'label' => 'Search',
        'type' => 'submit',
        'ignore' => true
    ));
  }
}