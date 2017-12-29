<div class='count_results'>
    <span class="search_icon fa fa-search"></span>
    <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->users->getTotalItemCount()),$this->users->getTotalItemCount())?></span>
    <span class="total_results">(<?php echo $this->total_content?>)</span>
    <span class="label_results"><?php echo $this->htmlLink(array('route'=>'user_general'), $this->label_content, array());?></span>
</div>

<?php if( 0 == count($this->users) ): ?>
  <div class="tip">
    <span>
      <?php echo $this->translate('There were no members found matching your search criteria.') ?>
    </span>
  </div>
 <?php endif;?>

<div class='browsemembers_results' id='browsemembers_results'>
    <?php $viewer = Engine_Api::_()->user()->getViewer();?>
    
    <?php if( count($this->users) ): ?>
      <ul id="browsemembers_ul">
        <?php foreach( $this->users as $user ): ?>
          <li>
            <?php echo $this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.profile')) ?>
            <?php 
            $table = Engine_Api::_()->getDbtable('block', 'user');
            $select = $table->select()
              ->where('user_id = ?', $user->getIdentity())
              ->where('blocked_user_id = ?', $viewer->getIdentity())
              ->limit(1);
            $row = $table->fetchRow($select);
            ?>
              <div class='browsemembers_results_info'>
                <?php echo $this->htmlLink($user->getHref(), $user->getTitle()) ?>
                <?php echo $user->status; ?>
                <?php if( $user->status != "" ): ?>
                  <div class='browsemembers_results_date'>
                    <?php echo $this->timestamp($user->status_date) ?>
                  </div>
                <?php endif; ?>
              </div>
			  <?php
              $about_me = "";
              $fieldStructure = Engine_Api::_()->fields()->getFieldsStructurePartial($user);
              foreach( $fieldStructure as $map ) {
                $field = $map->getChild();
                $value = $field->getValue($user);
                if($field->type == 'about_me') {
                    $about_me = $value['value'];
                }
              }
              ?>
              
			  <div class='browsemembers_results_message'>
			  <?php echo $about_me;?>
			  </div>
			  <?php if( $row == NULL ): ?>
              <?php if( $this->viewer()->getIdentity() ): ?>
              <div class='browsemembers_results_links'>
                <?php echo $this->userFriendship($user) ?>
              </div>
            <?php endif; ?>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php endif ?>
    
    <?php if( $this->users ):
        $pagination = $this->paginationControl($this->users, null, null, array(
          'pageAsQuery' => true,
          'query' => $this->formValues,
        ));
      ?>
      <?php if( trim($pagination) ): ?>
        <div class='browsemembers_viewmore' id="browsemembers_viewmore">
          <?php echo $pagination ?>
        </div>
      <?php endif ?>
    <?php endif; ?>
    
    <script type="text/javascript">
      page = '<?php echo sprintf('%d', $this->page) ?>';
      totalUsers = '<?php echo sprintf('%d', $this->totalUsers) ?>';
      userCount = '<?php echo sprintf('%d', $this->userCount) ?>';
    </script>
</div>