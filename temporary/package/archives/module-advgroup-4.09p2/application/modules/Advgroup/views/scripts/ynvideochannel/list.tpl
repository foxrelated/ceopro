<!-- Header -->
<div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
        <div class="headline">
            <h2>
                <?php echo $this->group->__toString() . " ";
                echo $this->translate(' &#187; VideoChannel Videos');
                ?>
            </h2>
        </div>
    </div>
</div>

<div class="generic_layout_container layout_main advgroup_list">
    <div class="generic_layout_container layout_right">
        <!-- Search Form -->
        <div class="">
            <?php echo $this->form->render($this); ?>
        </div>
    </div>

    <div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
            <!-- Menu Bar -->
            <div class="group_discussions_options">
                <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity(), 'slug' => $this->group->getSlug(), 'tab' => $this->tab), '<i class="fa fa-arrow-left"></i>' . $this->translate('Back to Group'), array(
                    'class' => 'buttonlink'
                )) ?>
                <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'ynvideochannel', 'action' => 'manage', 'subject' => $this->subject()->getGuid()), '<i class="fa fa-user"></i>' . $this->translate('Manage Videos'), array(
                    'class' => 'buttonlink'
                )) ?>
                <?php if ($this->canCreate): ?>
                    <?php $label = $this->translate('Create New Video'); ?>
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynvideochannel_general',
                        'action' => 'share-video',
                        'parent_type' => 'group',
                        'subject_id' => $this->group->group_id,
                    ), '<i class="fa fa-plus-square"></i>' . $label, array(
                        'class' => 'buttonlink'
                    ))
                    ?>
                <?php endif; ?>
            </div>

            <div class="ynvideochannel_count_videos">
                <i class="fa fa-video-camera" aria-hidden="true"></i>
                <?php $totalVideos = $this->paginator->getTotalItemCount(); ?>
                <?php echo $this->translate(array("%s video", "%s videos", $totalVideos), $totalVideos) ?>
            </div>
            <?php if ($totalVideos > 0): ?>
                <ul class="ynvideochannel_browse_video_items ynvideochannel_video_listing_items">
                    <?php foreach ($this->paginator as $video):
                        echo $this->partial('_videochannel_video_item.tpl', array('item' => $video, 'showAddto' => true));
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