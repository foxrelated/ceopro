<?php
$title = Engine_Api::_()->getApi('settings', 'core')->getSetting('core_general_site_title', $this->translate('_SITE_TITLE'));
$return_url = $this->return_url;
if(!$return_url)
{
	$return_url = '64-' . base64_encode($_SERVER['REQUEST_URI']);
}
?>
<?php if(!$this->status && $this->error): ?>
<div class="ynmobileview_login_error">
	<?php echo $this->error?>
</div>
<?php endif;?>
<div class="ynmb_LoginInner">
	<form id="user_form_login" enctype="application/x-www-form-urlencoded" class="global_form_box" action="<?php echo $this->url(array(), 'ynmobi_login', true).'?return_url=' .$return_url;?>" method="post">
		<div class="ynmb_login_question"> <?php echo $this->translate('Already have an account?');?> </div>
		<div class="ynmb_loginForm_wrapper">
			<div class="ynmb_login_emailWrapper">
				<input autocorrect="off" autocapitalize="off" class="text" name="email" id="email" placeholder="<?php echo $this->translate('Email address');?>" autofocus="autofocus" value="" type="email">
			</div>
			<div class="ynmb_login_passWrapper">
				<input autocorrect="off" autocapitalize="off" class="" name="password" id="password" placeholder="<?php echo $this->translate('Password');?>" type="password">
			</div>
		</div>
		<div class="ynmb_login_button ynmb_SigninBtn">
			<button name="submit" id="submit" type="submit"> <?php echo $this->translate('Sign In');?> </button>
		</div>
		<div class="ynmb_login_button ynmb_SignupBtnWrapper">
			<div class="ynmb_login_question"><?php echo $this->translate('New to').'&nbsp;'.$title.('?');?> </div>
			<div class="ynmb_SignupBtn">
				<a class="" href="<?php echo $this->url(array('controller' => 'signup'), 'default', true) ;?>"><?php echo $this->translate('Create New Account');?></a>
			</div>
		</div>
		<?php if($this->fbLoginEnabled || $this->TwLoginEnabled || $this->JrLoginEnabled): ?>
		<div class="ynmb_socialConnect_btnWrapper">
			<div class="ynmb_login_question ynmb_socialConnect_title"><?php echo $this->translate('Or sign in using:');?> </div>
			<div class="yn_socialbtn_wrapper">
				<!-- When Jarain API was enabled -->
				<div class="login_form_container">
					<div class="social_connect_btn">
						<!-- Facebook Connect -->
						  <?php if($this->fbLoginEnabled): ?>      
						   <div class ="fb_connect yl_facebook"> <?php echo $this->fblogin; ?> </div>      
						   <?php else: ?>
						  <?php endif; ?>    
						<!-- Facebook Connect End -->
						<!-- Twitter Connect  -->
						  <?php if($this->TwLoginEnabled): ?>      
						   <div class ="fb_connect yl_twitter"> <?php echo $this->twlogin; ?> </div>      
						   <?php else: ?>
						  <?php endif; ?>    
						<!-- Twitter Connect End -->
						<!-- Janrain Connect  -->
						  <?php if($this->JrLoginEnabled): ?>      
						   <div class ="fb_connect yl_janrain"> <?php echo $this->jrlogin; ?> </div>      
						   <?php else: ?>
						  <?php endif; ?>    
						<!-- Janrain Connect End -->					
					</div>		
				</div>
			</div>			
		</div>
		<?php endif; ?>
	</form>	
	<div class="ynmb_login_forgotPass">
		<span>
			<span>
				<a href="<?php echo $this->url(array('module'=>'user','controller' => 'auth','action'=>'forgot'), 'default', true) ;?>"><?php echo $this->translate('Forgot password?');?></a>
			</span>
			<br>
		</span>
	</div>	
</div>
