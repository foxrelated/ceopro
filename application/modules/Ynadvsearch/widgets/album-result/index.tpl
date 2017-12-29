<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.com/license/
 * @version    $Id: browse.tpl 10217 2014-05-15 13:41:15Z lucas $
 * @author     Sami
 */
?>
<?php
$this->headScript()-> appendScript('jQuery.noConflict();');
$album_listing_id = "advalbum_albums_listing";
$photoTable = Engine_Api::_()->getItemTable('album_photo');
?>

<div class='count_results'>
  <span class="search_icon fa fa-search"></span>
  <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $this->paginator->getTotalItemCount()),$this->paginator->getTotalItemCount())?></span>
  <span class="total_results">(<?php echo $this->total_content?>)</span>
  <span class="label_results"><?php echo $this->htmlLink(array('route' => 'album_general'), $this->label_content, array());?></span>
</div>
<div id='<?php echo $album_listing_id ?>' class="clearfix">
  <div class="ynsearch-listing-tab">
    <div title="<?php echo $this->translate('List view');?>" class="list-view" data-view="list"></div>
    <div title="<?php echo $this->translate('Grid view');?>" class="grid-view" data-view="grid"></div>
    <div title="<?php echo $this->translate('Pinterest view');?>" class="pinterest-view" data-view="pinterest"></div>
  </div>
  <div id="<?php echo $album_listing_id ?>_view" class="photo-list-content">
    <?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
    <!-- grid view -->
    <ul class="ynsearch_albums_grid_view yn-clearfix">
      <?php foreach( $this->paginator as $album ): ?>
        <li class="ynsearch_albums_items">
          <div class="ynsearch_albums_items_thumb">
            <a class="ynsearch_albums_items_thumb_temp" href="<?php echo $album->getHref(); ?>"></a>
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.profile'); ?>);"></span>
            <label class="count_photo"><?php echo $this->translate(array('%s', '%s', $album->count()),$this->locale()->toNumber($album->count())) ?></label>
          </div>
          <div class="ynsearch_albums_items_info">
            <?php echo $this->htmlLink($album, $album->getTitle(), array('class' => 'ynsearch_albums_items_thumb_title')) ?>
            <div class="ynsearch_albums_items_thumb_owner">
              <span><?php echo $this->translate('By');?></span>
              <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
            </div>
            <span class="ynsearch_thumbs_statis">
              <span class="yn_stats yn_stats_view">
                <?php echo $this->translate(array('%d <span>view</span>', '%d <span>views</span>', 18), $this->locale()->toNumber(18)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_comment">
                <?php echo $this->translate(array('%d <span>comment</span>', '%d <span>comments</span>', 18), $this->locale()->toNumber(18)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_like">
                <?php echo $this->translate(array('%d <span>like</span>', '%d <span>likes</span>', 18), $this->locale()->toNumber(18)); ?>
              </span>
            </span>
          </div>
        </li>
      <?php endforeach;?>
    </ul>
    <!-- pinterest view -->
    <ul id="<?php echo $album_listing_id; ?>_tiles" class="ynsearch_albums_pinterest_view">
      <?php foreach( $this->paginator as $album ): ?>
        <li id="thumbs-photo-album-<?php echo $album->album_id ?>" class='ynsearch_pinterest ynsearch_albums_items'>
          <div class="ynsearch_albums_items_thumb">
            <a class="ynsearch_albums_items_thumb_temp" href="<?php echo $album->getHref(); ?>"></a>
            <img src="<?php echo $album->getPhotoUrl('thumb.profile'); ?>">
            <label class="count_photo"><?php echo $this->translate(array('%s', '%s', $album->count()),$this->locale()->toNumber($album->count())) ?></label>
          </div>
          <div class="ynsearch_albums_items_info clearfix">
            <?php echo $this->htmlLink($album, $album->getTitle(), array('class' => 'ynsearch_albums_items_thumb_title')) ?>
            <div class="ynsearch_albums_items_thumb_owner">
              <span><?php echo $this->translate('By');?></span>
              <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
            </div>
            <div class="ynsearch_thumbs_stats ynsearch_stats">
              <div class="ynsearch_thumbs_stats-info yn_stats yn_stats_view">
                <label>6</label>
                <span>views</span>
              </div>
              <div class="ynsearch_thumbs_stats-info ynsearch_border yn_stats yn_stats_comment">
                <label>0</label>
                <span>comments</span>
              </div>
              <div class="ynsearch_thumbs_stats-info yn_stats yn_stats_like">
                <label>0</label>
                <span>likes</span>
              </div>
            </div>

          </div>
        </li>
      <?php endforeach;?>
    </ul>
    <!-- list view -->
    <ul id="<?php echo $album_listing_id; ?>_tiles" class="ynsearch_albums_list_view clearfix">
      <?php foreach( $this->paginator as $album ): ?>
        <li class='ynsearch_albums_items clearfix'>
          <div class="ynsearch_albums_items_thumb">
            <a class="ynsearch_albums_items_thumb_temp" href="<?php echo $album->getHref(); ?>"></a>
            <span style="background-image: url(<?php echo $album->getPhotoUrl('thumb.profile'); ?>)"></span>
          </div>
          <div class="ynsearch_albums_items_info">
            <?php echo $this->htmlLink($album, $album->getTitle(), array('class' => 'ynsearch_albums_items_thumb_title')) ?>
            <div class="ynsearch_albums_items_thumb_owner">
              <span><?php echo $this->translate('20 photos by');?></span>
              <?php echo $this->htmlLink($album->getOwner()->getHref(), $album->getOwner()->getTitle(), array('class' => 'thumbs_author')) ?>
            </div>
            <span class="ynsearch_thumbs_statis">
              <span class="yn_stats yn_stats_view">
                <?php echo $this->translate(array('%d <span>view</span>', '%d <span>views</span>', 18), $this->locale()->toNumber(18)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_comment">
                <?php echo $this->translate(array('%d <span>comment</span>', '%d <span>comments</span>', 18), $this->locale()->toNumber(18)); ?>
              </span>
              <i class="yn_dots">.</i>
              <span class="yn_stats yn_stats_like">
                <?php echo $this->translate(array('%d <span>like</span>', '%d <span>likes</span>', 18), $this->locale()->toNumber(18)); ?>
              </span>
            </span>
            <?php
               echo "<div class='album-photo-lists'>";
                $select = $photoTable->select()
                    ->where('album_id = ?', $album->getIdentity())->limit(20);
                $photo_list = $photoTable->fetchAll($select);
                foreach ($photo_list as $photo)
                {
                  echo '<a class="album-photo-list" href="'.$photo->getHref().'"><span style="background-image: url('.$photo->getPhotoUrl().');"></span></a>';
                }
               echo "</div>";
            ?>
          </div>
        </li>
      <?php endforeach;?>
    </ul>
  </div>

  <?php if( $this->paginator->count() > 1 ): ?>
    <?php echo $this->paginationControl(
        $this->paginator, null, null, array(
        'pageAsQuery' => false,
        'query' => $this->searchParams
    )); ?>
  <?php endif; ?>

  <?php elseif( $this->searchParams['category_id'] ): ?>
    <div class="tip">
      <span id="no-album-criteria">
        <?php echo $this->translate('There were no albums found matching your search criteria.');?>
      </span>
    </div>

  <?php else: ?>
  <div class="tip">
      <span id="no-album">
        <?php echo $this->translate('There were no albums found matching your search criteria.');?>
      </span>
  </div>
</div>
<?php endif; ?>