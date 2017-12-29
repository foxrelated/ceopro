
window.addEvent('domready', function() {
   $$('.ynmultilisting-category-sub-category').set('styles', {
        display : 'none'
    });
    
     $$('.ynmultilisting-category-collapse-control').addEvent('click', function(event) {

        var row = this.getParent('li');

        if (this.hasClass('ynmultilisting-category-collapsed')) {

        	var id = row.getAttribute('value');
        	var rowSubCategories = row.getAllNext('li.child_'+id);  

            this.removeClass('ynmultilisting-category-collapsed');
            this.addClass('ynmultilisting-category-no-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynmultilisting-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'block'
                    });
                }
            }

        } else {

        	var rowSubCategories = row.getAllNext('li');

            this.removeClass('ynmultilisting-category-no-collapsed');
            this.addClass('ynmultilisting-category-collapsed');

            for(var i = 0; i < rowSubCategories.length; i++) {

                if (!rowSubCategories[i].hasClass('ynmultilisting-category-sub-category')) {
                    break;
                } else {
                	var collapsedDivs = rowSubCategories[i].getElements('.ynmultilisting-category-collapse-control');

                	if (collapsedDivs.length > 0) {
                		collapsedDivs[0].removeClass('ynmultilisting-category-no-collapsed');
                		collapsedDivs[0].addClass('ynmultilisting-category-collapsed');
                	}

                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});