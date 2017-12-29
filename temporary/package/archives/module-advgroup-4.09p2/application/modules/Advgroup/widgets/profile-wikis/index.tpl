<script type="text/javascript">
        en4.core.runonce.add(function(){
            <?php if (!$this->renderOne): ?>
                var anchor = $('advgroup_profile_wikis').getParent();
                $('advgroup_wiki_previous').style.display = '<?php echo ( $this->pages->getCurrentPageNumber() == 1 ? 'none' : '' ) ?>';
                $('advgroup_wiki_next').style.display = '<?php echo ( $this->pages->count() == $this->pages->getCurrentPageNumber() ? 'none' : '' ) ?>';

                $('advgroup_wiki_previous').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->pages->getCurrentPageNumber() - 1) ?>
                        }
                    }), {
                        'element' : anchor
                    })
                });

                $('advgroup_wiki_next').removeEvents('click').addEvent('click', function(){
                    en4.core.request.send(new Request.HTML({
                        url : en4.core.baseUrl + 'widget/index/content_id/' + <?php echo sprintf('%d', $this->identity) ?>,
                        data : {
                            format : 'html',
                            subject : en4.core.subject.guid,
                            page : <?php echo sprintf('%d', $this->pages->getCurrentPageNumber() + 1) ?>
                        }
                    }), {
                        'element' : anchor
                    })
                });
            <?php endif; ?>
        });
    </script>

    <ul class="ynwiki_browse" style="padding-top: 10px;" id="advgroup_profile_wikis">
      <?php foreach( $this->pages as $item ): ?>
        <li>
          <div class='ynwiki_browse_photo'>
            <?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
          </div>
          <div class='ynwiki_browse_info'>
            <p class='ynwiki_browse_info_title'>
              <?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
            </p>
            <p class='ynwiki_browse_info_date'>
              <?php echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target'=>'_top')));?>
              |
              <?php echo $this->timestamp($item->creation_date) ?>
              <?php $revision = $item->getLastUpdated();
              if($revision):  ?>
              |
              <?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id);
             echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?>
             <?php echo $this->timestamp($revision->creation_date) ?>
              (<?php echo $this->htmlLink(array(
                      'action' => 'compare-versions',
                      'pageId' => $item->page_id,
                      'route' => 'ynwiki_general',
                      'reset' => true,
                    ), $this->translate("view change"), array(
                    )) ?>)
               <?php endif;?>
            </p>
            <?php foreach($item->getBreadCrumNode() as $node): ?>
                <?php echo $this->htmlLink($node->getHref(), $node->title) ?>
                &raquo;
                <?php endforeach; ?>
                <?php echo $this->htmlLink($item->getHref(), $item->title) ?>
          </div>
          <p class='ynwiki_browse_info_blurb' style="margin-left: 58px">
              <?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
          </p>
        </li>
      <?php endforeach; ?>
    </ul>

    <div>
        <div id="advgroup_wiki_previous" class="paginator_previous">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Previous'), array(
                'onclick' => '',
                'class' => 'buttonlink icon_previous'
            ));
            ?>
        </div>
        <div id="advgroup_wiki_next" class="paginator_next">
            <?php
            echo $this->htmlLink('javascript:void(0);', $this->translate('Next'), array(
                'onclick' => '',
                'class' => 'buttonlink_right icon_next'
            ));
            ?>
        </div>
        <div class="clear"></div>
    </div>