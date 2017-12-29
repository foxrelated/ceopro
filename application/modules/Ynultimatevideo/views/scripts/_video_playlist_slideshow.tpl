<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynultimatevideo
 * @author     YouNet Company
 */
?>
<?php
    $video = $this->video;
    $videoId = $video->getIdentity();
    $photoUrl = $video ->getPhotoUrl('thumb.main');
    if (!$photoUrl) $photoUrl = $this->baseUrl().'/application/modules/Ynultimatevideo/externals/images/nophoto_video_thumb_normal.png';
?>

<div class="ynultimatevideo_playlist_detail_item ms-slide">
    <img src="<?php echo $this->baseUrl().'/application/modules/Ynultimatevideo/externals/masterslider/blank.gif'?>" data-src="<?php echo $photoUrl ?>" alt="lorem ipsum dolor sit"/>

    <div class="ynultimatevideo_playlist_detail_infomation ms-thumb">
        <img src="<?php echo $photoUrl ?>" alt="">

        <div class="ynultimatevideo_playlist_detail_infomation_detail">
            <div class="ynultimatevideo_title">
                <?php echo $video->getTitle(); ?>
            </div>

            <div class="ynultimatevideo_owner">
                 <?php echo $video->getOwner()->getTitle(); ?>
            </div>

            <div class="ynultimatevideo_duration">
                 <?php echo $this->partial('_video_duration.tpl', 'ynultimatevideo', array('video' => $video)); ?>
            </div>
        </div>
    </div>

    <input type="hidden" class="video_id" value="<?php echo $videoId; ?>"/>
    <input type="hidden" class="title" value="<?php echo $video->getTitle(); ?>"/>
    <input type="hidden" class="href" value="<?php echo $video->getHref(); ?>"/>
    <input type="hidden" class="video_type" value="<?php echo $video->type; ?>"/>
    <input type="hidden" class="video_src" value="<?php echo $video->getVideo(); ?>"/>
</div>