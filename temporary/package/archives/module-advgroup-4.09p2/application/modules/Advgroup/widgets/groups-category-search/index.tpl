<?php $session = new Zend_Session_Namespace('mobile'); ?>
<ul class = "ymb_menuRight_wapper global_form_box" style="margin-bottom: 15px; padding:5px 10px 5px 10px;">
  <table style="width:100%">
     <?php foreach ($this->categories as $category): ?>
                    <tr class="advgroup_category_row">
                      <td <?php  $request = Zend_Controller_Front::getInstance()->getRequest(); if($request-> getParam('category_id') == $category -> category_id) echo 'class = "active"';?>>
                          <?php echo $this->htmlLink($category->getHref(), Engine_Api::_()->advgroup()->subPhrase($this -> translate($category->getTitle()),30),
                              array('class'=>'')); ?>
                          <?php if(count($category->getSubCategories()) > 0) : ?>
                            <span class="advgroup-category-collapse-control advgroup-category-collapsed"></span>
                          <?php else : ?>
                              <span class="advgroup-category-collapse-nocontrol"></span>
                          <?php endif; ?>
                      </td>
                    </tr>
                    <?php if(!$session-> mobile):
                    foreach ($category->getSubCategories() as $subCat) : ?>
                      <tr class="advgroup-category-sub-category">
                          <td>
                              <?php echo $this->htmlLink($subCat->getHref(), Engine_Api::_()->advgroup()->subPhrase($this -> translate($subCat->title),30),
                              array('class'=>'')); ?>
                          </td>
                      </tr>
                    <?php endforeach; endif;  ?>
            <?php endforeach; ?>
  </table>
</ul>

