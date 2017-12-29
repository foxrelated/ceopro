<?php
$this->headLink()
->prependStylesheet($this->baseUrl() . '/application/css.php?request=application/modules/Ultimatenews/externals/styles/ultimatenewsfeed.css')
        ->prependStylesheet($this->baseUrl().'/application/css.php?request=application/modules/Ultimatenews/externals/styles/slideshow.css');
$this->headScript()  
       ->appendFile($this->baseUrl() . '/application/modules/Ultimatenews/externals/scripts/slideshow/Navigation.js')
	   ->appendFile($this->baseUrl() . '/application/modules/Ultimatenews/externals/scripts/slideshow/Loop.js')
	   ->appendFile($this->baseUrl() . '/application/modules/Ultimatenews/externals/scripts/slideshow/SlideShow.js');
?>

<section id="news_navigation" class="demo">
	<div id="news_navigation-slideshow" class="slideshow">
        <?php
        $i = 0;
        foreach ($this->featuredultimatenews as $item) :
        $content = $item->description ? $item->description : $item->content;
        $i ++;
        ?>
        <span class="panel clearfix">
            <div class="wrapper">
                <div id="ultimatenews_list">
                    <div class="row_title">
						<?php if($item->logo_icon != "" && $item->logo_icon):?>
                            <p><img height="16px" width="16px" src="<?php echo $item->logo_icon?>" /></p>
                        <?php else: ?>
                            <i class="ynicon yn-rss-circle"></i>
                        <?php endif;?>
                        <h4> <?php echo $this->htmlLink($item->getHref(), $this->string() -> truncate($item->title, 50), array('target' => '_parent', 'title' => $item->title))?></h4>
                    </div>

                    <div class="blog_content">
                        <div class = "image_content">
                        	<?php
                    			if ($item->image != ""):
									$img_path = $item->image;
									if($item->photo_id)
									{
										$img_path = $item->getPhotoUrl();
									}
			                        echo("<img src='" . $img_path . "' />"); 
			                    ?>
                                <?php else:?>
                                    <?php
										echo '<img src="./application/modules/Ultimatenews/externals/images/news.png" alt=""/> ';
                                ?>
                            <?php endif;?>
                        </div>
                        <div class = "description_content">
                            <div class = "description" style=""> <?php echo $this->feedDescription($content);?></div>
                        </div>
						<div>
                            <div  class="datetime" ><?php echo $this->translate('Posted') . " " . $item->pubDate_parse;?></div>
                        </div>
                    </div>
                </div>
            </div>
        </span>
	    <?php endforeach;?>
	    <ul class="news_pagination" id="news_pagination">
			<li><a class="current" href="#lp1"></a></li>
			<?php for ($j = 2; $j <= $i; $j ++):?>
			<li><a href="#lp<?php echo $j?>"></a></li>
			<?php endfor;?>
		</ul>
    </div>
</section>

<div style="clear:both"></div>
<?php

function catch_that_image(&$des)
{
    $matches = '';
    preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $des, $matches);
    $first_img = @$matches [1] [0];

    if (empty($first_img))
    { //Defines a default image
        return '';
    }
    return $first_img;
}
?>
