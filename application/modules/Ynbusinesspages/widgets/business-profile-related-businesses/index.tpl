<?php
    $this->headScript()
        ->appendFile($this->baseUrl() . '/application/modules/Ynbusinesspages/externals/scripts/jquery-1.10.2.min.js')
        ->appendFile($this->baseUrl() . '/application/modules/Ynbusinesspages/externals/scripts/owl.carousel.js')
        ->appendFile($this->baseUrl() . '/application/modules/Ynbusinesspages/externals/scripts/prettify.js');
    $this->headLink()
        ->appendStylesheet($this->baseUrl() . '/application/modules/Ynbusinesspages/externals/styles/prettify.css')
        ->appendStylesheet($this->baseUrl() . '/application/modules/Ynbusinesspages/externals/styles/owl.carousel.css');
?>
<ul id="ynbusinesspages-profile-related-listings" class="ynbusinesspages-profile-related-listings owl-carousel owl-theme">
<?php foreach ($this->paginator as $business):?>
    <li class="item">
        <div class="ynbusinesspages-profile-related-item">
            <div class="ynbusinesspages-profile-related-item-header">
                <div class="ynbusinesspages-profile-related-item-title">
                    <?php echo $this->htmlLink($business->getHref(), $business->name); ?>
                </div>

                <?php $category = $business->getMainCategory();?>
                <?php if ($category):?>
                <div class="ynbusinesspages-profile-related-item-category">
                    <?php echo $this->htmlLink($category->getHref(), $category-> title);?>
                </div>
                <?php endif;?>
            </div>

            <div class="ynbusinesspages-profile-related-item-image">
                <div class="ynbusinesspages-profile-related-item-photo">
                   <?php echo Engine_Api::_()->ynbusinesspages()->getPhotoSpan($business); ?>
                </div>
            </div>
            <div class="ynbusinesspages-profile-related-item-content">
                <div class="ynbusinesspages-profile-related-item-info">
                    <?php echo Engine_Api::_()->ynbusinesspages()->renderBusinessRating($business->getIdentity(), false) . ' &nbsp;(' . $business->getReviewCount().')'; ?>
                </div>
                <?php if($mainLocation = $business->getMainLocation()): ?>
                <div class="ynbusinesspages-profile-related-item-location">
                    <i class="ynicon yn-map-marker"></i>
                    <?php echo $mainLocation;?>
                </div>
                <?php endif;?>
            </div>
        </div>
    </li>
<?php endforeach;?>
</ul>

<script type="text/javascript">
window.addEvent('domready',function(){
    var owl_article = jQuery('#ynbusinesspages-profile-related-listings');
    var paren_el = owl_article.parents('.layout_core_container_tabs').length;
    console.log(paren_el);
    var item_amount = parseInt(owl_article.find('.item').length); 
    var true_false = 0;
    if (item_amount > 1) {
        true_false = true;
    } else{
        true_false = false;
    }
    var rtl = false;
    if(jQuery("html").attr("dir") == "rtl") {
        rtl = true;
    }

    if(paren_el){
        $$('.tab_layout_ynbusinesspages_business_profile_related_businesses').removeEvent('click').addEvent('click',function(){
            initSlider();
        });
    }else{
        initSlider();
    }
    function initSlider() {
        jQuery("#ynbusinesspages-profile-related-listings").owlCarousel({
            rtl:rtl,
            nav:true_false,
            navText:["<i class='ynicon yn-arr-left'></i>","<i class='ynicon yn-arr-right'></i>"],
            loop: true_false,
            mouseDrag:true_false, 
            autoplay: false,
            dotsSpeed:1000,
            autoplayHoverPause:true,
            items:4,
            responsive : {
                320:{
                    items:1
                },
                480:{
                    items:2
                },
                768:{
                    items:3
                },
                992:{
                    items:4
                }
            }
        });
    }
});
</script>