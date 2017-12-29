<?php if(count($this->categories)>0):?>
<div class="generic_list_widget">
    <ul class = "ymb_menuRight_wapper yncategories_items global_form_box"  style="margin-bottom: 15px;">
    <?php $cats = $this->categories;
          $params = $this->params;
          foreach($cats as $cat): ?>
              <li value ='<?php echo $cat->getIdentity() ?>' class="ynblog_categories_blogs yncategories_item <?php if ($cat->parent_id > 0) echo 'yncategories_sub_item child_'.$cat->parent_id. ' level_'.$cat->level?>">
                  <a class="rss_link" href="<?php echo $this->url(array('action' => 'rss','category' => $cat->category_id), 'blog_general') ?>"></a>

                  <?php if($params['mode'] == '1'){
                      echo $this->htmlLink(
                      $this->url(array('user_id'=>$params['user_id'],'category'=>$cat->category_id,'sort'=>'recent'),'blog_view',true),
                      Engine_Api::_()->ynblog()->subPhrase($this->translate($this -> translate($cat->category_name)),30),
                      array('class'=>''));
                  }
                  else { ?>
                    <?php if($cat->getChildCount() > 0 && !$session-> mobile) : ?>
                      <div class="yncategories_have_child yncategories_collapsed">
                          <span class="ynicon yn-caret-down yncategories_submenu"></span>
                          <span class="ynicon yn-caret-right yncategories_mainmenu"></span>
                      </div>
                      <?php echo $this->htmlLink(
                           $this->url(array('category'=>$cat->category_id,'action'=>'listing','sort'=>'recent'),'blog_general',true),
                           Engine_Api::_()->ynblog()->subPhrase($this->translate($this -> translate($cat->category_name)),30),
                           array('class'=>'')); ?>
                    <?php else : ?>
                      <div class="yncategories_dont_have_child yncategories_last_sub_item">
                          <span class="ynicon yn-caret-right yncategories_mainmenu"></span>
                      </div>

                      <?php echo $this->htmlLink(
                          $this->url(array('category'=>$cat->category_id,'action'=>'listing','sort'=>'recent'),'blog_general',true),
                            $cat->category_name,
                          array('class'=>'')); ?>
                    <?php endif; ?>
                  <?php } ?>
              </li>
          <?php endforeach;?>
 </ul>
</div>
<?php endif;?>