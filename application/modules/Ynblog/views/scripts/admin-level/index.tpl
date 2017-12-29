<h2>
  <?php echo $this->translate('Advanced Blogs Plugin') ?>
</h2>

<script type="text/javascript">
  var fetchLevelSettings =function(level_id){    
    window.location.href= en4.core.baseUrl +'admin/ynblog/level/index/id/'+level_id;
  };
  function checkIt(evt) {
      evt = (evt) ? evt : window.event;
      var status = "";
      var charCode = (evt.which) ? evt.which : evt.keyCode;
      if (charCode > 31 && (charCode < 48 || charCode > 57)) {
          status = "<?php echo $this->translate("This field accepts numbers only.")?>";
          return false;
      }
      return true;
  }
</script>

<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      /*---- Render the menu ----*/
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <?php echo $this->form->render($this) ?>
  </div>

</div>