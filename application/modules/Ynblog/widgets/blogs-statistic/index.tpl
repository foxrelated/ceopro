<ul class="ynblog-statistic">
   <li class="ynblog-statistic-item">
      <i class="fa fa-files-o"></i>
      
      <div>
         <div class="ynblog_statistic_info">
            <?php echo $this->count_blogs; ?>
         </div>
         <div class="ynblog_statistic_field">
            <?php echo $this->translate(array('ynblog', 'Blogs', $this->count_blogs), $this->count_blogs);?>
         </div>
      </div>
   </li>

   <li class="ynblog-statistic-item">
      <i class="fa fa-users"></i>
   
      <div>
         <div class="ynblog_statistic_info">
            <?php echo $this->count_bloggers ?>
         </div>

         <div class="ynblog_statistic_field">
            <?php echo $this->translate(array('Active Blogger', 'Active Bloggers', $this->count_bloggers), $this->count_bloggers);?>
         </div>
      </div>
   </li>
</ul>