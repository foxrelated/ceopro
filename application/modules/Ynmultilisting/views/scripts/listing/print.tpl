<script type="text/javascript">
	window.addEvent('domready',function () {
		$$('.ynlisting_about_button').setStyle('display', 'none');
        $$('.ynmultilisting_get_location').setStyle('display', 'none');
        $$('span.fa-map-marker').setStyle('display', 'none');
        setTimeout(function(){
		    window.print();
		    setTimeout(function(){this.close();}, 1);
		}, 5000);
	});
</script>

</script>

<div class="ynmultilisting_detail_layout ynmultilisting_detail_layout_theme1 clearfix">
    
<?php
      $this->addHelperPath(APPLICATION_PATH . '/application/modules/Fields/View/Helper', 'Fields_View_Helper');
?>   
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

    <div class="ynmultilisting_style_1">

        <div class="listing_category">
            <span class="fa fa-folder-open"></span>
            <?php $i = 0;  $category = $this->listing->getCategory();  ?>
            <?php if($category) :?>
                <?php if($category -> level > 1) :?>
                    <?php foreach($category->getBreadCrumNode() as $node): ?>
                        <?php if($node -> category_id != $category -> getRootCategory() -> category_id) :?>
                            <?php if($i != 0) :?>
                                &nbsp;<i class="fa fa-angle-right"></i>&nbsp; 
                            <?php endif;?>
                            <?php $i++; echo $this->htmlLink($node->getHref(), $this->translate($node->shortTitle()), array()) ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if($category -> parent_id != 0 && $category -> parent_id  != $category -> getRootCategory() -> category_id) :?>
                                &nbsp;<i class="fa fa-angle-right"></i> &nbsp;
                    <?php endif;?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php else :?>
                    <?php echo $this->htmlLink($category->getHref(), $category->getTitle()); ?>
                <?php endif;?>
            <?php endif;?>
        </div>

        <?php if(count($this->photos) > 0):?>       
            <!-- template -->
                 <?php foreach($this->photos as $photo):?>
                    <?php if($this->listing->photo_id == $photo->file_id):?>
                        <div class="ynmultilisting-print-image">
                            <img src="<?php echo $photo->getPhotoUrl(); ?>" alt="<?php echo $photo->image_title; ?>"/> 
                        </div>
                    <?php break; endif;?>
                <?php endforeach;?> 
            <!-- end of template -->

            <?php else:?>
                <div class="ynmultilisting-print-image">
                    <img src="application/modules/Ynmultilisting/externals/images/nophoto_listing_thumb_main.png" alt="<?php echo $this->translate('No Photo')?>"/> 
                </div>  
            <?php endif;?>
            
        <div class="ynmultilisting-detail-content">
            <div class="listing_title"><?php echo $this->listing -> title; ?></div>

            <div class="listing_currency">
                <?php echo $this -> locale()->toCurrency($this->listing->price, $this->listing->currency); ?>
            </div>

            <div style="border-bottom: none" class="listing_description rich_content_body"><?php echo strip_tags($this->listing->short_description)?></div>
		    
        </div>
    </div>   
</div>
<script type="text/javascript">

    $$('.ynmultilisting_view_more').addEvent('click',function(){

        if($$('.ynmultilisting_view_more_popup').getStyle('display') == 'none'){
            $$('.ynmultilisting_view_more_popup').setStyle('display','block');
        }else{
            $$('.ynmultilisting_view_more_popup').setStyle('display','none');
        }
        
    });

    //hot fix case window.print style (overwrite screen to all)
    document.getElement('head').getElements('link[rel=stylesheet]').set('media', 'all');   
    // hot fix layout style theme
    $$('.layout_page_ynmultilisting_listing_print .layout_main').addClass('ynmultilisting_layout_theme1')
</script>
<br/>
<h3><?php echo $this -> translate('Information');?></h3>
<br/>
<?php echo $this->content()->renderWidget('ynmultilisting.listing-profile-info'); ?>
<br/>
<h3><?php echo $this -> translate('About Us');?></h3>
<br/>
<?php echo $this->content()->renderWidget('ynmultilisting.listing-profile-about'); ?>
<br/>
<h3><?php echo $this -> translate('Location');?></h3>
<br/>
<?php echo $this->content()->renderWidget('ynmultilisting.listing-profile-location'); ?>

