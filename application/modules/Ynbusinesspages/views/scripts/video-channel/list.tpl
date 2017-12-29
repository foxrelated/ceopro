<!-- Header -->
<div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
        <div class="headline">
            <h2>
                <?php echo $this->business->__toString() . " ";
                echo $this->translate('&#187; VideoChannel Video');
                ?>
            </h2>
        </div>
    </div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_list">
    <div class="generic_layout_container layout_right">
        <!-- Search Form -->
        <div class="">
            <?php echo $this->form->render($this); ?>
        </div>
    </div>

    <div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
            <!-- Menu Bar -->
            <div class="ynbusinesspages-profile-module-header">
                <!-- Menu Bar -->
                <div class="ynbusinesspages-profile-header-right">
                    <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business->getSlug(), 'tab' => $this->tab), '<i class="ynicon yn-arrow-left"></i>' . $this->translate('Back to Business'), array(
                        'class' => 'buttonlink'
                    )) ?>
                    <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller' => 'video-channel', 'action' => 'manage', 'subject' => $this->subject()->getGuid()), '<i class="ynicon yn-user"></i>' . $this->translate('Manage Videos'), array(
                        'class' => 'buttonlink'
                    )) ?>
                    <?php if ($this->canCreate): ?>
                        <?php $label = $this->translate('Create New Video'); ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynvideochannel_general',
                            'action' => 'share-video',
                            'parent_type' => 'ynbusinesspages_business',
                            'subject_id' => $this->business->business_id,
                        ), '<i class="ynicon yn-plus-circle"></i>' . $label, array(
                            'class' => 'buttonlink'
                        ))
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
        </div>
    </div>
</div>
<script type="text/javascript">
    ynvideochannelVideoOptions();
</script>