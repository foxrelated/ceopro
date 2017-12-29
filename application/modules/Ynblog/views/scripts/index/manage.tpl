<script type="text/javascript">
  var pageAction =function(page){
    $('page').value = page;
    $('blog_filter_form').submit();
  }
</script>

<div class='layout_right'>
  <?php echo $this->form->render($this) ?>

  <?php if( count($this->quickNavigation) > 0 ): ?>
    <div class="ynblogs_gutter_options" >
      <?php
        /*---- Render the menu ----*/
        echo $this->navigation()
          ->menu()
          ->setContainer($this->quickNavigation)
          ->render();
      ?>
    </div>
  <?php endif; ?>
</div>

<div class='layout_middle'>
  <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    

    <ul class="ynblog-mode-views clearfix" id="ynblog-my-entries">
      <?php foreach( $this->paginator as $item ): ?>
        <?php 
           $photoUrl = $item -> getPhotoUrl();
           if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynblog/externals/images/nophoto_blog_thumb_profile.png';
        ?>
        <li class="ynblog_listview_mode clearfix">

          <div class="ynblog-thumb clearfix">
              <?php 
                $creation_date = strtotime($item -> creation_date);
                $oldTz = date_default_timezone_get();
                if($this->viewer() && $this->viewer()->getIdentity())
                {
                   date_default_timezone_set($this -> viewer() -> timezone);
                }
                else 
                {
                   date_default_timezone_set( $this->locale() -> getTimezone());
                }
                $day = date("d", $creation_date); 
                $month = date("M", $creation_date);
                $year = date("Y", $creation_date);
                date_default_timezone_set($oldTz);
             ?>
             <div class="ynblog-mdy-listview">
                <span class="ynblog-mdy-day"><?php echo $day?></span>
                <span class="ynblog-mdy-month"><?php echo $month?></span>
                <span class="ynblog-mdy-year"><?php echo $year?></span>
             </div>
            <a href="<?php echo $item ->getHref(); ?>" style="width: calc(100% - 70px) ;background-image: url(<?php echo $photoUrl; ?>)">
            </a>
          </div>

          <div class="ynblog-info">
            <div class="ynblog-title-desc">
              <div class="ynblog-title">
                <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
              </div>

              <div class="ynblog-description">
                <?php echo strip_tags($item -> body);?>
              </div>
              
            </div>

            <div class="ynblog-stats">
              <span>
                <i class="fa fa-eye"></i>
                <?php echo $this -> translate(array('%s view', '%s view', $item -> view_count), $this->locale()->toNumber($item->view_count));?>
              </span>

              <?php $status = $item->getStatus();?>
                <span class ="ynblogs_browse_info_status_<?php echo $status['type'];?> ynblog_status">
                   <?php echo $this->translate($status['condition']);?>
                </span>
            </div>
          </div>

          <div class='ynblog_myentries_options'>
            <span>
              <?php
              echo $this->htmlLink(array(
                'route' => 'blog_specific',
                'action' => 'edit',
                'blog_id' => $item->getIdentity(),
                'reset' => true,
              ), '<i class="fa fa-pencil-square-o"></i>'.$this->translate('Edit Entry'), array(
                'class' => 'ynblog_btn_edit',
              ));?>
            </span>

            <span>
              <?php
              echo $this->htmlLink(array(
                  'route' => 'blog_specific',
                  'action' => 'delete',
                  'blog_id' => $item->getIdentity(),
                  'format' => 'smoothbox'
                  ), '<i class="fa fa-trash"></i>'.$this->translate('Delete Entry'), array(
                'class' => 'smoothbox ynblog_btn_delete'
              ));?>
            </span>
          </div>



        </li>
      <?php endforeach; ?>
    </ul>


  <?php elseif($this->search): ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any blog entries that match your search criteria.');?>
      </span>
    </div>
  <?php else: ?>
    <div class="tip">
      <span>
        <?php echo $this->translate('You do not have any blog entries.');?>
        <?php if( $this->canCreate ): ?>
          <?php echo $this->translate('Get started by %1$swriting%2$s a new entry.', '<a href="'.$this->url(array('action' => 'create'), 'blog_general').'">', '</a>'); ?>
        <?php endif; ?>
      </span>
    </div>
  <?php endif; ?>

  <?php echo $this->paginationControl($this->paginator, null, null, array(
    'pageAsQuery' => true,
    'query' => $this->formValues,
  )); ?>

</div>
<?php if(isset($this->bShowMessage) && $this->bShowMessage === true): ?>
<a href="<?php echo $this->url(array('action' => 'show-message'), 'blog_general'); ?>" class="smoothbox" id="ynblog_message"></a>
<script type="text/javascript">
  Smoothbox.open($('ynblog_message'));
</script>

<?php endif; ?>