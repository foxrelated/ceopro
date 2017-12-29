<h2><?php echo $this->translate("YouNet Multiple Listings Plugin") ?></h2>
<?php if( count($this->navigation) ): ?>
  <div class='tabs'>
    <?php
      // Render the menu
      echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
  </div>
<?php endif; ?>

<?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynmultilisting', 'controller' => 'category', 'action' => 'index'), $this -> translate("Back to Manage Categories"), array()); ?>

<div class="settings">
<?php echo $this->form->render(); ?>
</div>