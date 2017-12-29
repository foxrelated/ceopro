<?php 
	$db = Engine_Db_Table::getDefaultAdapter();
	$select = "SELECT * FROM engine4_core_modules WHERE name = 'ynresponsive1'";
	$module = $db->fetchRow($select);
	
	if($module['enabled']){		
		if(!Engine_Api::_()->ynresponsive1()->isMobile()){
			$this->headLink()
          ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Ultimatenews/externals/styles/Tooltip.css');
		}		
	}
	else{
		$this->headLink()
          ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Ultimatenews/externals/styles/Tooltip.css');
	}
?>

<script type="text/javascript">
    var pageAction =function(page){
        document.getElementById('nextpage').value = page;
        document.getElementById('gotoPage').submit();
    }
</script>
<div class='layout_middle'>
    <form name="gotoPage" id="gotoPage" method="post">
        <input type="hidden" name="nextpage" id="nextpage">
        <span style="display:none;">
        	<input type="text" name="categoryparent" id="categoryparent" value="<?php if (isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['category_parent'];?>" />
            <input type="text" name="category" id="category" value="<?php if (isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['category'];?>" />
            <input type="text" name="search" id="search" value="<?php if (isset($_SESSION['keysearch'])) echo $_SESSION['keysearch']['keyword'];?>" />
        </span>
        <div style="overflow: hidden;">	 
            <?php if ($this->paginator->getTotalItemCount() > 0):?>
                <ul class="blogs_browse ultimate_news">
                	<div>
	                	<div>
		                    <?php
							$feed_before = 0;
							$count = 0;
		                    foreach ($this->paginator as $item):
							$item_news = Engine_Api::_()->getItem('ultimatenews_content', $item['content_id']);
		                    $feed_id = $item_news->category_id; $count ++; ?>      	
		                    <?php if($feed_before != $feed_id):
		                    	$count = 0;?>
			                    </div>	
		                        <div style="clear:both"></div>
		                        </div>	
		                    	<h3>
		                    		
		                            <div>
		                            <?php echo $item['feed_name'];?>
		                            </div>
		                            
		                    	</h3>
		                    <div class="feed_group">
		                    <div class="feed_left">
		                    <?php $feed_before = $feed_id; endif;
		                    if($count < $this->wide):?>
		                        <li>          
		                            <div>
		                                <p class='blogs_browse_info_blurb clearfix'>
		                                    <?php
		                                    if ($item->image != "") {
												$img_path = $item->image;
												if($item->photo_id)
												{
													$img_path = $item->getPhotoUrl();
												}
					                        	echo("<img src='" . $img_path . "' align='left'/>");
											}
											else {
												echo '<img align="left" src="./application/modules/Ultimatenews/externals/images/news.png" alt=""/> ';
											}
											?>
											<p class="blogs_browse_info_title">
											<?php echo $this->htmlLink($item_news->getHref(), $item_news->title, array('target' => '_parent')); ?>
		                                   </p>
		                                   


		                                    <?php
		                                    $content = $item_news->description ? $item_news->description : $item_news->content;
		                                    echo $this->feedDescription($content, 200);
		                                    ?>
		                                </p>
		                                <div style="clear:both"></div>
		                                <p class="view_more">
		                                    <?php
		                                    $total_coment = $item['total_comment'];
		                                    if ($item['resource_id'] == NULL)
		                                        $total_coment--;
		                                    ?>
		                                    <span class="total_comment"><?php echo '' . $this->htmlLink($item_news->getHref(), $this->translate('Comments'), array('target' => '_parent')) . '<font>:&nbsp;' . $total_coment . '&nbsp;&nbsp;</font> ';?></span>
		                                    <a href="<?php echo($item_news->getHref());?>" target="_blank" ><?php echo $this->translate('View more') . '...';?></a>
		                                </p>
		                            </div>
		                        </li>
		                        <?php else:
			                        if($count == $this->wide):?>
			                   </div>
			                        <div class="feed_right">
			                        <?php endif;
			                        if($count < $this->wide + $this->narrow):?>
			                        <div class="sub_new">
			                        	<a target="_parent" class="yntooltip" href="<?php echo $item_news->getHref()?>">
			                        		<?php echo $item_news->title;?>
			                        		<span>
								               <div class="blogs_browse_info">
								                    <div class = "blogs_browse_info_blurb">
								                        <?php
					                                    if ($item->image != "") {
															$img_path = $item->image;
															if($item->photo_id)
															{
																$img_path = $item->getPhotoUrl();
															}
								                        	echo("<img src='" . $img_path . "' align='left'/>");
														}
														else {
															echo '<img align="left" src="./application/modules/Ultimatenews/externals/images/news.png" alt=""/> ';
														}
														?>
								                    </div>
								                    <div class ="description_content">                   
								                       <?php
					                                    $content = $item_news->description ? $item_news->description : $item_news->content;
					                                    echo $this->feedDescription($content, 200);
					                                    ?>
								                    </div>        
								            </span>          
								        </div>
			                        	</a>
										<p class='blogs_browse_info_date'>
		                                    <?php echo $this->translate('Posted');?>	               
		                                    <?php
		                                        echo($item_news->pubDate_parse);
											?>
										</p>
			                        </div>
		                        	<?php endif;?>
		                       <?php endif;?>
		                    <?php endforeach;?>
	                </div>	
	                <div style="clear:both"></div>
                </div>	
                </ul>

            <?php else:?>
                <div class="tip" style="margin-top: 10px;">
                    <span>
                         <?php echo $this->translate('No news with that criteria') ?>   
                    </span>
                </div>
            <?php endif;?>
            <br /><br />
            <?php //echo $this->paginationControl($this->paginator, null, array("pagination/ultimatenewspagination.tpl", "ultimatenews"));?>
        </div>
    </form>
</div>