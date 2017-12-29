<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;?>
<div class="layout_middle">
  <?php
  $options = $this->partial('_page_detail.tpl', array('page'=>$this->page,'viewer'=>$this->viewer,'can_rate'=>$this->can_rate));
  echo $options;
  ?>
  
  <h3><?php echo $this->translate("Versions Compared")?></h3>
  <div class="ynwiki_version" style="float: left; margin-right: 10px;">
    <h3 style="text-align: center;">
    <?php echo $this->htmlLink(array(
    'action' => 'preview-revision',
    'revisionId' => $this->old_version->revision_id,
    'route' => 'ynwiki_general',
    'reset' => true,
    ), $this->old, array(
    )) ?>
    </h3>
    <p style="text-align: center;">
      <?php
      $owner =  Engine_Api::_()->getItem('user', $this->old_version->user_id);
      echo $this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle());
      ?>
    </p>
    <p style="text-align: center;">
      <?php echo  $this->timestamp($this->old_version->creation_date);?>
    </p>
  </div>
  <div class="ynwiki_version">
    <?php if($this->page->revision_id == $this->new_version->revision_id):
    $str = $this->translate("CURRENT");
    else:
    $str = $this->new;
    endif;
    ?>
    <h3 style="text-align: center;">
    <?php echo $this->htmlLink(array(
    'action' => 'preview-revision',
    'revisionId' => $this->new_version->revision_id,
    'route' => 'ynwiki_general',
    'reset' => true,
    ), $str, array(
    )) ?>
    </h3>
    <p style="text-align: center;">
      <?php
      $owner =  Engine_Api::_()->getItem('user', $this->new_version->user_id);
      echo $this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle());
      ?>
    </p>
    <p style="text-align: center;">
      <?php echo  $this->timestamp($this->new_version->creation_date);?>
    </p>
  </div>
  <div style="padding-top: 5px;">
    <h4>
    <?php echo $this->htmlLink(array(
    'action' => 'history',
    'pageId' => $this->page->getIdentity(),
    'route' => 'ynwiki_general',
    'reset' => true,
    ), "&laquo; ".$this->translate('View Page History'), array(
    )) ?>
    </h4>
  </div>
  <div  style="padding-top: 10px">
    <b><?php echo $this->translate("Key")?> <span style="border: 1px solid rgb(204, 204, 204); display: inline-block; width: 16px;">&nbsp;</span>=<?php echo $this->translate("copy")?>, <span class="del" style="display: inline-block; width: 16px;">&nbsp;</span>=<?php echo $this->translate("delete")?>, <span class="ins" style="display: inline-block; width: 16px;">&nbsp;</span>=<?php echo $this->translate("insert")?>, <span class="rep" style="display: inline-block; width: 16px;">&nbsp;</span>=<?php echo $this->translate("replace");?></b>
  </div>
  <div class="panecontainer" style="width:99%; padding-top: 10px;">
    <p><?php echo $this->translate("Diff") ?>
      <span style="color:gray">(<?php echo $this->translate("diff:");?> <?php printf('%.3f', $this->exec_time); ?> <?php echo $this->translate("sec, rendering:")?> <?php printf('%.3f', $this->rendering_time); ?> <?php echo $this->translate("sec, diff len:")?> <?php echo $this->diff_len; ?> <?php echo $this->translate("chars")?>)</span></p><div>
      <div class="pane diff">
        <div class="ynwiki_content">
          <?php echo $this->rendered_diff; ?>
        </div>
      </div>
    </div>
  </div>
  </div>
  <style type="text/css">
  body {margin:0;border:0;padding:0;font:11pt sans-serif}
  body > h1 {margin:0 0 0.5em 0;font:2em sans-serif;background-color:#def}
  body > div {padding:2px}
  p {margin-top:0}
  ins * {color:green;background:#dfd;text-decoration:none}
  del * {color:red;background:#fdd;text-decoration:none}
  #params {margin:1em 0;font: 14px sans-serif}
  .panecontainer > p {margin:0;border:1px solid #bcd;border-bottom:none;padding:1px 3px;background:#def;font:14px sans-serif}
  .panecontainer > p + div {margin:0;padding:2px 0 2px 2px;border:1px solid #bcd;border-top:none}
  .pane {margin-top:0px;padding:0;border:0;width:100%;min-height:30em;overflow:auto;font:12px monospace}
  .diff {color:gray}
  .ins {
  background:#dfd;
  }
  .del {
  background:#fdd;
  }
  .rep {
  color: #008;
  background: #eef;
  }
  </style>