<?php $settings =  Engine_Api::_() -> getApi('settings', 'core');
$allow = $this->subject->authorization()->isAllowed($this->viewer, 'view');
$coverTbl = Engine_Api::_()->getDbTable('covers', 'ynbusinesspages');
$covers = $coverTbl -> getCoverByBusiness($this->subject);
$hasCover = count($covers) ? 1 : 0;
$coverPhotoUrl = '';
if ($hasCover)
{
	$coverPhotoUrl = $covers[0]->getPhotoUrl();
}
?>

<?php echo $this->partial('_popup-style.tpl') ?>

<div class="uiContextualDialogContent uiYnfbpp_business_popup <?php if ($hasCover) echo 'have_cover_business' ?>">
	<div class="uiYnfbppHovercardCover">
		<?php if($hasCover): ?>
			<span style="background-image:url(<?php echo $coverPhotoUrl ?>)"></span>
		<?php endif; ?>
		<h2 class="uiYnfbpp_title">
			<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
		</h2>
		<ul class="uiYnfbpp_communicate_icon">
			<?php if($allow && $settings->getSetting('ynfbpp.business.facebook', 1) && !empty($this -> subject -> facebook_link)) :?>
				<?php
				$facebook_link = $this -> subject -> facebook_link;
				$newFacebookLink = $facebook_link;
				if((strpos($facebook_link,'facebook.com') === false))
					$newFacebookLink = 'https://www.facebook.com/'.$facebook_link;
				if((strpos($newFacebookLink,'http') === false))
					$newFacebookLink = 'https://'.$facebook_link;
				?>
				<li>
					<a target="_blank" href="<?php echo $newFacebookLink ?>"><span class="ynicon yn-facebook"></span></a>
				</li>
			<?php endif;?>

			<?php if($allow && $settings->getSetting('ynfbpp.business.twitter', 1) && !empty($this -> subject -> twitter_link)) :?>
				<?php
				$twitter_link = $this -> subject -> twitter_link;
				$newTwitterLink = $twitter_link;
				if((strpos($twitter_link,'twitter.com') === false))
					$newTwitterLink = 'https://www.twitter.com/'.$twitter_link;
				if((strpos($newTwitterLink,'http') === false))
					$newTwitterLink = 'https://'.$twitter_link;
				?>
				<li>
					<a target="_blank" href="<?php echo $newTwitterLink ?>"><span class="ynicon yn-twitter"></span></a>
				</li>
			<?php endif;?>

			<?php if($allow && $settings->getSetting('ynfbpp.business.website', 1) && !empty($this->subject->web_address) && $this->subject->web_address[0]): ?>
				<?php
				$web_address = $this->subject->web_address;
				$new_web_address = $web_address[0];
				if((strpos($web_address[0],'http://') === false) && (strpos($web_address[0],'https://') === false))
					$new_web_address = 'http://' . $web_address[0];
				?>
				<li>
					<a target="_blank" href="<?php echo $new_web_address ?>"><span class="ynicon yn-global"></span></a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
	<div class="uiYnfbppHovercardTitle">
		<h2>
			<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
		</h2>
	</div>
	<div class="uiYnfbppHovercardStage">
		<div class="uiYnfbpp_avarta_owner">
			<a href="<?php echo $this->subject->getHref() ?>"  style="background-image:url(<?php echo $this->itemPhotoUrl($this->subject, 'thumb.profile') ?>)"></a>
		</div>
		<div class="uiYnfbpp_main_info">
			<div class="uiYnfbpp_info_content">
				<h2 class="uiYnfbpp_title ">
					<?php echo $this->htmlLink($this->subject->getHref(), $this->subject->getTitle()); ?>
				</h2>
				<ul class="uiYnfbpp_communicate_icon">
					<?php if($allow && $settings->getSetting('ynfbpp.business.facebook', 1) && !empty($this -> subject -> facebook_link)) :?>
						<?php
						$facebook_link = $this -> subject -> facebook_link;
						$newFacebookLink = $facebook_link;
						if((strpos($facebook_link,'facebook.com') === false))
							$newFacebookLink = 'https://www.facebook.com/'.$facebook_link;
						if((strpos($newFacebookLink,'http') === false))
							$newFacebookLink = 'https://'.$facebook_link;
						?>
						<li>
							<a target="_blank" href="<?php echo $newFacebookLink ?>"><span class="ynicon yn-facebook"></span></a>
						</li>
					<?php endif;?>

					<?php if($allow && $settings->getSetting('ynfbpp.business.twitter', 1) && !empty($this -> subject -> twitter_link)) :?>
						<?php
						$twitter_link = $this -> subject -> twitter_link;
						$newTwitterLink = $twitter_link;
						if((strpos($twitter_link,'twitter.com') === false))
							$newTwitterLink = 'https://www.twitter.com/'.$twitter_link;
						if((strpos($newTwitterLink,'http') === false))
							$newTwitterLink = 'https://'.$twitter_link;
						?>
						<li>
							<a target="_blank" href="<?php echo $newTwitterLink ?>"><span class="ynicon yn-twitter"></span></a>
						</li>
					<?php endif;?>

					<?php if($allow && $settings->getSetting('ynfbpp.business.website', 1) && !empty($this->subject->web_address) && $this->subject->web_address[0]): ?>
						<?php
						$web_address = $this->subject->web_address;
						$new_web_address = $web_address[0];
						if((strpos($web_address[0],'http://') === false) && (strpos($web_address[0],'https://') === false))
							$new_web_address = 'http://' . $web_address[0];
						?>
						<li>
							<a target="_blank" href="<?php echo $new_web_address ?>"><span class="ynicon yn-global"></span></a>
						</li>
					<?php endif; ?>
				</ul>
				<ul class="uiYnfbpp_personal_info clearfix">
					<?php if($allow && $settings->getSetting('ynfbpp.business.location', 1)): ?>
						<li class="uiYnfbpp_local">
							<span class="ynicon yn-map-marker"></span>
							<span><?php echo $this->subject->getMainLocation(); ?></span>
						</li>
					<?php endif; ?>
					<?php if($allow && $settings->getSetting('ynfbpp.business.email', 1) && !empty($this->subject->email)): ?>
						<li class="uiYnfbpp_website">
							<span class="ynicon yn-envelope"></span>
							<a href="mailto:<?php echo $this->subject->email ?>"><?php echo $this->subject->email ?></a>
						</li>
					<?php endif; ?>
				</ul>
				<?php if($allow && $settings->getSetting('ynfbpp.business.description', 1) && !empty($this->subject->description)): ?>
					<div class="uiYnfbpp_personal_decs">
						<span><?php echo strip_tags($this->subject->description) ?></span>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</div>
<?php if(($allow && $settings->getSetting('ynfbpp.business.location', 1) && $this->subject->getMainLocation(true)) || (isset($this->actions) && !empty($this->actions))): ?>
<div class="uiYnfbppHovercardFooter clearfix">
	<?php if($allow && $settings->getSetting('ynfbpp.business.location', 1) && $mainLocation = $this->subject->getMainLocation(true)): ?>
		<div class="uiynfbpp_get_localtion">
			<?php echo $this->htmlLink(
				array('route' => 'ynbusinesspages_general','action'=> 'direction', 'id' => $mainLocation->getIdentity()),
				'<span class="ynicon yn-location-arrow"></span><span>' . $this->translate("Get direction") . '</span>',
				array('class' => 'buttonlink smoothbox')); ?>
		</div>
	<?php endif; ?>
		<?php if(isset($this->actions) && !empty($this->actions)): ?>
		<ul class="uiYnfbppListHorizontal">
			<?php foreach($this->actions as $action): ?>
			<li class="uiYnfbppListItem">
				<?php echo $action?>
			</li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>
</div>
<?php endif; ?>
