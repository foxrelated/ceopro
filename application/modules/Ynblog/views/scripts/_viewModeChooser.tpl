<?php if (count($this->mode_enabled) > 1): ?>
<div class="ynblog-choose-view-mode">
    <div id="ynblog-view-mode-button-<?php echo $this -> identity;?>" class="ynblog-modeview-button">
        <?php if(in_array('list', $this -> mode_enabled)):?>
            <span class="ynblog-btn-list-view" rel="ynblog_list-view" ><i class="fa fa-th-list"></i></span>
        <?php endif; ?>
        <?php if(in_array('grid', $this -> mode_enabled)):?>
            <span class="ynblog-btn-grid-view" rel="ynblog_grid-view" ><i class="fa fa-th"></i></span>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>