<h2>
  <?php echo $this->translate('Advanced Blogs Plugin') ?>
</h2>

<?php if( count($this->navigation) ): ?>
<div class='tabs'>
    <?php
    // Render the menu
    //->setUlClass()
    echo $this->navigation()->menu()->setContainer($this->navigation)->render()
    ?>
</div>
<?php endif; ?>

<div class='clear'>
  <div class='settings'>
    <form class="global_form">
      <div>
      <h3><?php echo $this->translate("Blog Entry Categories") ?></h3>
      <p class="description">
        <?php echo $this->translate("BLOG_VIEW_SCRIPTS_ADMINSETTINGS_CATEGORIES_DESCRIPTION") ?>
      </p>
        <div class="ynblog_breadcrum">
          <?php if ($this->category->category_id != 0): ?>
          <?php
            $parent = $this->category->getParent();
            if ($parent) {
              $superparent = $parent->getParent();
              if ($superparent) {
                echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'categories', 'parent_id' =>$superparent->category_id), $this->translate($superparent->shortTitle()), array()).' &raquo; ';
              }
              echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'categories', 'parent_id' =>$parent->category_id), $this->translate($parent->shortTitle()), array()).' &raquo; ';
          }
          ?>
          <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'categories', 'parent_id' =>$this->category->category_id), $this->translate($this->category->shortTitle()), array()) ?>
          <?php endif; ?>
        </div>
          <?php if(count($this->categories)>0):?>

      <table class='admin_table'>
        <thead>

          <tr>
            <th><?php echo $this->translate("Category Name") ?></th>
            <th><?php echo $this->translate("Owner") ?></th>
            <th><?php echo $this->translate("Number of Times Used") ?></th>
            <th><?php echo $this->translate("Sub-Categories") ?></th>
            <th><?php echo $this->translate("Options") ?></th>
          </tr>

        </thead>
        <tbody>
          <?php foreach ($this->categories as $category): ?>

          <tr>
            <td><?php echo $category->category_name?></td>
            <td><?php echo $category->user_id?></td>
            <td><?php echo $category->getUsedCount()?></td>
            <td><?php echo $category->getChildCount()?></td>
            <td>
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'edit-category', 'id' =>$category->getIdentity()), $this->translate('edit'), array(
                'class' => 'smoothbox',
              )) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'delete-category', 'id' =>$category->getIdentity()), $this->translate('delete'), array(
                'class' => 'smoothbox',
              )) ?>
              <?php if($category->level < $this->maxCategoryLevel) :?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'add-category', 'parent_id' =>$category->category_id), $this->translate('add sub-category'), array(
              'class' => 'smoothbox',
              )) ?>
              |
              <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'categories', 'parent_id' =>$category->category_id), $this->translate('view sub-category'), array(
              )) ?>
              <?php endif;?>
            </td>
          </tr>

          <?php endforeach; ?>

        </tbody>
      </table>

      <?php else:?>
      <br/>
      <div class="tip">
      <span><?php echo $this->translate("There are currently no categories.") ?></span>
      </div>
      <?php endif;?>
      <br/>

      <?php echo $this->htmlLink(array('route' => 'admin_default', 'module' => 'ynblog', 'controller' => 'settings', 'action' => 'add-category', 'parent_id' =>$this->category->getIdentity()), $this->translate('Add New Category'), array(
      'class' => 'smoothbox buttonlink',
      'style' => 'background-image: url(application/modules/Core/externals/images/admin/new_category.png);')) ?>
      </div>
    </form>
  </div>
</div>
