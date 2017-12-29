<h3><?php echo $this->translate('Import Listings') ?></h3>
<p style="line-height: 1.5em; margin-bottom: 10px"><?php echo $this->translate("YNMULTILISTING_IMPORT_DESCRIPTION") ?></p>
<div id="ynmultilisting-import-tab">
    <div id="ynmultilisting-file-tab">
        <?php echo $this->htmlLink(array('route' => 'ynmultilisting_import', 'action' => 'file'), $this->translate('Import Listings From Files'))?>
    </div>
    <div id="ynmultilisting-module-tab" class="active">
        <?php echo $this->htmlLink(array('route' => 'ynmultilisting_import', 'action' => 'module'), $this->translate('Import Listings From Modules'))?>
    </div>
</div>
<?php if ($this->error): ?>
<div class="tip">
    <span><?php echo $this->message;?></span>
</div>
<?php else: ?>
<?php
    echo $this->form->render($this);
?>
<?php endif; ?>