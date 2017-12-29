<div class="ynbusinesspages-profile-module-header">
    <!-- Menu Bar -->
    <div class="ynbusinesspages-profile-header-right">
        <?php if ($this->paginator->getTotalItemCount() > 0): ?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynbusinesspages_extended',
                'controller' => 'video-channel',
                'action' => 'list',
                'business_id' => $this->business->getIdentity(),
                'subject' => $this->subject()->getGuid(),
                'tab' => $this->identity,
            ), '<i class="ynicon yn-list-view"></i>' . $this->translate('Browse Videos'), array(
                'class' => 'buttonlink'
            ))
            ?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynbusinesspages_extended',
                'controller' => 'video-channel',
                'action' => 'manage',
                'subject' => $this->subject()->getGuid()), '<i class="ynicon yn-user"></i>' . $this->translate('Manage Videos'), array(
                'class' => 'buttonlink'
            )) ?>
        <?php endif; ?>

        <?php if ($this->canCreate): ?>
            <?php echo $this->htmlLink(array(
                'route' => 'ynvideochannel_general',
                'action' => 'share-video',
                'parent_type' => 'ynbusinesspages_business',
                'subject_id' => $this->business->business_id,
            ), '<i class="ynicon yn-plus-circle"></i>' . $this->translate('Create New Video'), array(
                'class' => 'buttonlink'
            ));
            ?>
        <?php endif; ?>
    </div>
</div>

<div class="ynvideochannel_count_videos">
    <i class="ynicon yn-video-camera" aria-hidden="true"></i>
    <?php $totalVideos = $this->paginator->getTotalItemCount(); ?>
    <?php echo $this->translate(array("%s video", "%s videos", $totalVideos), $totalVideos) ?>
</div>
<?php if ($totalVideos > 0): ?>
    <ul class="ynvideochannel_browse_video_items ynvideochannel_video_listing_items">
        <?php foreach ($this->paginator as $video):
            echo $this->partial('_videochannel_video_item.tpl', 'ynvideochannel', array('item' => $video, 'showAddto' => true));
        endforeach; ?>
    </ul>
    <?php echo $this->paginationControl($this->paginator, null, null, array(
        'pageAsQuery' => true,
        'query' => $this->formValues
    ));
    ?>
<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate('No videos found.'); ?>
        </span>
    </div>
<?php endif; ?>
<script type="text/javascript">
    ynvideochannelVideoOptions();
</script>
