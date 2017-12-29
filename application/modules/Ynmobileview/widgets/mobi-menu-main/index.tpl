<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

?>
<div class="ynmb_menuMain_wrapper" id="ynmb_menuMain_wrapper">
	<div class="ynmb_search_form">
	  <?php if($this->search_check):?>
		<div id="global_search_form_container">
		   <form id="global_search_form" action="<?php echo $this->url(array('controller' => 'search'), 'default', true) ?>" method="get">
			  <input type='text' class='text suggested' name='query' id='global_search_field' size='20' maxlength='100' alt='<?php echo $this->translate('Search') ?>' />
		   </form>
		</div>
	  <?php endif;?>		
	</div>
	 
	<?php
	$request = Zend_Controller_Front::getInstance()->getRequest();
	$module = $request->getModuleName();
	$controller = $request->getControllerName();
	$action = $request-> getActionName();
	$viewer = $this->viewer;
	if($viewer->getIdentity()):
		$photoUrl = $viewer->getPhotoUrl("thumb.icon");
		if(!$photoUrl)
		{
			$photoUrl = 'application/modules/User/externals/images/nophoto_user_thumb_icon.png';
		}
		?>	
		<div class="ynmb_menu_userInfo <?php if($module == 'ynmobileview' && $controller == 'index' && $action == 'profile') echo "active";?>">
			<a class="ynmb_menu_userInfo_link ynmb_menuMain_borderStyle" href="<?php echo $viewer->getHref();?>">
				<div class="ynmb_menu_userThumb">
					<i class="ynmb_menu_avatarUser" style="background-image:url(<?php echo $photoUrl; ?>)"> </i>
				</div>												
				<div class="ynmb_menu_nameUser">
					<?php echo $viewer->getTitle();?>
				</div>
			</a>			
		</div>
	<?php else:
		if(!($module == 'ynmobileview' && $controller == 'index')):
				$return_url = $this->return_url;
				if(!$return_url)
				{
					$return_url = '64-' . base64_encode($_SERVER['REQUEST_URI']);
				}
				?>
				<div class="ynm_signin ynmb_menu_userInfo <?php if($module == 'ynmobileview' && $controller == 'index' && $action == 'login') echo "active";?>">
				<a  class="ynmb_menu_userInfo_link ynmb_menuMain_borderStyle" href="<?php echo $this->url(array(), 'ynmobi_login', true).'?return_url=' .$return_url;?>" id="sign_in" class="ynmb_sortBtn_btn ynmb_touchable ynmb_a_btnStyle">
					<div class="ynmb_menu_nameUser">
						<?php echo $this->translate('Sign In');?>
					</div>
				</a>
				</div>
		<?php endif;
	 endif;?>	
	
	<div class="ynmb_menuMain_Inner">
		<h3 class="ynmb_menuMain_borderStyle"> <?php echo $this->translate('Menu') ?> </h3>		
		<ul class="ynmb_menu_navigation">
		  <?php
			foreach( $this->navigation as $item ):
			  $attribs = array_diff_key(array_filter($item->toArray()), array_flip(array(
			  'reset_params', 'route', 'module', 'controller', 'action', 'type',
			  'visible', 'label', 'href'
			  )));
			  if( !isset($attribs['active']) )
			  {
				$attribs['active'] = false;
			  }

			  // support allow custom menu items to be highlighted
			  if( false !== strpos($attribs['class'], 'custom_') )
			  {
				$uri = parse_url($attribs['uri'], PHP_URL_PATH);
				if( isset($_SERVER['REQUEST_URI']) && false !== strpos($_SERVER['REQUEST_URI'], $uri)){
				  $attribs['active'] = true;
				}
			  }
			  // support allow mobi menu items to be highlighted
			   if(false !== strpos($attribs['class'], '_'.$module) && $controller != 'settings' )
			   {
			   		$attribs['active'] = true;
			   }
			   $lable = $this->translate($item->getLabel());
			?>
			  <li<?php echo($attribs['active']?' class="active"':'')?>>
				<?php 				
				$attribs['class'] .= ' ynmb_menuMain_borderStyle';
				echo $this->htmlLink($item->getHref(), $lable, $attribs)
				?>
			 </li> 
			  <?php if(false !== strpos($attribs['class'], 'core_main_home') && $viewer->getIdentity()): 
			  	$message_count = Engine_Api::_()->messages()->getUnreadMessageCount($viewer);?>
			  	<li <?php echo(($module == 'messages')?' class="active"':'')?>> 
					<?php echo $this->htmlLink($this->url(array( 'action' => 'inbox'), 'messages_general'), $this->translate('Messages') . (($message_count > 0 )?'<div class="ynmb_menu_messageCount"><span>'.( $message_count ) . '</span></div>':''), array('class' => 'menu_core_main core_main_message ynmb_menuMain_borderStyle'))?> 
				<li/>
			  <?php endif;?>
		  <?php endforeach; ?>
			<li> <?php echo $this->htmlLink($this->url().'?mobile=0', $this->translate('Full Site'), array('class' => 'menu_core_main core_main_fullsite ynmb_menuMain_borderStyle'))?> <li/>
			<?php if($viewer->getIdentity()):?>
				<li <?php echo(($module == 'user' && $controller == 'settings')?' class="active"':'')?>> <?php echo $this->htmlLink($this->url(array('controller' => 'settings', 'action' => 'general'), 'user_extended'), $this->translate('Settings'), array('class' => 'menu_core_main core_main_settings ynmb_menuMain_borderStyle'))?> <li/>			
				<li> <a href="<?php echo $this->url(array('controller' => 'logout'), 'default', true) ;?>" class="menu_core_main core_main_auth ynmb_menuMain_borderStyle"><?php echo $this->translate('Logout');?></a> <li/>
			<?php endif;?>
		</ul>
	</div>
	<?php if( 1 !== count($this->languageNameList) ): ?>
		<div class="ynmb_languages">
		    <form method="post" action="<?php echo $this->url(array('controller' => 'utility', 'action' => 'locale'), 'default', true) ?>" class="ynmb_footerLanaguage">
		      <?php $selectedLanguage = $this->translate()->getLocale() ?>
		      <?php echo $this->formSelect('language', $selectedLanguage, array('onchange' => '$(this).getParent(\'form\').submit();'), $this->languageNameList) ?>
		      <?php echo $this->formHidden('return', $this->url()) ?>
		    </form>
	    </div>
	<?php endif; ?>
	<div class="ynmb_copyright"><?php echo $this->translate('Copyright &copy;%s', date('Y')) ?></div>
</div>

<script type='text/javascript'>
  en4.core.runonce.add(function(){
    if($('global_search_field')){
      new OverText($('global_search_field'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
  });
</script>
<script type="text/javascript">
		jQuery(function(){
			jQuery('ul.ynmb_menu_navigation li').each(function(){
				jQuery(this).click(function(){
					jQuery('ul.ynmb_menu_navigation').find('li').removeClass('active');
					jQuery(this).addClass('active');
				})
			});
		});
	</script>