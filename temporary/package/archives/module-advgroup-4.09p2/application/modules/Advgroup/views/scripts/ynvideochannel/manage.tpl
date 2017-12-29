<!-- Header -->
<div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
        <div class="headline">
            <h2>
                <?php echo $this->group->__toString()." " ;
                echo $this->translate(' &#187; VideoChannel Videos');
                ?>
            </h2>
        </div>
    </div>
</div>

<div class="generic_layout_container layout_main advgroup_list">
    <div class="generic_layout_container layout_right">
        <!-- Search Form -->
        <div class="search">
            <?php echo $this->form->render($this);?>
        </div>
    </div>

    <div class="generic_layout_container layout_middle">
        <div class="generic_layout_container ">
            <div class="group_discussions_options">
                <!-- Menu Bar -->
                <?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity(), 'slug' => $this->group-> getSlug(), 'tab' => $this -> tab), '<i class="fa fa-arrow-left"></i>'.$this->translate('Back to Group'), array(
                    'class' => 'buttonlink'
                )) ?>

                <?php echo $this->htmlLink(array('route' => 'group_extended', 'controller'=>'ynvideochannel','action'=>'list','subject' => $this->subject()->getGuid()), '<i class="fa fa-list"></i>'.$this->translate('Browse Videos'), array(
                    'class' => 'buttonlink active'
                )) ?>

                <?php if( $this->canCreate ): ?>
                    <?php echo $this->htmlLink(array(
                        'route' => 'ynvideochannel_general',
                        'action' => 'share-video',
                        'parent_type' =>'group',
                        'subject_id' =>  $this->group->group_id,
                    ), '<i class="fa fa-plus-square"></i>'.$this->translate('Create New Video'), array(
                        'class' => 'buttonlink'
                    )) ;
                    ?>
                <?php endif; ?>
            </div>

            <?php
            $totalVideos = $this->paginator->getTotalItemCount();
            ?>
            <div class="ynvideochannel_count_videos">
                <i class="fa fa-video-camera"></i>
                <?php echo $this -> translate(array("%s video", "%s videos", $totalVideos), $totalVideos)?>
            </div>
            <?php if ($totalVideos > 0) : ?>
                <ul class="ynvideochannel_video_manage_items ynvideochannel_video_listing_items">
                    <?php foreach ($this->paginator as $video) : ?>
                        <?php echo $this->partial('_videochannel_video_item.tpl', array('item' => $video, 'showAddto' => false));?>
                    <?php endforeach; ?>
                </ul>
                <?php
                echo $this->paginationControl($this->paginator, null, null, array(
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
    en4.core.runonce.add(function()
    {
        if($('title'))
        {
            new OverText($('title'),
                {
                    poll: true,
                    pollInterval: 500,
                    positionOptions: {
                        position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                        edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
                        offset: {
                            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
                            y: 2
                        }
                    }
                });
        }
    });
</script>