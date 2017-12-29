<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    Forum
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: edit.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>
<script type="text/javascript">
function deleteAttach()
{
	if(confirm('<?php echo $this->translate("Are you sure you want to delete this attachment?")?>'))
	{
	   $('check_delete').value = 1;
	   $('attached-wrapper').innerHTML = "";
	   $('attach_group-wrapper').style.display = "block";
	}
}
function updateUploader()
{
  if($('photo_delete').checked) {
    $('photo_group-wrapper').style.display = 'block';
  }
  else 
  {
    $('photo_group-wrapper').style.display = 'none';
  }
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
            <i class="ynicon yn-arr-right"></i>
            <?php echo $this->htmlLink(array('route' => 'ynforum_topic', 'topic_id' => $this->topic->getIdentity()), $this->topic->title); ?>
        </span>
    	<span class="advforum_navigation_item">
            <?php echo $this->translate('Edit Post'); ?>
        </span>   
   </div>
</div>
<?php echo $this->form->render($this) ?>
