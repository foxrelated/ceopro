<div class="ynmember-birthday-day ynmember-clearfix">
	<?php if($this->paginator->getTotalItemCount() > 4):?>
		<div class="ynmember-birthday-more">
			<a href="<?php echo $this->url(array('controller' =>'member', 'action' => 'birthday', 'date' => date('d'), 'month' => date('m'), 'year' => date('Y')),'ynmember_extended');?>">
				<?php echo $this->translate("View more &#187;");?>
			</a>
		</div>
	<?php endif;?>
</div>

<?php if($this->paginator->getTotalItemCount()):?>
<ul class="ynmember-birthday-items ynmember-clearfix">
	<?php foreach ($this->paginator as $user):?>		
		<li>
			<div class="ynmember-birthday-item">
		    	<div class="ynmember-birthday-item-avatar">
			    	<?php $background_image = $this->layout()->staticBaseUrl."application/modules/User/externals/images/nophoto_user_thumb_profile.png"; ?>
			    	<?php $userPhoto = $user->getPhotoUrl('thumb.profile');?>
					<?php if ($userPhoto) 
						$background_image = $userPhoto; ?>
					<?php echo $this->htmlLink($user->getHref(), '<span alt="'.$user->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$user->getTitle())) ?>
			    </div>
			    <div class="ynmember-birthday-item-hover">
			    	<a class="ynmember-birthday-item-title" href='<?php echo $user->getHref();?>'>
			    		<span class="ynicon yn-search"></span>
			    		<?php echo $user->getTitle();?>
			    	</a>
			    </div>
			</div>
	    </li>
	<?php endforeach;?>
</ul>
<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no members have birthday today.') ?>
		</span>
    </div>
<?php endif; ?>
