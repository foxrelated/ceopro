<div>
  <?php echo $this->form->render($this) ?>
</div>
<script type="text/javascript">
 var pageAction =function(page){
    $('page').value = page;
    $('filter_form').submit();
  }
</script>
