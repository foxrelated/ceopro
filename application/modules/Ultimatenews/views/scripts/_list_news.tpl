<ul class="blogs_browse">
    <?php if($this -> tag)
	{
		$t_table = Engine_Api::_()->getDbtable('tags', 'core');
		$tm_table = Engine_Api::_()->getDbtable('tagMaps', 'core');
		$tName = $t_table->info('name');
		$tmName = $tm_table->info('name');
	}
    foreach ($this->paginator as $item):?>      	
        <li style="clear: both;">          
            <div class='ultimatenews_feed_browse_info'>
            	<?php if($item->logo_icon != "" && $item->mini_logo):?>
                    <p><img src="<?php echo $item->logo_icon?>" /></p>
                <?php else:?>
                    <p><span class="ynicon yn-rss-circle"></span></p>
                <?php endif;?>
                <p class='blogs_browse_info_title'>
                <h4><?php echo $this->htmlLink($item->getHref(), $item->title, array('target' => '_parent'))?></h4>
                </p>
                <p class='blogs_browse_info_date'>
                    <?php echo $this->translate('Posted');?>	               
                    <?php
                        echo($item->pubDate_parse. " " . $this->translate('by') . ": " . Engine_Api::_()->getItem('user',$item->owner_id));
                    ?>               
                </p>
                <?php if($this -> favorite):?>
                	<p>
                        <?php echo $this->htmlLink(array(
				              'action' => 'un-favorite',
				              'content_id' => $item->getIdentity(),
				              'route' => 'ultimatenews_general',
				              'reset' => true,
				            ), $this->translate('Unfavourite'), array(
				              'class' => 'buttonlink smoothbox icon_ultimatenews_unfavourite',
				            )) ?>
                        </p>
                  <?php endif;?>               
                <p class='blogs_browse_info_blurb'>
                    <?php
                    if ($item->image != ""){
						$img_path = $item->image;
						if($item->photo_id)
						{
							$img_path = $item->getPhotoUrl();
						}
						echo("<img src='" . $img_path . "' align='left'  style='padding-right:5px;' />");
					}
					
                    $content = $item->description ? $item->description : $item->content;
                    echo $this->feedDescription($content, 2000);
                    ?>
                </p>
                <?php if($this -> tag):?>
                	<p class="tag_name">
						<?php 
						//Get tags
						$filter_select = $tm_table->select()->from($tmName,"$tmName.*")
							->setIntegrityCheck(false)
							->where("$tmName.resource_id = ?",$item->getIdentity());
							
						$select = $t_table->select()->from($tName,array("$tName.*","Count($tName.tag_id) as count"));
						$select->joinLeft($filter_select, "t.tag_id = $tName.tag_id",'');
						$select  ->order("$tName.text");
						$select  ->group("$tName.text");
						$select  ->where("t.resource_type = ?","ultimatenews_content");
						$tags = $t_table->fetchAll($select);
						echo $this->translate("Tags: ");
							if(count($tags) > 0):
								$i = 0;
								foreach($tags as $tag): 
									$tag_name = $tag -> text;
									$search  = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ');
									$replace = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
									$str = str_replace($search, $replace, $tag_name);
									
									$tag_name = preg_replace('/([a-z])([A-Z])/', '$1 $2', $tag_name);
									$tag_name = strtolower($str);
									$tag_name = preg_replace('/[^a-z0-9-]+/i', '-', $tag_name);
									$tag_name = preg_replace('/-+/', '-', $tag_name);
									$tag_name = trim($tag_name, '-');
									$i ++;
								?>
									<a  href='javascript:void(0);' onclick="javascript:tagAction(<?php echo $tag->tag_id; ?>, '<?php echo $tag_name; ?>');" title = "<?php echo $tag->text?>"><?php echo $tag->text?></a><?php if($i != count($tags) && $tag_name) echo ', '?>
								<?php endforeach;
							else:
								echo $this->translate("None");
						endif; ?> 
					</p>
                <?php endif;?>
                <div style="clear:both"></div>
                <a href="<?php echo($item->link_detail);?>" >
                	<?php if ($item->logo != "" && $item->display_logo == 1) :?> 
                		<?php echo "<img src='" . $item->logo . "'  alt=''/>"?> 
                	<?php endif;?>  
                </a>

                <p class="view_more">
                    <?php
                    $total_coment = $item->total_comment;
                    if ($item->resource_id == NULL)
                        $total_coment--;
                    ?>
                    <span class="total_comment"><?php echo '' . $this->htmlLink($item->getHref(), $this->translate('Comments'), array('target' => '_parent')) . '<font style="font-weight: bold;">:&nbsp;' . $total_coment . '&nbsp;&nbsp;</font> ';?></span>
                    <a href="<?php echo($item->getHref());?>" target="_blank" ><?php echo $this->translate('View more') . '...';?></a>
                </p>
            </div>
        </li>
    <?php endforeach;?>
</ul>