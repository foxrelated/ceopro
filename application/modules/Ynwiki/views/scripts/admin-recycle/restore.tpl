  <form method="post" class="global_form_popup">
    <div>
      <h3><?php echo $this->translate("Restore Page?") ?></h3>
      <p>
        <?php if($this->childs > 0)
                echo $this->translate("All parent pages will be restore . Are you sure that you want to restore this page?");
            else
                echo $this->translate("Are you sure that you want to restore this page?") ?>
      </p>
      <br />
      <p>
        <input type="hidden" name="confirm" value="<?php echo $this->page_id?>"/>
        <button type='submit'><?php echo $this->translate("Restore") ?></button>
        <?php echo $this->translate(" or ") ?> 
        <a href='javascript:void(0);' onclick='javascript:parent.Smoothbox.close()'>
        <?php echo $this->translate("cancel") ?></a>
      </p>
    </div>
  </form>

<?php if( @$this->closeSmoothbox ): ?>
<script type="text/javascript">
  TB_close();
</script>
<?php endif; ?>
