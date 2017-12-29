<?php
function finalizeUrl($url)
{
    if ($url)
    {
        if (strpos($url, 'https://') === FALSE && strpos($url, 'http://') === FALSE)
        {
            $pageURL = 'http';
            if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on")
            {
                $pageURL .= "s";
            }
            $pageURL .= "://";
            $pageURL .= $_SERVER["SERVER_NAME"];
            $url = $pageURL . '/'. ltrim( $url, '/');
        }
    }

    return $url;
}

$og = '<meta property="og:image" content="' . finalizeUrl($this-> blog->getPhotoUrl()) . '" />';
$og .= '<meta property="og:title" content="' . $this-> blog->getTitle() . '" />';
$og .= '<meta property="og:image:width" content="640" />';
$og .= '<meta property="og:image:height" content="442" />';
$og .= '<meta property="og:description" content="' . $this->string()->striptags($this-> blog->getDescription()) . '" />';
$og .= '<meta property="og:url" content="' . finalizeUrl($this-> blog->getHref()) . '" />';
$og .= '<meta property="og:updated_time" content="' . strtotime($this-> blog->modified_date) . '" />';
$og .= '<meta property="og:type" content="website" />';
$this->layout()->headIncludes .= $og;
?>

<script type="text/javascript">
var categoryAction = function(category){
    $('category').value = category;
    $('ynblog_filter_form').submit();
  }
var tagAction =function(tag){
    $('tag').value = tag;
    $('ynblog_filter_form').submit();
  }
</script>
<style>
  pre {
    white-space: normal;
  }
</style>

<?php 
   $photoUrl = $this-> blog ->getPhotoUrl();
   if (!$photoUrl)  $photoUrl = $this->baseUrl().'/application/modules/Ynblog/externals/images/nophoto_blog_thumb_main.png';
?>
<div class="ynblog-detail-thumb">
  <div class="ynblog-detail-thumb-bg" style="background-image: url(<?php echo $photoUrl ?>)"></div>

  <div class="ynblog-detail-thumb-info">
        <?php if( $this->category ): ?>

        <span>
          <a href='<?php echo $this->url(array('user_id'=>$this->blog->owner_id,'category'=>$this->category->category_id,'sort'=>'recent'),'blog_view',true);?>'>
            <?php echo '<i class="fa fa-folder-open"></i>'.$this->translate($this->category->category_name) ?>
          </a>
        </span>
        <?php endif; ?>

      <span>
        <?php echo '<i class="fa fa-eye"></i>'.$this->translate(array('%s view', '%s views', $this->blog->view_count), $this->locale()->toNumber($this->blog->view_count)) ?>
      </span>

      <?php 
        $creation_date = strtotime($this -> blog -> creation_date);
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
          <i class="fa fa-clock-o"></i><?php echo $month?> <?php echo $day?>, <?php echo $year?>
        </span>
  </div>
</div>

<div class="ynblog-detail-title">
  <?php echo $this->blog->getTitle() ?>
</div>

<div class='ynblog-blog-detail'>

    <div class="ynblog_entrylist_entry_body rich_content_body">
      <?php echo $this->blog->body ?>
    </div>

    <br/>
    <div class="ynblog_detail_tags">
      <?php if (count($this->blogTags )):?>
        <i class="fa fa-tags"></i> <?php echo $this->translate('Tags: ') ?>
        <?php foreach ($this->blogTags as $tag): ?>
          <a href='javascript:void(0);' onclick='javascript:tagAction(<?php echo $tag->getTag()->tag_id; ?>);'>#<?php echo $tag->getTag()->text?></a>&nbsp;
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
</div>
<br/>


<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?>
<!-- Go to www.addthis.com/dashboard to customize your tools --> 
<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
<script type="text/javascript">
  function eventHandler(evt) { 
      if (evt.type == 'addthis.menu.share') { 
          en4.core.request.send(new Request.JSON({
            'method' : 'post', 
              'url' :  en4.core.baseUrl + 'blogs/share',
              'data' : {
                  'blog_id' : <?php echo $this->blog->getIdentity() ?>
              },
              'onComplete':function(responseObject) {  
                $('share_value').innerHTML = responseObject.share;
              }
          }));
      }
  }
  addthis.addEventListener('addthis.menu.share', eventHandler);
</script>

<?php $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";?>
