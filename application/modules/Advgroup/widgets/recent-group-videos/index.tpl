<?php
	$this->headScript()-> appendScript('jQuery.noConflict();'); 
?>
<script type="text/javascript">
    en4.core.runonce.add(function(){
        addEventForButtonAddTo();
        <?php if (!$this->renderOne): ?>
            var anchor = $('ynvideo_recent_videos').getParent();
            $('ynvideo_videos_previous').style.display = '<?php echo ( $this->paginator->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
            $('ynvideo_videos_next').style.display = '<?php echo ( $this->paginator->count() == $this->paginator->getCurrentPageNumber() ? 'none' : '' ) ?>';

            $('ynvideo_videos_previous').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() - 1) ?>
                    }
                }), {
                    'element' : anchor
                })
            });

            $('ynvideo_videos_next').removeEvents('click').addEvent('click', function(){
                en4.core.request.send(new Request.HTML({
                    url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                    data : {
                        format : 'html',
                        subject : en4.core.subject.guid,
                        page : <?php echo sprintf('%d', $this->paginator->getCurrentPageNumber() + 1) ?>
                    }
                }), {
                    'element' : anchor
                })
            });
        <?php endif; ?>
    });
</script>

<?php
	 $ynvideo_enable = Engine_Api::_() -> advgroup() ->checkYouNetPlugin('ynvideo');
 ?>
<?php if( $this->canCreate ): ?>
	<?php 
		if($ynvideo_enable)
		{
			echo $this->htmlLink(array(
				'route' => 'video_general',
				'action' => 'create',
				'parent_type' =>'group',
				'subject_id' =>  $this->group->group_id,
			), $this->translate('Create New Video'), array(
			'class' => 'buttonlink icon_group_video_new'
			)) ;
		}
		else
		{
			echo $this->htmlLink(array(
				'route' => 'video_general',
				'action' => 'create',
				'parent_type' =>'group',
				'subject_id' =>  $this->group->getGuid(),
			), $this->translate('Create New Video'), array(
			'class' => 'buttonlink icon_group_video_new'
			)) ;
		}
	?>
<?php endif; ?>

<?php if(count($this->paginator)>0):?>
    <ul class="generic_list_widget ynvideo_widget videos_browse ynvideo_frame ynvideo_list" id="ynvideo_recent_videos">
        <?php foreach ($this->paginator as $item): ?>
        <?php
             $table = Engine_Api::_() -> getDbTable('highlights', 'advgroup');
             $select = $table -> select() -> where("group_id = ?", $this->group->group_id) -> where('item_id = ?', $item->getIdentity()) -> where("type = 'video'") -> limit(1);		
			 $row = $table -> fetchRow($select);
		?>
            <li <?php echo isset($this->marginLeft)?'style="margin-left:' . $this->marginLeft . 'px"':''?>>
                <?php
                echo $this->partial('_video_listing.tpl', 'advgroup', array(
                    'video' => $item,
                    'recentCol' => $this->recentCol
                ));
                ?>
                <div class="ynvideo_title">
                     <?php if($row->highlight) :?>
                   			<strong style="color: red;"><?php echo " - " . $this->translate("highlighted"); ?></strong> 
                    <?php endif;?>
               </div>
            </li>
            
        <?php endforeach; ?>
    </ul>

<?php else:?>
  <div class="tip">
    <span>
      <?php echo $this->translate('No videos have been added in this group yet.');?>
    </span>
  </div>
<?php endif;?>