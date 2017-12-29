<script type="text/javascript">
 <?php if($this->blog):?>
  function become()
  {
     var request = new Request.JSON({
            'method' : 'post',
            'url' :  '<?php echo $this->url(array('action' => 'become'), 'blog_general') ?>',
            'data' : {
                'blog_id' : <?php echo $this->blog->blog_id; ?>

            },
            'onComplete':function(responseObject)
            {
                $('become_count').innerHTML =  <?php echo $this->blog->become_count; ?> +1 ;
                $('btnBecome').innerHTML = "";
            }
        });
        request.send();
  }
  <?php endif;?>
</script>
<div class="ynblogs_gutter_options" >
  <ul>
      <?php
        /*--- Render the menu ---*/
        echo $this->navigation()
          ->menu()
          ->setContainer($this->gutterNavigation)
          ->setUlClass('navigation')
          ->render();
      ?>
      <?php if($this->blog && $this->viewer->getIdentity() > 0): ?>
          <?php if(Ynblog_Api_Core::checkUserBecome($this->viewer->getIdentity(),$this->blog->blog_id)): ?>
              <li id="btnBecome">
                  <a class="buttonlink icon_ynblog_become" onclick="this.disabled=true; become();" href="javascript:;">
                    <?php echo $this->translate('Become Member');?>
                  </a>
              </li>
          <?php endif; ?>
      <?php endif; ?>
  </ul>
</div>

<?php if($this->blog):?>
  <div class="ynblog_count_member" style="padding:5px 2px 5px 4px;"><span style="font-weight: bold"><?php echo $this->translate('Members');?>:</span> <span id = "become_count"><?php echo $this->blog->become_count?></span> <?php echo $this->translate('member(s)'); ?></div>
<?php $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";?>
<?php $url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>
 <div style="display: inline; float: left; width: 100%;margin-top:5px;">
        <div class="chartblock chartblock-orange">
            <p><?php echo $this->translate('Shares');?> <span title="<?php echo $this -> translate("Total shares for this time period");?>" class="q ">?</span></p>
            <h4 class="" id="share_value"><?php echo $this->blog->share_count  ?></h4>
        </div>
        <div class="chartblock chartblock-blue">
            <p><?php echo $this->translate('Clicks');?> <span title="<?php echo $this -> translate("Total traffic back from shares for this time period");?>" class="q ">?</span></p>
            <h4 class=""><?php $clicks = Ynblog_Api_Addthis::widget('clicks',$url); ?></h4>
        </div>
        <div class="chartblock chartblock-dark">
            <p><?php echo $this->translate('Viral Lift');?> <span title="<?php echo $this -> translate("Percentage increase in traffic due to shares and clicks");?>" class="q ">?</span></p>
            <h4><?php echo ($shares!=0)?round(($clicks*100)/$shares,2):'0'; ?>%</h4>
        </div>
    </div>

<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
    <!-- Go to www.addthis.com/dashboard to customize your tools -->  
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 

<script type="text/javascript">
  function eventHandler(evt) { 
      if (evt.type == 'addthis.menu.share') { 
          en4.core.request.send(new Request.JSON({
            'method' : 'post', 
              'url' :  en4.core.baseUrl + 'blogs/share',
              'data' : {
                  'blog_id' : <?php echo $this->blog->getIdentity() ?>
              },
              'onComplete':function(responseObject) {  
                $('share_value').innerHTML = responseObject.share;
              }
          }));
      }
  }
  addthis.addEventListener('addthis.menu.share', eventHandler);
</script>
<?php endif;?>