<?php
class Ynadvsearch_Form_WikiSearch extends Engine_Form
{
  public function init()
  {
    $this
      ->setAttribs(array(
        'id' => 'filter_form',
        'class' => 'global_form_box',
      ))
      ->setMethod('GET')
      ; 
    
    $this->addElement('Text', 'title', array(
      'label' => 'Name',
    ));
    $this->addElement('Text', 'owner', array(
      'label' => 'Creator',
    ));
   
    $this->addElement('Select', 'orderby', array(
      'label' => 'Browse By',
      'multiOptions' => array(
        'creation_date' => 'Most Recent',
        'view_count' => 'Most Viewed',
        'follow_count' => 'Most Followed',
        'favourite_count' => 'Most Favourite',
        'like_count' => 'Most Liked',
        'comment_count' => 'Most Commented',
        'rate_ave' => 'Most Rated',
      ),
    ));

    $this->addElement('Hidden', 'page', array(
      'order' => 100
    ));

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