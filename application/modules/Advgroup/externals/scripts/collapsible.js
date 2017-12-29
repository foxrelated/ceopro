/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
window.addEvent('domready', function() {
   $$('.advgroup-category-sub-category').set('styles', {
        display : 'none'
    });
    
    $$('.advgroup-category-collapse-control').addEvent('click', function(event) {
        var row = this.getParent('tr');
        var rowSubCategories = row.getAllNext('tr');
        if (this.hasClass('advgroup-category-collapsed')) {
            this.removeClass('advgroup-category-collapsed');
            this.addClass('advgroup-category-no-collapsed')
            for(i = 0; i < rowSubCategories.length; i++) {
                if (!rowSubCategories[i].hasClass('advgroup-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'table-row'
                    });
                }
            }
        } else {
            this.removeClass('advgroup-category-no-collapsed');
            this.addClass('advgroup-category-collapsed');
            for(i = 0; i < rowSubCategories.length; i++) {
                if (!rowSubCategories[i].hasClass('advgroup-category-sub-category')) {
                    break;
                } else {
                    rowSubCategories[i].set('styles', {
                        display : 'none'
                    });
                }
            }
        }
    }); 
});