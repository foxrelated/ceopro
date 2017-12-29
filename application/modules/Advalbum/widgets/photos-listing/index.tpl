<?php echo $this->html_full;?>
<?php if( count($this->paginator) > 1 ): ?>
  <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"), array(
    'pageAsQuery' => true,
    'query' => $this->formValues
)); ?>
 <?php endif; ?>

