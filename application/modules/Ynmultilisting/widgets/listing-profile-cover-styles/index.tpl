<!--
	Please go search
	 STYLE 1 - MOBILE
	 STYLE 2 
	 STYLE 3
	 Add-This Button BEGIN 
-->

<script type="text/javascript">
<?php if ($this->listing->theme == 'theme2' || $this->listing->theme == 'theme3' || $this->listing->theme == 'theme5') : ?>
    if ( window.getSize().x > 991 ){
        window.addEvent('domready', function() {
            var right = document.getElement('.layout_right');
            if (right) right.setStyle('width', '370px');
        })
    }
<?php endif; ?>
function addToCompare(obj, id) {
    var url = obj.get('rel');
    var jsonRequest = new Request.JSON({
        url : url,
        onSuccess : function(json, text) {
            if (!json.error) {
                obj.set('html', '<?php echo $this->translate('Remove from Compare')?>');
                obj.set('onclick', 'removeFromCompare(this, '+id+')');
                var params = {};
                params['format'] = 'html';
                var request = new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/name/ynmultilisting.compare-bar',
                    data : params,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        $$('.layout_ynmultilisting_compare_bar').destroy();
                        var body = document.getElementsByTagName('body')[0];
                        Elements.from(responseHTML).inject(body);
                        eval(responseJavaScript);
                    }
                });
                request.send();
            }
            else {
                alert(json.message);
            }
        }
    }).get({value:1});
}

function removeFromCompare(obj, id) {
    var url = obj.get('rel');
    var jsonRequest = new Request.JSON({
        url : url,
        onSuccess : function(json, text) {
            if (!json.error) {
                obj.set('html', '<?php echo $this->translate('Add to Compare')?>');
                obj.set('onclick', 'addToCompare(this, '+id+')');
                var params = {};
                params['format'] = 'html';
                var request = new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/name/ynmultilisting.compare-bar',
                    data : params,
                    onSuccess: function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        $$('.layout_ynmultilisting_compare_bar').destroy();
                        var body = document.getElementsByTagName('body')[0];
                        Elements.from(responseHTML).inject(body);
                        eval(responseJavaScript);
                    }
                });
                request.send();
            }
            else {
                alert(json.message);
            }
        }
    }).get({value:0});
}
function like_listing(ele) {   
    if (ele.className=="ynmultilisting_like") {
        var request_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'like', 'subject' => $this->listing->getGuid()), 'default', true); ?>';
    } else {
        var request_url = '<?php echo $this->url(array('module' => 'core', 'controller' => 'comment', 'action' => 'unlike', 'subject' => $this->listing->getGuid()), 'default', true); ?>';
    }
    new Request.JSON({
        url:request_url ,
        method: 'post',
        data : {
            format: 'json',
            'type':'ynmultilisting_listing',
            'id': <?php echo $this->listing->getIdentity() ?>
                    
        },
        onComplete: function(responseJSON, responseText) {
            if (responseJSON.error) {
                en4.core.showError('An error has occurred processing the request. The target may no longer exist.' + '<br /><br /><button onclick="Smoothbox.close()">Close</button>');
            } else {
                if (ele.className=="ynmultilisting_like") {
                    ele.setAttribute("class", "ynmultilisting_unlike")|| ele.setAttribute("className", "ynmultilisting_unlike");
                    ele.title= '<?php echo $this->translate("Liked") ?>';
                    ele.innerHTML = '<span class="fa fa-heart active"></span>';
                    $$('.ynmultilisting_point_like').each(function(el) {
                    	var value = el.get('text');
                    	value = parseInt(value);
                    	el.set('text', value+1);
                    });                   
                } else {    
                    ele.setAttribute("class", "ynmultilisting_like")|| ele.setAttribute("className", "ynmultilisting_like"); 
                    ele.title= '<?php echo $this->translate("Like") ?>';                     
                    ele.innerHTML = '<span class="fa fa-heart"></span>';
                    $$('.ynmultilisting_point_like').each(function(el) {
                    	var value = el.get('text');
                    	value = parseInt(value);
                    	el.set('text', value-1);
                    });
                }                   
            }
        }
    }).send();
}

function checkOpenPopup(url) {
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
<div class="ynmultilisting_detail_layout ynmultilisting_detail_layout_<?php echo $this->listing->theme; ?> clearfix">
    
<?php
      $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
?>   


	
 
<!-- STYLE 2 -->    
<?php if ($this->listing->theme == 'theme2') : ?>
    <!-- Base MasterSlider style sheet -->
    <link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider.css" />
     
    <!-- Master Slider Skin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>

    <!-- MasterSlider Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/ms-lightbox.css' rel='stylesheet' type='text/css'>

    <!-- Prettyphoto Lightbox jQuery Plugin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/prettyPhoto.css"  rel='stylesheet' type='text/css'/>    
     
    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery.easing.min.js"></script>
     
    <!-- Master Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/masterslider.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery.prettyPhoto.js"></script>

    <div class="ynmultilisting_style_2 clearfix">

        <div class="listing_category">
                <span class="fa fa-folder-open"></span>
                <?php $i = 0;  $category = $this->listing->getCategory();  ?>
                <?php if($category) :?>
                	<?php if($category -> level > 1) :?>
						<?php foreach($category->getBreadCrumNode() as $node): ?>
							<?php if($node -> category_id != $category -> getRootCategory() -> category_id) :?>
								<?php if($i != 0) :?>
									&raquo;	
								<?php endif;?>
			        			<?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
			        		<?php endif; ?>
		         	 	<?php endforeach; ?>
			         	<?php if($category -> parent_id != 0 && $category -> parent_id  != $category -> getRootCategory() -> category_id) :?>
									&raquo;	
						<?php endif;?>
			         	<?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
	         		<?php else :?>
	         			<?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
	         		<?php endif;?>
	         	<?php endif;?>
        </div>

        <div class="listing_theme2_info_top clearfix">
        
            <div class="listing_title"><?php echo $this->listing -> title; ?></div>

            <div class="listing_currency">
                <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
                <?php if ($this->listing->isAllowed('comment')) : ?>
                <div class="btn-fa">
                    <?php if ($this->listing->likes()->isLike($this->viewer())) : ?>
                    <a title="<?php echo $this->translate("Unlike this listing")?>" id="ynmultilisting_unlike" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_unlike">
                        <span class="fa fa-heart active"></span>
                    </a>
                    <?php else : ?>
                    <a title="<?php echo $this->translate("Like this listing") ?>" id="ynmultilisting_like" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_like"> 
                        <span class="fa fa-heart"></span>
                    </a>      
                    <?php endif;?>
                </div>
                <?php endif; ?>
            </div>

        </div>

        <div class="ynmultilisting-detail-content">

            <div class="listing_review clearfix">
                <div class="listing_rating">
                    <?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $this->listing)); ?>

                    <span class="review">
                        <?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $this->listing->review_count), $this->listing->review_count);?>
                    </span>
                    
                    <?php if ($this->can_review){
                        echo $this->htmlLink(
                            array(
                                'route' => 'ynmultilisting_review',
                                'action' => 'create',
                                'id' => $this->listing->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            '<span class="fa fa-pencil-square-o"></span>'.$this->translate('Add your Review'),
                            array(
                                'class' => 'listing-add-review smoothbox'
                            )
                        );
                    } else if ($this->has_review) {
                        echo $this->htmlLink(
                            array(
                                'route' => 'ynmultilisting_review',
                                'action' => 'edit',
                                'id' => $this->my_review->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            '<span class="fa fa-pencil-square-o"></span> '.$this->translate('You have reviewed.'),
                            array(
                                'class' => 'reviewed smoothbox'
                            )
                        );
                    }?>

                </div>                
            </div>

            <div class="listing_expired">
                    <?php
                        $expirationDateObj = null;
                        if (!is_null($this -> listing -> expiration_date) && !empty($this -> listing -> expiration_date) && $this -> listing -> expiration_date) 
                        {
                            $expirationDateObj = new Zend_Date(strtotime($this -> listing->expiration_date));
                        }
                        if( $this->viewer() && $this->viewer()->getIdentity() ) {
                            $tz = $this->viewer()->timezone;
                            if (!is_null($expirationDateObj))
                            {
                                $expirationDateObj->setTimezone($tz);
                            }
                        }
                    ?>

                <span class="listing_expired_title"><?php echo $this -> translate('Expired');?>:</span>  <span class="listing_expired_date"><?php echo (!is_null($expirationDateObj)) ? date('M d Y', $expirationDateObj -> getTimestamp()) : ''; ?></span>

                <?php echo "&nbsp;&nbsp;-&nbsp;&nbsp;".$this -> translate('Liked')?>: <b class="ynmultilisting_point_like"><?php echo $this -> listing -> like_count;?></b>
                
            </div>

            <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div>

            <div class="listing_contact">
                <?php echo $this->htmlLink(
                    array(
                        'route' => 'ynmultilisting_specific',
                        'action' => 'email-to-friends',
                        'listing_id' => $this->listing->getIdentity()
                    ),
                    $this->translate('<span class="fa fa-envelope"></span> Email to Friends'),
                    array(
                        'class' => 'smoothbox'
                    )
                )?>
                
                <?php 
                echo $this->htmlLink(
	                array(
	                    'route' => 'ynmultilisting_specific',
	                    'action' => 'print',
	                    'listing_id' => $this->listing->getIdentity()
	                ),
	                '<span class="fa fa-print"></span>'.$this->translate('Print'),
	                array('target' => '_blank'))
                ?>
                
                <?php if ($this->listing->isAllowed('edit') || $this->listing->isAllowed('share') || $this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                <div class="ynmultilisting_view_more">
                    <span class="fa fa-caret-down"></span><?php echo $this->translate('More')?>
                   
                    <div class="ynmultilisting_view_more_popup">
                        <?php if ($this->listing->isAllowed('edit')) : ?>
                        <div id="edit">
                             <?php $url = $this -> url(array(
                                'action' => 'edit',
                                'listing_id' => $this->listing->getIdentity(),
                                ),'ynmultilisting_specific', true)
                            ;?>
                            <a href="<?php echo $url?>"><?php echo $this->translate('Edit')?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ($this->listing->isAllowed('share')) : ?>
                        <div id="share">
                            <?php $url = $this -> url(array(
                                'module' => 'activity',
                                'controller' => 'index',
                                'action' => 'share',
                                'type' => 'ynmultilisting_listing',
                                'id' => $this->listing->getIdentity(),
                                'format' => 'smoothbox'),'default', true)
                            ;?>
                            <a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Share')?></a>
                        </div>
                        <?php endif; ?>
                        <div id="report">
                            <?php
                            $url = $this->url(array(
                                'module' => 'core',
                                'controller' => 'report',
                                'action' => 'create',
                                'subject' => $this->listing->getGuid(),
                                'format' => 'smoothbox'),'default', true);
                            ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Report') ?></a>
                        </div>
                        <?php if ($this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                        <div id="transfer_owner">
                            <?php
	                        $url = $this->url(array(
	                            'action' => 'transfer-owner',
	                            'listing_id' => $this->listing->getIdentity(),
	                            ),'ynmultilisting_specific', true);
	                        ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Transfer Owner') ?></a>
                        </div>
                        <?php endif; ?>
                        
                        <!-- HOANGND add to compare-->
	                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
	                    	<div id="compare">
	                        <?php if ($this->listing->inCompare()) : ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Remove from Compare')?>
	                            </a>
	                        <?php else: ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Add to Compare')?>
	                            </a>
	                        <?php endif; ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to compare-->
	
	                    <!-- HOANGND add to wishlist-->
	                    <?php if($this->viewer()->getIdentity()) :?>
	                    	<div id="wishlist">
	                        <?php echo $this->htmlLink(
	                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $this->listing->getIdentity()),
	                        $this->translate('Add to Wish List'),
	                        array('class' => 'smoothbox')) ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to wishlist-->
                        
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <div style="margin-top: 20px">
                <!-- Add-This Button BEGIN -->
                <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?>
                <!-- Go to www.addthis.com/dashboard to customize your tools --> 
                <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script>
                 <!-- Add-This Button END -->  
            </div>
        </div>

        <div class="ynmultilisting-slider-master">
            <div class="ynmultilisting-tab-content">
            <?php if(count($this->photos) > 0):?>       
                <!-- template -->
                <div class="ynmultilisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                    <?php foreach($this->photos as $photo):?>
                    <?php if($this->listing->photo_id == $photo->file_id):?>
                        <div class="ms-slide">
                            <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                            <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                            <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $photo->image_title; ?>"><i class="fa fa-search fa-3x"></i></a>
                        </div>
                    <?php break; endif; ?>
                    <?php endforeach;?>
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id != $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a href="<?php echo $photo->getPhotoUrl(); ?>" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $photo->image_title; ?>"><i class="fa fa-search fa-3x"></i></a>
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>

                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width: 800,
                        height: 340,
                        space: 5,
                        loop: true,
                        autoplay: true,
                        speed: 10,
                        view:'fade'
                    });
                    slider.control('arrows');  
                    slider.control('lightbox');
                    slider.control('thumblist' , {autohide:false ,dir:'h'});
                     
                    jQuery(document).ready(function(){
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto();
                    });  
                </script>
            <?php else:?>
                <!-- no photo -->
                <div class="ynmultilisting-photo-details ms-lightbox-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                             <div class="ms-slide">
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png" alt="<?php echo $this->translate('No Photo')?>"/> 
                                <img class="ms-thumb" src="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png" alt="thumb" />
                                <a href="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png" class="ms-lightbox" rel="prettyPhoto[gallery1]" title="<?php echo $this->translate('No Photo')?>"></a>
                            </div>
                    </div>
                </div>
                 <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width: 800,
                        height: 340,
                        space: 5,
                        loop: true,
                        autoplay: true,
                        speed: 10,
                        view:'fade'
                    });
                    slider.control('arrows');  
                    slider.control('lightbox');
                    slider.control('thumblist' , {autohide:false ,dir:'h'});
                     
                    jQuery(document).ready(function(){
                        jQuery("a[rel^='prettyPhoto']").prettyPhoto();
                    });  
                </script>
            <?php endif;?>

            <?php if(count($this->videos) > 0):?>
                <div class="ynmultilisting-video-details ms-lightbox-template" style="display: none;">
                    <div class="master-slider ms-skin-default" id="masterslider2">
                     <?php foreach($this->videos as $video):?>
                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id == $video->getIdentity() && $this->listing->video_type = $video_type):?>
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                   $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => $this->module_video,
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php break; endif; ?>
                    <?php endforeach;?> 
                    <?php foreach($this->videos as $video):?>
                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id != $video->getIdentity() && $this->listing->video_type = $video_type):?>
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => $this->module_video,
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>
            <?php else:?>
                <!-- no video -->
            <?php endif;?>
            </div>

            <?php if(count($this->videos) > 0):?>
            <div class="ynmultilisting-tab">
                <div class="ynmultilisting-tab-item active" data-id="ynmultilisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                <div class="ynmultilisting-tab-item" data-id="ynmultilisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
            </div>
            <?php endif; ?>
        </div>
        <script type="text/javascript">
            var active_slider2 = 0;            

            $$('.ynmultilisting-tab .ynmultilisting-tab-item').addEvent('click', function(){
                $$('.ynmultilisting-tab-content > div').hide();
                $$('.ynmultilisting-tab-content').getElement( "."+this.get('data-id') ).show();

                $$('.ynmultilisting-tab .ynmultilisting-tab-item').removeClass('active');
                this.addClass('active');

                if (active_slider2 == 0) {
                    (function( $ ) {
                        $(function() {
                            // More code using $ as alias to jQuery
                            var slider2 = new MasterSlider();

                            slider2.setup('masterslider2' , {
                                width: 800,
                                height: 330,
                                space:5,
                                loop:true,
                                autoplay: true,
                                speed: 10,
                                view:'fade'
                            });
                            slider2.control('arrows');  
                            slider2.control('lightbox');
                            slider2.control('thumblist' , {autohide:false ,dir:'h'});
                        });
                    })(jQuery);                    

                    active_slider2 = 1;
                }
            });

            window.onload = function(){
                var theme_height = $$('.ynmultilisting_style_2')[0].getSize().y;
                // alert(theme_height);

                $$('.ynmultilisting_layout_theme2 .layout_core_content')[0].setStyles({
                    'height': theme_height,
                    'margin-top': -theme_height,
                });

                $$('.ynmultilisting_layout_theme2 .layout_middle').setStyle( 'padding-top', theme_height );
                $$('.ynmultilisting_layout_theme2 .layout_right').setStyle( 'padding-top', theme_height );
            };

            window.addEvent('resize:throttle(100)', function(){
                // Will only fire once every 100 ms
                var theme_height = $$('.ynmultilisting_style_2')[0].getSize().y;
                // alert(theme_height);

                $$('.ynmultilisting_layout_theme2 .layout_core_content')[0].setStyles({
                    'height': theme_height,
                    'margin-top': -theme_height,
                });

                $$('.ynmultilisting_layout_theme2 .layout_middle').setStyle( 'padding-top', theme_height );
                $$('.ynmultilisting_layout_theme2 .layout_right').setStyle( 'padding-top', theme_height );
            });
        </script>
    </div>



<!-- STYLE 3 -->

<?php elseif ($this->listing->theme == 'theme3') : ?>
    <!-- Base MasterSlider style sheet -->
    <link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider.css" />
     
    <!-- Master Slider Skin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>
      
    <!-- MasterSlider Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/ms-caro3d.css' rel='stylesheet' type='text/css'>
     
    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery.easing.min.js"></script>
     
    <!-- Master Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/masterslider.min.js"></script>
    
    <div class="ynmultilisting_style_3">
        <div class="listing_category">
            <span class="fa fa-folder-open"></span>
            <?php $i = 0;  $category = $this->listing->getCategory();  ?>
            <?php if($category) :?>
                <?php if($category -> level > 1) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != $category -> getRootCategory() -> category_id) :?>
                            <?php if($i != 0) :?>
                                &raquo; 
                            <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if($category -> parent_id != 0 && $category -> parent_id  != $category -> getRootCategory() -> category_id) :?>
                                &raquo; 
                    <?php endif;?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php else :?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php endif;?>
            <?php endif;?>
        </div>

        <div class="ynmultilisting-slider-master">
            <div class="ynmultilisting-tab-content">
            <?php if(count($this->photos) > 0):?>       
                <!-- template -->
                <div class="ynmultilisting-photo-details ms-caro3d-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id == $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                            </div>
                        <?php break; endif; ?>
                    <?php endforeach;?>
                    <?php foreach($this->photos as $photo):?>
                        <?php if($this->listing->photo_id != $photo->file_id):?>
                            <div class="ms-slide">
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                            </div>
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>
                <!-- end of template -->

                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width:460,
                        height:290,
                        space:0,
                        loop:true,
                        autoplay: true,
                        speed: 10,
                        view:'flow'
                    });
                     
                    slider.control('arrows');  
                </script>
            <?php else:?>
                <!-- no photo -->
                <div class="ynmultilisting-photo-details ms-caro3d-template">
                    <div class="master-slider ms-skin-default" id="masterslider">
                            <div class="ms-slide">
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png" alt="<?php echo $this->translate('No Photo')?>"/> 
                            </div>
                    </div>
                </div>
                <script type="text/javascript">      
                    jQuery.noConflict();

                    var slider = new MasterSlider();
                    slider.setup('masterslider' , {
                        width:460,
                        height:290,
                        space:0,
                        loop:true,
                        autoplay: true,
                        speed: 10,
                        view:'flow'
                    });
                     
                    slider.control('arrows');  
                </script>
            <?php endif;?>

            <?php if(count($this->videos) > 0):?>
                <div class="ynmultilisting-video-details ms-caro3d-template" style="display: none;">
                    <div class="master-slider ms-skin-default" id="masterslider2">
                     <?php foreach($this->videos as $video):?>
                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id == $video->getIdentity() && $this->listing->video_type = $video_type):?>
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => $this->module_video,
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                            
                        <?php break; endif; ?>
                    <?php endforeach;?>

                    <?php foreach($this->videos as $video):?>
                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id != $video->getIdentity() && $this->listing->video_type = $video_type):?>
                            <?php 
                                $embedded = "";
                                if ($video->type == 1) {
                                    $embedded = "//www.youtube.com/embed/".$video->code;
                                } elseif ($video->type == 2) {
                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
                                } elseif ($video->type == 4) {
                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
                                } else {
                                	$embedded = 'http://' . $_SERVER['HTTP_HOST']
									      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
									        'module' => $this->module_video,
									        'controller' => 'video',
									        'action' => 'external',
									        'video_id' => $video->getIdentity(),
									      ), 'default', true) . '?format=frame';
                                }
                            ?>
                            <div class="ms-slide">
                            	<?php 
                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
									if(empty($video_src))
									{
										$video_src = $video -> getPhotoUrl('thumb.normal'); 
									}
                            	?>
                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
                            </div> 
                        <?php endif;?>
                    <?php endforeach;?>
                    </div>
                </div>

            <?php else:?>
                <!-- no video -->
            <?php endif;?>
            </div>


            <?php if(count($this->videos) > 0):?>
            <div class="ynmultilisting-tab">
                <div class="ynmultilisting-tab-item active" data-id="ynmultilisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                <div class="ynmultilisting-tab-item" data-id="ynmultilisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
            </div>
            <?php endif; ?>
        </div>

        <script type="text/javascript">
            var active_slider2 = 0;

            $$('.ynmultilisting-tab .ynmultilisting-tab-item').addEvent('click', function(){
                $$('.ynmultilisting-tab-content > div').hide();
                $$('.ynmultilisting-tab-content').getElement( "."+this.get('data-id') ).show();

                $$('.ynmultilisting-tab .ynmultilisting-tab-item').removeClass('active');
                this.addClass('active');

                if (active_slider2 == 0) {
                    (function( $ ) {
                        $(function() {
                            // More code using $ as alias to jQuery
                            var slider2 = new MasterSlider();

                            slider2.setup('masterslider2' , {
                                width:460,
                                height:270,
                                space:0,
                                loop:true,
                                autoplay: true,
                                speed: 10,
                                view:'flow'
                            });                     
                            slider2.control('arrows'); 
                        });
                    })(jQuery);                    

                    active_slider2 = 1;
                }
            });
        </script>

        <div class="listing_title"><?php echo $this->listing -> title; ?></div>
        
        <div class="listing_currency">
            <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
             <?php if ($this->listing->isAllowed('comment')) : ?>
            <div class="btn-fa">
                <?php if ($this->listing->likes()->isLike($this->viewer())) : ?>
                <a title="<?php echo $this->translate("Unlike this listing")?>" id="ynmultilisting_unlike" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_unlike">
                    <span class="fa fa-heart active"></span>
                </a>
                <?php else : ?>
                <a title="<?php echo $this->translate("Like this listing") ?>" id="ynmultilisting_like" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_like"> 
                    <span class="fa fa-heart"></span>
                </a>      
                <?php endif;?>
            </div>
            <?php endif; ?>
        </div>

        <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div>

        <div class="listing_review clearfix">
            <div class="listing_rating">
                <?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $this->listing)); ?>

                <span class="review">
                    <b class="ynmultilisting_point_rating">&nbsp;<?php echo $this -> listing -> rating;?>&nbsp;</b>
                    <?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $this->listing->review_count), $this->listing->review_count);?>
                </span>
                <?php if ($this->can_review){
                    echo $this->htmlLink(
                        array(
                            'route' => 'ynmultilisting_review',
                            'action' => 'create',
                            'id' => $this->listing->getIdentity(),
                            'tab' => $this->identity,
                            'page' => $this->page
                        ),
                        '<span class="fa fa-pencil-square-o"></span>'.$this->translate('Add your Review'),
                        array(
				            'class' => 'listing-add-review smoothbox'
				        )
                    );
                } else if ($this->has_review) {
                    echo $this->htmlLink(
                        array(
                            'route' => 'ynmultilisting_review',
                            'action' => 'edit',
                            'id' => $this->my_review->getIdentity(),
                            'tab' => $this->identity,
                            'page' => $this->page
                        ),
                        '<span class="fa fa-pencil-square-o"></span> '.$this->translate('You have reviewed.'),
                        array(
                            'class' => 'reviewed smoothbox'
                        )
					);
                }?>
                
            </div>

            <div class="listing_expired">
                    <?php
                        $expirationDateObj = null;
                        if (!is_null($this -> listing -> expiration_date) && !empty($this -> listing -> expiration_date) && $this -> listing -> expiration_date) 
                        {
                            $expirationDateObj = new Zend_Date(strtotime($this -> listing->expiration_date));
                        }
                        if( $this->viewer() && $this->viewer()->getIdentity() ) {
                            $tz = $this->viewer()->timezone;
                            if (!is_null($expirationDateObj))
                            {
                                $expirationDateObj->setTimezone($tz);
                            }
                        }
                    ?>

                <span class="listing_expired_title"><?php echo $this -> translate('Expired');?>:</span>  <span class="listing_expired_date"><?php echo (!is_null($expirationDateObj)) ? date('M d Y', $expirationDateObj -> getTimestamp()) : ''; ?></span>

                 <?php echo "&nbsp;&nbsp;-&nbsp;&nbsp;".$this -> translate('Liked')?>: <b class="ynmultilisting_point_like"><?php echo $this -> listing -> like_count;?></b>
                
            </div>


        </div>

        <div class="listing_contact">
                <?php echo $this->htmlLink(
                    array(
                        'route' => 'ynmultilisting_specific',
                        'action' => 'email-to-friends',
                        'listing_id' => $this->listing->getIdentity()
                    ),
                    $this->translate('<span class="fa fa-envelope"></span> Email to Friends'),
                    array(
                        'class' => 'smoothbox'
                    )
                )?>
                
                <?php 
                echo $this->htmlLink(
	                array(
	                    'route' => 'ynmultilisting_specific',
	                    'action' => 'print',
	                    'listing_id' => $this->listing->getIdentity()
	                ),
	                '<span class="fa fa-print"></span>'.$this->translate('Print'),
	                array('target' => '_blank'))
                ?>

                <?php if ($this->listing->isAllowed('edit') || $this->listing->isAllowed('share') || $this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                <div class="ynmultilisting_view_more">
                    <span class="fa fa-caret-down"></span><?php echo $this->translate('More')?>
                    
                    <div class="ynmultilisting_view_more_popup">
                        <?php if ($this->listing->isAllowed('edit')) : ?>
                        <div id="edit">
                             <?php $url = $this -> url(array(
                                'action' => 'edit',
                                'listing_id' => $this->listing->getIdentity(),
                                ),'ynmultilisting_specific', true)
                            ;?>
                            <a href="<?php echo $url?>"><?php echo $this->translate('Edit')?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ($this->listing->isAllowed('share')) : ?>
                        <div id="share">
                            <?php $url = $this -> url(array(
                                'module' => 'activity',
                                'controller' => 'index',
                                'action' => 'share',
                                'type' => 'ynmultilisting_listing',
                                'id' => $this->listing->getIdentity(),
                                'format' => 'smoothbox'),'default', true)
                            ;?>
                            <a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Share')?></a>
                        </div>
                        <?php endif; ?>
                        <div id="report">
                            <?php
                            $url = $this->url(array(
                                'module' => 'core',
                                'controller' => 'report',
                                'action' => 'create',
                                'subject' => $this->listing->getGuid(),
                                'format' => 'smoothbox'),'default', true);
                            ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Report') ?></a>
                        </div>
                        <?php if ($this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                        <div id="transfer_owner">
                            <?php
	                        $url = $this->url(array(
	                            'action' => 'transfer-owner',
	                            'listing_id' => $this->listing->getIdentity(),
	                            ),'ynmultilisting_specific', true);
	                        ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Transfer Owner') ?></a>
                        </div>
                        <?php endif; ?>
                        
                        <!-- HOANGND add to compare-->
	                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
	                    	<div id="compare">
	                        <?php if ($this->listing->inCompare()) : ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Remove from Compare')?>
	                            </a>
	                        <?php else: ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Add to Compare')?>
	                            </a>
	                        <?php endif; ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to compare-->
	
	                    <!-- HOANGND add to wishlist-->
	                    <?php if($this->viewer()->getIdentity()) :?>
	                    	<div id="wishlist">
	                        <?php echo $this->htmlLink(
	                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $this->listing->getIdentity()),
	                        $this->translate('Add to Wish List'),
	                        array('class' => 'smoothbox')) ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to wishlist-->
                    </div>
                </div>
                <?php endif;?>
            </div>

            <div class="ynmultilisting-btn-social">
                  <!-- Add-This Button BEGIN -->
                    <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?>
                 <!-- Add-This Button END -->  
            </div>
    </div>



<!-- STYLE 4 -->
<?php elseif ($this->listing->theme == 'theme4') : ?>
      
    <!-- Revolution Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/revolution/revolution-settings.css' rel='stylesheet' type='text/css'>
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/revolution/revolution-style.css' rel='stylesheet' type='text/css'>
     
    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js"></script>
     
    <!-- Revolution Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery.themepunch.plugins.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery.themepunch.revolution.min.js"></script>
    
    <div class="ynmultilisting_style_4">
        <div class="listing_category">
            <span class="fa fa-folder-open"></span>
            <?php $i = 0;  $category = $this->listing->getCategory();  ?>
            <?php if($category) :?>
                <?php if($category -> level > 1) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != $category -> getRootCategory() -> category_id) :?>
                            <?php if($i != 0) :?>
                                &raquo; 
                            <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if($category -> parent_id != 0 && $category -> parent_id  != $category -> getRootCategory() -> category_id) :?>
                                &raquo; 
                    <?php endif;?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php else :?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php endif;?>
            <?php endif;?>
        </div>


        <div class="listing_title"><?php echo $this->listing -> title; ?></div>
             
        
        <div class="listing-info">
            <div class="listing_currency">
                <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
            </div>

             <?php if ($this->listing->isAllowed('comment')) : ?>
            <div class="listing-heart btn-fa">
                <?php if ($this->listing->likes()->isLike($this->viewer())) : ?>
                <a title="<?php echo $this->translate("Unlike this listing")?>" id="ynmultilisting_unlike" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_unlike">
                    <span class="fa fa-heart active"></span>
                </a>
                <?php else : ?>
                <a title="<?php echo $this->translate("Like this listing") ?>" id="ynmultilisting_like" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_like"> 
                    <span class="fa fa-heart"></span>
                </a>      
                <?php endif;?>
            </div>
            <?php endif; ?>

            <div class="listing-box-3">
                 <div class="listing_expired">
                        <?php
                            $expirationDateObj = null;
                            if (!is_null($this -> listing -> expiration_date) && !empty($this -> listing -> expiration_date) && $this -> listing -> expiration_date) 
                            {
                                $expirationDateObj = new Zend_Date(strtotime($this -> listing->expiration_date));
                            }
                            if( $this->viewer() && $this->viewer()->getIdentity() ) {
                                $tz = $this->viewer()->timezone;
                                if (!is_null($expirationDateObj))
                                {
                                    $expirationDateObj->setTimezone($tz);
                                }
                            }
                        ?>
    
                    <span class="listing_expired_title"><?php echo $this -> translate('Expired');?>:</span>  <span class="listing_expired_date"><?php echo (!is_null($expirationDateObj)) ? date('M d Y', $expirationDateObj -> getTimestamp()) : ''; ?></span>
                    
                    <br>

                    <?php echo $this -> translate('Liked')?>: <b class="ynmultilisting_point_like"><?php echo $this -> listing -> like_count;?></b>
                    
                </div> 

                <div class="listing_review clearfix">
                    <div class="listing_rating">
                        <?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $this->listing)); ?>

                        <span class="review">
                            <b class="ynmultilisting_point_rating">&nbsp;<?php echo $this -> listing -> rating;?>&nbsp;</b>
                            <?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $this->listing->review_count), $this->listing->review_count);?>
                        </span>
                    </div>           

                </div>

                
                <?php if ($this->can_review || $this->has_review): ?>
                <div class="listing-add-review">
                    <?php if ($this->can_review){
                        echo $this->htmlLink(
                            array(
                                'route' => 'ynmultilisting_review',
                                'action' => 'create',
                                'id' => $this->listing->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            '<span class="fa fa-pencil-square-o"></span>'.$this->translate(' Add your Review'),
                            array(
                                'class' => 'listing-add-review smoothbox'
                            )
                        );
                    } else if ($this->has_review) {
                        echo $this->htmlLink(
                            array(
                                'route' => 'ynmultilisting_review',
                                'action' => 'edit',
                                'id' => $this->my_review->getIdentity(),
                                'tab' => $this->identity,
                                'page' => $this->page
                            ),
                            '<span class="fa fa-pencil-square-o"></span> '.$this->translate('You have reviewed.'),
                            array(
                                'class' => 'reviewed smoothbox'
                            )
                        );
                    }?>
                </div>
                <?php endif; ?>

            </div>



            <div class="listing_contact">
                <div>
                    <?php echo $this->htmlLink(
                        array(
                            'route' => 'ynmultilisting_specific',
                            'action' => 'email-to-friends',
                            'listing_id' => $this->listing->getIdentity()
                        ),
                        $this->translate('Email to Friends').'<span class="fa fa-envelope"></span>',
                        array(
                            'class' => 'smoothbox'
                        )
                    )?>
                </div>
                
                <div>
                   <?php 
	                echo $this->htmlLink(
		                array(
		                    'route' => 'ynmultilisting_specific',
		                    'action' => 'print',
		                    'listing_id' => $this->listing->getIdentity()
		                ),
		                '<span class="fa fa-print"></span>'.$this->translate('Print'),
		                array('target' => '_blank'))
	                ?>
                </div>



                <?php if ($this->listing->isAllowed('edit') || $this->listing->isAllowed('share') || $this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                <div class="ynmultilisting_view_more">
                <a href="javascript:void(0)">
                    <?php echo $this->translate('More ')?><span class="fa fa-caret-down"></span>
                </a>
                    <div class="ynmultilisting_view_more_popup">
                        <?php if ($this->listing->isAllowed('edit')) : ?>
                        <div id="edit">
                             <?php $url = $this -> url(array(
                                'action' => 'edit',
                                'listing_id' => $this->listing->getIdentity(),
                                ),'ynmultilisting_specific', true)
                            ;?>
                            <a href="<?php echo $url?>"><?php echo $this->translate('Edit')?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ($this->listing->isAllowed('share')) : ?>
                        <div id="share">
                            <?php $url = $this -> url(array(
                                'module' => 'activity',
                                'controller' => 'index',
                                'action' => 'share',
                                'type' => 'ynmultilisting_listing',
                                'id' => $this->listing->getIdentity(),
                                'format' => 'smoothbox'),'default', true)
                            ;?>
                            <a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Share')?></a>
                        </div>
                        <?php endif; ?>
                        <div id="report">
                            <?php
                            $url = $this->url(array(
                                'module' => 'core',
                                'controller' => 'report',
                                'action' => 'create',
                                'subject' => $this->listing->getGuid(),
                                'format' => 'smoothbox'),'default', true);
                            ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Report') ?></a>
                        </div>
                        <?php if ($this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                        <div id="transfer_owner">
                            <?php
                            $url = $this->url(array(
                                'action' => 'transfer-owner',
                                'listing_id' => $this->listing->getIdentity(),
                                ),'ynmultilisting_specific', true);
                            ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Transfer Owner') ?></a>
                        </div>
                        <?php endif; ?>
                        <!-- HOANGND add to compare-->
	                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
	                    	<div id="compare">
	                        <?php if ($this->listing->inCompare()) : ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Remove from Compare')?>
	                            </a>
	                        <?php else: ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Add to Compare')?>
	                            </a>
	                        <?php endif; ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to compare-->
	
	                    <!-- HOANGND add to wishlist-->
	                    <?php if($this->viewer()->getIdentity()) :?>
	                    	<div id="wishlist">
	                        <?php echo $this->htmlLink(
	                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $this->listing->getIdentity()),
	                        $this->translate('Add to Wish List'),
	                        array('class' => 'smoothbox')) ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to wishlist-->
                    </div>
                </div>
                <?php endif;?>
            </div>

            <div style="margin-top:15px;">
                  <!-- Add-This Button BEGIN -->
                    <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?>
                 <!-- Add-This Button END -->  
            </div>
        </div>


        <div class="listing-box-left">
            <div class="box-listing-slider">   
                <div class="ynmultilisting-tab-content">
                <div id="ynmultilisting-photo-details">
                    <div class="listing-slider">

                        <?php if(count($this->photos) > 0):?> 
                        <div id="slider-photo-style4" class="tp-banner">
                            <ul>
                                <!-- get main photo -->
                                 <?php foreach($this->photos as $photo):?>
                                    <?php if($this->listing->photo_id == $photo->file_id):?>
                                        <li data-transition="fade" data-slotamount="5" data-masterspeed="700" data-thumb="<?php echo $photo->getPhotoUrl(); ?>" data-title="<?php echo $photo->image_title; ?>">
                                        <img src="<?php echo $photo->getPhotoUrl(); ?>"   alt="slidebg1"  data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat">
                                        </li>  
                                    <?php break; endif; ?>
                                <?php endforeach;?>
                                
                                <!-- get other photos -->
                                <?php foreach($this->photos as $photo):?>
                                    <?php if($this->listing->photo_id != $photo->file_id):?>
                                        <li data-transition="fade" data-slotamount="5" data-masterspeed="700" data-thumb="<?php echo $photo->getPhotoUrl(); ?>" data-title="<?php echo $photo->image_title; ?>">
                                            <img src="<?php echo $photo->getPhotoUrl(); ?>"   alt="slidebg1"  data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat">
                                        </li>       
                                    <?php endif;?>
                                <?php endforeach;?>                 
                            </ul>
                        </div>

                        <?php else:?><!-- no photo -->  
                        <div id="slider-photo-style4" class="tp-banner"> 
                            <ul>                    
                                <li data-transition="fade" data-slotamount="5" data-masterspeed="700" data-thumb="application/modules/Ynmultilisting/externals/images/blank.gif">       
                                    <img src="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png" alt="<?php echo $this->translate('No Photo')?>" data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat"/> 
                                 </li>                    
                            </ul>     
                        </div>
                        <?php endif;?>
                    </div>
                </div>
                
                <?php if(count($this->videos) > 0):?>
                <div id="ynmultilisting-video-details" style="display: none;">
                    <div class="listing-slider">
                        <div id="slider-video-style4" class="tp-banner" >
                            <ul>
                            	<?php foreach($this->videos as $video):?>
		                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id == $video->getIdentity() && $this->listing->video_type = $video_type):?>
		                            <?php 
		                                $embedded = "";
		                                if ($video->type == 1) {
		                                    $embedded = "//www.youtube.com/embed/".$video->code;
		                                } elseif ($video->type == 2) {
		                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
		                                } elseif ($video->type == 4) {
		                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
		                                } else {
		                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
		                                          . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
		                                            'module' => $this->module_video,
		                                            'controller' => 'video',
		                                            'action' => 'external',
		                                            'video_id' => $video->getIdentity(),
		                                          ), 'default', true) . '?format=frame';
		                                }
	                                    $video_src = $video -> getPhotoUrl('thumb.large'); 
	                                    if(empty($video_src))
	                                    {
	                                        $video_src = $video -> getPhotoUrl('thumb.normal'); 
	                                    }
		                            ?>
		                            
		                            <?php if(in_array($video -> type, array('1','2','4'))) :?>
		                            	<!-- SLIDE  -->
		                                <li data-transition="fade" data-slotamount="5" data-masterspeed="700" >
		                                    <div class="tp-caption fullscreenvideo"
		                                        data-autoplay="false"
		                                        data-autoplayonlyfirsttime="false"
		                                        data-nextslideatend="true"
		                                        data-thumbimage="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?> style="z-index: 8">
		                                        <iframe src='<?php echo $embedded; ?>' width='800' height='360' style='width:100%;height:100%;'></iframe>
		                                    </div>
		                                </li>
		                            <?php else :?>
		                            	 <!-- SLIDE  -->
		                                <li data-transition="fade" data-slotamount="5" data-masterspeed="700" >
		                                    <div class="tp-caption fullscreenvideo"
		                                        data-autoplay="false"
		                                        data-autoplayonlyfirsttime="false"
		                                        data-nextslideatend="false" 
		                                        style="z-index: 8">
		                                        <video class="" preload="none"  loop width="100%" height="100%"
		                                          poster="<?php echo $video_src; ?>">
		                                        <source src="<?php echo $embedded;?>" type='video/mp4' />
		                                      </video>
		                                    </div>
		                                </li>
		                            <?php endif;?>
		                            
		                        <?php break; endif; ?>
		                    <?php endforeach;?>
                            
                            <?php foreach($this->videos as $video):?>
	                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id != $video->getIdentity() && $this->listing->video_type = $video_type):?>
	                            <?php 
	                                $embedded = "";
	                                if ($video->type == 1) {
	                                    $embedded = "//www.youtube.com/embed/".$video->code;
	                                } elseif ($video->type == 2) {
	                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
	                                } elseif ($video->type == 4) {
	                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
	                                } else {
	                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
	                                          . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
	                                            'module' => $this->module_video,
	                                            'controller' => 'video',
	                                            'action' => 'external',
	                                            'video_id' => $video->getIdentity(),
	                                          ), 'default', true) . '?format=frame';
	                                }
									$video_src = $video -> getPhotoUrl('thumb.large'); 
                                    if(empty($video_src))
                                    {
                                        $video_src = $video -> getPhotoUrl('thumb.normal'); 
                                    }
	                            ?>
	                            <?php if(in_array($video -> type, array('1','2','4'))) :?>
		                            	<!-- SLIDE  -->
		                                <li data-transition="fade" data-slotamount="5" data-masterspeed="700" >
		                                    <div class="tp-caption fullscreenvideo"
		                                        data-autoplay="false"
		                                        data-autoplayonlyfirsttime="false"
		                                        data-nextslideatend="true"
		                                        data-thumbimage="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?> style="z-index: 8">
		                                        <iframe src='<?php echo $embedded; ?>' width='800' height='360' style='width:100%;height:100%;'></iframe>
		                                    </div>
		                                </li>
	                           <?php else :?>
		                            	 <!-- SLIDE  -->
		                                <li data-transition="fade" data-slotamount="5" data-masterspeed="700" >
		                                    <div class="tp-caption fullscreenvideo"
		                                        data-autoplay="false"
		                                        data-autoplayonlyfirsttime="false"
		                                        data-nextslideatend="false" 
		                                        style="z-index: 8">
		                                        <video class="" preload="none"  loop width="100%" height="100%"
		                                          poster="<?php echo $video_src; ?>">
		                                        <source src="<?php echo $embedded;?>" type='video/mp4' />
		                                      </video>
		                                    </div>
		                                </li>
	                            <?php endif;?>
	                        <?php endif;?>
	                    <?php endforeach;?>
                            </ul>
                        </div>
                    </div>
                </div>
				<?php endif;?>

                </div> 

                <?php if(count($this->videos) > 0):?>
                <div class="ynmultilisting-tab">
                    <div class="ynmultilisting-tab-item active" data-id="ynmultilisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                    <div class="ynmultilisting-tab-item" data-id="ynmultilisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
                </div>
                <?php endif; ?>
            </div>



            <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div>   
        </div> 

        <script type="text/javascript">
            jQuery.noConflict();
            jQuery(document).ready(function() {
                 jQuery('#slider-photo-style4').revolution(
                    {
                        navigationStyle:"preview1",
                    });
            }); //ready 

            $$('.ynmultilisting-tab .ynmultilisting-tab-item').addEvent('click', function(){
                $$('.ynmultilisting-tab-content > div').hide();
                $$('.ynmultilisting-tab-content').getElement( "#"+this.get('data-id') ).show();

                $$('.ynmultilisting-tab .ynmultilisting-tab-item').removeClass('active');
                this.addClass('active');
                if(this.get('data-id') == 'ynmultilisting-photo-details'){
                    jQuery.noConflict();
                    jQuery(document).ready(function() {
                        jQuery('#slider-video-style4').revpause();
                         jQuery('#slider-photo-style4').revolution(
                            {
                                navigationStyle:"preview1",
                            });
                    }); //ready            
                    
                }else{
                    jQuery.noConflict();
                    jQuery(document).ready(function() {
                        jQuery('#slider-photo-style4').revpause();
                         jQuery('#slider-video-style4').revolution(
                            {
                                navigationStyle:"preview1",
                            });
                    }); //ready
                    
                }
                
            });

        </script>    



    </div>


<!-- STYLE 5 -->
<?php elseif ($this->listing->theme == 'theme5') : ?>
    <!-- Base MasterSlider style sheet -->
    <link rel="stylesheet" href="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider.css" />

    <!-- Master Slider Skin -->
    <link href="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/masterslider-style.css" rel='stylesheet' type='text/css'>

    <!-- MasterSlider Template Style -->
    <link href='<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/styles/masterslider/ms-showcase2.css' rel='stylesheet' type='text/css'>

    <!-- jQuery -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js"></script>
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/jquery.easing.min.js"></script>

    <!-- Master Slider -->
    <script src="<?php echo $this->baseUrl()?>/application/modules/Ynmultilisting/externals/scripts/masterslider.min.js"></script>
    
    <div class="ynmultilisting_style_5">

        <div class="listing_category">
            <span class="fa fa-folder-open"></span>
            <?php $i = 0;  $category = $this->listing->getCategory();  ?>
            <?php if($category) :?>
                <?php if($category -> level > 1) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != $category -> getRootCategory() -> category_id) :?>
                            <?php if($i != 0) :?>
                                &raquo; 
                            <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if($category -> parent_id != 0 && $category -> parent_id  != $category -> getRootCategory() -> category_id) :?>
                                &raquo; 
                    <?php endif;?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php else :?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php endif;?>
            <?php endif;?>
        </div>


        <div class="listing_title"><?php echo $this->listing -> title; ?></div>


        <div class="listing-box-infomation">
            <div class="listing-box-infomation-left">
                <div class="listing_currency">
                    <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
                </div>
                
                <div class="listing_review_heart">
                    <div class="listing_review clearfix">
                        <div class="listing_rating">
                            <?php echo $this->partial('_listing_rating_big.tpl', 'ynmultilisting', array('listing' => $this->listing)); ?>

                            <b class="ynmultilisting_point_rating">&nbsp;<?php echo number_format($this->listing -> rating, 1, '.', ''); ?>&nbsp;</b>

                            <span class="review">
                                <?php echo $this -> translate(array("(%s review)", "(%s reviews)" , $this->listing->review_count), $this->listing->review_count);?>
                            </span>
                        </div> 
                        
                        <?php if($this->can_review || $this->has_review) :?>
                        <div class="listing-add-review">
                            <?php if ($this->can_review){
                                echo $this->htmlLink(
                                    array(
                                        'route' => 'ynmultilisting_review',
                                        'action' => 'create',
                                        'id' => $this->listing->getIdentity(),
                                        'tab' => $this->identity,
                                        'page' => $this->page
                                    ),
                                    '<span class="fa fa-pencil-square-o"></span>'.$this->translate(' Add your Review'),
                                    array(
                                        'class' => 'listing-add-review smoothbox'
                                    )
                                );
                            } else if ($this->has_review) {
                                echo $this->htmlLink(
		                            array(
		                                'route' => 'ynmultilisting_review',
		                                'action' => 'edit',
		                                'id' => $this->my_review->getIdentity(),
		                                'tab' => $this->identity,
		                                'page' => $this->page
		                            ),
		                            '<span class="fa fa-pencil-square-o"></span> '.$this->translate('You have reviewed.'),
		                            array(
		                                'class' => 'reviewed smoothbox'
		                            )
		                        );
                            }?>
                        </div>  
                        <?php endif; ?>        
                    </div>

                    <?php if ($this->listing->isAllowed('comment')) : ?>
                    <div class="listing-heart btn-fa">
                        <?php if ($this->listing->likes()->isLike($this->viewer())) : ?>
                        <a title="<?php echo $this->translate("Unlike this listing")?>" id="ynmultilisting_unlike" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_unlike">
                            <span class="fa fa-heart active"></span>
                        </a>
                        <?php else : ?>
                        <a title="<?php echo $this->translate("Like this listing") ?>" id="ynmultilisting_like" href="javascript:void(0);" onClick="like_listing(this);" class="ynmultilisting_like"> 
                            <span class="fa fa-heart"></span>
                        </a>      
                        <?php endif;?>
                    </div>
                    <?php endif; ?>
                </div>


                <div class="listing_expired">
                    <?php
                        $expirationDateObj = null;
                        if (!is_null($this -> listing -> expiration_date) && !empty($this -> listing -> expiration_date) && $this -> listing -> expiration_date) 
                        {
                            $expirationDateObj = new Zend_Date(strtotime($this -> listing->expiration_date));
                        }
                        if( $this->viewer() && $this->viewer()->getIdentity() ) {
                            $tz = $this->viewer()->timezone;
                            if (!is_null($expirationDateObj))
                            {
                                $expirationDateObj->setTimezone($tz);
                            }
                        }
                    ?>

                <span class="listing_expired_title"><?php echo $this -> translate('Expired');?>:</span>  <span class="listing_expired_date"><?php echo (!is_null($expirationDateObj)) ? date('M d Y', $expirationDateObj -> getTimestamp()) : ''; ?></span>

                 <?php echo "&nbsp;&nbsp;-&nbsp;&nbsp;".$this -> translate('Liked')?>: <b class="ynmultilisting_point_like"><?php echo $this -> listing -> like_count;?></b>
                
            	</div>

                <div class="listing_description rich_content_body"><?php echo $this->listing->short_description?></div> 
            </div>
            

            <div class="listing_contact">
                <div>
                    <?php echo $this->htmlLink(
                        array(
                            'route' => 'ynmultilisting_specific',
                            'action' => 'email-to-friends',
                            'listing_id' => $this->listing->getIdentity()
                        ),
                        '<span class="fa fa-envelope"></span>'.$this->translate('Email to Friends'),
                        array(
                            'class' => 'smoothbox'
                        )
                    )?>
                </div>
                
                <div>
                    <?php 
	                echo $this->htmlLink(
		                array(
		                    'route' => 'ynmultilisting_specific',
		                    'action' => 'print',
		                    'listing_id' => $this->listing->getIdentity()
		                ),
		                '<span class="fa fa-print"></span>'.$this->translate('Print'),
		                array('target' => '_blank'))
	                ?>
                </div>



                <?php if ($this->listing->isAllowed('edit') || $this->listing->isAllowed('share') || $this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                <div class="ynmultilisting_view_more">
                <a href="javascript:void(0)">
                    <span class="fa fa-caret-down"></span><?php echo $this->translate('More')?>
                </a>
                    <div class="ynmultilisting_view_more_popup">
                        <?php if ($this->listing->isAllowed('edit')) : ?>
                        <div id="edit">
                            <?php $url = $this -> url(array(
                                'action' => 'edit',
                                'listing_id' => $this->listing->getIdentity(),
                                ),'ynmultilisting_specific', true)
                            ;?>
                            <a href="<?php echo $url?>"><?php echo $this->translate('Edit')?></a>
                        </div>
                        <?php endif; ?>
                        <?php if ($this->listing->isAllowed('share')) : ?>
                        <div id="share">
                            <?php $url = $this -> url(array(
                                'module' => 'activity',
                                'controller' => 'index',
                                'action' => 'share',
                                'type' => 'ynmultilisting_listing',
                                'id' => $this->listing->getIdentity(),
                                'format' => 'smoothbox'),'default', true)
                            ;?>
                            <a href="javascript:void(0);" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Share')?></a>
                        </div>
                        <?php endif; ?>
                        <div id="report">
                            <?php
                            $url = $this->url(array(
                                'module' => 'core',
                                'controller' => 'report',
                                'action' => 'create',
                                'subject' => $this->listing->getGuid(),
                                'format' => 'smoothbox'),'default', true);
                            ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Report') ?></a>
                        </div>
                        <?php if ($this->viewer()->isAdmin() || $this->listing->isOwner($this->viewer())) : ?>
                        <div id="transfer_owner">
                            <?php
                            $url = $this->url(array(
                                'action' => 'transfer-owner',
                                'listing_id' => $this->listing->getIdentity(),
                                ),'ynmultilisting_specific', true);
                            ?>
                            <a href="javascript:void(0)" onclick="checkOpenPopup('<?php echo $url?>')"><?php echo $this->translate('Transfer Owner') ?></a>
                        </div>
                        <?php endif; ?>
                        <!-- HOANGND add to compare-->
	                    <?php if(!Engine_Api::_()->ynmultilisting()->isMobile()) :?>
	                    	<div id="compare">
	                        <?php if ($this->listing->inCompare()) : ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="removeFromCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Remove from Compare')?>
	                            </a>
	                        <?php else: ?>
	                            <a class="no-icon listing-add-to-compare_<?php echo $this->listing->getIdentity()?>" href="javascript:void(0)" rel="<?php echo $this->url(array('action' => 'add-to-compare', 'listing_id' => $this->listing -> getIdentity()), 'ynmultilisting_specific', true)?>" onclick="addToCompare(this, <?php echo $this->listing -> getIdentity();?>)">
	                                <?php echo $this->translate('Add to Compare')?>
	                            </a>
	                        <?php endif; ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to compare-->
	
	                    <!-- HOANGND add to wishlist-->
	                    <?php if($this->viewer()->getIdentity()) :?>
	                    	<div id="wishlist">
	                        <?php echo $this->htmlLink(
	                        array('route' => 'ynmultilisting_wishlist', 'action' => 'add', 'listing_id' => $this->listing->getIdentity()),
	                        $this->translate('Add to Wish List'),
	                        array('class' => 'smoothbox')) ?>
	                        </div>
	                    <?php endif;?>
	                    <!-- add to wishlist-->
                    </div>
                </div>
                <?php endif;?>
            </div>
            
            <div style="margin-top:15px;">
                  <!-- Add-This Button BEGIN -->
                    <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?>
                 <!-- Add-This Button END -->  
            </div>

        </div>


        <div class="listing-box-slider">
            <div class="ynmultilisting-tab-content">
                <!-- template -->
                <div class="ynmultilisting-photo-details ms-showcase2-template ms-showcase2-vertical">
                    <!-- masterslider -->
                    <div class="master-slider ms-skin-default" id="masterslider">
                        <?php if(count($this->photos) > 0):?> 
							<?php foreach($this->photos as $photo):?>
		                        <?php if($this->listing->photo_id == $photo->file_id):?>
		                             <div class="ms-slide">
		                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
		                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl(); ?>" alt="thumb" />
		                            </div>
		                        <?php break; endif;?>
	                        <?php endforeach;?>
	                        <?php foreach($this->photos as $photo):?>
		                        <?php if($this->listing->photo_id != $photo->file_id):?>
		                             <div class="ms-slide">
		                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
		                                <img class="ms-thumb" src="<?php echo $photo->getPhotoUrl(); ?>" alt="thumb" />
		                            </div>
		                        <?php endif;?>
	                        <?php endforeach;?>
                        <?php else:?>
                        	<div class="ms-slide">
	                            <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_main.png" alt="<?php echo $this->translate('No Photo')?>"/> 
	                            <img class="ms-thumb" src="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_profile.png" alt="thumb" />
                       		</div>
                        <?php endif;?>
                    </div>
                    <!-- end of masterslider -->
                </div>
                <!-- end of template -->   

                <script type="text/javascript">      
                    jQuery.noConflict();
                    var slider = new MasterSlider();

                 
                    slider.control('arrows');  
                    slider.control('scrollbar' , {dir:'h'});   
                    slider.control('thumblist' , {
                        autohide:false, 
                        dir:'v', 
                        arrows:false, 
                        align:'left', 
                        width:115, 
                        height:120, 
                        margin:15, 
                        space:13, 

                        hideUnder:300
                    });
                     
                    slider.setup('masterslider' , {
                        width:370,
                        height:424,
                        loop: true,
                        autoplay: true,
                        space:15
                    });
                </script>
                <!-- end of template --> 

                <div class="ynmultilisting-video-details ms-showcase2-template ms-showcase2-vertical" style="display: none;">
                    <!-- masterslider -->
                    <div class="master-slider ms-skin-default" id="masterslider2">
                        <?php foreach($this->videos as $video):?>
                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id == $video->getIdentity() && $this->listing->video_type = $video_type):?>
	                            <?php 
	                                $embedded = "";
	                                if ($video->type == 1) {
	                                    $embedded = "//www.youtube.com/embed/".$video->code;
	                                } elseif ($video->type == 2) {
	                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
	                                } elseif ($video->type == 4) {
	                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
	                                } else {
	                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
										      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
										        'module' => $this->module_video,
										        'controller' => 'video',
										        'action' => 'external',
										        'video_id' => $video->getIdentity(),
										      ), 'default', true) . '?format=frame';
	                                }
	                            ?>
	                            <div class="ms-slide">
	                            	<?php 
	                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
										if(empty($video_src))
										{
											$video_src = $video -> getPhotoUrl('thumb.normal'); 
										}
	                            	?>
	                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
	                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
	                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
	                            </div> 
	                        <?php break; endif; ?>
                    	<?php endforeach;?> 
                    	<?php foreach($this->videos as $video):?>
                        <?php $guid = $video->getGUID();
                        $guid_video = Engine_Api::_()->getItemByGuid($guid);
                        $video_type = $guid_video->getType(); ?>
                        <?php if($this->listing->video_id != $video->getIdentity() && $this->listing->video_type = $video_type):?>
	                            <?php 
	                                $embedded = "";
	                                if ($video->type == 1) {
	                                    $embedded = "//www.youtube.com/embed/".$video->code;
	                                } elseif ($video->type == 2) {
	                                    $embedded = "//player.vimeo.com/video/".$video->code."?portrait=0";
	                                } elseif ($video->type == 4) {
	                                    $embedded = "//www.dailymotion.com/embed/video/".$video->code;
	                                } else {
	                                    $embedded = 'http://' . $_SERVER['HTTP_HOST']
										      . Zend_Controller_Front::getInstance()->getRouter()->assemble(array(
										        'module' => $this->module_video,
										        'controller' => 'video',
										        'action' => 'external',
										        'video_id' => $video->getIdentity(),
										      ), 'default', true) . '?format=frame';
	                                }
	                            ?>
	                            <div class="ms-slide">
	                            	<?php 
	                            		$video_src = $video -> getPhotoUrl('thumb.large'); 
										if(empty($video_src))
										{
											$video_src = $video -> getPhotoUrl('thumb.normal'); 
										}
	                            	?>
	                                <img src="application/modules/Ynmultilisting/externals/images/blank.gif" data-src="<?php echo $video_src; ?>" alt="<?php echo $video->title; ?>"/>
	                                <img class="ms-thumb" src="<?php echo $video->getPhotoUrl('thumb.normal'); ?>" alt="thumb" />
	                                <a data-type="video" href="<?php echo $embedded; ?>"></a>
	                            </div> 
	                        <?php endif;?>
                    	<?php endforeach;?>
                    </div>
                    <!-- end of masterslider -->
                </div>

            </div><!--tab content -->

            <?php if(count($this->videos) > 0):?>
            <div class="ynmultilisting-tab">
                <div class="ynmultilisting-tab-item active" data-id="ynmultilisting-photo-details"><span class="fa fa-picture-o"></span> Photo</div>
                <div class="ynmultilisting-tab-item" data-id="ynmultilisting-video-details"><span class="fa fa-video-camera"></span> Video</div>
            </div>
            <?php endif; ?> 
            
            <script type="text/javascript">
                var active_slider2 = 0;

                $$('.ynmultilisting-tab .ynmultilisting-tab-item').addEvent('click', function(){
                    $$('.ynmultilisting-tab-content > div').hide();
                    $$('.ynmultilisting-tab-content').getElement( "."+this.get('data-id') ).show();

                    $$('.ynmultilisting-tab .ynmultilisting-tab-item').removeClass('active');
                    this.addClass('active');

                    if (active_slider2 == 0) {
                        (function( $ ) {
                            $(function() {
                                // More code using $ as alias to jQuery
                                var slider2 = new MasterSlider();

                                slider2.control('arrows');  
                                slider2.control('scrollbar' , {dir:'h'});   
                                slider2.control('thumblist' , {
                                    autohide:false, 
                                    dir:'v', 
                                    arrows:false, 
                                    align:'left', 
                                    width:115, 
                                    height:120, 
                                    margin:15, 
                                    space:13, 
                                    hideUnder:300
                                });
                                 
                                slider2.setup('masterslider2' , {
                                    width:370,
                                    height:424,
                                    space:15,
                                    loop: true,
                                    autoplay: true
                                });
                            });
                        })(jQuery);                    

                        active_slider2 = 1;
                    }
                });
            </script>


        </div>

    </div><!--ynmultilisting_style_5 -->
<?php endif; ?>




</div>



<script type="text/javascript">   
    $$('.ynmultilisting_view_more').addEvent('click',function(){

        if($$('.ynmultilisting_view_more_popup').getStyle('display') == 'none'){
            $$('.ynmultilisting_view_more_popup').setStyle('display','block');
        }else{
            $$('.ynmultilisting_view_more_popup').setStyle('display','none');
        }

        
    });

    // hot fix layout style theme
    $$('.layout_page_ynmultilisting_profile_index  .layout_main').addClass('ynmultilisting_layout_<?php echo $this->listing->theme; ?> <?php if ( defined("YNRESPONSIVE_ACTIVE")) echo YNRESPONSIVE_ACTIVE; ?>');
</script>
