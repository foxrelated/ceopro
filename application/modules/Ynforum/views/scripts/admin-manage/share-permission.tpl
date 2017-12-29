<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: delete-forum.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>
<script language="javascript">
 function reloadPermission(val) {        
        var url = '<?php echo $this->form->getAction()?>' + '&mod_id=' + val;   
        this.location = url;        
    }
</script>    
<div class='settings share-permission'>
    <?php echo $this->form->render($this) ?>
</div>