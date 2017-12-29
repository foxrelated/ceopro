<style>
#global_page_ynmultilisting-admin-category-edit-category form p.description
{
	display: inline-block;
}
</style>

<?php echo $this->form->setAttrib('class', 'global_form_popup')->render($this) ?>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>

<script type="text/javascript">
window.addEvent('domready', function(){
	var addMoreAddRatingCriteria = function(obj, e, params) {
		if (e)
        {
	        e.preventDefault();
        }
        var oriAddInfo = $('rating_criteria-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('name','more_rating_criteria[]');
        if (params.id)
        {
        	children[1].getChildren()[0].set('name','rating_criteria_' + params.id);
        }
        children[1].getChildren()[0].set('class','btn_form_inline');
        if (params.value)
        {
	        children[1].getChildren()[0].set('value',params.value);
        }
        else
        {
        	children[1].getChildren()[0].set('value','');
        }
       
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('rating_criteria-wrapper', 'after');
        return newAddInfo;
    };

    var addMoreAddReviewCriteria = function(obj, e, params) {
        if (e)
        {
	        e.preventDefault();
        }
        var oriAddInfo = $('review_criteria-wrapper');
        var newAddInfo = oriAddInfo.clone(true);
        var children = newAddInfo.getChildren();
        children[0].getChildren()[0].destroy();
        children[1].getChildren()[0].set('name','more_review_criteria[]');
        if (params.id)
        {
        	children[1].getChildren()[0].set('name','review_criteria_' + params.id);
        }
        children[1].getChildren()[0].set('class','btn_form_inline');
        if (params.value)
        {
	        children[1].getChildren()[0].set('value',params.value);
        }
        else
        {
        	children[1].getChildren()[0].set('value','');
        }
        var remove = new Element('a', {
            href: 'javascript:void(0)',
            html: '',
            'class': 'fa fa-minus-circle',
            events : {
                click: function(event) {
                    event.preventDefault();
                    this.getParent('.form-wrapper').destroy();
                }
            }
        });
        newAddInfo.getElement('.description a').destroy();
        newAddInfo.getElement('.description').grab(remove);
        newAddInfo.inject('review_criteria-wrapper', 'after');
        return newAddInfo;
    };
    
    $('add_more_rating_criteria').addEvent('click', function(event) {
  		addMoreAddRatingCriteria(this, event, {"value":null,"id":null});
  	}); 
    $('add_more_review_criteria').addEvent('click', function(event) {
  		addMoreAddReviewCriteria(this, event, {"value":null,"id":null});
  	});

  	<?php if ($this -> reviewCriteria):?>
	  	reviewCriteria = <?php echo $this -> reviewCriteria;?>;
		if (reviewCriteria && reviewCriteria.length)
		{
			for(i =0; i < reviewCriteria.length; i ++)
			{
				params = {"value":reviewCriteria[i].title};
				params.id = reviewCriteria[i].id;
				addMoreAddReviewCriteria(null, null, params);
			}
		}
  	<?php endif;?>
  	<?php if ($this -> ratingCriteria):?>
	  	ratingCriteria = <?php echo $this -> ratingCriteria;?>;
	  	if (ratingCriteria && ratingCriteria.length)
		{
	  		for(i =0; i < ratingCriteria.length; i ++)
			{
	  			params = {"value":ratingCriteria[i].title};
				params.id = ratingCriteria[i].id;
				addMoreAddRatingCriteria(null, null, params);
			}
		}
	<?php endif;?>
});
</script>
