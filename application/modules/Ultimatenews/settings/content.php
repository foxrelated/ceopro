<?php
return array(
    array(
        'title' => 'Menu News',
        'description' => 'Displays menu newa on listing News page.',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.menu-ultimatenews',
    ),
    array(
        'title' => 'Detail article',
        'description' => 'Displays detail article in detail page.',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.article-detail',
        'autoEdit' => true,
		'adminForm'=> array(
				'elements' => array(								
						array(
								'Text',
								'max',											
								array(
										'label' => 'Number of related news on widget',
										'value' => 10,				
								)
						),								
				),
		),
    ),    
    array(
        'title' => 'Categories',
        'description' => 'Displays categories on home page.',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.categories-ultimatenews',
        'defaultParams' => array(
            'title' => 'Categories',
        ),
    ),
    array(
        'title' => 'Recent News',
        'description' => 'Displays Recent News',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.lasted-ultimatenews',
        'defaultParams' => array(
            'title' => 'Recent News',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'title' => 'Recent News',
                    )
                ),
                array(
                    'Text',
                    'max',
                    array(
                        'label' => 'Max Item Count',
                        'description' => 'Number of shown data item of each widget.',
                        'value' => 5,
                        'validators' => array(
                        	array('Int', true),
        					array('GreaterThan',true,array(0))
						),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Search News',
        'description' => 'Displays search News on Listing ultimatenews page.',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.search-ultimatenews',
    ),
    array(
        'title' => 'Top News',
        'description' => 'Displays Top News',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.top-ultimatenews',
        'defaultParams' => array(
            'title' => 'Top News',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'title' => 'Top News',
                    )
                ),
                array(
                    'Text',
                    'max',
                    array(
                        'label' => 'Max Item Count',
                        'description' => 'Number of shown data item of each widget.',
                        'value' => 5,
                        'validators' => array(
                        	array('Int', true),
        					array('GreaterThan',true,array(0))
						),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Most Commented News',
        'description' => 'Displays Most Commented News',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.most-commented-ultimatenews',
        'defaultParams' => array(
            'title' => 'Most Commented News',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'title' => 'Most Commented News',
                    )
                ),
                array(
                    'Text',
                    'max',
                    array(
                        'label' => 'Max Item Count',
                        'description' => 'Number of shown data item of each widget.',
                        'value' => 5,
                        'validators' => array(
                        	array('Int', true),
        					array('GreaterThan',true,array(0))
						),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Most Liked News',
        'description' => 'Displays Most Liked News',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.most-liked-ultimatenews',
        'defaultParams' => array(
            'title' => 'Most Liked News',
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'title' => 'Most Liked News',
                    )
                ),
                array(
                    'Text',
                    'max',
                    array(
                        'label' => 'Max Item Count',
                        'description' => 'Number of shown data item of each widget.',
                        'value' => 5,
                        'validators' => array(
                        	array('Int', true),
        					array('GreaterThan',true,array(0))
						),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Featured News',
        'description' => 'Displays Featured News',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.featured-ultimatenews',
        'defaultParams' => array(
            'title' => 'Featured News',
            'max' => '10'
        ),
        'adminForm' => array(
            'elements' => array(
                array(
                    'Text',
                    'title',
                    array(
                        'label' => 'Title',
                        'title' => 'Featured News',
                    )
                ),
                array(
                    'Text',
                    'max',
                    array(
                        'label' => 'Max Item Count',
                        'description' => 'Number of shown data item of each widget.',
                        'value' => 10,
                        'validators' => array(
                        	array('Int', true),
        					array('GreaterThan',true,array(0))
						),
                    )
                ),
            )
        ),
    ),
    array(
        'title' => 'Listing News',
        'description' => 'Displays Listing News page',
        'category' => 'YNC - Ultimate News',
        'type' => 'widget',
        'name' => 'ultimatenews.list-ultimatenews',
        'autoEdit' => true,
		    'defaultParams' => array(
		      'title' => '',
		      'feeds_per_page' => 3,
		      'wide' => 3,
		   	  'narrow' => 7
		    ),
		    'adminForm' => "Ultimatenews_Form_WidgetCategory"
		),
		  
	array(
	    'title' => 'Show News With Category',
	    'description' => 'Show News With Category.',
	    'category' => 'YNC - Ultimate News',
	    'type' => 'widget',
	    'name' => 'ultimatenews.category-ultimatenews',
	    'autoEdit' => true,
	    'defaultParams' => array(
		      'title' => 'News',
		      'category_id' => 1,
		      'wide' => 3,
		   	  'narrow' => 7
		),
		'adminForm' => "Ultimatenews_Form_WidgetNewsCategory"
  	),
  	array(
	    'title' => 'Tag News',
	    'description' => 'Displays tag news on browse news page.',
	    'category' => 'YNC - Ultimate News',
	    'type' => 'widget',
	    'name' => 'ultimatenews.tag-news',
	    'defaultParams' => array(
		      'title' => 'Tags',
		),
  ),
)
?>