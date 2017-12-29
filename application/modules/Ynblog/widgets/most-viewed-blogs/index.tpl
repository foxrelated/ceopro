<?php if (count($this->blogs)): ?>
    <?php if (!$this->identity) $this->identity = 0; ?>
    <?php echo $this->partial('_viewModeChooser.tpl', 'ynblog', array(
        'identity' => $this->identity,
        'mode_enabled' => $this->mode_enabled
    )); ?>
    <ul class="ynblog-mode-views clearfix" id="ynblog-content-mode-views-<?php echo $this->identity?>">
      <?php
        foreach( $this->blogs as $item )
        {
            if ($item->checkPermission())
            {
                echo $this->partial('_listItem.tpl', 'ynblog', array('item' => $item, 'type' => 'view'));
                echo $this->partial('_gridItem.tpl', 'ynblog', array('item' => $item, 'type' => 'view'));
            }
        }
        ?>
        <?php if(count($this->blogs) == $this->limit): ?>
            <li>
              <div class="ynblog-more clearfix">
                  <a href="<?php echo $this->url(array(),'default'); ?>blogs/listing/orderby/view_count" >
                    <?php echo $this->translate('View all');?>
                  </a>
              </div>
            </li>
        <?php endif; ?>
    </ul>


    <script type="text/javascript">
      window.addEvent('domready', function(){
          ynblogRenderViewMode(<?php echo $this->identity?>, '<?php echo $this->view_mode ?>', <?php echo json_encode($this->mode_enabled) ?>);
      });
    </script>
<?php else: ?>
    <div class="tip">
            <span>
                <?php echo $this->translate("No blogs found.") ?>
            </span>
    </div>
<?php endif; ?>
