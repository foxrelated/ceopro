<h2>
    <?php echo $this->translate('YouNet Multiple Listings Plugin') ?>
</h2>
<?php if( count($this->navigation) ): ?>
    <div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
    </div>
<?php endif; ?>

<h3><?php echo $this->translate('Listing Menu') ?></h3>

<?php if ($this->error): ?>
<div class="tip">
    <span><?php echo $this->message;?></span>
</div>
<?php else: ?>
    
<div id="listing-type">
    <span><?php echo $this->translate('Listing type: %s', $this->htmlLink($this->listingType->getHref(), $this->listingType->getTitle()))?></span>
</div>

<p><?php echo $this->translate("YNMULTILISTING_MANAGE_MENU_DESCRIPTION") ?></p>

<div class='clear'>
    <div class='settings'>
    <?php echo $this->form->render($this); ?>
    </div>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        //add event for top and more categories
        $$('#top_categories li input[type="checkbox"][name="top_category[]"]').addEvent('click', function() {
            if (this.checked) {
                var inputs = $$('#top_categories li input[type="checkbox"][name="top_category[]"]:checked');
                if (inputs.length > 8) {
                    alert('<?php echo $this->translate('Top categories reach limit!')?>');
                    this.set('checked', false);
                }
            }    
        });
        
        $$('#more_categories li input[type="checkbox"][name="more_category[]"]').addEvent('click', function() {
            if (this.checked) {
                var inputs = $$('#more_categories li input[type="checkbox"][name="more_category[]"]:checked');
                if (inputs.length > 8) {
                    alert('<?php echo $this->translate('More categories reach limit!')?>');
                    this.set('checked', false);
                }
            }    
        });    
    });
</script>
<?php endif; ?>