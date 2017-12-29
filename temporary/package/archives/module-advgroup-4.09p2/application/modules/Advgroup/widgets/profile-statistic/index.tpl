<ul class = "global_form_box" style="background: none; margin-bottom: 5px;overflow: hidden;">
      <?php $group = $this->group;?>
      <div class="statistic_info"> <?php echo $this->translate('Total Views');?></div>
      <div class="statistic_count">  <?php echo $group->view_count;?> </div>
        <br/>
      <div class="statistic_info"> <?php echo $this->translate('Total Members');?></div>
      <div class="statistic_count">  <?php echo $group->member_count;?> </div>
       <br/>
      <div class="statistic_info"> <?php echo $this->translate('Total Albums');?></div>
      <div class="statistic_count">  <?php echo $this->count_albums?> </div>
        <br/>
      <div class="statistic_info"> <?php echo $this->translate('Total Photos');?></div>
      <div class="statistic_count">  <?php echo $this->count_photos?> </div>
        <br/>
      <div class="statistic_info"> <?php echo $this->translate('Total Topics'); ?></div>
      <div class="statistic_count">  <?php echo $this->count_topics?> </div>
        <br/>
      <div class="statistic_info"> <?php echo $this->translate('Staff List'); ?></div>
        <br/>
      <div class="statistic_staff_list">
        <ul>
          <?php foreach( $this->staff as $info ): ?>
            <li style="word-wrap:break-word;">
              <?php echo $info['user']->__toString() ?>
              <?php if( $this->group->isOwner($info['user']) ): ?>
                (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('owner') ) ?>)
              <?php else: ?>
                (<?php echo ( !empty($info['membership']) && $info['membership']->title ? $info['membership']->title : $this->translate('officer') ) ?>)
              <?php endif; ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
 </ul>
