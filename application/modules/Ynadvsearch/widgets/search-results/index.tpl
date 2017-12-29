<div id="ynadvsearch_loading" class="ynadvsearch_loading" style="display: none">
    <img src='application/modules/Ynadvsearch/externals/images/loading.gif'/>
</div>
<ul class = "ynadvsearch_searchresult" id="ynadvsearch_searchresults">
<?php if($this->noSearchQuery): ?>
<li>
  <div class="tip">
    <span>
      <?php echo $this->translate('Please enter a search query.') ?>
    </span>
  </div>
  </li>
<?php elseif( $this->noResult): ?>
  <li>
  <div class="tip">
    <span>
      <?php echo $this->translate('No results were found.') ?>
    </span>
  </div>
  </li>
<?php else: ?>


   <?php
   if ($this->mod && $this->mod != '') {
        $mod = $this->mod;
   }
   else {
        $mod = '';
   }
   $searchModuleTable = new Ynadvsearch_Model_DbTable_Modules;
   foreach( $this->results as $module => $res ):
   ?>
   <li>
   <div class="ynadvsearch-result" id = "result_<?php echo $module?>">
   <?php
     if ($mod != $module) {
        $mod = $module;
     }

    if (!isset($this->isViewMore)) {
        $title = $searchModuleTable->getModuleTitleByName($module);
            echo "<div class='ybo_headline'><h3 class='ynadvsearch-title ybo_headline'>" . strtoupper($this->translate($title)) . "</h3></div>";
    }

   $count = 0;
   echo "<div class='ynadvsearch-result-items ynadvsearch-clearfix'>";
   foreach ($res as $item) :
   $count++;
   $noPermission = false;
   $icon = 'application/modules/Ynadvsearch/externals/images/lock.png';
   $warning_title = '';
   $warning_description = '';
   $href_private = 'javascript:;';
   $warning_title = $this->highlightText($item->title, $this->query);
   if (!Engine_Api::_()->getApi('search', 'ynadvsearch')->checkItemPermission($item)) 
   {
       $noPermission = true;
	   $warning_description = $this -> translate("Private item");
	   $icon = 'application/modules/Ynadvsearch/externals/images/lock.png';
   }
   if ($count > 10) {
       continue;
   }
   $type = $item->type;
   $item = $this->item($item->type, $item->id);
   if(!$item)
   {
       continue;
   }
   else 
   {
       $href_private = $item -> getHref();
   }
   
   ?>
    <div class="ynadvsearch-result-item ynadvsearch-clearfix">
    <?php if($noPermission):?>
      <div class="ynadvsearch-result-item-photo">
      	<a href="<?php echo $href_private?>">
      		<img src="<?php echo $icon?>"  class="thumb.icon"/>
      	</a>
      </div>
      <div class="ynadvsearch-result-item-info">
        <a href="<?php echo $href_private?>" class="search_title"><?php echo $warning_title;?></a>
        <div class="search_description">
        	<?php echo $warning_description;?>
        </div>
      </div>
    <?php else:?>
      <div class="ynadvsearch-result-item-photo">
        <?php echo $this->htmlLink($item->getHref(), $this->itemPhoto($item, 'thumb.icon')) ?>
      </div>
      <div class="ynadvsearch-result-item-info">
        <?php
        if( '' != $this->query ):
            echo $this->htmlLink($item->getHref(), $this->highlightText($item->getTitle(), $this->query), array('class' => 'search_title'));
            else:
            echo  $this->htmlLink($item->getHref(), $item->getTitle(), array('class' => 'search_title'));
          ?>
        <?php endif; ?>
        <div class="search_description">
          <?php
          if( $module != "news" && $module != 'ultimatenews') {
             if ($module == "activity") {
                echo strip_tags(($item->body));
             }
            else {
                echo $this->viewMore(strip_tags($item->getDescription()));
            }

          }
          ?>
        </div>
      </div>
      <?php endif;?>
    </div>
    <?php endforeach;?>
    </div>

    <?php if ($count > $this->limit) : ?>
        <div class = "show_more_result" id = "<?php echo $module?>_show_more_result_<?php echo $this->from?>">
        <a href='javascript:void(0);' onclick="showMore('<?php echo $module?>','<?php echo $this->from?>')"><?php echo $this->translate('Show More Results')?></a>
        </div>
        <div class="show_more_result" id="<?php echo $module?>_result_loading_<?php echo $this->from?>" style="display: none;">
          <img src='<?php echo $this->layout()->staticBaseUrl ?>application/modules/Core/externals/images/loading.gif' style='float:left;margin-right: 5px;' />
          <?php echo $this->translate("Loading ...") ?>
        </div>
<?php endif;?>
 </div>
</li>
  <?php endforeach; ?>
  <?php endif; ?>
</ul>
<script type="text/javascript">
function showMore(module,from){
    var url = '<?php echo $this->url(array('module' => 'core','controller' => 'widget','action' => 'index','name' => 'ynadvsearch.search-results', 'isViewMore' => 1), 'default', true) ?>';
    $(module + '_show_more_result_' + from).destroy();
    $(module + '_result_loading_' + from).style.display = '';
    var limit = '<?php echo $this->limit?>';
    limit = parseInt(limit);
    from = parseInt(from);
    var value = limit + from + 1;
    var request = new Request.HTML({
      url : url,
      data : {
        format : 'html',
        'phrase' : '<?php echo $this->query?>',
        'search' : module,
        'from' : value,
        'type' : 'ajax'
      },
      onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
            $(module + '_result_loading_' + from).destroy();
            var result = Elements.from(responseHTML);
            var results = result.getElement('.ynadvsearch-result-items').getChildren();
            var more_result = result.getElement('#'+ module + '_show_more_result_' + value);
            var loading = result.getElement('#'+ module + '_result_loading_' + value);
            results.each(function(el) {
                el.inject($("result_" + module).getElement('.ynadvsearch-result-items'));
            });
            more_result.inject($("result_" + module));
            loading.inject($("result_" + module));
        }
    });
   request.send();
  }

</script>
