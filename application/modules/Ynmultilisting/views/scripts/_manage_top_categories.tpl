<div id="top_categories-wrapper" class="form-wrapper">
	<div id="top_categories-label" class="form-label">
		<label><?php echo $this->translate('Top Categories Section')?></label>
		<p class="description"><?php echo $this->translate('Can add up to 8 categories')?></p>
	</div>
	<div id="top_categories-element" class="form-element">
		
		<?php if (count($this->categories) ): ?>
		<ul id="top_categories" class="ynmultilisting-category">
		<?php $count = 0;?>
		<?php foreach ($this->categories as $category) : ?>
            <li value ='<?php echo $category->getIdentity() ?>' class="ynmultilisting-category_row <?php if ($category->level > 1) echo 'ynmultilisting-category-sub-category child_'.$category->parent_id.' level_'.$category->level?>">
				<?php if(count($category->getChildList()) > 0) : ?>
                    <div class="ynmultilisting-category-collapse-control ynmultilisting-category-collapsed"></div>
                <?php else : ?>
                    <div class="ynmultilisting-category-collapse-nocontrol"><i class="fa fa-plus-square" aria-hidden="true"></i></div>
                <?php endif; ?>
                <span class="category-title" style="margin-left: <?php echo ($category['level'] - 1)*30;?>px"><?php echo $category->getTitle();?></span>
                <span class="checkbox">
                    <?php 
                    $checked = false;
                    if (count($this->params)) $checked = (isset($this->params['top_category'])) ? in_array($category->getIdentity(), $this->params['top_category']) : false;
                    elseif (!$this->listingtype->manage_menu) $checked = ($count < 5) ? true : false;
                    else $checked = $category->top_category;
                    ?>
                    <input type="checkbox" value="<?php echo $category->getIdentity()?>" name="top_category[]" <?php if ($checked) echo 'checked'?>/>
                </span>
            </li>  
        <?php $count++;?>
		<?php endforeach;?>
		</ul>
		<?php else :?>
		<div class="tip"><span><?php echo $this->translate('No categories for selection.')?></span></div>
		<?php endif; ?>				
	</div>
</div>

