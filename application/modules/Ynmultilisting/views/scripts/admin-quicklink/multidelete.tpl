<div class="settings">
<div class='global_form'>
  <?php if ($this->ids):?>
  <form method="post">
    <div>
      <h3><?php echo $this->translate("Delete the selected Quick Links?") ?></h3>
      <p>
        <?php echo $this->translate(array('Are you sure that you want to delete %s Quick Link?','Are you sure that you want to delete %s Quick Links?', $this->count),$this->count) ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value='true'/>
        <input type="hidden" name="ids" value="<?php echo $this->ids?>"/>
        <input type="hidden" name="listingtype_id" value="<?php echo $this->listingtype_id?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo Zend_Registry::get('Zend_Translate')->_(' or ') ?>
        <a href='<?php echo $this->url(array('action' => 'index', 'listingtype_id' => $this->listingtype_id));?>'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>
  <?php else: ?>
  <form>
    <div>
      <h3><?php echo $this->translate("Delete the selected Quick Links?") ?></h3>
      <p>
        <?php echo $this->translate("Please select at least one Quick Link to delete.") ?>
      </p>
      <br/>
      <a href="<?php echo $this->url(array('action' => 'index', 'listingtype_id' => $this->listingtype_id)) ?>" class="buttonlink icon_back">
        <?php echo $this->translate("Go Back") ?>
      </a>
    </div>
   </form>
  <?php endif;?>
</div>
</div>
<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
