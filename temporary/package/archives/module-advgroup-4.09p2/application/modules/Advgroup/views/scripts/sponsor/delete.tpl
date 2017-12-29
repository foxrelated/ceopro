<div class='global_form_popup'>
  <form method="post" class="global_form" action="<?php echo $this->url() ?>">
    <div>
      <div>
        <h3>
          <?php echo $this->translate('Delete Group Sponsor?');?>
        </h3>
        <p>
          <?php echo $this->translate('Are you sure that you want to delete the group sponsor? It will not be recoverable after being deleted.'); ?>
        </p>
        <br />
        <p>
          <input type="hidden" name="confirm" value="true"/>
          <button type='submit'><?php echo $this->translate('Delete');?></button>
          or <a name="cancel" id="cancel" type="button" href="javascript:void(0);" onclick="parent.Smoothbox.close();"><?php echo $this->translate('Cancel');?></a>
        </p>
      </div>
    </div>
  </form>
</div>