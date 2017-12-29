<?php if(!empty($this->feed_only) || !$this->end_of_feed ):?>
<script type="text/javascript">
  en4.core.runonce.add(function() {

    var activity_count = <?php echo sprintf('%d', $this->activity_count) ?>;
    var next_id = <?php echo sprintf('%d', $this->next_id) ?>;
    var subject_guid = '<?php echo $this->subjectGuid ?>';
    var end_of_feed = <?php echo ( $this->end_of_feed ? 'true' : 'false' ) ?>;

    var group_activity_viewmore = window.groupActivityViewMore = function(next_id,subject_guid)
    {
      if(en4.core.request.isRequestActive()) return;

      var url ='<?php echo $this->url(array('action' => 'viewmore'), 'group_activity', true); ?>';
        $('yn_group_feed_viewmore').style.display = 'none';
        $('yn_group_feed_loading').style.display = '';

      var group_feed_request = new Request.HTML({
        url: url,

        data: {
          format     : 'html',
          'maxid'    : next_id,
          'feed_only': true,
          'nolayout' : true,
          'subject'  : subject_guid
        },

        evalScripts : true,

        onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript)
        {
             Elements.from(responseHTML).inject($('yn-group-activity-feed'));
             en4.core.runonce.trigger();
             Smoothbox.bind($('yn-group-activity-feed'));
        }
     });
     group_feed_request.send();
     }

     if( next_id > 0 && !end_of_feed ) {
        $('yn_group_feed_viewmore').style.display = '';
        $('yn_group_feed_loading').style.display = 'none';
        $('yn_group_feed_viewmore_link').removeEvents('click').addEvent('click', function(event){
          event.stop();
          group_activity_viewmore(next_id, subject_guid);
        });
     }
     else
     {
        $('yn_group_feed_viewmore').style.display = 'none';
        $('yn_group_feed_loading').style.display = 'none';
     }
     });
</script>
<?php endif;?>
<?php if( empty($this->group_actions) ) {
              return;
            }
            else $actions = $this->group_actions;
      ?>
<?php foreach( $actions as $action ):?>
     <li id="activity-item-<?php echo $action->action_id ?>">
            <!-- User's profile photo -->
            <div class='feed_item_photo'>
             <?php echo $this->htmlLink($action->getSubject()->getHref(),
                        $this->itemPhoto($action->getSubject(), 'thumb.icon', $action->getSubject()->getTitle()));?>
            </div>
            <!-- Feed Item Body -->
            <div class='feed_item_body'>
              <!-- Main Content -->
                  <span class="<?php echo ( empty($action->getTypeInfo()->is_generated) ? 'feed_item_posted' : 'feed_item_generated' ) ?>">
                    <?php echo $action->getContent()?>
                  </span>

            <!-- Attachments -->
              <?php if( $action->getTypeInfo()->attachable && $action->attachment_count > 0 ): // Attachments ?>
                <div class='feed_item_attachments'>
                  <?php if( $action->attachment_count > 0 && count($action->getAttachments()) > 0 ): ?>
                    <?php if( count($action->getAttachments()) == 1 &&
                            null != ( $richContent = current($action->getAttachments())->item->getRichContent()) ): ?>
                      <?php echo $richContent; ?>
                    <?php else: ?>
                      <?php foreach( $action->getAttachments() as $attachment ): ?>
                        <span class='feed_attachment_<?php echo $attachment->meta->type ?>'>
                        <?php if( $attachment->meta->mode == 0 ): // Silence ?>
                        <?php elseif( $attachment->meta->mode == 1 ): // Thumb/text/title type actions ?>
                          <div>
                            <?php
                              if ($attachment->item->getType() == "core_link")
                              {
                                $attribs = Array('target'=>'_blank');
                              }
                              else
                              {
                                $attribs = Array();
                              }
                            ?>
                            <?php if( $attachment->item->getPhotoUrl() ): ?>
                              <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), $attribs) ?>
                            <?php endif; ?>
                            <div>
                              <div class='feed_item_link_title'>
                                <?php
                                  echo $this->htmlLink($attachment->item->getHref(), $attachment->item->getTitle() ? $attachment->item->getTitle() : '', $attribs);
                                ?>
                              </div>
                              <div class='feed_item_link_desc'>
                                <?php echo $this->viewMore($attachment->item->getDescription()) ?>
                              </div>
                            </div>
                          </div>
                        <?php elseif( $attachment->meta->mode == 2 ): // Thumb only type actions ?>
                          <div class="feed_attachment_photo">
                            <?php echo $this->htmlLink($attachment->item->getHref(), $this->itemPhoto($attachment->item, 'thumb.normal', $attachment->item->getTitle()), array('class' => 'feed_item_thumb')) ?>
                          </div>
                        <?php elseif( $attachment->meta->mode == 3 ): // Description only type actions ?>
                          <?php echo $this->viewMore($attachment->item->getDescription()); ?>
                        <?php elseif( $attachment->meta->mode == 4 ): // Multi collectible thingy (@todo) ?>
                        <?php endif; ?>
                        </span>
                      <?php endforeach; ?>
                      <?php endif; ?>
                  <?php endif; ?>
                </div>
              <?php endif; ?>

            <!-- Icon, time since -->
            <?php
              $icon_type = 'activity_icon_'.$action->type;
              list($attachment) = $action->getAttachments();
              if( is_object($attachment) && $action->attachment_count > 0 && $attachment->item )
              {
                $icon_type .= ' item_icon_'.$attachment->item->getType() . ' ';
              }
           ?>
             <div class='feed_item_date feed_item_icon <?php echo $icon_type ?>'>
                <ul>
                    <li>
                      <?php echo $this->timestamp($action->getTimeValue()) ?>
                    </li>
                </ul>
             </div>
          </div>
     </li>
<?php endforeach;?>
