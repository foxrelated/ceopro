<script type="text/javascript">
function makeRead(actionid)
{
      en4.core.request.send(new Request.JSON({
        url : en4.core.baseUrl + 'ynresponsive1/index/markread',
        data : {
          format     : 'json',
          'actionid' :  actionid
        },
        'onComplete' : function(response)
        {
              var content = $('content_'+actionid).innerHTML;
              var arrHref = content.split("href");
              if(arrHref[2])
              {
                var strlink = arrHref[2];
                var arrstr = strlink.split('"');
                window.location = arrstr[1];
              }
              else if(arrHref[1])
              {
                  strlink = arrHref[1];
                  arrstr = strlink.split('"');
                  window.location = arrstr[1]; 
              }    
        }
      })); 
}
</script>	
<?php if (count($this->updates)): ?>
<div class="ynadvmenu_dropdownHaveContent">
	<ul class="ynadvmenu_Contentlist">
	<?php foreach ($this->updates as $item):?>
		<?php
		ob_start();
          try { 
		$subject = $item->getSubject()?>
		<li <?php if( !$item->read ): ?> class = "ynadvmenu_Contentlist_unread" <?php endif; ?> onclick="makeRead(<?php echo $item->getIdentity();?>)" value="<?php echo $item->getIdentity();?>" >
			<a><?php echo $this->itemPhoto($subject, 'thumb.icon');?></a>   
			<div class="ynadvmenu_ContentlistInfo">
				<div class="ynadvmenu_NameUser" id="content_<?php echo $item->getIdentity();?>" onclick="makeRead(<?php echo $item->getIdentity();?>)">
					 <?php echo $item->__toString() ?>
				</div>
				<div class="ynadvmenu_postIcon activity_icon_status notification_type_<?php echo $item->type ?>"> 
					<span class="timestamp"> <?php echo $this->timestamp($item->date)?> </span> 
				</div>
			</div>
		</li>
	<?php 
	 } catch( Exception $e ) 
	 {
        ob_end_clean();
        if( APPLICATION_ENV === 'development' ) {
          //echo $e->__toString();
        }
        continue;
      }
          ob_end_flush();
      endforeach;?>
	</ul>
</div>
<?php else:?>
<div class="ynadvmenu_dropdownNoContent">
	<?php echo $this->translate("You have no new updates.") ?>
</div>
<?php endif;?>
