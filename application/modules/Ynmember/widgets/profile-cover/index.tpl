<?php 
$this -> headScript() 
        -> appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/jquery-1.10.2.min.js')
        -> appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/jquery-ui-1.11.4.min.js')
        -> appendFile($this->layout()->staticBaseUrl . 'application/modules/Ynmember/externals/scripts/jquery.form.min.js');

function finalizeUrl($url)
{
    if ($url)
    {
        if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
        {
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
            {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            $pageURL .= $_SERVER["SERVER_NAME"];
            $url = $pageURL . '/'. ltrim( $url, '/');
        }
    }

    return $url;
}

$og = '<meta property="og:image" content="' . finalizeUrl($this->subject()->getPhotoUrl()) . '" />';
$og .= '<meta property="og:image:width" content="640" />';
$og .= '<meta property="og:image:height" content="442" />';
$og .= '<meta property="og:title" content="' . $this->subject()->getTitle() . '" />';
$og .= '<meta property="og:description" content="' . $this->string()->striptags($this->subject()->getDescription()) . '" />';
$og .= '<meta property="og:url" content="' . finalizeUrl($this->subject()->getHref()) . '" />';
$og .= '<meta property="og:updated_time" content="' . strtotime($this->subject()->modified_date) . '" />';
$og .= '<meta property="og:type" content="website" />';
if ($this->subject->cover_id)
{
$og .= '<meta property="og:image" content="' . finalizeUrl(Engine_Api::_()->getDbtable('files', 'storage')->find($this->subject->cover_id)->current()->map()) . '" />';
}
$this->layout()->headIncludes .= $og;
?>

<script type="text/javascript">
var cover_top = <?php echo $this->subject->cover_top?>;

function repositionCover() {
    jQuery('.resize-btns').show();
    jQuery('.repositon-btn').hide();
    jQuery('.change-cover-btn').hide();
    jQuery('.ynmember-profile-cover-picture-img')
    .css('cursor', 's-resize')
    .draggable({
        scroll: false,
        axis: "y",
        cursor: "s-resize",
        drag: function (event, ui) {
            y1 = jQuery('.ynmember-profile-cover-picture').height();
            y2 = jQuery('.ynmember-profile-cover-picture-img').height();
            
            if (ui.position.top >= 0) {
                ui.position.top = 0;
            }
            else
            if (ui.position.top <= (y1-y2)) {
                ui.position.top = y1-y2;
            }
        },
        
        stop: function(event, ui) {
            jQuery('input.cover-position').val(ui.position.top);
        }
    });
}

function saveReposition() {
    if (jQuery('input.cover-position').length == 1) {
        posY = jQuery('input.cover-position').val();
        new Request.JSON({
            'url': '<?php echo $this->url(array('action'=>'reposition-cover', 'controller'=>'edit', 'subject'=>$this->subject->getGuid()),'ynmember_extended', true)?>',
            'method': 'post',
            'data' : {
                'position' : posY
            },
            'onSuccess': function(responseJSON, responseText) {
                if (responseJSON.status == true) {
                    cover_top = posY;
                    jQuery('.resize-btns').hide();
                    jQuery('.repositon-btn').show();
                    jQuery('.change-cover-btn').show();
                }
                else {
                    alert(responseJSON.message);
                }            
            }
        }).send();
    }
}

function cancelReposition() {
    jQuery('.ynmember-profile-cover-picture-img').css('top', cover_top+'px');
    jQuery('.resize-btns').hide();
    jQuery('.repositon-btn').show();
    jQuery('.change-cover-btn').show();
    jQuery('input.cover-position').val(cover_top);
}
</script>

<div class="ynmember-profile-cover-main ynmember-clearfix">
	<div class="ynmember-profile-cover">
		<?php
		$coverPhotoUrl = "";
		if ($this->subject->cover_id)
		{
			$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->subject->cover_id)->current();
			if($coverFile)
				$coverPhotoUrl = $coverFile->map();
		}
		?>				
		<div class="ynmember-profile-cover-picture" style="background-image: url('<?php echo $coverPhotoUrl; ?>');">
			<?php if (!empty($coverPhotoUrl)) :?>
			<img class="ynmember-profile-cover-picture-img" src="<?php echo $coverPhotoUrl?>" style="top:<?php echo $this->subject->cover_top.'px'?>"/>
			<?php endif;?>
			<?php if ($this->subject->authorization()->isAllowed($this->viewer(), 'edit')) : ?>
			<div class="cover-btn-div">
				<?php if (!empty($coverPhotoUrl)) :?>
		        <a class="repositon-btn cover-btn" href="javascript:void(0)" onclick="repositionCover();"><span class="ynicon yn-arrows-v"></span><?php echo $this->translate('Reposition Cover Photo')?></a>
		        <div class="resize-btns cover-btn" style="display: none;">
		            <a class="cover-btn" href="javascript:void(0)" onclick="saveReposition();"><?php echo $this->translate('Save Position')?></a>
		            <a class="cover-btn" href="javascript:void(0)" onclick="cancelReposition();"><?php echo $this->translate('Cancel')?></a>
		            <input class="cover-position" name="pos" value="<?php echo $this->subject->cover_top?>" type="hidden">
		        </div>
		        <?php endif; ?>
				<?php 
				$label = ($coverPhotoUrl == '') ? $this->translate('Upload Cover Photo') : $this->translate('Change Cover Photo');
				echo $this->htmlLink(array(
					'route' => 'ynmember_extended',
					'controller' => 'edit',
					'action' => 'edit-cover-photo',
					'id' =>  $this->subject->getIdentity(),
				), '<span class="ynicon yn-pencil-square-o"></span>'.$label, array(
				'class' => 'smoothbox cover-btn change-cover-btn'
				)) ; ?>
			</div>
			<?php endif; ?>
		</div>		
	</div>
	<div class="ynmember-profile-info">
		<div class="ynmember-profile-avatar">
			<span>
			<?php $background_image = Engine_Api::_()->ynmember()->getMemberPhoto($this->subject());?>
            <?php
            echo $this->htmlLink(array(
                'route' => 'ynmember_extended',
                'controller' => 'edit',
                'action' => 'edit-photo',
                'id' =>  $this->subject()->getIdentity(),
            ), '<span alt="'.$this->subject()->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>',
                array(
                    'title'=>$this->subject()->getTitle(),
                    'class'=>'smoothbox'
                ));
            ?>
			</span>
		</div>

		<div class="ynmember-profile-information">
			<!-- title -->
			<div class="ynmember-profile-information-title"><?php echo $this -> subject -> getTitle(); ?></div>
			
			<!-- member type -->
			<?php if( !empty($this->memberType) ): ?>
		    <div class="ynmember-profile-information-type"><?php echo $this->translate($this->memberType) ?></div>
	   		<?php endif; ?>		
		   
		   	<div class="ynmember-profile-information-stats clearfix">
		  		<!-- view -->
		  		<div>
		  			<?php echo $this->translate(array('<span>%s</span> view', '<span>%s</span> views', $this->subject->view_count), $this->locale()->toNumber($this->subject->view_count)) ?>
		  		</div>
	        	
	        	<!-- friend -->
	        	<div>
	        	<?php $direction = Engine_Api::_()->getApi('settings', 'core')->getSetting('user.friends.direction', 0);
			    if ( $direction == 0 ): ?>
			      	<?php echo $this->translate(array('<span>%s</span> follower', '<span>%s</span> followers', $this->friendCount), $this->locale()->toNumber($this->friendCount)) ?>      
			    <?php else: ?>  
			    	<?php echo $this->translate(array('<span>%s</span> friend', '<span>%s</span> friends', $this->friendCount), $this->locale()->toNumber($this->friendCount)) ?>
			    <?php endif; ?>		  			
		  		</div>
			</div>

			<!-- updated -->
			<div class="ynmember-profile-information-lastupdate">
			<span class="ynicon yn-pencil-square-o" title="<?php echo $this->translate("Last Update"); ?>"></span>
			<?php 
  				if($this->subject->modified_date != "0000-00-00 00:00:00"){
		        	echo $this->timestamp($this->subject->modified_date);
		      	}
		      	else{
		          	echo $this->timestamp($this->subject->creation_date);
		      	}
		    ?>
			</div>

		    <!-- join -->
		    <div class="ynmember-profile-information-joined">
		    	<span class="ynicon yn-sign-in" title="<?php echo $this->translate("Joined"); ?>"></span>
				<?php echo $this->timestamp($this->subject->creation_date) ?>
		    </div>
		</div>
		
	</div>	
</div>