<?php
class Ynmultilisting_Form_Review_Search extends Engine_Form
{
    public function init()
    {
        $this -> setAttribs(array('class' => 'global_form_box filters', 'id' => 'member_filter_form'))
            -> setMethod('GET');

        $this->addElement('Text', 'keyword', array(
            'label' => 'Keyword',
            'alt' => 'Keyword'
        ));

        $this->addElement('Text', 'review_for', array(
            'label' => 'For Listing',
            'alt' => 'For Listing'
        ));

        $this->addElement('Select', 'type', array(
            'label' => 'Review By',
            'multiOptions' => array(
                '' => 'All',
                'member' => 'Member Only',
                'editor' => 'Editor Only',
            ),
        ));

        $this->addElement('Text', 'review_by', array(
            'label' => 'Reviewer',
            'alt' => 'Reviewer'
        ));

        $view = Zend_Registry::get("Zend_View");
        $this->addElement('Select', 'filter_rating', array(
            'label' => 'Average Rating',
            'multiOptions' => array(
                '' => '',
                '5' => $view -> translate(array("%s star", "%s stars", 5), 5),
                '4' => $view -> translate(array("%s star", "%s stars", 4), 4),
                '3' => $view -> translate(array("%s star", "%s stars", 3), 3),
                '2' => $view -> translate(array("%s star", "%s stars", 2), 2),
                '1' => $view -> translate(array("%s star", "%s stars", 1), 1),
            ),
        ));

        $this->addElement('Select', 'orderby', array(
            'label' => 'Sort By',
            'multiOptions' => array(
                'helpful_count' => 'Most Useful',
                'creation_date' => 'Most Recent',
                'most_rating' => 'Most Rated',
                'least_rating' => 'Least Rated',
            ),
        ));

        $this->addElement('Select', 'category_id', array(
            'required'  => false,
            'allowEmpty'=> true,
            'label' => 'Category',
        ));
        $listingtype = Engine_Api::_()->ynmultilisting()->getCurrentListingType();
        $categories = $listingtype -> getCategories();

        foreach ($categories as $item)
        {
            $this -> category_id -> addMultiOption($item['category_id'], str_repeat("-- ", $item['level'] - 1) . $view -> translate($item['title']));
        }

        $this->addElement('hidden', 'page', array(
            'order' => '100'
        ));

        // Buttons
        $this->addElement('Button', 'search', array(
            'label' => 'View Reviews',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}
