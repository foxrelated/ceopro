<script type="text/javascript">
   var pageAction =function(page)
   {
    $('page').value = page;
    $('filter_form').submit();
  }
</script>

<?php if( $this->form ): ?>
  <?php echo $this->form->render($this) ?>
<?php endif ?>
