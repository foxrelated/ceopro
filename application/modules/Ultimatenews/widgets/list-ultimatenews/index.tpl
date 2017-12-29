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
		                    <?php foreach ($this->paginator as $cateItem): ?>
		                    	<h3>
		                    		<?php if($cateItem->logo!= "" && $cateItem->logo):?>
										<img class="ultimate_news-logo" src="<?php echo $cateItem->logo?>" />
		                            <?php else:?>
										<span class="ynicon yn-rss-circle"></span>
		                            <?php endif;?>
		                            <a class="ynulnews_title" href="<?php echo $this->url(array('controller' => 'index', 'action'=>'feed', 'category'=> $cateItem->category_id),'ultimatenews_feed')?>">
		                            <?php echo $cateItem->category_name;?>
		                            </a>
		                            
		                            <?php if($this->subscribe):?>
		                            	
		                                <a class="link_subscribe smoothbox" href="<?php echo $this->url(array('action'=>$cateItem->isSubscribe(),'feed' => $cateItem->getIdentity()), 'ultimatenews_extended')?>" > 
		                                		<?php echo $this->translate(ucfirst($cateItem->isSubscribe()))?> 
		                                </a>
		                            <?php endif;?>	
		                            
		                            <?php if ($cateItem->category_logo != "" || $cateItem->category_logo == 1) :?>
		                              	 <a class="ynulnews_logo" href="<?php echo($cateItem->url_resource);?>" > 
		                                		<?php echo "<img src='" . $cateItem->category_logo . "'  alt=''/>"?> 
		                                </a>
		                            <?php endif;?> 
		                             
		                    	</h3>
		                    	<?php
		                    	$param = (isset($_SESSION['keysearch'])) ? (array(
		                    			 	'category_id' => $_SESSION['keysearch']['category'],
								            'search' => $_SESSION['keysearch']['searchText'],
											'category_parent' => $_SESSION['keysearch']['category_parent'],
								            'page' => $_SESSION['keysearch']['nextpage'],
								            'start_date' => $_SESSION['start_date'],
								            'end_date' => $_SESSION['end_date'],
								            'getcommment' => true,
											'limit' => $this->wide + $this->narrow
								)) : (array(
											'limit' => $this->wide + $this->narrow
									)) ;
								$param['order'] = 'pubDate';
								$param['direction'] = 'DESC';
								$param['getcommment'] = TRUE;
								$param['approved'] = 1;
		                    	$contents = $cateItem->getTopContents($param);
		                    	?>
								<div class="feed_group">
									<div class="feed_left">
										<?php $count  = count($contents);
										if($count < $this-> wide)
										{
											$this -> wide = $count;
										}
										for ($i = 0; $i < $this-> wide; $i++): ?>
											<?php $item_news = $contents[$i]; ?>
											<?php if (is_object($item_news)): ?>
											<li>
											<div>
				                                <p class='blogs_browse_info_blurb'>
				                                    <?php
				                                    if ($item_news->image != "")
													{
														$img_path = $item_news->image;
														if($item_news->photo_id)
														{
															$img_path = $item_news->getPhotoUrl();
														}
				                                        echo("<img width = '145px' src='" . $img_path . "'  style='padding-right:10px;' />");
													}
													else {
														echo '<img width = "145px" align="left" src="./application/modules/Ultimatenews/externals/images/news.png" style="padding-right:10px;" alt=""/> ';
													}
													?>
													<p class="blogs_browse_info_title">
													<?php echo $this->htmlLink($item_news->getHref(), $item_news->title, array('target' => '_parent')); ?>
				                                   	</p>
				                                   	<p class='blogs_browse_info_date'>
				                                    <?php echo $this->translate('Posted');?>	               
				                                    <?php
				                                        echo($item_news->pubDate_parse);//. " " . $this->translate('by') . ": " . Engine_Api::_()->getItem('user',$item->owner_id));
				                                    ?>               
				                               		</p>
				                                    <?php
				                                    $content = $item_news->description ? $item_news->description : $item_news->content;
				                                    echo $this->feedDescription($content, 200);
				                                    ?>
				                                </p>
				                                <div style="clear:both"></div>
				                                <p class="view_more">
				                                    <?php
				                                    $total_coment = $item_news->total_comment;
				                                    if ($item_news->resource_id == NULL)
				                                        $total_coment--;
				                                    ?>
				                                    <span class="total_comment">
				                                    	<?php echo '' . $this->htmlLink($item_news->getHref(), $this->translate('Comments'), array('target' => '_parent')) . '<font style="font-weight: bold;">:&nbsp;' . $total_coment . '&nbsp;&nbsp;</font> ';?>
				                                    </span>
				                                    <a href="<?php echo($item_news->getHref());?>" target="_blank" ><?php echo $this->translate('View more') . '...';?></a>
				                                </p>
			                            	</div>
			                            </li>
			                            <?php endif; ?>
									<?php endfor; ?>
									</div>
									<div class="feed_right">
										<?php 
										$count  = count($contents);
										$narrow = $this->wide + $this->narrow;
										if($count < $this->wide + $this->narrow)
										{
											$narrow = $count;
										}
										
										for($j = $this->wide; $j < $narrow; $j++): ?>
											<?php $item_news = $contents[$j]; ?>
											<?php if (is_object($item_news)): ?>
											
												<div class="sub_new">
						                        	<a target="_parent" class="yntooltip" href="<?php echo $item_news->getHref()?>">
						                        		<?php echo $item_news->title;?>
						                        		<span>
											               <div class="blogs_browse_info">
											                    <div class = "blogs_browse_info_blurb">
											                        <?php
											                        if ($item_news->image != "")
																	{
																		$img_path = $item_news->image;
																		if($item_news->photo_id)
																		{
																			$img_path = $item_news->getPhotoUrl();
																		}
								                                        echo("<img width = '100' src='" . $img_path . "' align='left'  style='padding-right:10px;' />");
																	}
																	else {
																		echo '<img width = "100" align="left" src="./application/modules/Ultimatenews/externals/images/news.png" style="padding-right:10px;" alt=""/> ';
																	}
																	?>
											                    </div>
											                    <div class ="description_content" style="color: #fff">                   
											                       <?php
									                                    $content = $item_news->description ? $item_news->description : $item_news->content;
									                                    echo $this->feedDescription($content, 200);
								                                    ?>
											                    </div>        
															</div>
											            </span>          
											        
						                        	</a>
													<p class='blogs_browse_info_date'>
				                                    <?php echo $this->translate('Posted');?>	               
				                                    <?php
				                                        echo($item_news->pubDate_parse);
													?>
													</p>
						                        </div>
						             		<?php endif; ?>
										<?php endfor; ?>
									</div>
									<div style="clear:both"></div>
								</div>
		                    	
		                    <?php endforeach; ?>
						</div>	
                        <div style="clear:both"></div>
					</div>	
                </ul>
            <?php else:?>
                <div class="tip" style="margin-top: 10px;">
                    <span>
                         <?php echo $this->translate('No news to display') ?>   
                    </span>
                </div>
            <?php endif;?>
            <br /><br />
            <?php echo $this->paginationControl($this->paginator, null, array("pagination/ultimatenewspagination.tpl", "ultimatenews"));?>
        </div>
    </form>
</div>