<script type="text/javascript">
  var notificationPageCount = <?php echo sprintf('%d', $this->notifications->count()); ?>;
  var notificationPage = <?php echo sprintf('%d', $this->notifications->getCurrentPageNumber()); ?>;
  var loadMoreNotifications = function() {
    notificationPage++;
    new Request.HTML({
      'url' : en4.core.baseUrl + 'ynresponsive1/index/pulldown',
      'data' : {
        'format' : 'html',
        'page' : notificationPage
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        $('notifications_loading_main').setStyle('display', 'none');
        if( '' != responseHTML.trim() && notificationPageCount > notificationPage ) {
          $('notifications_viewmore').setStyle('display', '');
        }
        $('notifications_main').innerHTML += responseHTML;
      }
    }).send();
  };
  en4.core.runonce.add(function(){
    if($('notifications_viewmore_link')){
      $('notifications_viewmore_link').addEvent('click', function() {
        $('notifications_viewmore').setStyle('display', 'none');
        $('notifications_loading_main').setStyle('display', '');
        loadMoreNotifications();
      });
    }

    if($('notifications_markread_link_main')){
      $('notifications_markread_link_main').addEvent('click', function() {
        $('notifications_markread_main').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->translate("0 Updates");?>');
      });
    }
    
    $('notifications_main').addEvent('click', function(event){
        event.stop(); //Prevents the browser from following the link.
        var current_link = event.target;
        var notification_li = $(current_link).getParent('li');
        if(current_link.get('href')){
          en4.core.request.send(new Request.JSON({
            url : en4.core.baseUrl + 'ynresponsive1/index/markread',
            data : {
              format     : 'json',
              'actionid' : notification_li.get('value')
            },
            onSuccess : window.location = current_link.get('href')
          }));
        }
    });

  });
</script>

<div class='notifications_layout'>

  <div class=''>
    <h3 class="sep">
      <span><?php echo $this->translate("Your Notifications") ?></span>
    </h3>
    <ul class='notifications' id="notifications_main">
      <?php if( $this->notifications->getTotalItemCount() > 0 ): ?>
        <?php
          foreach( $this->notifications as $notification ):
          ob_start();
          try { 
          	$subject = $notification->getSubject(); ?>
            <li <?php if( !$notification->read ): ?> class = "ynadvmenu_Contentlist_unread"<?php $this->hasunread = true; ?> <?php endif; ?> value="<?php echo $notification->getIdentity();?>" >
				<a><?php echo $this->itemPhoto($subject, 'thumb.icon');?></a>   
				<div class="ynadvmenu_ContentlistInfo">
					<div class="ynadvmenu_NameUser" id="content_<?php echo $notification->getIdentity();?>">
						 <?php echo $notification->__toString() ?>
					</div>
					<div class="ynadvmenu_postIcon activity_icon_status notification_type_<?php echo $notification->type ?>"> 
						<span class="timestamp"> <?php echo $this->timestamp($notification->date)?> </span> 
					</div>
				</div>
			</li>
          <?php
          } catch( Exception $e ) {
            ob_end_clean();
            if( APPLICATION_ENV === 'development' ) {
              //echo $e->__toString();
            }
            continue;
          }
          ob_end_flush();
          endforeach;
        ?>
      <?php else: ?>
        <li>
          <?php echo $this->translate("You have no notifications.") ?>
        </li>
      <?php endif; ?>
    </ul>

    <div class="notifications_options">
      <?php if( $this->hasunread ): ?>
        <div class="notifications_markread" id="notifications_markread_main">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
            'id' => 'notifications_markread_link_main',
            'class' => 'buttonlink notifications_markread_link'
          )) ?>
        </div>
      <?php endif; ?>
      <?php if( $this->notifications->getTotalItemCount() > 10 ): ?>
        <div class="notifications_viewmore" id="notifications_viewmore">
          <?php echo $this->htmlLink('javascript:void(0);', $this->translate('View More'), array(
            'id' => 'notifications_viewmore_link',
            'class' => 'buttonlink icon_viewmore'
          )) ?>
        </div>
      <?php endif; ?>
      <div class="notifications_viewmore" id="notifications_loading_main" style="display: none;">
        <img src='application/modules/Core/externals/images/loading.gif' style='float:left; margin-right: 5px;' />
        <?php echo $this->translate("Loading ...") ?>
      </div>
    </div>
  </div>
</div>
