<?php
/**
 * Younetco
 *
 * @category   Application_Extensions
 * @package    AdvGroup
 * @copyright  Copyright 2014 Younetco
 * @author     MYNT
 */
?>
<?php 
	$this->headLink()
		->prependStylesheet($this->baseUrl(). '/application/modules/Advgroup/externals/scripts/carousel/owl.carousel.css');
	$this->headScript()
		->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/carousel/jquery-1.9.1.min.js')
		->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/carousel/jquery-migrate-1.1.1.min.js')
		->appendFile($this->baseUrl() . '/application/modules/Advgroup/externals/scripts/carousel/owl.carousel.min.js');
?>

<script type="text/javascript">
var gl_setting_clicked = false;
var gl_invitation_proceed_clicked = false;

en4.core.runonce.add(function() 
{
	$$('.layout_core_container_tabs .more_tab > a').addEvent('click', function(e){
		this.getParent('.layout_core_container_tabs').setStyles({
			'min-height': this.getPrevious('.tab_pulldown_contents_wrapper').getElements('li').length * 37
		});
	});	
	
	$('advgroup_widget_cover_settings').addEvent('click', function(e) {
		if (gl_setting_clicked == false)
		{
			gl_setting_clicked = true;
			$$(".advgroup-detail-setting").set("style", "");
			if($$(".advgroup-detail-setting > div").length > 0){
				$$('.layout_core_container_tabs').setStyles({
					'min-height': $$(".advgroup-detail-setting > div").length * 37
				});
			}
			
			gl_invitation_proceed_clicked = false;
			$$(".advgroup-detail-request").set("style", "display: none;");
		}
		else
		{
			gl_setting_clicked = false;
			$$(".advgroup-detail-setting").set("style", "display: none;");
		}
    });

	invitation_proceed = $('advgroup_widget_cover_invitation_proceed');
	if (invitation_proceed !== null)
	{
		$('advgroup_widget_cover_invitation_proceed').addEvent('click', function(e) {
			if (gl_invitation_proceed_clicked == false)
			{
				gl_invitation_proceed_clicked = true;
				$$(".advgroup-detail-request").set("style", "");

				gl_setting_clicked = false;
				$$(".advgroup-detail-setting").set("style", "display: none;");
			}
			else
			{
				gl_invitation_proceed_clicked = false;
				$$(".advgroup-detail-request").set("style", "display: none;");
			}
			
	    });
	}
	
    
});

function checkOpenPopup(url)
{
	  if(window.innerWidth <= 480)
	  {
	  	Smoothbox.open(url, {autoResize : true, width: 300});
	  }
	  else
	  {
	  	Smoothbox.open(url);
	  }
}


</script>

<?php
$coverPhotoUrl = "";
if ($this->group->cover_photo)
{
	$coverFile = Engine_Api::_()->getDbtable('files', 'storage')->find($this->group->cover_photo)->current();
	if($coverFile)
		$coverPhotoUrl = $coverFile->map();
}
?>

<div class="advgroup-widget-profile-cover">
	<?php
		$groupPhotoUrl = ($this->group->getPhotoUrl())
			? ($this->group->getPhotoUrl())
			: $this->layout()->staticBaseUrl . 'application/modules/Advgroup/externals/images/nophoto_group_thumb_profile.png';
	?>

    <?php if ($coverPhotoUrl!="") : ?>
    <div class="profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url(<?php echo $coverPhotoUrl; ?>);"></span>
    </div>
    <?php else : ?>
    <div class="profile-cover-picture">
        <span class="profile-cover-picture-span" style="background-image: url('application/modules/Advgroup/externals/images/advgroup_default_cover.jpg');"></span>
    </div>
    <?php endif; ?>
    <div class="profile-cover-avatar">
        <span style="background-image: url(<?php echo $groupPhotoUrl; ?>);"></span>
    </div>
   <div class="advgroup-detail-info">
        <div class="info-top ynclearfix">
            <div class="advgroup-detail-action">
            		
            	<?php if($this->aJoinButton && is_array($this->aJoinButton)):?>
	                <?php if (count($this->aJoinButton) == '2'):?>
	                			<div id="advgroup_widget_cover_invitation_proceed">               				
	                				<a title="<?php echo $this->aJoinButton[0]['label']; ?>" class='smoothbox <?php echo $this->aJoinButton[0]['class'];?>' href="<?php echo $this->url($this->aJoinButton[0]['params'], $this->aJoinButton[0]['route'], array());?>">
	                					<i class="ynicon yn-check"></i>
	                				</a>
	                			</div>
	                			<div id="advgroup_widget_cover_invitation_proceed">               				
	                				<a title="<?php echo $this->aJoinButton[1]['label']; ?>" class='smoothbox <?php echo $this->aJoinButton[1]['class'];?>' href="<?php echo $this->url($this->aJoinButton[1]['params'], $this->aJoinButton[1]['route'], array());?>">
	                					<i class="ynicon yn-del"></i>
	                				</a>
	                			</div>
					<?php else:?>
								<?php if (isset($this->aJoinButton['params']['action'])) 
								{
									$action = $this->aJoinButton['params']['action'];
								}
								?>
								<div class="">
				                	<a href="<?php echo $this->url($this->aJoinButton['params'], $this->aJoinButton['route'], array());?>" class="<?php echo $this->aJoinButton['class'];?>" title="<?php echo $this->aJoinButton['label']; ?>">
				                		<?php if ($action === 'join'):?>
				                			<i class="ynicon yn-sign-in"></i>
				                		<?php elseif ($action === 'leave'):?>
				                			<i class="ynicon yn-sign-out"></i>
				                		<?php elseif ($action === 'request'):?>
				                			<i class="ynicon yn-question-circle"></i>
				                		<?php elseif ($action === 'cancel'):?>
				                			<i class="ynicon yn-question-circle"></i>
				                		<?php endif;?>			                				                	
				                	</a>
                				</div>
					<?php endif;?>                
                <?php endif;?>
                
  
                <?php if ($this->viewer() -> getIdentity()):
	                $url = $this -> url(array(
						'module' => 'activity',
						'controller' => 'index',
						'action' => 'share',
						'type' => $this->group -> getType(),
						'id' => $this->group -> getIdentity(),
						'format' => 'smoothbox'),'default', true);?>
		          <div class="">
						<a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')"><i class="ynicon yn-share" title="<?php echo $this -> translate("Share this group")?>"></i></a>
				  </div>
                <?php endif;?>
              
                <?php if ($this->viewer()->getIdentity()): ?>
	                <?php if($this->aReportButton):?>

	            			                	
	                	 <div class="">	     
		                	<a href="<?php echo $this->url($this->aReportButton['params'], 
		                	$this->aReportButton['route'], array());?>" 
		                	class="<?php echo $this->aReportButton['class'];?>" 
		                	title="<?php echo $this->aReportButton['label']; ?>" target=""> 
		                	</a>
	                	 </div>	
			  			     	
	                <?php endif;?>
                <?php endif;?>
                   
                <?php if ($this->viewer()->getIdentity()): ?>
                	<div id="advgroup_widget_cover_settings"><i class="ynicon yn-gear" title="<?php echo $this -> translate("Group options")?>"></i></div>
                <?php endif;?>
			</div>
			<div class="advgroup-detail-main">
				<div>
					<strong title="Group Name"><?php echo $this->translate($this->group->title) ?></strong>
					by
					<strong title="Group Owner">
						<a href=""><?php echo $this->translate($this->group->getOwner()) ?></a>
					</strong>
				</div>
				<div>
					<span>
						<i class="ynicon yn-alarm" title="Time create"></i>
						<?php echo $this->group->getTimeAgo() ?>
					</span>
					<span>
						<i class="ynicon yn-user" title="Guest"></i>
						<?php echo $this->translate(array("%s member", "%s member", $this->group->countGroupMembers()),$this->group->countGroupMembers()); ?>
					</span>
				</div>
				<?php
					$location = json_decode($this->group->location);
				?>
				<?php if($location->{'location'} != "0"):?>
				<div class="location-info">
					<span title="<?php print $location->{'location'}; ?>">
						<i class="ynicon yn-map-marker" title="Location"></i>
						<?php				
							print $location->{'location'};						
						?>
					</span>
				</div>
				<?php endif;?>
			</div>
            <div class="advgroup-detail-setting" style="display: none;">            	     
            	
				
				  <?php  if($this->aEditButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aEditButton['class'])) echo $this->aEditButton['class'];?>"  href="<?php echo $this->url($this->aEditButton['params'], $this->aEditButton['route'], array());?>">
						<?php echo $this -> translate($this->aEditButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aStyleButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aStyleButton['class'])) echo $this->aStyleButton['class'];?>" href="<?php echo $this->url($this->aStyleButton['params'], $this->aStyleButton['route'], array());?>" >
						<?php echo $this -> translate($this->aStyleButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aDeleteButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aDeleteButton['class'])) echo $this->aDeleteButton['class'];?>" href="<?php echo $this->url($this->aDeleteButton['params'], $this->aDeleteButton['route'], array());?>" >
						<?php echo $this -> translate($this->aDeleteButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				 <?php if($this->aCreateSubGroupButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aCreateSubGroupButton['class'])) echo $this->aCreateSubGroupButton['class'];?>" href="<?php echo $this->url($this->aCreateSubGroupButton['params'], $this->aCreateSubGroupButton['route'], array());?>" >
						<?php echo $this -> translate($this->aCreateSubGroupButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				 <?php if($this->aTrasferButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aTrasferButton['class'])) echo $this->aTrasferButton['class'];?>" href="<?php echo $this->url($this->aTrasferButton['params'], $this->aTrasferButton['route'], array());?>" >
						<?php echo $this -> translate($this->aTrasferButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aMessageButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aMessageButton['class'])) echo $this->aMessageButton['class'];?>" href="<?php echo $this->url($this->aMessageButton['params'], $this->aMessageButton['route'], array());?>">
						<?php echo $this -> translate($this->aMessageButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aInviteButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aInviteButton['class'])) echo $this->aInviteButton['class'];?>" href="<?php echo $this->url($this->aInviteButton['params'], $this->aInviteButton['route'], array());?>" >
						<?php echo $this -> translate($this->aInviteButton['label']); ?>
					</a>
				</div>
				<?php endif;?>  
				
				 <?php if($this->aProfileInvitenewButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileInvitenewButton['class'])) echo $this->aProfileInvitenewButton['class'];?>" href="<?php echo $this->url($this->aProfileInvitenewButton['params'], $this->aProfileInvitenewButton['route'], array());?>" >
						<?php echo $this -> translate($this->aProfileInvitenewButton['label']); ?>
					</a>
				</div>
				<?php endif;?>                                     
																		
				 <?php if($this->aInviteManageButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aInviteManageButton['class'])) echo $this->aInviteManageButton['class'];?>" href="<?php echo $this->url($this->aInviteManageButton['params'], $this->aInviteManageButton['route'], array());?>">
						<?php echo $this -> translate($this->aInviteManageButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				 <?php if($this->aProfileAlbumButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileAlbumButton['class'])) echo $this->aProfileAlbumButton['class'];?>" href="<?php echo $this->url($this->aProfileAlbumButton['params'], $this->aProfileAlbumButton['route'], array());?>" >
						<?php echo $this -> translate($this->aProfileAlbumButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				  <?php if($this->aProfileDiscussionButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileDiscussionButton['class'])) echo $this->aProfileDiscussionButton['class'];?>" href="<?php echo $this->url($this->aProfileDiscussionButton['params'], $this->aProfileDiscussionButton['route'], array());?>" >
						<?php echo $this -> translate($this->aProfileDiscussionButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				 <?php if($this->aProfileEventButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileEventButton['class'])) echo $this->aProfileEventButton['class'];?>" href="<?php echo $this->url($this->aProfileEventButton['params'], $this->aProfileEventButton['route'], array());?>">
						<?php echo $this -> translate($this->aProfileEventButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				 <?php if($this->aProfilePollButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfilePollButton['class'])) echo $this->aProfilePollButton['class'];?>" href="<?php echo $this->url($this->aProfilePollButton['params'], $this->aProfilePollButton['route'], array());?>">
						<?php echo $this -> translate($this->aProfilePollButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				 <?php if($this->aProfileVideoButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileVideoButton['class'])) echo $this->aProfileVideoButton['class'];?>" href="<?php echo $this->url($this->aProfileVideoButton['params'], $this->aProfileVideoButton['route'], array());?>" >
						<?php echo $this -> translate($this->aProfileVideoButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				 <?php if($this->aProfileUsefulLinkButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileUsefulLinkButton['class'])) echo $this->aProfileUsefulLinkButton['class'];?>" href="<?php echo $this->url($this->aProfileUsefulLinkButton['params'], $this->aProfileUsefulLinkButton['route'], array());?>">
						<?php echo $this -> translate($this->aProfileUsefulLinkButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aProfileActivityButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileActivityButton['class'])) echo $this->aProfileActivityButton['class'];?>" href="<?php echo $this->url($this->aProfileActivityButton['params'], $this->aProfileActivityButton['route'], array());?>">
						<?php echo $this -> translate($this->aProfileActivityButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aProfileMusicButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileMusicButton['class'])) echo $this->aProfileMusicButton['class'];?>" href="<?php echo $this->url($this->aProfileMusicButton['params'], $this->aProfileMusicButton['route'], array());?>" >
						<?php echo $this -> translate($this->aProfileMusicButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aProfileMp3MusicButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aProfileMp3MusicButton['class'])) echo $this->aProfileMp3MusicButton['class'];?>" href="<?php echo $this->url($this->aProfileMp3MusicButton['params'], $this->aProfileMp3MusicButton['route'], array());?>" >
						<?php echo $this -> translate($this->aProfileMp3MusicButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aFileSharingButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aFileSharingButton['class'])) echo $this->aFileSharingButton['class'];?>" href="<?php echo $this->url($this->aFileSharingButton['params'], $this->aFileSharingButton['route'], array());?>">
						<?php echo $this -> translate($this->aFileSharingButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aWikiButton):?>
				<div class="">
					<a class = "<?php if(!empty($this->aWikiButton['class'])) echo $this->aWikiButton['class'];?>" href="<?php echo $this->url($this->aWikiButton['params'], $this->aWikiButton['route'], array());?>">
						<?php echo $this -> translate($this->aWikiButton['label']); ?>
					</a>
				</div>
				<?php endif;?>
				
				<?php if($this->aProfileListingButton):?>
                <div class="">
                    <a class = "<?php if(!empty($this->aProfileListingButton['class'])) echo $this->aProfileListingButton['class'];?>" href="<?php echo $this->url($this->aProfileListingButton['params'], $this->aProfileListingButton['route'], array());?>" >
                        <?php echo $this -> translate($this->aProfileListingButton['label']); ?>
                    </a>
                </div>
                <?php endif;?>
                
                <?php if($this->aProfileSocialMusicAlbumButton):?>
                <div class="">
                    <a class = "<?php if(!empty($this->aProfileSocialMusicAlbumButton['class'])) echo $this->aProfileSocialMusicAlbumButton['class'];?>" href="<?php echo $this->url($this->aProfileSocialMusicAlbumButton['params'], $this->aProfileSocialMusicAlbumButton['route'], array());?>" >
                        <?php echo $this -> translate($this->aProfileSocialMusicAlbumButton['label']); ?>
                    </a>
                </div>
                <?php endif;?>
                
                <?php if($this->aProfileSocialMusicSongButton):?>
                <div class="">
                    <a class = "<?php if(!empty($this->aProfileSocialMusicSongButton['class'])) echo $this->aProfileSocialMusicSongButton['class'];?>" href="<?php echo $this->url($this->aProfileSocialMusicSongButton['params'], $this->aProfileSocialMusicSongButton['route'], array());?>" >
                        <?php echo $this -> translate($this->aProfileSocialMusicSongButton['label']); ?>
                    </a>
                </div>
                <?php endif;?>
                <?php if($this->aProfileUltimatevideoButton):?>
                <div class="">
                    <a class = "<?php if(!empty($this->aProfileUltimatevideoButton['class'])) echo $this->aProfileUltimatevideoButton['class'];?>" href="<?php echo $this->url($this->aProfileUltimatevideoButton['params'], $this->aProfileUltimatevideoButton['route'], array());?>" >
                        <?php echo $this -> translate($this->aProfileUltimatevideoButton['label']); ?>
                    </a>
                </div>
                <?php endif;?>
				<?php if($this->aProfileVideochannelButton):?>
					<div class="">
						<a class = "<?php if(!empty($this->aProfileVideochannelButton['class'])) echo $this->aProfileVideochannelButton['class'];?>" href="<?php echo $this->url($this->aProfileVideochannelButton['params'], $this->aProfileVideochannelButton['route'], array());?>" >
							<?php echo $this -> translate($this->aProfileVideochannelButton['label']); ?>
						</a>
					</div>
				<?php endif;?>
			</div>    
        </div>
		<div class="info-bottom ynclearfix">
			<div class="advgroup-detail-contact">
				<?php if(count($this->staff)>1):?>					
				<strong><?php echo $this->translate("Group Officer:"); ?></strong>
				<div class="advgroup_members_parent">
					<div id="advgroup_members" class="owl-carousel">
						<?php foreach( $this->staff as $info ): ?>       
						<?php if( !$this->group->isOwner($info['user']) ): ?>
						<div>
						<?php if($info['user'] -> getPhotoUrl("thumb.icon")):?>																										
							<a href="<?php echo $info['user']->getHref() ?>">
								<img src="<?php echo $info['user'] -> getPhotoUrl("thumb.icon"); ?>" />
							</a>
						<?php else:?>
							<a href="<?php echo $info['user']->getHref() ?>">
								<img src="<?php echo $this->baseURL(); ?>/application/modules/User/externals/images/nophoto_user_thumb_profile.png" />
							</a>
						<?php endif;?>
						</div>
						<?php endif;?>		
						<?php endforeach; ?>
					</div>
				</div>
				<?php endif;?>
			</div>
			<div class="advgroup-detail-more">
				<strong><?php echo $this->translate("Basic Information:"); ?></strong>
				<?php if($this->group->getCategory()):?>
				<!-- Category -->
				<div>
					<span>
						<i class="ynicon yn-bars" title="Category"></i>
					</span>
					<?php echo $this->group->getCategory(); ?>
				</div>
				<?php endif; ?>
				<?php if($this->aContactButton):?>
				<div>
					<span>
						<i class="ynicon yn-envelope" title="Contact"></i>
					</span>
					<a href="<?php echo $this->url($this->aContactButton['params'], $this->aContactButton['route'], array());?>">
						<?php echo $this->translate($this->aContactButton['label']); ?>
					</a>				
				</div>
				<?php endif;?>
				<!-- End Category -->
				<br/>
				<!-- Add-This Button -->
				<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
		        <!-- Go to www.addthis.com/dashboard to customize your tools -->  
		        <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script>
			</div>
		</div>
	</div>     
</div>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advgroup/externals/scripts/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="<?php echo $this->baseUrl()?>/application/modules/Advgroup/externals/scripts/carousel/owl.carousel.min.js"></script>
<script type="text/javascript">
	jQuery.noConflict();
	(function($){
		$(function(){
			jQuery(document).ready(function(){
				if(jQuery('#advgroup_members > div').length > 5){
					jQuery('#advgroup_members').owlCarousel({
						loop: true,
						margin: 5,
						slideBy: 5,
						responsiveClass:true,
						navText: ['',''],
						responsive:{
							0:{
								items: 5,
								nav: true
							}
						}
					});
				}
			});
		});
	})(jQuery);
</script>