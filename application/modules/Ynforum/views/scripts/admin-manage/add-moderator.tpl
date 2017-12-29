<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: add-moderator.tpl 8342 2011-01-29 06:42:26Z john $
 * @author     Sami
 */
?>

<?php echo $this->form->render($this) ?>

<div class="forum_admin_manage_users">
    <ul id="user_list"></ul>
</div>
<script type="text/javascript">
    window.addEvent('domready', function() {
        $('forum_form_admin_moderator_create').addEvent('submit', function(event) {
            event.stop();
            updateUsers();
        });
    });

    function addModerator(user_id) {
        document.getElementById('user_id').set('value', user_id);
        document.getElementById('forum_form_admin_moderator_create').submit();
    }

    function updateUsers() {
        var request = new Request/*.HTML*/({
            url : '<?php echo $this->url(array('module' => 'ynforum', 'controller' => 'manage', 'action' => 'user-search'), 'admin_default', true); ?>',
            method: 'GET',
            data : {
                format : 'html',
                page : '1',
                <?php echo $this->objectParamName ?> : '<?php echo $this->object->getIdentity(); ?>',
                      username : document.getElementById('username').value
            },
          'onSuccess' : function(/*responseTree, responseElements,*/ responseHTML/*, responseJavaScript*/) {
              if( responseHTML.length > 0 ) {
                  document.getElementById('user_list').setStyle('display', 'block');
              } else {
                  alert('<?php echo $this->translate('There is no user found !!!')?>')
                  document.getElementById('user_list').setStyle('display', 'none');
              }
              document.getElementById('user_list').set('html', responseHTML);
              parent.Smoothbox.instance.doAutoResize();
              return false;
          }
      });
      request.send();
    }
</script>