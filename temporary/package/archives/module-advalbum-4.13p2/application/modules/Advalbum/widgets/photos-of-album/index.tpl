<?php $session = new Zend_Session_Namespace('mobile'); ?>

<div class='ynadvalbum_photo_detail_albums ym_view_list_photo'>
    <?php if($session -> mobile){ ?>
    <a href="javascript:void(0)" class="slideshow_button toggleSlideshowMobile" id='advalbum_slideshow'><?php echo $this->translate('Slide Show');?></a>
    <div class="ymb_photo_list">
        <?php echo $this->html_photo_list; ?>
        <?php if(count($this->paginator)>1): ?>
        <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
            array(
            'pageAsQuery' => false,
            'query' => $this->formValues
        )); ?>
        <?php endif; ?>
    </div>
    <div class="ymb_mobile_slideshow">
        <?php echo $this->html_mobile_slideshow; ?>
    </div>
    <?php }else{ ?>
    <div class="ymb_photo_list">
        <div class="ynadvalbum_album_detail-stats clearfix">
            <div class="ynadvalbum_photo_detail-stats-info advalbum_album_statis">
                <div class="ynadvalbum-photo"><?php echo $this->translate('Photos: ');?><?php echo $this->locale()->toNumber($this->album->count());?></div>
                <div class="ynadvalbum-views yn_stats_view"><i class="yn_dots">|</i><span><?php echo $this->translate('Views: ');?></span><?php echo $this->locale()->toNumber($this->album->view_count);?><i class="yn_dots">|</i></div>
                <div class="ynadvalbum-comments yn_stats_comment"><span><?php echo $this->translate('Comments: ');?></span><?php echo $this->locale()->toNumber($this->album->comment_count);?></div>
            </div>
            <div class="ynadvalbum-view-slideshow">
                <?php $slideshow_url = $this->album->getHref(array('slideshow'=>'1')) . '/effect/kenburns' ?>
                <a href="javascript:;" class="slideshow_button" id='advalbum_slideshow' onclick="return popupSlideshow('<?php echo $slideshow_url ?>')"><?php echo '<span class="ynicon yn-play-circle"></span>'.$this->translate('view slideshow');?></a>
            </div>
        </div>
        <?php echo $this->html_photo_list; ?>
    </div>
    <?php if(count($this->paginator)>1): ?>
    <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum"),
        array(
        'pageAsQuery' => false,
        'query' => $this->formValues
        )); ?>
    <br />
    <?php endif; ?>
    <?php } ?>
</div>