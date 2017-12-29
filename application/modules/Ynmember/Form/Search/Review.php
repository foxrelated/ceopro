<?php
class Ynmember_Form_Search_Review extends Engine_Form
{
	public function init() 
	{
        $this -> setAttribs(array('class' => 'global_form_box', 'id' => 'filter_form'))
              -> setMethod('GET');
       
        $this->addElement('Text', 'review_for', array(
            'label' => 'Search Member',
            'alt' => 'Search members'
        ));

        $this->addElement('Text', 'review_by', array(
            'label' => 'Review By',
            'alt' => 'Review by'
        ));
        
        $this->addElement('Text', 'keyword', array(
            'label' => 'Keyword',
            'alt' => 'Keyword'
        ));
        
        $this->addElement('Select', 'orderby', array(
            'label' => 'Browse By',
            'multiOptions' => array(
                'creation_date' => 'Most Recent',
        		'most_rating' => 'Highest Rated',
        		'least_rating' => 'Least Rated',
        		'helpful' => 'Most Helpful',
                'view_count' => 'Most Viewed',
                'comment_count' => 'Most Commented',
            ),
        ));
        
        $view = Zend_Registry::get("Zend_View");
        $this->addElement('Select', 'filter_rating', array(
            'label' => 'Ratings',
            'multiOptions' => array(
        		'-1' => '',
                '5' => $view -> translate(array("%s star", "%s stars", 5), 5),
        		'4' => $view -> translate(array("%s star", "%s stars", 4), 4),
        		'3' => $view -> translate(array("%s star", "%s stars", 3), 3),
        		'2' => $view -> translate(array("%s star", "%s stars", 2), 2),
                '1' => $view -> translate(array("%s star", "%s stars", 1), 1),
            ),
        ));
        
        // Buttons
        $this->addElement('Button', 'search', array(
            'label' => 'Search',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}