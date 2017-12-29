<div class="ynmb_openMenuMain_btnWrapper ynmb_touch ynmb_displayBoxstyle" id="">
	<a onclick="toggleOpenMenuMain(this);" class="ynmb_openMenuMain_btn" href="javascript: void(0);">
		<span class="ynmb_openMenuMain">
			<span> <?php echo $this->translate("Main Menu") ?> </span>
		</span>
	</a>
</div>
<div class="ynadvmenu_notification" id="ynadvmenu_notification">
	<?php if($this->viewer->getIdentity()):?>
		<div id="ynadvmenu_FriendsRequestUpdates" class="ynadvmenu_mini_wrapper">
			<a href="javascript:void(0);" class="ynadvmenu_NotifiIcon" id = "ynadvmenu_friends">
				<span id="ynadvmenu_FriendIconCount" class="ynadvmenu_NotifiIconWrapper" style="display:none"><span id="ynadvmenu_FriendCount"></span></span>
			</a>
			<div class="ynadvmenuMini_dropdownbox" id="ynadvmenu_friendUpdates" style="display: none;">
				<div class="ynadvmenu_dropdownHeader">
					<div class="ynadvmenu_dropdownArrow"></div>				  
				</div>
				<div class="ynadvmenu_dropdownTitle">
					<h3><?php echo $this->translate("Friend Requests") ?> </h3>				
					<a href="<?php echo $this->url(array(),'user_general', true)?>"><?php echo $this->translate("Find Friends") ?></a>
				</div>
				<div class="ynadvmenu_dropdownContent" id="ynadvmenu_friends_content">
					<!-- Ajax get and out content to here -->										
				</div>				
				<div class="ynadvmenu_dropdownFooter">
					<a class="ynadvmenu_seeMore" href="<?php echo $this->url(array('module' => 'ynmobileview', 'controller' => 'index','action' => 'friend-requests'),'default')?>">
						<span><?php echo $this->translate("See All Friend Requests") ?> </span>
					</a>				
				</div>				
			</div>
		</div>
		<div id="ynadvmenu_MessagesUpdates" class="ynadvmenu_mini_wrapper">
			<a href="javascript:void(0);" class="ynadvmenu_NotifiIcon" id="ynadvmenu_messages" >
				<span id="ynadvmenu_MessageIconCount" class="ynadvmenu_NotifiIconWrapper" style="display:none"><span id="ynadvmenu_MessageCount"></span></span>
			</a>
			<div class="ynadvmenuMini_dropdownbox" id="ynadvmenu_messageUpdates" style="display: none;">
				<div class="ynadvmenu_dropdownHeader">
					<div class="ynadvmenu_dropdownArrow"></div>				  
				</div>
				<div class="ynadvmenu_dropdownTitle">
					<h3><?php echo $this->translate("Messages") ?> </h3>				
					<a href="<?php echo $this->url(array('action'=>'compose'),'messages_general', true)?>"><?php echo $this->translate("Send a New Message") ?></a>
				</div>
				<div class="ynadvmenu_dropdownContent" id="ynadvmenu_messages_content">
					<!-- Ajax get and out content to here -->
				</div>				
				<div class="ynadvmenu_dropdownFooter">
					<a class="ynadvmenu_seeMore" href="<?php echo $this->url(array('action'=>'inbox'),'messages_general') ?>">
						<span><?php echo $this->translate("See All Messages") ?> </span>
					</a>				
				</div>				
			</div>
		</div>

		<div id="ynadvmenu_NotificationsUpdates" class="ynadvmenu_mini_wrapper">
			<a href="javascript:void(0);" class="ynadvmenu_NotifiIcon" id = "ynadvmenu_updates">
				<span id="ynadvmenu_NotifyIconCount" class="ynadvmenu_NotifiIconWrapper"><span id="ynadvmenu_NotifyCount"></span></span>
			</a>
			<div class="ynadvmenuMini_dropdownbox" id="ynadvmenu_notificationUpdates" style="display: none;">
				<div class="ynadvmenu_dropdownHeader">
					<div class="ynadvmenu_dropdownArrow"></div>				  
				</div>
				<div class="ynadvmenu_dropdownTitle">
					<h3><?php echo $this->translate("Notifications") ?> </h3>									
				</div>
				<div class="ynadvmenu_dropdownContent" id="ynadvmenu_updates_content">
					<!-- Ajax get and out contetn to here -->
				</div>				
				<div class="ynadvmenu_dropdownFooter">
					<a class="ynadvmenu_seeMore" href="<?php echo $this->url(array('module' => 'ynmobileview', 'controller' => 'index','action' => 'notifications'),'default')?>">
						<span><?php echo $this->translate("See All Notifications") ?> </span>
					</a>				
				</div>				
			</div>
		</div>	
	<?php endif; ?>
</div>	
<?php
	$request = Zend_Controller_Front::getInstance() -> getRequest();
	$module = $request -> getModuleName();
	$controller = $request -> getControllerName();
	$action = $request -> getActionName(); 
	?>
<div class="ynmb_sortBtn_Wrapper ynmb_displayBoxstyle" id="">
	<!-- Sign In--> 
	<div class="ynmb_sortBtn_actionSheet">
		<?php if(!$this->viewer->getIdentity() && $module == 'ynmobileview' && $controller == 'index'):
				$return_url = $this->return_url;
				if(!$return_url)
				{
					$return_url = '64-' . base64_encode($_SERVER['REQUEST_URI']);
				}
				?>
				<a href="<?php echo $this->url(array(), 'ynmobi_login', true).'?return_url=' .$return_url;?>" id="sign_in" class="ynmb_sortBtn_btn ynmb_touchable ynmb_a_btnStyle">
					<?php echo $this->translate('Sign In');?>
				</a>
			<?php endif;?>
	</div>
	<!-- Advanced Album Categories  -->
	<?php
			if(($module == 'advalbum' && $controller == 'index' && $action == 'browse') || ($module == 'advalbum' && $controller == 'index' && $action == 'listing') || ($module == 'ynvideo' && $controller == 'index' && $action == 'index') || ($module == 'ynvideo' && $controller == 'index' && $action == 'list') || ($module == 'advgroup' && $controller == 'index' && $action == 'browse') || ($module == 'advgroup' && $controller == 'index' && $action == 'listing')){?>
				<div class="ynmb_sortBtn_actionSheet">
					<a onclick="toggleOpenMenuRight(this);" class="ynmb_openMenuRight_btn ynmb_categories_icon ynmb_sortBtn_btn ynmb_touchable ynmb_a_btnStyle" href="javascript: void(0);">
						<span class="ynmb_openMenuRight">
							<span> <?php echo $this->translate("Categories") ?> </span>
						</span>
					</a>
				</div>
		<?php } ?>	
	
	<!-- End Advanced Album Categories  -->
</div>
<script type='text/javascript'>
  var toggleUpdatesPulldown = function(event, element, user_id) {
    if( element.className=='updates_pulldown' ) {
      element.className= 'updates_pulldown_active';
      showNotifications();
    } else {
      element.className='updates_pulldown';
    }
  }
	
  var toggleSortPulldown = function(event, element, user_id) {
    if( element.className=='sort_pulldown' ) {
      element.className= 'sort_pulldown_active';
    } else {
      element.className='sort_pulldown';
    }
  }  
  if (typeof jQuery != 'undefined') 
  { 
     jQuery.noConflict();
  }
  var notificationUpdater;
  <?php if($this->viewer->getIdentity()):?>
  var hide_all_drop_box = function(except)
  {
      //hide all sub-minimenu
      $$('.updates_pulldown_active').set('class','updates_pulldown');
      // reset inbox
      if (except != 1) {
          $('ynadvmenu_messages').removeClass('notifyactive');
          $('ynadvmenu_messageUpdates').hide();
          inbox_status = false;
          inbox_count_down = 1;
      }
      if (except != 2) {
          // reset friend
          $('ynadvmenu_friends').removeClass('notifyactive');
          $('ynadvmenu_friendUpdates').hide();
          friend_status = false;
          friend_count_down = 1;
      }
      if (except != 3) {
            // reset notification
          $('ynadvmenu_updates').removeClass('notifyactive');
          $('ynadvmenu_notificationUpdates').hide();
          notification_status = false;
          notification_count_down = 1;
      }
  }
  //refresh box
  var refreshBox = function(box) {
      var img_loading = '<?php echo $this->baseUrl(); ?>/application/modules/Ynmobileview/externals/images/loading.gif';
      if (box == 1) {
          // refresh message box
          inbox_count_down = 1;
          $('ynadvmenu_messages_content').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
          inbox();
      } else if (box == 2) {
          // refresh friend box
          friend_count_down = 1;
          $('ynadvmenu_friends_content').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
          freq();
      } else if (box == 3) {
          // refresh notification box
          notification_count_down = 1;
          $('ynadvmenu_updates_content').innerHTML = '<center><img src="'+ img_loading +'" border="0" /></center>';
          notification();
      }
  }
  var isLoaded = [0, 0, 0]; // friend request, message, notifcation
  var timerNotificationID = 0;
  //time to check for notification updates (in seconds)
  var updateTimes = <?php echo Engine_Api::_()->getApi('settings','core')->getSetting('core.general.notificationupdate',30000) ?>; 
  var getNotificationsTotal = function()
  {
    var notif = new Request.JSON({
           url    :  '<?php echo $this->layout()->staticBaseUrl?>'  +  'application/lite.php?module=ynmobileview&name=total&viewer_id=' + <?php echo $this->viewer->getIdentity() ?>,
           onSuccess : function(data) {
                if(data != null)
                {
                     if (data.notification > 0) {
                          var notification_count = $('ynadvmenu_NotifyCount');
                          notification_count.set('text', data.notification);
                          notification_count.getParent().setStyle('display', 'block');
                          $('ynadvmenu_NotificationsUpdates').className += " ynadvmenu_hasNotify";
                          isLoaded[2] = 0;
                     }
                     else
                     {
                        var notification_count = $('ynadvmenu_NotifyCount');
                        notification_count.getParent().setStyle('display', 'none'); 
                        $('ynadvmenu_NotificationsUpdates').className = "ynadvmenu_mini_wrapper"; 
                     }
                     if (data.freq > 0) {
                          var friend_req_count = $('ynadvmenu_FriendCount');
                          friend_req_count.set('text', data.freq);
                          friend_req_count.getParent().setStyle('display', 'block');
                          $('ynadvmenu_FriendsRequestUpdates').className += " ynadvmenu_hasNotify";
                          isLoaded[0] = 0;
                     }
                     else
                     {
                         var friend_req_count = $('ynadvmenu_FriendCount');
                         friend_req_count.getParent().setStyle('display', 'none');
                         $('ynadvmenu_FriendsRequestUpdates').className = "ynadvmenu_mini_wrapper"; 
                     }
                     if (data.msg > 0) {
                          var msg_count = $('ynadvmenu_MessageCount');
                          msg_count.set('text', data.msg);
                          msg_count.getParent().setStyle('display', 'block');
                          $('ynadvmenu_MessagesUpdates').className += " ynadvmenu_hasNotify";
                          isLoaded[1] = 0;
                     }
                     else
                     {
                          var msg_count = $('ynadvmenu_MessageCount');
                           msg_count.getParent().setStyle('display', 'none');
                           $('ynadvmenu_MessagesUpdates').className = "ynadvmenu_mini_wrapper";
                     }
                }              
           }
    }).get();
    <?php if($this->viewer()->getIdentity() > 0): ?>
    if(updateTimes > 10000){
        timerNotificationID = setTimeout(getNotificationsTotal, updateTimes);
    }
    <?php endif; ?>
 }

  var inbox = function() {
       new Request.HTML({
           'url'    :    en4.core.baseUrl + 'ynmobileview/index/message',
           'data' : {
                'format' : 'html',
                'page' : 1
            },
            'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        if(responseHTML)
                        {
                              $('ynadvmenu_messages_content').innerHTML = responseHTML;
                              $('ynadvmenu_MessageCount').getParent().setStyle('display', 'none'); 
                              $('ynadvmenu_MessagesUpdates').removeClass('ynadvmenu_hasNotify'); 
                              $('ynadvmenu_messages_content').getChildren('ul').getChildren('li').each(function(el){
                                  el.addEvent('click', function(){inbox_count_down = 1;});
                              });
                        }
            }
       }).send();
   }
   //inbox();

   var freq = function() {
       new Request.HTML({
           'url'    :    en4.core.baseUrl + 'ynmobileview/index/friend',
           'data' : {
                'format' : 'html',
                'page' : 1
            },
            'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                if(responseHTML)
                { 
                    $('ynadvmenu_friends_content').innerHTML = responseHTML;
                    $('ynadvmenu_FriendCount').getParent().setStyle('display', 'none');
                    $('ynadvmenu_FriendsRequestUpdates').removeClass('ynadvmenu_hasNotify');
                    $('ynadvmenu_friends_content').getChildren('ul').getChildren('li').each(function(el){
                           el.addEvent('click', function(){friend_count_down = 1;});
                    });
                }
            }
       }).send();
   }
   //freq();

   var notification = function() {
       new Request.HTML({
           'url'    :    en4.core.baseUrl + 'ynmobileview/index/notification',
           'data' : {
                'format' : 'html',
                'page' : 1
            },
            'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                if(responseHTML)
                { 
                    $('ynadvmenu_updates_content').innerHTML = responseHTML;    
                    $('ynadvmenu_NotifyCount').getParent().setStyle('display', 'none');  
                    $('ynadvmenu_NotificationsUpdates').removeClass('ynadvmenu_hasNotify');      
                    $('ynadvmenu_updates_content').getChildren('ul').getChildren('li').each(function(el){
                       el.addEvent('click', function(){notification_count_down = 1;});
                    });
                }
            }
       }).send();
   }
   //notification();
  // Show Inbox Message
  var inbox_count_down = 1;
  var inbox_status = false; // false -> not shown, true -> shown
  $('ynadvmenu_messages').addEvent('click', function() 
  {
      hide_all_drop_box(1);
      if (inbox_status) inbox_count_down = 1; 
      if (!inbox_status) {
          // show
          $(this).addClass('notifyactive');
          $('ynadvmenu_messageUpdates').setStyle('display', 'block');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
      } else {
          // hide
          $(this).removeClass('notifyactive');
          $('ynadvmenu_messageUpdates').setStyle('display', 'none');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
      }
      inbox_status = inbox_status ? false : true;
      if (isLoaded[1] == 0) 
      {
          refreshBox(1);
          isLoaded[1] = 1;
      }
  });
  // Friend box
  var friend_count_down = 1;
  var friend_status = false;
  $('ynadvmenu_friends').addEvent('click', function(){
  	
      hide_all_drop_box(2);
      if (friend_status) friend_count_down = 1;
      if (!friend_status) {
          $(this).addClass('notifyactive');
          $('ynadvmenu_friendUpdates').setStyle('display', 'block');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
      } else {
          $(this).removeClass('notifyactive');
          $('ynadvmenu_friendUpdates').setStyle('display', 'none');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
      }
      friend_status = friend_status ? false : true; 

      // Set all message as read
      if (isLoaded[0] == 0) {
          refreshBox(2);
          isLoaded[0] = 1;   // get again is check isloaded = 0      
      }
  });
  //Notification box
  var notification_count_down = 1;
  var notification_status = false;
  $('ynadvmenu_updates').addEvent('click', function()
  {
      hide_all_drop_box(3);
      if (notification_status) notification_count_down = 1;
      if (!notification_status) {
          // active
          $(this).addClass('notifyactive');
          $('ynadvmenu_notificationUpdates').setStyle('display', 'block');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
      } else {
          $(this).removeClass('notifyactive');
          $('ynadvmenu_notificationUpdates').setStyle('display', 'none');
          var elements = document.getElements('video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'block';
				  });
				  var elements = document.getElements('img.thumb_video');
				  elements.each(function(e)
				  {
				  	e.style.display = 'none';
				  });
      }
      notification_status = notification_status ? false : true;

      if (isLoaded[2] == 0) {
          refreshBox(3);
          isLoaded[2] = 1;
      }
  });
  do_confrim_friend = false;
  
  $(document).addEvent('click', function() 
  {
        if (inbox_status && inbox_count_down <= 0) {
            $('ynadvmenu_messages').removeClass('notifyactive');
            $('ynadvmenu_messageUpdates').setStyle('display', 'none');
            var elements = document.getElements('video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
					  var elements = document.getElements('img.thumb_video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
            inbox_status = false;            
            inbox_count_down = 1;
        } else if (inbox_status) {
            inbox_count_down = (inbox_count_down <= 0) ? 0 : --inbox_count_down;
        }         
        
        if (friend_status && friend_count_down <= 0) {
            if (do_confrim_friend) {do_confrim_friend = false; return false;}
            $('ynadvmenu_friends').removeClass('notifyactive');
            $('ynadvmenu_friendUpdates').setStyle('display', 'none');
             var elements = document.getElements('video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
					  var elements = document.getElements('img.thumb_video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'none';
					  });
            friend_status = false;            
            friend_count_down = 1;
        } else if (friend_status) {
            friend_count_down = (friend_count_down <= 0) ? 0 : --friend_count_down;
        } 
        if (notification_status && notification_count_down <= 0) {
            $('ynadvmenu_updates').removeClass('notifyactive');
            $('ynadvmenu_notificationUpdates').setStyle('display', 'none');
            var elements = document.getElements('video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'block';
					  });
					  var elements = document.getElements('img.thumb_video');
					  elements.each(function(e)
					  {
					  	e.style.display = 'none';
					  });
            notification_status = false;            
            notification_count_down = 1;
        } else if (notification_status) {
            notification_count_down = (notification_count_down <= 0) ? 0 : --notification_count_down;
        }
   });
<?php endif;?>
var firefox = false;
if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent))
{ //test for Firefox/x.x or Firefox x.x (ignoring remaining digits);
 var ffversion=new Number(RegExp.$1) // capture x.x portion and store as a number
 if (ffversion>=1)
 {
     firefox = true;
 } 
}

var isMouseLeaveOrEnter = function(e, handler)
{    
    if (e.type != 'mouseout' && e.type != 'mouseover') return false;
    var reltg = e.relatedTarget ? e.relatedTarget :
    e.type == 'mouseout' ? e.toElement : e.fromElement;
    while (reltg && reltg != handler) reltg = reltg.parentNode;
    return (reltg != handler);
}
var addAndRemoveElement = function()
{
	//check and update all search.
    if($('0_0_3_alias_location') !== undefined &&  $('0_0_3_alias_location') !== null)
    {
    	$('0_0_3_alias_location').style.display = 'none';
    	$('0_0_3_alias_location-label').style.display = 'none';
	    $$('#search').addEvent('change', function()
	    {
	        $(this).getParent('form').submit();
	    });
    }
    if($('ynevent_form_browse_filter') !== undefined &&  $('ynevent_form_browse_filter') !== null)
    {
	    $$('#keyword').addEvent('change', function()
	    {
	        $(this).getParent('form').submit();
	    });
	    $$('#within').addEvent('change', function()
	    {
	        $(this).getParent('form').submit();
	    });
	     $$('#location').addEvent('change', function()
	    {
	        $(this).getParent('form').submit();
	    });
    }
    if($('avdgroup_video_filter_form') !== undefined &&  $('avdgroup_video_filter_form') !== null)
    {
	    $$('#title').addEvent('change', function()
	    {
	        $(this).getParent('form').submit();
	    });
    }
    //check and remove link to create items
    if($$('.layout_page_album_index_browse .tip span')[0] !== undefined && $$('.layout_page_album_index_browse .tip span')[0] !== null)
    {
    	$$('.layout_page_album_index_browse .tip span')[0].innerHTML = '<?php echo $this->translate("Nobody has created an album yet.")?>';
    }
    if($$('.layout_page_album_index_manage .tip span')[0] !== undefined && $$('.layout_page_album_index_manage .tip span')[0] !== null)
    {
    	$$('.layout_page_album_index_manage .tip span')[0].innerHTML = '<?php echo $this->translate("You do not have any albums yet.")?>';
    }
    if($$('.layout_page_classified_index_index .tip span')[0] !== undefined && $$('.layout_page_classified_index_index .tip span')[0] !== null)
    {
    	$$('.layout_page_classified_index_index .tip span')[0].innerHTML = '<?php echo $this->translate("Nobody has posted a classified listing with that criteria.")?>';
    }
    if($$('.layout_page_classified_index_manage .tip span')[0] !== undefined && $$('.layout_page_classified_index_manage .tip span')[0] !== null)
    {
    	$$('.layout_page_classified_index_manage .tip span')[0].innerHTML = '<?php echo $this->translate("You do not have any classified listings.")?>';
    }
    if($$('.layout_page_music_index_browse .tip span')[0] !== undefined && $$('.layout_page_music_index_browse .tip span')[0] !== null)
    {
    	$$('.layout_page_music_index_browse .tip span')[0].innerHTML = '<?php echo $this->translate("There is no music uploaded yet.")?>';
    }
    if($$('.layout_page_music_index_manage .tip span')[0] !== undefined && $$('.layout_page_music_index_manage .tip span')[0] !== null)
    {
    	$$('.layout_page_music_index_manage .tip span')[0].innerHTML = '<?php echo $this->translate("There is no music uploaded yet.")?>';
    }
    if($$('.layout_page_video_index_browse .tip span')[0] !== undefined && $$('.layout_page_video_index_browse .tip span')[0] !== null)
    {
    	$$('.layout_page_video_index_browse .tip span')[0].innerHTML = '<?php echo $this->translate("Nobody has posted a video with that criteria.")?>';
    }
    if($$('.layout_page_video_index_manage .tip span')[0] !== undefined && $$('.layout_page_video_index_manage .tip span')[0] !== null)
    {
    	$$('.layout_page_video_index_manage .tip span')[0].innerHTML = '<?php echo $this->translate("You do not have any videos.")?>';
    }
    
    // move discription
    if($('global_page_album-photo-view') !== undefined && $('global_page_album-photo-view') !== null)
    {
    	var element = $$('.layout_page_album_photo_view p.photo-description');
    	if(element[0] && $('media_photo_div'))
    	{
    		$('media_photo_div').parentNode.insertBefore(element[0], $('media_photo_div').nextSibling);
    	}
    }
    <?php if(!$this->viewer->getIdentity()):?>
    if($$('.layout_ynmobileview_mobi_menu_logo')[0] !== undefined && $$('.layout_ynmobileview_mobi_menu_logo')[0] !== null)
    {
    	$('ynadvmenu_notification').innerHTML = $$('.layout_ynmobileview_mobi_menu_logo')[0].innerHTML;
    }
    <?php endif;?>
}
var initDefault = function ()
{
	<?php if($this->viewer->getIdentity()):?>
		getNotificationsTotal();
	<?php endif;?>
	addAndRemoveElement();
}
window.onload = initDefault;
</script>