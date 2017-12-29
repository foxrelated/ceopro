<div class="ynbusinesspages-profile-fields"><ul class="ynbusinesspages-overview-list-information">
		<?php if(isset($this -> params['phone']) && $this -> params['phone']):?>
		<?php if(!empty($this -> business -> phone)) :?>
		<li>
			<span><i class="ynicon yn-phone-ring"></i></span>
			<?php $phone = implode(' </span><br /><span>', $this -> business -> phone); ?>
			<span><?php echo $phone; ?></span>
		</li>
		<?php endif;?>
		<?php endif;?>
		
		<?php if(isset($this -> params['fax']) && $this -> params['fax']):?>
		<?php if(!empty($this -> business -> fax)) :?>
		<li>
			<span><i class="ynicon yn-fax"></i></span>
			<?php $fax = implode(' </span><br /><span>', $this -> business -> fax); ?>
			<span><?php echo $fax; ?></span>
		</li>
		<?php endif;?>
		<?php endif;?>

		<?php if(isset($this -> params['email']) && $this -> params['email']):?>
		<?php if(!empty($this -> business -> email)) :?>
		<li>			
			<span><i class="ynicon yn-envelope"></i></span>
			<span>
				<a href="mailto:<?php echo $this -> business -> email;?>"><?php echo $this -> business -> email;?></a>
			</span>
		</li>
		<?php endif;?>
		<?php endif;?>
	
		<?php if(isset($this -> params['website']) && $this -> params['website']):?>
		<?php if(!empty($this -> business -> web_address)) :?>
		<li>			
			<span><i class="ynicon yn-global"></i></span>
			<?php foreach($this -> business -> web_address as $itemlist) :?>
				<?php $newWebsiteURl = $itemlist; ?>
				<?php if((strpos($itemlist,'http://') === false) && (strpos($itemlist,'https://') === false)) $newWebsiteURl = 'http://'.$itemlist; ?>
				<span><a href="<?php echo $newWebsiteURl; ?>"><?php echo $itemlist; ?></a></span>
			<?php endforeach;?>
		</li>
		<?php endif;?>
		<?php endif;?>

		<?php if(isset($this -> params['facebook']) && $this -> params['facebook']):?>
		<?php if(!empty($this -> business -> facebook_link)) :?>
		<li>
			<span><i class="ynicon yn-facebook-circle"></i></span>
			<?php $facebook_link = $this -> business -> facebook_link ?>
			<?php $newFacebookLink = $facebook_link ?>
			<?php if((strpos($facebook_link,'facebook.com') === false)) $newFacebookLink = 'https://www.facebook.com/'.$facebook_link ?>
			<?php if((strpos($newFacebookLink,'http') === false)) $newFacebookLink = 'https://'.$facebook_link; ?>
			<?php $view = Zend_Registry::get("Zend_View");?>
            <?php $fbiconUrl = $view->layout()->staticBaseUrl.'application/modules/Ynbusinesspages/externals/images/facebook.jpg'; ?>
            <span>
				<a href="<?php echo $newFacebookLink ?>"><?php echo $newFacebookLink ?></a>
            </span>

		</li>
		<?php endif;?>
		<?php endif;?>

		<?php if(isset($this -> params['twitter']) && $this -> params['twitter']):?>
		<?php if(!empty($this -> business -> twitter_link)) :?>
		<li>
			<span><i class="ynicon yn-twitter-circle"></i></span>
			<?php $twitter_link = $this -> business -> twitter_link ?>
			<?php $newTwitterLink = $twitter_link ?>
			<?php if((strpos($twitter_link,'twitter.com') === false)) $newTwitterLink = 'https://www.twitter.com/'.$twitter_link ?>
			<?php if((strpos($newTwitterLink,'http') === false)) $newTwitterLink = 'https://'.$twitter_link; ?>
			<?php $view = Zend_Registry::get("Zend_View");?>
			<?php $tticonUrl = $view->layout()->staticBaseUrl.'application/modules/Ynbusinesspages/externals/images/twitter.jpg'; ?>
			<span>
				<a href="<?php echo $newTwitterLink ?>"><?php echo $newTwitterLink ?></a>
			</span>
		</li>
		<?php endif;?>
		<?php endif;?>
	</ul>
</div>

