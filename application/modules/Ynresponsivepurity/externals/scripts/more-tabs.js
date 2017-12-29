window.addEvent('domready', function(){

    if ($$('.more_tab').length) {
        $$('.more_tab')[0].addEvent('click', function(){
            this.setStyle('position','relative');
            var contents = this.getElement('.tab_pulldown_contents_wrapper');
            var container = this.getParent('.layout_core_container_tabs');

            if ((this.getPosition().x + contents.getDimensions().x) > (container.getPosition().x + container.getDimensions().x)) {
                contents.setStyle('left', 'auto');
                contents.setStyle('right', '0');
            }

            if ( contents.getPosition().x <  container.getPosition().x) {
                contents.setStyle('left', '0');
                contents.setStyle('right', 'auto');
            }
        });
    }
});
