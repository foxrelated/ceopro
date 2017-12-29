<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: topic-create.tpl 8918 2011-05-05 20:22:53Z shaun $
 * @author     John
 */
?>

<script type="text/javascript">
    function showUploader() {
        $('photo').style.display = 'block';
        $('photo-label').style.display = 'none';
    }
</script>
<div class="forum_breadcumb">
	<div>
    <?php
        echo $this->partial('_navigation.tpl', array(
            'linkedCategories' => $this->linkedCategories,
            'navigationForums' => $this->navigationForums,
        ));
    ?>
    	<span class="advforum_navigation_item">
            <?php echo $this->translate('Post Topic'); ?>
        </span>   
   </div>
</div>
<?php echo $this->form->render($this) ?>