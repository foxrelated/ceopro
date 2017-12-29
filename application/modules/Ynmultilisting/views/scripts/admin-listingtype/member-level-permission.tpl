<h2><?php echo $this->translate("YouNet Multiple Listings Plugin") ?></h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
	<?php
      // Render the menu
      //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<?php if ($this->error): ?>
<div class="tip">
    <span><?php echo $this->message;?></span>
</div>
<?php else: ?>
<div class='clear'>
    <div class='settings'>
    <?php echo $this->form->render($this); ?>
    </div>
</div>
<script type="text/javascript">
    $('level_id').addEvent('change', function(){
        window.location.href = en4.core.baseUrl + 'admin/ynmultilisting/listingtype/member-level-permission/id/<?php echo $this->listingtype_id;?>/level_id/'+this.get('value');
    });
</script>
<?php endif; ?>