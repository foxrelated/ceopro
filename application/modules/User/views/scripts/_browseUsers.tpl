<?php
/**
 * SocialEngine
 *
 * @category   Application_Core
 * @package    User
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: _browseUsers.tpl 9979 2013-03-19 22:07:33Z john $
 * @author     John
 */
?>
<?php if ($this->isAjaxSearch): ?>
<h3>
  <?php echo $this->translate(array('%s member found.', '%s members found.', $this->totalUsers),$this->locale()->toNumber($this->totalUsers)) ?>
</h3>
<?php endif; ?>
<?php $viewer = Engine_Api::_()->user()->getViewer();?>

<?php if( count($this->users) ): ?>
  <?php
    $ulClass = '';
    if( !$this->viewer()->getIdentity() ) {
      $ulClass = 'public_user';
    }
  ?>

  <ul id="browsemembers_ul" class="grid_wrapper <?php echo $ulClass;?>">
    <?php foreach( $this->users as $user ): ?>
      <li>
        <?php echo $this->htmlLink($user->getHref(), $this->itemBackgroundPhoto($user, 'thumb.profile')) ?>
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
            <span>
              <?php echo $user->status; ?>
              <?php if( $user->status != "" ): ?>
            </span>
            <div>
              <?php echo $this->timestamp($user->status_date) ?>
            </div>
            <?php endif; ?>
          </div>
           <?php if( $row == NULL ): ?>
          <?php if( $this->viewer()->getIdentity() ): ?>
          <div class='browsemembers_results_links'>
            <?php echo $this->userFriendship($user) ?>
          </div>
        <?php endif; ?>
        <p class="half_border_bottom"></p>
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
