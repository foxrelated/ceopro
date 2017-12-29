<div class="forum_breadcumb">
	<div>
    <?php
        echo $this->partial('_navigation.tpl', array(
            'linkedCategories' => $this->linkedCategories,
            'navigationForums' => $this->navigationForums,
        ));
    ?>
    	<span class="advforum_navigation_item">
            <?php echo $this->htmlLink(array('route' => 'ynforum_post', 'action' => 'edit','post_id' => $this->post->getIdentity()), $this->translate('YNFORUM_EDIT_TOPIC')); ?>
        </span> 
    	<span class="advforum_navigation_item">
            <?php echo $this->translate('YNFORUM_ADD_PHOTO'); ?>
        </span>   
   </div>
</div>
<h5><?php echo $this->translate("YNFORUM_POST_ATTACHED_PHOTO"); ?></h5>

<div class="layout_middle">

  <ul id="attached_files"  <?php if($this->postPhotos->getTotalItemCount()==0) echo 'style="display:none"'?> class="attached_files thumbs thumbs_nocaptions">
    <?php foreach( $this->postPhotos as $photo ):?>
      <li id="thumbs-photo-<?php echo $photo->getIdentity() ?>">
        <a class="thumbs_photo" href="javascript:void();">
          <span style="background-image: url(<?php echo $photo->getPhotoUrl('thumb.normal'); ?>);"></span>
        </a>
        <p class="thumbs_info"><a class="attachfile-remove" href="javascript:void()" onclick="return removeManageAttachFile('<?php echo $photo->getIdentity()?>')">Remove</a></p>
      </li>
    <?php endforeach;?>
  </ul>

<form enctype="application/x-www-form-urlencoded" action="" method="post">
	<div class="ynforum_input_choose">
		<input type="radio" name="photo_option" id="photo_option-1" value="1" checked="checked" onclick="post_photo_choose();"><label for="photo_option-1"><?php echo $this->translate("Attach from computer")?></label>
	</div>

<?php if(Engine_Api::_() -> hasModuleBootstrap('advalbum') || Engine_Api::_() -> hasModuleBootstrap('album')):?>
	
	<?php	if(Engine_Api::_() -> hasModuleBootstrap('advalbum'))
		{
			$table = Engine_Api::_() -> getItemTable('advalbum_album');
		}
		else {
			$table = Engine_Api::_() -> getItemTable('album');
		}		
		$select = $table -> select() -> where('owner_id = ?', $this->viewer -> getIdentity()) -> order('modified_date DESC');	
		$paginator = Zend_Paginator::factory($select);
	
	?>
<?php else:

	$nopluginalbum = true;
?>

<?php endif;?>
   
	<?php echo $this->form->html5_upload->render(); ?>
    
	<div class="ynforum_input_choose">
		<input type="radio" name="photo_option" id="photo_option-0" value="0" onclick="post_photo_choose();"><label for="photo_option-0"><?php echo $this->translate("Choose from my library")?></label>
	</div>
   	<?php if(isset($paginator) && $paginator->getTotalItemCount() > 0 ): ?>
	
	<div class="ynforum_choose_album_photo_library">	
	    <div class="ynforum_choose_album_library">
		    <ul class="thumbs">
		      <?php foreach( $paginator as $album ): ?>
		        <li class="ynforum_album_photo">
		          <a class="thumbs_photo" href="javascript:void();">
		            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.normal'); ?>);"></span>
		          </a>
		          <p class="thumbs_info">
		            <span class="thumbs_title">
		              <?php echo $this->string()->truncate($album->getTitle(), 15) ?>
		            </span>
		            <?php echo $this->translate(array('%s photo', '%s photos', $album->count()),$this->locale()->toNumber($album->count())) ?>
		          </p>		          
		         
		         <a href="javascript:void();" onclick="return album_choose('<?php echo $album->getIdentity()?>')"><?php echo $this->translate("View Photos")?></a>
		          
		         
		        </li>
		      <?php endforeach;?>
		    </ul>    
		    
		    
	   </div>  
	   <div class="ynforum_choose_photo_library">
	   		<a id='repickalbum' href="javascript:void()" onclick="return return_album()" class="repickalbum buttonlink icon_back" style="clear:both; margin-bottom: 5px;"><?php echo $this->translate('Repick Album')?></a>
	   		
	   		<div class="ynforum_choose_photo_item"></div>
	   		<div id="ynforum_buttons" class='buttons'>
            	<div><button name='clear_selection' id='clear_selection' onclick='clear_select()' type='button'><?php echo $this -> translate('YNFORUM_CLEAR_SELECTION')?></button></div>
            	<div><button name='select_all' id='select_all' onclick='photo_selectall()' type='button'><?php echo $this -> translate('YNFORUM_SELECT_ALL')?></button></div>
            	<div><button name='attached_lib' id='attached_lib' onclick='attached_select_alubm_photo_lib()' type='button'><?php echo $this -> translate('YNFORUM_ATTACHED_SELECT')?></button></div>
       	 	</div>	   				
	   </div>
   </div>
   <?php else: ?>
    <div class="tip">
      <span>
      	
        <?php
			if($nopluginalbum)
			{
				echo $this->translate('You do not install albums plugin yet');
			}
			else{
				echo $this->translate('You do not have any albums yet. Get started by %1$screating%2$s your first album!', '<a target="_blank" href="'.$this->url(array('action' => 'upload'),'album_general').'">', '</a>');
			}
        ?>
        		      
      </span>
    </div>
   <?php endif; ?>
   <?php echo $this->form->post_id->render(); ?>
   <?php echo $this->form->html5uploadfileids->render(); ?>
   <?php echo $this->form->html5importfile->render(); ?>
   <?php echo $this->form->ynforumpostuploadfile->render(); ?>
  
<div class="ynforum_button"> 
  
	<?php echo $this->form->submit->render(); ?>
	<?php echo $this->form->managePhoto->render(); ?>
</div>
</form>     
</div>


   
<script type="text/javascript">		
	
	var photo_selectall = function()
	{	
		
		var checks = $$('.ynforum_choose_photo_item ul li input[type=checkbox]');		
		for (i = 0; i < checks.length; i++) {		
			checks[i].checked = true;	
		}	
	}
	
	var post_photo_choose = function()
	{
		var option = $$("input[name='photo_option']:checked")[0].value;
		
	 	if(option == 0)
	 	{
	 		$$('.html5_upload_file')[0].hide();
	 		$$('.ynforum_choose_album_photo_library')[0].show();
	 	}
	 	else
	 	{
	 		$$('.html5_upload_file')[0].show();
	 		$$('.ynforum_choose_album_photo_library')[0].hide();
	 	}
	}
	
	var clear_select = function()
	{
		var checks = $$('.ynforum_choose_photo_item ul li input[type=checkbox]');		
		for (i = 0; i < checks.length; i++) {		
			checks[i].checked = false;	
		}
	}
	
	var attached_select_html5uploadfileids = function()
	{
		var imgs = $$('.ynforum_photo_item');
		
		for (i = 0; i < imgs.length; i++) {	
			file_id = 	(imgs[i].id).substring(8,(imgs[i].id).length);
			var html = $('attached_files').get('html') + "<li id='file_id_"+file_id+"'> <a class='thumbs_photo' id='file_id_"+file_id+"'>" + imgs[i].get('html') + "</a> <p class='thumbs_info'><a class='attachfile-remove' href='javascript:void()' onclick='return removeAttachFile("+file_id+")' >Remove</a></p></li>";
			$('attached_files').set('html', html);
			
			$('ynforumpostuploadfile').value = $('ynforumpostuploadfile').value + ' ' + file_id;
			
			$('attached_files').show();
		}
		$('files').set('html','')
		$('progress').style.display = 'none';
		$('progress-percent').style.display = 'none';
		$('attached_select').style.display = 'none';
	}
	
	var removeAttachFile = function(file_id)
	{		
		$('file_id_'+file_id).dispose();
		$('ynforumpostuploadfile').value = $('ynforumpostuploadfile').value.replace(file_id, '');
		
		if($('ynforumpostuploadfile').value.trim() == "")
		{
			$('attached_files').hide();
		}
		
		return false;
	}
	
	var attached_select_alubm_photo_lib = function()
	{
		var attach_files = $('ynforumpostuploadfile').value.split(' ');
		var checks = $$('.ynforum_choose_photo_item ul li input[type=checkbox]');
		var notice = true;
		
		for (i = 0; i < checks.length; i++) {		
			if(checks[i].checked)
			{	
				var flag = true;
				for(j = 1; j <attach_files.length ; j++)
				{
					if(checks[i].value == attach_files[j])
					{
						flag = false;
					}
				}
				if(flag)
				{				
					var html = $('attached_files').get('html') + "<li id='file_id_"+checks[i].value+"'> <a class='thumbs_photo' href='javascript:void();'>" + $$('#thumbs-photo-'+checks[i].value+' a.thumbs_photo')[0].get('html') + "</a> <p class='thumbs_info'><a class='attachfile-remove' href='javascript:void()' onclick='return removeAttachFile("+checks[i].value+")' >Remove</a></p></li>";
					$('attached_files').set('html', html);					
					$('thumbs-photo-'+checks[i].value).dispose();
					
					$('html5importfile').value = $('html5importfile').value + ' ' + checks[i].value;
					$('ynforumpostuploadfile').value = $('ynforumpostuploadfile').value + ' ' + checks[i].value;
					notice = false;
				}
			}	
		}	
		if(notice)
		{
			alert("<?php echo $this->translate('Please select once photo or choose another photo.'); ?>");
			return false;
		}	
		else{
			$('attached_files').show();
		}
		if($$('.ynforum_choose_photo_item ul li input[type=checkbox]').length ==0)
		{
			$$('.ynforum_choose_photo_item')[0].hide();
		}
		
	}
	
	var removeManageAttachFile = function (file_id)
	{
		$('thumbs-photo-'+file_id).dispose();
		
		request = new Request.JSON({
                   'format' : 'json',
                   'url' : '<?php echo $this->url(Array('action'=>'delete-photo',), 'ynforum_post') ?>',
                   'data': {
                     'photo_id' : file_id
                   },
                  'onSuccess' : function(responseJSON, responseHTML) {
                                       
                  }
                });

        request.send();
        
        $('html5uploadfileids').value = $('html5uploadfileids').value.replace(file_id, '');
        $('html5importfile').value = $('html5importfile').value.replace(file_id, '');
        $('ynforumpostuploadfile').value = $('ynforumpostuploadfile').value.replace(file_id, '');
        return false;
	}
	
	var album_choose  = function(album_id)
	{		
	 	
	 	$$('.ynforum_choose_album_library')[0].hide();
	 	$('repickalbum').show();
	 	$('ynforum_buttons').show();
	 	
	 	request = new Request.JSON({
                   'format' : 'json',
                   'url' : '<?php echo $this->url(Array('action'=>'render-album-photos',), 'ynforum_post') ?>',
                   'data': {
                     'album_id' : album_id
                   },
                  'onSuccess' : function(responseJSON, responseHTML) {
                    $$('.ynforum_choose_photo_item')[0].show();
                    $$('.ynforum_choose_photo_item')[0].innerHTML = responseJSON.html;
                    
                  }
                });

        request.send();
        return false;
	}
	var return_album = function ()
	{		
		$$('.ynforum_choose_album_library')[0].show();
		$$('.ynforum_choose_photo_item')[0].hide();
		$('repickalbum').hide();
		$('ynforum_buttons').hide();
		return false;
		
	}
</script>