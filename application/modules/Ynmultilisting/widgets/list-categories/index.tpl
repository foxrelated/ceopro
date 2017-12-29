<?php
    $session = new Zend_Session_Namespace('mobile');
?>
<div id="categories_widget">
    <div class="generic_layout_container">
            <ul class="ymb_menuRight_wapper ynmultilisting-category">
                <?php foreach ($this->categories as $category) : ?>
                    <li value ='<?php echo $category->getIdentity() ?>' class="ynmultilisting-category_row <?php if ($category->level > 1) echo 'ynmultilisting-category-sub-category child_'.$category->parent_id.' level_'.$category->level?>">
                        <div 
                        <?php  $request = Zend_Controller_Front::getInstance()->getRequest(); 
                        if($request-> getParam('category_id') == $category -> category_id) echo 'class = "active"';?>>
                            <?php if(count($category->getChildList()) > 0 && !$session-> mobile) : ?>
                                <div class="ynmultilisting-category-collapse-control ynmultilisting-category-collapsed"></div>
                            <?php else : ?>
                                <div class="ynmultilisting-category-collapse-nocontrol"><i class="fa fa-plus-square" aria-hidden="true"></i></div>
                            <?php endif; ?>

                            <?php 
                                echo $this->htmlLink(
                                        $category->getHref(), 
                                        $this->string()->truncate($category->getTitle(), 20),
                                        array('title' => $category->getTitle()));
                            ?>                    
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
    </div>
</div>
<?php if($session -> mobile): ?>
<style type="text/css">
    ul.ymb_menuRight_wapper.ynmultilisting-category li.ynmultilisting-category_row{
        border-top: 1px solid #161515!important;
        border-bottom: 1px solid #252525!important;
        height: 42px;
        line-height: 42px;
    }
    ul.ymb_menuRight_wapper.ynmultilisting-category li.ynmultilisting-category_row a{
        border: none !important;
        font-size: 21px !important;
    }

</style>
<script>
    
    function  restore(){
        $$('#ynmb_siteWrapper')[0].setStyle('height','100%');
    }
    
    $('global_header').grab($('categories_widget'));
    var btn_mobile_category = new Element('div', 
        {html:
            '<a onclick="toggleOpenMenuRight(this);restore();" class="ynmb_openMenuRight_btn ynmb_sortBtn_btn ynmb_touchable ynmb_categories_icon ynmb_a_btnStyle" href="javascript: void(0);"><span class="ynmb_openMenuRight"><span><?php echo $this->translate("Categories")?></span></span></a>'}) ;
    btn_mobile_category.addClass('ynmb_sortBtn_actionSheet');
    $$('.ynmb_sortBtn_Wrapper')[0].grab( btn_mobile_category );
</script>
<?php endif; ?>