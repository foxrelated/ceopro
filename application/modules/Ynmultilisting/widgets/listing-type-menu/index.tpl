<?php $settings = Engine_Api::_()->getApi('settings', 'core'); ?>
<?php 
	$max_listingtype_ori = $max_listingtype = $settings->getSetting('ynmultilisting_max_listingtype', 8);
	$showmore = ($max_listingtype > 0) ? $settings->getSetting('ynmultilisting_menu_showmore', 1) : 0;
	if ($showmore) $max_listingtype = $max_listingtype - 1;
?>

<?php if($settings->getSetting('ynmultilisting_use_custom_background_color', '0')): ?>
    <style>
    #listingtype-items .listingtype-title {
        background-color: <?php echo $settings->getSetting('ynmultilisting_menu_backgroundcolor', '#54a9d8'); ?>
    }
    </style>
<?php endif; ?>
<style>
    #listingtype-show-more>li.listtype-item div.listingtype-title a{
        border-left: 1px solid <?php echo $settings->getSetting('ynmultilisting_menu_backgroundbar', '#54a9d8'); ?>;
        border-right: 1px solid <?php echo $settings->getSetting('ynmultilisting_menu_backgroundbar', '#54a9d8'); ?>;
    }
    #listingtype-show-more>li:last-child.listtype-item div.listingtype-title a{
        border-bottom: 1px solid <?php echo $settings->getSetting('ynmultilisting_menu_backgroundbar', '#54a9d8'); ?>;
    }
    #listingtype-items .listingtype-title:hover {
        background-color: <?php echo $settings->getSetting('ynmultilisting_menu_hovercolor', '#FFFFFF'); ?>;
        border-color: <?php echo $settings->getSetting('ynmultilisting_menu_hovercolor', '#000000'); ?> !important;
    }

    #listingtype-items .listingtype-title a {
        color: <?php echo $settings->getSetting('ynmultilisting_menu_textcolor', '#FFFFFF'); ?>
    }
    #listingtype-items .active .listingtype-title{
        background-color: <?php echo $settings->getSetting('ynmultilisting_menu_hovercolor', '#fff'); ?>;
        border-color: <?php echo $settings->getSetting('ynmultilisting_menu_hovercolor', '#fff'); ?> !important;
    }
    #listingtype-items .active .listingtype-title a {
        color: <?php echo $settings->getSetting('ynmultilisting_menu_textcolor', '#000000'); ?>;

    }
    
    #listingtype-items {
    	background-color: <?php echo $settings->getSetting('ynmultilisting_menu_backgroundbar', '#54a9d8'); ?>
    }
    
    #listingtype-items div.listingtype-title:after{
    	border-right-style: <?php echo $settings->getSetting('ynmultilisting_menu_linetype', 'solid'); ?>
    }
</style>
<?php $session = new Zend_Session_Namespace('mobile'); ?>
<ul id="listingtype-items">
	<?php $count = 0;?>
	<?php $hasMove = false; ?>
    <?php foreach ($this->listingtypes as $listingtype) :?>
    <?php if ($count >= $max_listingtype && $max_listingtype_ori > 0 && $max_listingtype >= 0 && !$hasMove && !$session -> mobile) : ?>
    <?php if ($showmore && count($this->listingtypes) > $max_listingtype) :?>
    <?php $hasMove = true;?>

    <li class="listtype-item show-more">
        <div class="listingtype-title title-more">
        <a href="javascript:void(0)"><?php echo $this->translate('More').'&nbsp;<i class="fa fa-caret-down"></i>'?></a> 
        </div>

    <ul id="listingtype-show-more">
    <?php else:?>
    </ul>
    <?php break; ?>
    <?php endif;?>	
    <?php endif;?>	
    <li class="listtype-item <?php if ($listingtype->getIdentity() == $this->current_listingtype_id) echo 'active'?>" id="listingtype_<?php echo $listingtype->getIdentity()?>" rel="<?php echo $listingtype->getIdentity()?>">
        <div class="listingtype-title"><?php echo $this->htmlLink($listingtype->getHref(), $listingtype->getTitle())?></div>
        <?php $top_categories = $listingtype->getTopCategories();?>
        <?php $more_categories = $listingtype->getMoreCategories();?>
        <?php $promotion = $listingtype->getPromotion();?>
        <?php if (count($top_categories) || count($more_categories) || $promotion) :?>
        <div class="listingtype-content">
        	<?php if (count($top_categories)): ?>
            <div class="top-catgories">
                <h5>top categories</h5>
                <ul>
                    <?php foreach ($top_categories as $category) :?>
                    <li class="category-item">
                        <span class="category-photo"><?php echo $this->itemPhoto($category, 'thumb.icon')?></span>
                        <span class="category-link"><?php echo $this->htmlLink($category->getHref(array('listingtype_id' => $listingtype->getIdentity())), $category->getTitle())?></span>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
            <?php endif;?>
            
            <?php if (count($more_categories)): ?>
            <div class="more-catgories">
                <h5>more categories</h5>
                <ul>
                    <?php foreach ($more_categories as $category) :?>
                    <li class="category-item">
                        <span class="category-link"><i class="fa fa-angle-right"></i> &nbsp; <?php echo $this->htmlLink($category->getHref(array('listingtype_id' => $listingtype->getIdentity())), $category->getTitle())?></span>
                    </li>
                    <?php endforeach;?>
                </ul>
            </div>
            <?php endif;?>
            
            <?php if ($promotion) :?>
            <div class="promotion-section">
                <?php $photoUrl = ($promotion && $promotion->photo_id) ? $promotion->getPhotoUrl('thumb.main') : './application/modules/Ynmultilisting/externals/images/nophoto_promotion_thumb_main.png';?>
                <div class="promotion-background" style="background-image: url('<?php echo $photoUrl;?>');">                 
                    <div class="promotion-detail" style="color: <?php echo $promotion->text_color;?>">
                        <a class="link" href="<?php echo $promotion->link?>"><?php echo $promotion->title?></a>
                        
                        <p class="content"><?php echo $promotion->content?></p>
                    </div>    
                    <div class="promotion-backgroud-color" style="background-color: <?php echo $promotion->text_background_color;?>"></div>            
                </div>
            </div>
			<?php endif;?>
        </div>
        <?php endif;?>
    </li>
    <?php $count++;?>
    <?php endforeach;?>
</ul>

<?php if ($hasMove) :?>
</li></ul>
<?php endif;?>
<script type="text/javascript">

if(window.innerWidth < '769'){
    $$('#listingtype-items .active').addEvent('click',function(e){
        e.preventDefault();
    })

    $$('#listingtype-items').addEvent('click',function(){
        if (this.getStyle('height') == 'auto'){
            this.setStyle('height','43px');
        }else{
            this.setStyle('height','auto');
        }
    })    
}

</script>