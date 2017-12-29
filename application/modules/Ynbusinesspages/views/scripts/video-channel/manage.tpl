<!-- Header -->
<div class="generic_layout_container layout_top">
    <div class="generic_layout_container layout_middle">
        <div class="headline">
            <h2>
                <?php echo $this->business->__toString()." " ;
                echo $this->translate('&#187; VideoChannel Videos');
                ?>
            </h2>
        </div>
    </div>
</div>

<div class="generic_layout_container layout_main ynbusinesspages_list">
    <div class="generic_layout_container layout_right">
        <!-- Search Form -->
        <div class="search">
            <?php echo $this->form->render($this);?>
        </div>
    </div>

    <div class="generic_layout_container layout_middle">
        <div class="generic_layout_container">
            <div class="ynbusinesspages-profile-module-header">
                <!-- Menu Bar -->
                <div class="ynbusinesspages-profile-header-right">
                    <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_profile', 'id' => $this->business->getIdentity(), 'slug' => $this->business-> getSlug(), 'tab' => $this -> tab), '<i class="ynicon yn-arrow-left"></i>'.$this->translate('Back to Business'), array(
                        'class' => 'buttonlink'
                    )) ?>

                    <?php echo $this->htmlLink(array('route' => 'ynbusinesspages_extended', 'controller'=>'video-channel','action'=>'list','subject' => $this->subject()->getGuid()), '<i class="ynicon yn-list-view"></i>'.$this->translate('Browse Videos'), array(
                        'class' => 'buttonlink active'
                    )) ?>

                    <?php if( $this->canCreate ): ?>
                        <?php echo $this->htmlLink(array(
                            'route' => 'ynvideochannel_general',
                            'action' => 'share-video',
                            'parent_type' =>'ynbusinesspages_business',
                            'subject_id' =>  $this->business->business_id,
                        ), '<i class="ynicon yn-plus-circle"></i>'.$this->translate('Create New Video'), array(
                            'class' => 'buttonlink'
                        )) ;
                        ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php
            $totalVideos = $this->paginator->getTotalItemCount();
            ?>
            <div class="ynvideochannel_count_videos">
                <i class="ynicon yn-video-camera"></i>
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