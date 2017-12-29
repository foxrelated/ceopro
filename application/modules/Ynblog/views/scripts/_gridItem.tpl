<?php 
   $photoUrl = $this-> item ->getPhotoUrl();
   if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynblog/externals/images/nophoto_blog_thumb_profile.png';
?>

<li class="ynblog_gridview_mode">
  <div>
    <div class="ynblog-thumb">
      <a href="<?php echo $this-> item -> getHref(); ?>" style="background-image:url(<?php echo $photoUrl; ?>)"></a>
      
      <div class="ynblog-owner">
        <?php
          $owner = $this -> item->getOwner();
          echo $this->htmlLink($owner->getOwner()->getHref(), $this->itemPhoto($owner->getOwner(), 'thumb.icon'));

          echo '<span>'.$this->translate('By %1$s', $this->htmlLink($owner->getHref(), $owner->getTitle())).'</span>';
        ?>
      </div>
    </div>

    <div class="ynblog-info">
        <div class="ynblog-title-desc">
          <div class="ynblog-title">
                <?php echo $this->htmlLink($this -> item->getHref(), $this -> item->getTitle()) ?>
          </div>

          <div class="ynblog-description">
            <?php echo strip_tags($this -> item -> body);?>
          </div>
        </div>

        <div class="ynblog-stats">
          <?php 
            $creation_date = strtotime($this -> item -> creation_date);
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
            <span>
              <i class="fa fa-clock-o"></i> 
              <?php echo $month?> <?php echo $day?>, <?php echo $year?>
            </span>

            <span>
              <?php 
                if($this -> type == 'view'){
                  echo '<i class="fa fa-eye"></i> '.$this -> item -> view_count;
                }elseif ($this -> type == 'comment') {
                  echo '<i class="fa fa-comments-o"></i> '.$this -> item -> comment_count;
                }elseif ($this -> type == 'top') {
                  echo '<i class="fa fa-thumbs-o-up"></i> '.$this -> item -> total_like;
                }elseif ($this -> type == 'new'){
                  echo '<i class="fa fa-eye"></i> '.$this -> item -> view_count;
                }
              ?>
            </span>
            
        </div>
    </div>
  </div>
</li>

<script>
  function setCookie(cname, cvalue, exdays) {
      var d = new Date();
      d.setTime(d.getTime() + (exdays*24*60*60*1000));
      var expires = "expires="+d.toUTCString();
      document.cookie = cname + "=" + cvalue + "; " + expires;
  }

  function getCookie(cname) {
      var name = cname + "=";
      var ca = document.cookie.split(';');
      for(var i=0; i<ca.length; i++) {
          var c = ca[i];
          while (c.charAt(0)==' ') c = c.substring(1);
          if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
      }
      return "";
  }
</script>