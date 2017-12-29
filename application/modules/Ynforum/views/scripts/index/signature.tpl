<div class="headline">
  <h2>
    <?php echo $this->translate('Forum Signatures'); ?>
  </h2>
  <div class="tabs">
    <?php
// Render the menu
echo $this->navigation()->menu()->setContainer($this->navigation)->render();
?>
  </div>
</div>

<div class="global_form">
  <?php if ($this->form->saveSuccessful) : ?>
    <h3><?php echo $this->translate('Settings were successfully saved.'); ?></h3>
  <?php endif; ?>
  <?php echo $this->form->render($this) ?>
</div>