<script type="text/javascript">
   function follow_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'wiki/follow',
            'data' : {
            	  'pageId' : <?php echo $this->page->page_id?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynwiki_unfollow" href="javascript:;" onclick="unfollow_page()">' + '<?php echo $this->translate("Unfollow")?>' + '</a>';
            }
        });
        request.send();  
   } 
   function unfollow_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'wiki/un-follow-ajax',
            'data' : {
                'pageId' : <?php echo $this->page->page_id?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('follow_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynwiki_follow" href="javascript:;" onclick="follow_page()">' + '<?php echo $this->translate("Follow")?>' + '</a>';
            }
        });
        request.send();  
   }
   function favourite_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'wiki/favourite',
            'data' : {
                'pageId' : <?php echo $this->page->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynwiki_unfavourite" href="javascript:;" onclick="unfavourite_page()">' + '<?php echo $this->translate("Unfavourite wikis")?>' + '</a>';
            }
        });
        request.send();  
   } 
   function unfavourite_page()
   {
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  en4.core.baseUrl + 'wiki/un-favourite-ajax',
            'data' : {
                'pageId' : <?php echo $this->page->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {  
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink menu_ynwiki_favourite" href="javascript:;" onclick="favourite_page()">' + '<?php echo $this->translate("Favourite wikis")?>' + '</a>';
            }
        });
        request.send();  
   } 
   function confirmDelete()
   {
   		<?php
            if(count($this->page->getChilds())>0):
        ?>
            return confirm("<?php echo $this->translate('All child pages will be deleted . Are you sure to delete this page?'); ?>")
        <?php else:?>
   		   return confirm("<?php echo $this->translate('Are you sure to delete this page?'); ?>")
        <?php endif; ?>
   }
 </script>
 <div>
<?php echo $this->itemPhoto($this->page, 'thumb.profile')?>  
</div> 
<div id="profile_options">
<ul class="ynwiki_profile_options_menu">
 <?php  $viewer = Engine_Api::_()->user()->getViewer(); ?>
 <?php 
// 		 $rootpage = $this->page->getRootParent($this->page->page_id);
// 		 if(!$rootpage)
// 		 	$rootpage = $this->page;
 ?>
 <?php if(!$this->page->isOwner($viewer) && $viewer->getIdentity() > 0):?>
  <?php if( !$this->page-> membership() -> isMember($viewer)):?>
  		<li>
		  	<?php echo $this->htmlLink(array(
		  			'route' => 'ynwiki_general', 
		  			'action' => 'join', 
		  			'pageId' => $this->page->getIdentity(),
		  			'fromPageId' => $this->page->parent_page_id, 
		  			'format' => 'smoothbox'),
		  			 $this->translate('Join Wiki Space'), 
		  			array('class' => 'buttonlink smoothbox menu_ynwiki_join'));      ?>
		 </li>
  <?php else:?>	
		<li>
		  	<?php echo $this->htmlLink(array(
		  			'route' => 'ynwiki_general', 
		  			'action' => 'leave', 
		  			'pageId' => $this->page->getIdentity(),
		  			'fromPageId' => $this->page->parent_page_id, 
		  			'format' => 'smoothbox'),
		  			 $this->translate('Leave Wiki Space'), 
		  			array('class' => 'buttonlink smoothbox menu_ynwiki_leave'));      ?>
		 </li>
  <?php endif;?>
 <?php endif;?>

 <?php if($this->page->authorization()->isAllowed($viewer,'edit') ):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'edit',
                  'pageId' => $this->page->getIdentity(),
                  'fromPageId' => $this->page->parent_page_id,
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Edit'), array('class'=>'buttonlink menu_ynwiki_edit'
                )) ?>
    </li>
     <?php endif;?> 
    <?php if($this->page->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0 && Zend_Controller_Action_HelperBroker::getStaticHelper('requireAuth')->setAuthParams('ynwiki_page', null, 'create')->checkRequire()):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'create',
                  'fromPageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Add A Child Page'), array('class'=>'buttonlink menu_ynwiki_add'
                )) ?>
    </li>  
     <?php endif;?>
     <?php if($this->page->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0):?> 
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'attach',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Attach File'), array('class'=>'buttonlink menu_ynwiki_attach'
                )) ?>
    </li>  
    <?php endif;?>
     <?php if($this->page->authorization()->isAllowed($viewer,'delete') && $viewer->getIdentity() > 0):?>
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'delete',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Delete'), array('class'=>'buttonlink menu_ynwiki_delete',
                'onclick' => "return confirmDelete();")) ?>
    </li>  
     <?php endif;?> 
    <?php if($this->page->authorization()->isAllowed($viewer,'edit') && $viewer->getIdentity() > 0):?>
   <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'move-location',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Move Location'), array('class'=>'buttonlink menu_ynwiki_move'
                )) ?>
    </li>  
     <?php endif;?>  
    <?php

  
    if($this->page->authorization()->isAllowed($viewer,'restrict') && $viewer->getIdentity() > 0):
?>     
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'set-permission',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Restrict'), array('class'=>'buttonlink menu_ynwiki_restrict'
                )) ?>
    </li>   
    <?php endif;?> 
   <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.print', 0) == 1 || $viewer->getIdentity() > 0): ?>
   <li class="ynwiki_print">
    <a href="javascript:;" class="buttonlink menu_ynwiki_print" onclick="window.open('<?php echo $this->page->getPrintHref();?>', 'mywindow', 'location=1,status=1,scrollbars=1,  width=900,height=700')" > <?php echo $this->translate('Print');?> </a>
    </li>
    <?php endif;?>
    <?php if(Engine_Api::_()->getApi('settings', 'core')->getSetting('ynwiki.download', 0) == 1 || $viewer->getIdentity() > 0): ?>
    <li class="ynwiki_pdf">
    <?php echo $this->htmlLink(array(
                  'action' => 'download',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Download As PDF'), array('class'=>'buttonlink menu_ynwiki_download'
                )); ?>
    </li>  
    <?php endif;?> 
    <?php if($this->page->authorization()->isAllowed($viewer,'view') && $viewer->getIdentity() > 0):?>
    <li id = "follow_id">
    <?php if($this->page->checkFollow()):  ?>
     <?php  /* echo $this->htmlLink(array(
                      'action' => 'follow',
                      'pageId' => $this->page->getIdentity(),
                      'route' => 'ynwiki_general',
                      'reset' => true,
                    ), $this->translate('Follow'), array('class' => 'smoothbox'
      
                    )); */   ?>
      <a class = 'buttonlink menu_ynwiki_follow' href="javascript:;" onclick="follow_page()"><?php echo $this->translate('Follow')?></a>
   <?php
    else: ?>
        <?php /* echo $this->htmlLink(array(
                  'action' => 'un-follow',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Unfollow'), array('class' => 'smoothbox'
                )); */?>
        <a class = 'buttonlink menu_ynwiki_unfollow' href="javascript:;" onclick="unfollow_page()"><?php echo $this->translate('Unfollow')?></a>
    <?php
    endif; 
    ?>
   </li>  
    <?php endif; ?>
    <?php if($this->page->authorization()->isAllowed($viewer,'view') && $viewer->getIdentity() > 0):?>
    <li id = "favourite_id">
    <?php if($this->page->checkFavourite()): ?>
       <?php /* echo $this->htmlLink(array(
                      'action' => 'favourite',
                      'pageId' => $this->page->getIdentity(),
                      'route' => 'ynwiki_general',
                      'reset' => true,
                    ), $this->translate('Favourite'), array('class' => 'smoothbox'
                    )); */ ?>
      <a class = 'buttonlink menu_ynwiki_favourite' href="javascript:;" onclick="favourite_page()"><?php echo $this->translate('Favourite wikis')?></a>
    <?php
    else: ?>
       <?php /* echo $this->htmlLink(array(
                  'action' => 'un-favourite',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Unfavourite'), array('class' => 'smoothbox'
                )); */
       ?>
       <a class = 'buttonlink menu_ynwiki_unfavourite' href="javascript:;" onclick="unfavourite_page()"><?php echo $this->translate('Unfavourite wikis')?></a>
    <?php
    endif; 
    ?>
    </li>  
     <?php endif;?> 
    
    <li>
    <?php echo $this->htmlLink(array(
                  'action' => 'history',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('View History'), array('class'=>'buttonlink menu_ynwiki_history'
                )) ?>
    </li>  
     <li> 
     <?php  echo $this->htmlLink(array(
                  'action' => 'report',
                  'pageId' => $this->page->getIdentity(),
                  'route' => 'ynwiki_general',
                  'reset' => true,
                ), $this->translate('Report'), array('class' => 'smoothbox buttonlink menu_ynwiki_report'
                )); ?>
      </li>
      <li>
      <?php echo $this->translate("Page ID"); ?>: <?php echo $this->page->getIdentity();?>
      </li>
    </ul> 
 </div>     
