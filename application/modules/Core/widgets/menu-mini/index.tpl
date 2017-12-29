<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Core
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: index.tpl 9747 2012-07-26 02:08:08Z john $
 * @author     John
 */
?>

<?php
  $themes = Engine_Api::_()->getDbtable('themes', 'core')->fetchAll();
  $activeTheme = $themes->getRowMatching('active', 1);
  $manifest = APPLICATION_PATH . '/application/themes/' . $activeTheme->name . '/manifest.php';
  $themeManifest = null;
  if( file_exists($manifest) ) {
    $themeManifest = require($manifest);
  }
?>
<div id='core_menu_mini_menu'>
  <ul>
    <?php foreach( $this->navigation as $item ): ?>
      <li>
        <?php
          $linkTitle = '';
          $subclass = '';

          if( stripos($activeTheme->name, 'insignia') !== false || (isset($themeManifest['options']) && isset($themeManifest['options']['menu-icons'])) ) {
            $linkTitle = $this->translate(strip_tags($item->getLabel()));
            if( $this->showIcons ) {
              $subclass = ' show_icons';
            }
          }
          $class = !empty($item->class) ? $item->class . $subclass : null;
        ?>
        <?php echo $this->htmlLink($item->getHref(), $this->translate($item->getLabel()), array_filter(array(
        'class' => $class,
        'alt' => ( !empty($item->alt) ? $item->alt : null ),
        'target' => ( !empty($item->target) ? $item->target : null ),
        'title' => $linkTitle,
        ))) ?>

        <!-- For displaying count bubble : START -->
        <?php
          $countText = filter_var($item->getLabel(), FILTER_SANITIZE_NUMBER_INT);
        ?>
        <?php if( stripos($item->class, 'core_mini_update') !== false ) : ?>
          <span class="minimenu_update_count_bubble <?php echo $subclass ?>" id="update_count">
            <?php echo $countText; ?>
          </span>
        <?php elseif( stripos($item->class, 'core_mini_messages') !== false && !empty($countText) ) : ?>
          <span class="minimenu_message_count_bubble <?php echo $subclass ?>" id="message_count">
            <?php echo $countText; ?>
          </span>
        <?php endif; ?>
        <!-- For displaying count bubble : END -->
      </li>
    <?php endforeach; ?>
    <?php if($this->search_check):?>
      <li id="global_search_form_container">
        <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
          <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' />
        </form>
      </li>
    <?php endif;?>
  </ul>
</div>

<span  style="display: none;" class="updates_pulldown" id="core_mini_updates_pulldown">
  <div class="pulldown_contents_wrapper">
    <div class="pulldown_contents">
      <ul class="notifications_menu" id="notifications_menu">
        <div class="notifications_loading" id="notifications_loading">
          <i class="fa fa-spin fa-spinner" style='margin-right: 5px;' ></i>
          <?php echo $this->translate("Loading ...") ?>
        </div>
      </ul>
    </div>
    <div class="pulldown_options">
      <?php echo $this->htmlLink(array('route' => 'default', 'module' => 'activity', 'controller' => 'notifications'),
         $this->translate('View All Updates'),
         array('id' => 'notifications_viewall_link')) ?>
      <?php echo $this->htmlLink('javascript:void(0);', $this->translate('Mark All Read'), array(
        'id' => 'notifications_markread_link',
      )) ?>
    </div>
  </div>
</span>


<script type='text/javascript'>
  var notificationUpdater;

  en4.core.runonce.add(function(){
    if($('notifications_markread_link')){
      $('notifications_markread_link').addEvent('click', function() {
        //$('notifications_markread').setStyle('display', 'none');
        en4.activity.hideNotifications('<?php echo $this->string()->escapeJavascript($this->translate("0 Updates"));?>');
      });
    }

    <?php if ($this->updateSettings && $this->viewer->getIdentity()): ?>
    notificationUpdater = new NotificationUpdateHandler({
              'delay' : <?php echo $this->updateSettings;?>
            });
    notificationUpdater.start();
    window._notificationUpdater = notificationUpdater;
    <?php endif;?>

    // combining mini-menu and search widget if next to each other
    var menuElement = $('global_header').getElement('.layout_core_menu_mini');
    var nextWidget = menuElement.getNext();
    if( nextWidget && nextWidget.hasClass('layout_core_search_mini') ) {
      nextWidget.removeClass('generic_layout_container').inject(menuElement, 'bottom');
      return;
    }
    previousWidget = menuElement.getPrevious();
    if( previousWidget && previousWidget.hasClass('layout_core_search_mini') ) {
      previousWidget.removeClass('generic_layout_container').inject(menuElement, 'top');
    }

  });

  var updateElement = $('core_menu_mini_menu').getElement('.core_mini_update');
  if( updateElement ) {
    updateElement.set('id', 'updates_toggle');
    $('core_mini_updates_pulldown').setStyle('display', 'inline-block').inject(updateElement.getParent().set('id', 'core_menu_mini_menu_update'));
    updateElement.inject($('core_mini_updates_pulldown'));
    $('core_mini_updates_pulldown').addEvent('click', function() {
      var element = $(this);
      if( element.className=='updates_pulldown' ) {
        element.className= 'updates_pulldown_active';
        showNotifications();
      } else {
        element.className='updates_pulldown';
      }
    });
  }

  var updateNotificationsMenuPositions = function() {
    if( !DetectMobileQuick() ) {
      var pulldown = $('core_mini_updates_pulldown').getElement('.pulldown_contents_wrapper');
      var coordinates = pulldown.getCoordinates();
      if( coordinates.left + coordinates.width + 100 - window.getSize().x > 0 ) {
        pulldown.setStyle('marginLeft', -(coordinates.width - updateElement.getCoordinates().width));
      }
    }
  };

  var showNotifications = function() {
    updateNotificationsMenuPositions();
    en4.activity.updateNotifications();
    new Request.HTML({
      'url' : en4.core.baseUrl + 'activity/notifications/pulldown',
      'data' : {
        'format' : 'html',
        'page' : 1
      },
      'onComplete' : function(responseTree, responseElements, responseHTML, responseJavaScript) {
        if( responseHTML ) {
          // hide loading icon
          if($('notifications_loading')) $('notifications_loading').setStyle('display', 'none');

          $('notifications_menu').innerHTML = responseHTML;
          updateNotificationsMenuPositions();
          $('notifications_menu').addEvent('click', function(event){
            event.stop(); //Prevents the browser from following the link.

            var current_link = event.target;
            var notification_li = $(current_link).getParent('li');

            // if this is true, then the user clicked on the li element itself
            if( notification_li.id == 'core_menu_mini_menu_update' ) {
              notification_li = current_link;
            }

            var forward_link;
            if( current_link.get('href') ) {
              forward_link = current_link.get('href');
            } else{
              forward_link = $(current_link).getElements('a:last-child').get('href');
            }

            if( notification_li.get('class') == 'notifications_unread' ){
              notification_li.removeClass('notifications_unread');
              en4.core.request.send(new Request.JSON({
                url : en4.core.baseUrl + 'activity/notifications/markread',
                data : {
                  format     : 'json',
                  'actionid' : notification_li.get('value')
                },
                onSuccess : function() {
                  window.location = forward_link;
                }
              }));
            } else {
              window.location = forward_link;
            }
          });
        } else {
          $('notifications_loading').innerHTML = '<?php echo $this->string()->escapeJavascript($this->translate("You have no new updates."));?>';
        }
      }
    }).send();
  };
</script>
