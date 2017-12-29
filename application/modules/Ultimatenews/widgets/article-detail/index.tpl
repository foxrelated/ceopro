<script type="text/javascript">
  var tagAction =function(tag, tag_name)
  {
        window.location = en4.core.baseUrl + 'news/tag/' + tag + '/' + tag_name;
  }
  function favourite_news()
   {
   	   var url = '<?php echo $this -> url(array('action' => 'favorite-ajax'), 'ultimatenews_general', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'content_id' : <?php echo $this->content->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink icon_ultimatenews_unfavourite" href="javascript:;" onclick="unfavourite_news()">' + '<?php echo $this->translate("Unfavorite")?>' + '</a>';
            }
        });
        request.send();
   }
   function unfavourite_news()
   {
   	   var url = '<?php echo $this -> url(array('action' => 'un-favorite-ajax'), 'ultimatenews_general', true)?>';
       var request = new Request.JSON({
            'method' : 'post',
            'url' :  url,
            'data' : {
                'content_id' : <?php echo $this->content->getIdentity()?>
            },
            'onComplete':function(responseObject)
            {
                obj = document.getElementById('favourite_id');
                obj.innerHTML = '<a class = "buttonlink icon_ultimatenews_favourite" href="javascript:;" onclick="favourite_news()">' + '<?php echo $this->translate("Favorite")?>' + '</a>';
            }
        });
        request.send();
   }
</script>
<?php
	if(Engine_Api::_() -> hasModuleBootstrap('ynresponsive1')){
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

<?php $category = $this->category;
if($category['logo'] != "" && $category['mini_logo']):?>
    <p class="ultimatenews_mini_logo"><img src="<?php echo $category['logo']?>" /></p>
<?php else: ?>
	<p><span class="ynicon yn-rss-circle"></span></p>
<?php endif;?>
<h3>
	<?php if ($this->content->link_detail) : ?>
		<a target="_blank" href="<?php echo $this->content->link_detail;?>"><?php echo $this -> content -> title;?></a>
	<?php else : ?>
		<?php echo $this -> content -> title;?>
	<?php endif; ?>
</h3>

<div class="ultimatenews_header">
	<p class="blog_entrylist_entry_date">
		<?php $category = $this->category;
		if($category['category_parent_id'] > 0):
		$cat = Engine_Api::_()->getItem('ultimatenews_categoryparent',$category['category_parent_id']);
		if($cat):?>
			<a href="<?php echo $this->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>$cat->category_id ),'ultimatenews_categoryparent')?>">
	            <?php echo $this->translate($cat->category_name); ?> &#187;
	         </a>
	    <?php endif; else:?>
	    	<a  href="<?php echo $this->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=> 0),'ultimatenews_categoryparent')?>">
	        <?php echo $this->translate('Other');?> &#187;
	        </a>
		<?php endif;?>

		<?php if(isset($category['category_id'])) : ?>
			<a href="<?php echo $this->url(array('controller' => 'index', 'action'=>'feed', 'category'=> $category['category_id']),'ultimatenews_feed')?>">
			<?php echo $category['category_name'];?> &#187;
			</a>
		<?php endif;?>

		<?php if ($this->content->link_detail) : ?>
			<a target="_blank" href="<?php echo $this->content->link_detail;?>"><?php echo $this -> content -> title;?></a>
		<?php else : ?>
			<?php echo $this -> content -> title;?>
		<?php endif; ?>

	</p>
	<p class="blog_entrylist_entry_date">
		<?php echo $this -> translate('Posted date').": ";
			if ($this -> content -> pubDate_parse)
	        	echo $this -> content -> pubDate_parse;
	        else
	        	echo $this -> content -> posted_date;
		?>
	</p>
</div>
<div>
	<div class="ultimatenews_favorite" id = "favourite_id">
		<?php if(!$this -> content -> checkFavourite()):?>
			<a class="buttonlink icon_ultimatenews_favourite" href="javascript:;" onclick="favourite_news()"><?php echo $this -> translate("Favourite") ?></a>
		<?php else:?>
			<a class = 'buttonlink icon_ultimatenews_unfavourite' href="javascript:;" onclick="unfavourite_news()"><?php echo $this->translate('Unfavourite')?></a>
		<?php endif;?>
	</div>
	<span>
		<?php echo $this->htmlLink(array(
			'module'=>'activity',
			'controller'=>'index',
			'action'=>'share',
			'route'=>'default',
			'type'=>'ultimatenews_content',
			'id' => $this->content->getIdentity(),
			'format' => 'smoothbox'
		), $this->translate("Share"), array('class' => 'icon_ultimatenews_share buttonlink smoothbox')); ?>
	</span>
</div>
<div class="clear"></div>

<div class="blog_entrylist_entry_body">
	<strong class = "ultimate_news_description"><?php echo $this -> content -> description; ?></strong>
    <?php echo $this -> content -> content; ?>
	<div style="clear:both"></div>
	<a href="<?php echo($this->content->link_detail);?>" target="_blank" >
		<?php if ($this->category || $this->category[0]['category_logo'] == "") :?>
			<?php echo $this->translate("")?>
		<?php else:?><?php echo "<img src='". $this->category[0]['category_logo']."'  alt=''/>"
	?> <?php endif;?></a>
	<div style="clear:both"></div>
	<?php if ($this->content->link_detail) : ?>
	<p class="">
		<a href="<?php echo($this->content->link_detail);?>" target="_blank" ><?php echo $this -> translate('Original Link') . '...';?></a>
	</p>
	<?php endif; ?>
	<br/>
	<?php echo '<span class="ynulnews_tag_name">'.$this->translate("Tags: ").'</span>';
		if(count($this->tags) > 0):
			$i = 0;
			foreach($this->tags as $tag):
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
				<a  href='javascript:void(0);' onclick="javascript:tagAction(<?php echo $tag->tag_id; ?>, '<?php echo $tag_name; ?>');" title = "<?php echo $tag->text?>"><?php echo $tag->text?></a><?php if($i != count($this -> tags) && $tag_name) echo ', '?>
			<?php endforeach;
		else:
			echo $this->translate("None");
	endif; ?>
	<div style="clear:both"></div>
</div>
<div class="ultimatenews_clear_line" style="clear:both"></div>
<div class="generic_layout_container layout_core_comments">
	<?php  echo $this -> action("lists", "index", "ultimatenews", array());
?>
</div>
<div id="share" class="clearfix">
	<span class="ynulnews_report"><?php echo $this -> htmlLink(Array(
        'module' => 'core',
        'controller' => 'report',
        'action' => 'create',
        'route' => 'default',
        'subject' => "ultimatenews_content_" . $this -> content -> getIdentity(),
        'format' => 'smoothbox'
    ), '<span class="ynicon yn-flag-alt"></span>'.$this -> translate("Report"), array('class' => 'smoothbox buttonlink'));
		?></span>
	<span class="ynulnews_print" onclick="printpage();"><?php echo '<span class="ynicon yn-print"></span>'.$this->translate("Print")?></span>
	<span class="addthis_toolbox addthis_default_style">
	   <?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.buttons', '<!-- Go to www.addthis.com/dashboard to customize your tools --> <div class="addthis_sharing_toolbox"></div>'); ?> 
	</span>
    <script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=<?php echo Engine_Api::_() -> getApi('settings', 'core') -> getSetting('yncore.addthis.pub', 'younet');?>"></script> 
 <!-- Add-This Button END -->
</div>
<script type="text/javascript">
	function printpage() {
		window.print();
	}

	function clickPostComment() {
		var div = document.getElementById('comments');

		if(div) {
			var tagA = div.getElementsByTagName('a')[0];
			if(tagA) {
				tagA.onclick();
			}
		}
	}

	var categoryAction = function(category) {
		$('category').value = category;
		$('filter_form').submit();
	}
	var dateAction = function(start_date, end_date) {
		$('start_date').value = start_date;
		$('end_date').value = end_date;
		$('filter_form').submit();
	}

	window.addEvent('domready', function() {
		$('comment-form').style.display = '';
		$$('.ultimatenews_entrylist_entry_body a').each(function(a) {
			a.setAttribute('target', '_default');
		});
	});

</script>

<?php if(count($this->relatedNews)>0): ?>
<br />
<h5 class="title_read_on"><?php echo $this->translate("Read On:");?></h5>

<div class="related_news_block">
	<?php foreach($this->relatedNews as $new):?>
		<div class="sub_new">
			<span class="ynicon yn-bars"></span>
			<a target="_parent" class="yntooltip" href="<?php echo $new->getHref()?>">	<?php echo $new->getTitle();?>
				<span>
				   <div class="blogs_browse_info">
						<div class = "blogs_browse_info_blurb">
							<?php
							if ($new->image != "")
							{
								$img_path = $new->image;
								if($new->photo_id)
								{
									$img_path = $new->getPhotoUrl();
								}
								echo("<img src='" . $img_path . "' align='left'/>");
							}
							else {
								echo '<img src="./application/modules/Ultimatenews/externals/images/news.png" alt=""/> ';
							}
							?>
						</div>
						<div class ="description_content">
						   <?php
								$content = $new->description ? $new->description : $new->content;
								echo $this->feedDescription($content, 200);
							?>
						</div>
					</div>
				</span>
			</a>
			<p class='blogs_browse_info_date'>
	        <?php echo $this->translate('Posted');?>
	        <?php
	            echo($new->pubDate_parse);
			?>
			</p>
	    </div>
	<?php endforeach;?>

</div>

<?php endif;?>

