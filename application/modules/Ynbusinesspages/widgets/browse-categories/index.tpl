<?php if (count($this->categories)): ?>
<div class="ynbusinesspages_categories_main ynbusinesspages-clearfix">
<?php foreach ($this->categories as $category) :?>
    <div class="browse_category">
        <div class="item-category-browser">
            <?php 
                $children = $category->getChildList();
                $numOfBusinesses = $category->getNumOfBusinesses();
            ?>
            <div class="category-browser-top">
                <?php if ($category->photo_id != 0) : ?>
                    <span class="category_icon"><?php echo $this->itemPhoto($category, 'thumb.icon')?></span>
                <?php else: ?>
                    <span class="category_icon"><i class="ynicon yn-photo-o"></i></span>
                <?php endif; ?>
                <span class="category-title"><?php echo $this->htmlLink($category->getHref(), $category->getTitle())?></span>
                <span class="category-description"><?php echo $this->translate(array("%s Business", "%s Businesses", $numOfBusinesses), $numOfBusinesses) ;?></span>

                <?php if ( count($children) ) : ?>            
                    <ul class="category-child-list">
                    <?php $icount = 1; ?>
                    <?php foreach($children as $child) : ?>
                        <?php if ($icount <= 4) :?>
                        <?php $numOfBusinesses = $child->getNumOfBusinesses();?>
                        <li>
                            <i class="ynicon yn-caret-right"></i>
                            <?php echo $this->htmlLink($child->getHref(), $child->getTitle())?>
                        </li>
                        <?php endif; ?>
                        <?php $icount++; ?>
                    <?php endforeach; ?>
                    </ul>
                <?php endif; ?>


                <?php if ( count($children) > 4) : ?>
                    <span class="category-sub-area">
                        <span class=""><?php echo $this -> translate("See all sub-categories <i class='ynicon yn-arr-double-right'></i>")?></span>
                        <span class="category-sub-btn"><i class="ynicon yn-plus"></i></span>
                    </span>
                <?php endif; ?>    
            </div>

            <?php if ( count($children) > 4) : ?>            
                <ul class="category-child-list">
                <?php $icount = 1; ?>
                <?php foreach($children as $child) : ?>
                    <?php if ($icount > 4) :?>
                    <?php $numOfBusinesses = $child->getNumOfBusinesses();?>
                    <li>
                        <i class="ynicon yn-caret-right"></i>
                        <?php echo $this->htmlLink($child->getHref(), $child->getTitle())?>
                    </li>
                    <?php endif; ?>
                    <?php $icount++; ?>
                <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach;?>
</div>

<script type="text/javascript">
    $$('.category-sub-area').addEvent('click', function() {
        var this_parent = this.getParent('.item-category-browser');

        if ( this_parent.hasClass('ynbusinesspages-open-subcategories') ) {
            this_parent.removeClass('ynbusinesspages-open-subcategories');
        } else {
            $$('.ynbusinesspages-open-subcategories').removeClass('ynbusinesspages-open-subcategories');
            this_parent.addClass('ynbusinesspages-open-subcategories');
        }

        // this_parent.toggleClass();
    });

    $$('.ynbusinesspages_categories_main')[0].getParent('.layout_middle').setStyle('padding-bottom', '150px');
</script>

<!-- ELSE NOT HAVE ANY TO SHOW -->
<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate('There are no available categories.'); ?>
    </span>
</div>
<?php endif; ?>