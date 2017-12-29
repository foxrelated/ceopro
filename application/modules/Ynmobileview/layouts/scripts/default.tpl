<?php
/**
 * @package    Ynmobileview
 * @copyright  YouNet Company
 * @license    http://auth.younetco.com/license.html
 */

?>
<?php echo $this->doctype()->__toString() ?>
<?php $locale = $this->locale()->getLocale()->__toString(); 
$orientation = ( $this->layout()->orientation == 'right-to-left' ? 'rtl' : 'ltr' ); ?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $locale ?>" lang="<?php echo $locale ?>" dir="<?php echo $orientation ?>">
<head>
  <base href="<?php echo rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $this->baseUrl(), '/'). '/' ?>" />

  
  <?php // ALLOW HOOKS INTO META ?>
  <?php echo $this->hooks('onRenderLayoutMobileDefault', $this) ?>
  
  <?php // TITLE/META ?>
  <?php
    $counter = (int) $this->layout()->counter;
    $staticBaseUrl = $this->layout()->staticBaseUrl;
    
    $request = Zend_Controller_Front::getInstance()->getRequest();
    $this->headTitle()
      ->setSeparator(' - ');
    $pageTitleKey = 'pagetitle-' . $request->getModuleName() . '-' . $request->getActionName()
        . '-' . $request->getControllerName();
    $pageTitle = $this->translate($pageTitleKey);
    if( $pageTitle && $pageTitle != $pageTitleKey ) {
      $this
        ->headTitle($pageTitle, Zend_View_Helper_Placeholder_Container_Abstract::PREPEND);
    }
    $this
      ->headTitle($this->translate($this->layout()->siteinfo['title']), Zend_View_Helper_Placeholder_Container_Abstract::PREPEND)
      ;
    $this->headMeta()
      ->appendHttpEquiv('Content-Type', 'text/html; charset=UTF-8')
      ->appendHttpEquiv('Content-Language', 'en-US');
    
    // Make description and keywords
    $description = '';
    $keywords = '';
    
    $description .= ' ' .$this->layout()->siteinfo['description'];
    $keywords = $this->layout()->siteinfo['keywords'];

    if( $this->subject() && $this->subject()->getIdentity() ) {
      $this->headTitle($this->subject()->getTitle());
      
      $description .= ' ' .$this->subject()->getDescription();
      if (!empty($keywords)) $keywords .= ',';
      $keywords .= $this->subject()->getKeywords(',');
    }
    
    $this->headMeta()->appendName('description', trim($description));
    $this->headMeta()->appendName('keywords', trim($keywords));
    $this->headMeta()->appendName('viewport', 'initial-scale=1.0, maximum-scale=1.0, user-scalable=0');

    // Get body identity
    if( isset($this->layout()->siteinfo['identity']) ) {
      $identity = $this->layout()->siteinfo['identity'];
    } else {
      $identity = $request->getModuleName() . '-' .
          $request->getControllerName() . '-' .
          $request->getActionName();
    }
  ?>
  <?php echo $this->headTitle()->toString()."\n" ?>
  <?php echo $this->headMeta()->toString()."\n" ?>


  <?php // LINK/STYLES ?>
  <?php
    $this->headLink(array(
      'rel' => 'favicon',
      'href' => ( isset($this->layout()->favicon)
        ? $this->baseUrl() . $this->layout()->favicon
        : '/favicon.ico' ),
      'type' => 'image/x-icon'),
      'PREPEND');
    $themes = array();
    if( !empty($this->layout()->themes) ) {
      $themes = $this->layout()->themes;
    } else {
      $themes = array('default');
    }
    
	// Always load YN Mobile Theme
	$this->headLink() ->prependStylesheet($staticBaseUrl . 'application/css.php?request=application/modules/Ynmobileview/externals/styles/ynmobile.css');
	$this->headLink() ->prependStylesheet($staticBaseUrl . 'application/css.php?request=application/modules/Ynmobileview/externals/styles/default_mobile.css');
	
	// Check Adv feed and load css
	if(Engine_Api::_() -> hasModuleBootstrap('ynfeed'))
	{
		$this->headLink() ->prependStylesheet($staticBaseUrl . 'application/css.php?request=application/modules/Ynmobileview/externals/styles/adv_feed.css');
	}
	
	if( $orientation == 'rtl' ) 
	{
        // @todo add include for rtl
    }
	
    // add custom style
	$stylesTable = Engine_Api::_() -> getDbtable('styles', 'ynmobileview');
	$select = $stylesTable->select()->where('active = 1')->limit(1);
	$row = $stylesTable->fetchRow($select);
	if($row && $row->css)
	{
		$this->headStyle()->prependStyle($row->css);
	}
	
    // Process
    foreach( $this->headLink()->getContainer() as $dat ) {
      if( !empty($dat->href) ) {
        if( false === strpos($dat->href, '?') ) {
          $dat->href .= '?c=' . $counter;
        } else {
          $dat->href .= '&c=' . $counter;
        }
      }
    }
  ?>
  <?php echo $this->headLink()->toString()."\n" ?>
  <?php echo $this->headStyle()->toString()."\n" ?>

  <?php // TRANSLATE ?>
  <?php $this->headScript()->prependScript($this->headTranslate()->toString()) ?>

  <?php // SCRIPTS ?>
  <script type="text/javascript">
    <?php echo $this->headScript()->captureStart(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>

    Date.setServerOffset('<?php echo date('D, j M Y G:i:s O', time()) ?>');
    
    en4.orientation = '<?php echo $orientation ?>';
    en4.core.environment = '<?php echo APPLICATION_ENV ?>';
    en4.core.language.setLocale('<?php echo $this->locale()->getLocale()->__toString() ?>');
    en4.core.setBaseUrl('<?php echo $this->url(array(), 'default', true) ?>');
    en4.core.staticBaseUrl = '<?php echo $this->escape($staticBaseUrl) ?>';
    en4.core.loader = new Element('img', {src: en4.core.staticBaseUrl + 'application/modules/Core/externals/images/loading.gif'});
    en4.isMobile = true;
    
    <?php if( $this->subject() ): ?>
      en4.core.subject = {
        type : '<?php echo $this->subject()->getType(); ?>',
        id : <?php echo $this->subject()->getIdentity(); ?>,
        guid : '<?php echo $this->subject()->getGuid(); ?>'
      };
    <?php endif; ?>
    <?php if( $this->viewer()->getIdentity() ): ?>
      en4.user.viewer = {
        type : '<?php echo $this->viewer()->getType(); ?>',
        id : <?php echo $this->viewer()->getIdentity(); ?>,
        guid : '<?php echo $this->viewer()->getGuid(); ?>'
      };
    <?php endif; ?>
    if( <?php echo ( Zend_Controller_Front::getInstance()->getRequest()->getParam('ajax', false) ? 'true' : 'false' ) ?> ) {
      en4.core.dloader.attach();
    }
    
    <?php echo $this->headScript()->captureEnd(Zend_View_Helper_Placeholder_Container_Abstract::PREPEND) ?>
	
	
	// Open Menu Main - Body
	var toggleOpenMenuMain = function(element) {
		var heightMenuLeft = 0;
		if($$('.menuClosing').length)
		{
			if ($$('.ynmb_menuMain_wrapper')[0]) {
				heightMenuLeft = $$('.ynmb_menuMain_wrapper')[0].getSize().y;
			}
			var string = $$('.menuClosing')[0].get('class');
			string = string.replace("menuClosing","menuShowing");
			$$('.menuClosing').set('class', string);
			$$('#ynmb_siteWrapper')[0].setStyle('min-height', heightMenuLeft + 'px');
			$$('#ynmb_siteWrapper')[0].setStyle('height', heightMenuLeft + 'px');
			$$('#ynmb_siteWrapper')[0].setStyle('overflow', 'hidden');
		}
		else
		{
			var string = $$('.menuShowing')[0].get('class');
			string = string.replace("menuShowing","menuClosing");
			$$('.menuShowing').set('class', string);
			$$('#ynmb_siteWrapper')[0].setStyle('min-height','');
			$$('#ynmb_siteWrapper')[0].setStyle('height', 'auto');
		}		
	}	
	var toggleOpenMenuRight = function(element) {
		var heightMenuLeft = 0;
		console.log(element);
		if($$('.menuRightClosing').length)
		{
			if ($$('.ymb_menuRight_wapper')[0]) {
				heightMenuLeft = $$('.ymb_menuRight_wapper')[0].getSize().y;
			}
			var string = $$('.menuRightClosing')[0].get('class');
			string = string.replace("menuRightClosing","menuRightShowing");
			$$('.menuRightClosing').set('class', string);
			$$('#ynmb_siteWrapper')[0].setStyle('min-height', heightMenuLeft + 'px');
			$$('#ynmb_siteWrapper')[0].setStyle('height', heightMenuLeft + 'px');
			$$('#ynmb_siteWrapper')[0].setStyle('overflow', 'hidden');
		}
		else
		{
			var string = $$('.menuRightShowing')[0].get('class');
			string = string.replace("menuRightShowing","menuRightClosing");
			$$('.menuRightShowing').set('class', string);
			$$('#ynmb_siteWrapper')[0].setStyle('min-height','');
			$$('#ynmb_siteWrapper')[0].setStyle('height', 'auto');
		}		
	}
	</script>
	
	<?php 
    $this->headScript()
      ->prependFile($staticBaseUrl . 'externals/smoothbox/smoothbox4.js')
      ->prependFile($staticBaseUrl . 'application/modules/User/externals/scripts/core.js')
      ->prependFile($staticBaseUrl . 'application/modules/Core/externals/scripts/core.js')
      ->prependFile($staticBaseUrl . 'externals/chootools/chootools.js')
      ->prependFile($staticBaseUrl . 'externals/mootools/mootools-more-1.4.0.1-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
      ->prependFile($staticBaseUrl . 'externals/mootools/mootools-core-1.4.5-full-compat-' . (APPLICATION_ENV == 'development' ? 'nc' : 'yc') . '.js')
	  ->appendFile($this->baseUrl() . '/application/modules/Ynmobileview/externals/scripts/jquery.js');
    
   $request = Zend_Controller_Front::getInstance()->getRequest();
	$module = $request -> getModuleName();
	$controller = $request -> getControllerName();
	$action = $request -> getActionName();
    if($module =='advalbum' && $controller == 'index' && $action == 'browse')
    {
    	 $this->headScript()
		 	->appendFile($staticBaseUrl . 'application/modules/Advalbum/externals/scripts/jquery-1.7.1.min.js')
      		->appendFile($staticBaseUrl . 'application/modules/Advalbum/externals/scripts/idangerous.swiper-2.0.min.js');
    }
	
    // Process
    foreach( $this->headScript()->getContainer() as $dat ) {
      if( !empty($dat->attributes['src']) ) {
        if( false === strpos($dat->attributes['src'], '?') ) {
          $dat->attributes['src'] .= '?c=' . $counter;
        } else {
          $dat->attributes['src'] .= '&c=' . $counter;
        }
      }
    }
	?>
  <?php echo $this->headScript()->toString()."\n" ?>    
  <?php if($this->subject() && $this->subject() -> getType() == 'user'):
  	if($this->subject()->cover_id !== null ): ?>
	  <div>
	    <?php $iMain = Engine_Api::_() -> getItem('storage_file', $this->subject() -> cover_id);
			$coverUrl = '';
	    	if($iMain)
	    	{
	    		$coverUrl = $iMain -> map();
	    	}
	    	if($coverUrl):?>
	    		<style type="text/css">
	    			.layout_page_ynmobileview_index_profile .layout_middle:before 
	    			{
						background-image: url(<?php echo $coverUrl?>);
					}
	    		</style>
	    	<?php endif;?>
	  </div>
	<?php endif; endif; ?>
</head>
<body id="global_page_<?php echo $identity ?>" class="ynmb_siteBody menuClosing menuRightClosing">
  <div id="ynmb_siteWrapper">
	  <div id="global_header">
		<?php echo $this->content('header_ynmobileview') ?>
		<?php 
			$request = Zend_Controller_Front::getInstance()->getRequest();
			$module = $request -> getModuleName();
			$controller = $request -> getControllerName(); 
			$action = $request -> getActionName();
			if(($module == 'advalbum' && $controller == 'index' && $action == 'browse') || ($module == 'advalbum' && $controller == 'index' && $action == 'listing')){
				echo $this->content()->renderWidget('advalbum.albums-categories');
			} else if(($module == 'ynvideo' && $controller == 'index' && $action == 'index') || ($module == 'ynvideo' && $controller == 'index' && $action == 'list')){

				echo $this->content()->renderWidget('ynvideo.list-categories');
			} else if(($module == 'advgroup' && $controller == 'index' && $action == 'browse') || ($module == 'advgroup' && $controller == 'index' && $action == 'listing')){
				echo $this->content()->renderWidget('advgroup.groups-category-search');
			}
		?>
	  </div>
	  <div id='global_wrapper'>
	  	<?php echo $this->content()->renderWidget('ynmobileview.mobi-menu-mini') ?>
			<div id='global_content'>		
			  <?php echo $this->layout()->content ?>
			</div>
	  </div>
	  <?php if(!Engine_Api::_()->user()->getViewer()->getIdentity()):  ?>
	  <div id="global_footer">
		<?php echo $this->content('footer_ynmobileview') ?>
	  </div>
	  <?php endif; ?>
  </div>
  <?php
  	if($module == 'advalbum' && $controller == 'index' && $action=='browse'){
  ?>
  <script type="text/javascript">
	jQuery(function(){
		jQuery('.ymbHomeAbumSlideshow .ymb_thumb_slide').each(function(){
			jQuery(this).swiper({
					slidesPerView:'auto',
					calculateHeight: true
				})
		});
	});
	</script>
	<?php } ?>
	<?php
  	if($module == 'chat' && $controller == 'index' && $action=='index'){
	?>
	<script type="text/javascript">
		var updateUserOnline = function()
		{
			(function( $ ) 
			{
				$(function() 
				{
					if($(".cus_user_list"))
					{
						$(".cus_user_list").remove();
					}
					var user_online = $('ul.chat_users.chat_users_list > li').length;
					$("ul.chat_users_list").before("<div class='cus_user_list'><span><strong>" + user_online + "</strong> Users Online</span></div>");
					$(".cus_user_list").on('click', function()
					{
						$(this).parent().closest('.chat_users_wrapper').toggleClass('active');
					});
					$('textarea.chat_input').blur(function()
					{
						$('ul#im_container').show();
					}).focus(function() {		
						$('ul#im_container').hide();
					});
				});
			})(jQuery);
			var height = screen.height - 350;
			if(height < 150)
			{
				height = 150;
			}
			if(height > 400)
			{
				height = 400;
			}
			var chat_wrapper = $$('.chat_messages_wrapper');
			if(chat_wrapper.length)
			{
				chat_wrapper[0].style.height = height + 'px';
			}
			setTimeout(updateUserOnline, 10000);
		}
		window.addEvent('domready', function()
		{
			updateUserOnline();
			var chat_input = $$('.chat_input');
			if( chat_input.length )
			{
				chat_input[0].setProperty('placeholder', '<?php echo $this -> translate('Enter your message')?>');
			}
		});
	</script>
	<?php } ?>

	<script type="text/javascript">

		// hook update layout menu style more
		window.addEvent('domready', function()
		{
		     Smoothbox.Modal.Iframe.prototype.onOpen=function()
		     {
		       try
		       {
		         document.body.addClass('ynsmoothbox_open');

		        }   
		       catch(ex){ }
		       this.fireEvent('openbefore', this);
		     };

		     // close smoothbox
		     Smoothbox.Modal.Iframe.prototype.onClose=function()
		     {
		       try{ 
		         document.body.removeClass('ynsmoothbox_open');
		       }
		       catch(ex) { } 
		       this.fireEvent('closeafter', this);
		     };
			  // check if smoothbox windows auto add class in body
			  var window_url = document.URL;
			  if( window_url.contains("smoothbox") ) {
			    
			    // jQuery('html').addClass('ynsmoothbox_window');
			    document.body.addClass('ynsmoothbox_content');
			        
			  }
			if ( $$('.headline .tabs ul.navigation').length ) {
				var ynmobile_main_menu = $$('.headline .tabs ul.navigation')[0];
				if (ynmobile_main_menu.getElements('li').length > 3 ) {

					// move tab navigation
					var ynmobile_main_menu_tabs = $$('.headline .tabs')[0].removeClass('tabs').set({
							id: 'tabs',
						});

					ynmobile_main_menu_tabs.getParent('.headline').getParent().grab(ynmobile_main_menu_tabs, 'top');
					ynmobile_main_menu.addClass('ymb_navigation_more').removeClass('navigation');

					// check element and each element
					var ynmobile_main_menu_more = new Element('li').addClass('ymb_show_more_menus'),
						ynmobile_main_menu_more_div = new Element('div').addClass('ymb_listmore_option').set({
							html: '<div class="ymb_bg_showmore"><i class="ymb_arrow_showmore"></i></div>',	
						}),
						ynmobile_main_menu_more_a = new Element('a').addClass('ymb_showmore_menus').set({
							href: 'javascript:void(0)',
							html: '<i class="icon_showmore_menus"><?php echo $this -> translate("Show more")?></i>',
							events: {
        						click: function(){ 
        							ynmobile_main_menu_more_div.toggle();
        							$$('.layout_main .layout_middle').setStyle('min-height', ynmobile_main_menu_more_div.getSize().y );
        						},
		 					},
						});

					// restruct HTML
					ynmobile_main_menu_more.grab( ynmobile_main_menu_more_a );
					ynmobile_main_menu.getElements('li').each( function(element, index) {
						if ( index >= 2 ) {
							var element_div = new Element('div').set({ html: element.get('html') });
							element.destroy();
							ynmobile_main_menu_more_div.grab( element_div );
						}
					});

					ynmobile_main_menu_more.grab( ynmobile_main_menu_more_div )
					ynmobile_main_menu.grab(ynmobile_main_menu_more);
				}
			}
		});
	</script>
</body>
</html>
