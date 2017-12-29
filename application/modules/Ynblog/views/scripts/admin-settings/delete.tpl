  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Delete Blog Category?") ?></h3>
      <p>
        <?php echo $this->translate("Are you sure that you want to delete this category? It will not be recoverable after being deleted.") ?>
      </p>

      <?php if(count($this->moveCates) > 0 && $this->hasBlogs > 0):?>
      <br />
      <p class="description">
        <?php echo $this->translate("If you delete this category, all existing blogs will be moved to another one.");?>
      </p>
      <p class="description">
        <?php echo $this->translate("Move to Category");?>
        <select name='move_category'>
          <option value ='none'><?php echo $this->translate('None') ?></option>
          <?php foreach($this->moveCates as $item) :?>
          <?php if ($item->category_id != $this->moveNodeID && $item->parent_id != $this->moveNodeID ): ?>
          <option value='<?php echo $item->getIdentity()?>'>
            <?php echo $this->translate(str_repeat('--', $item->level - 1).$item->category_name)?>
          </option>
          <?php endif; ?>
          <?php endforeach; ?>
        </select>
      </p>
      <?php endif ;?>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->moveNodeID?>"/>
        <button type='submit'><?php echo $this->translate("Delete") ?></button>
        <?php echo $this->translate(' or ');?>
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'><?php echo $this->translate('cancel');?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
