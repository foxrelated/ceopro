<script type="text/javascript">
    var categoryAction = function(category){
        $('category').value = category;
        $('filter_form').submit();
    }
    var tagAction =function(tag){
        $('tag').value = tag;
        $('filter_form').submit();
    }
    var dateAction =function(start_date, end_date){
        $('start_date').value = start_date;
        $('end_date').value = end_date;
        $('filter_form').submit();
    }
</script>
<?php echo $this->content()->renderWidget('ultimatenews.menu-ultimatenews') ?>
<div class='layout_middle'>
    <ul class='blogs_entrylist'>
        <li>
        	<?php if($item->logo_icon != "" && $item->mini_logo):?>
                <p style="float: left; margin-top: 9px; padding-right: 5px"><img src="<?php echo $item->logo_icon?>" /></p> 	
            <?php endif;?>
            <h3>
                <a target="_blank" href="<?php echo $this->content->link_detail?>"><?php echo $this->content->title?></a>
            </h3>
            <div class="blog_entrylist_entry_date">
                <?php echo $this->translate('Posted date');?> 
                <?php
                //if ($this->content->author == null || $this->content->author == "")
                //    echo( date('Y-m-d', $this->content->pubDate) . " " . $this->translate('by  Unknown') );
                //else
                    echo(date('Y-m-d', $this->content->pubDate) . " " . $this->translate('by') . ": " . Engine_Api::_()->getItem('user',$this->content->owner_id));
                ?>               
            </div>
            <div class="blog_entrylist_entry_body">

                <?php
                if ($this->content->content != '')
                {
                    echo $this->content->content;
                }
                else
                {
                    echo $this->content->description;
                }
                ?>
                <div style="clear:both"></div>

                <a href="<?php echo($this->content->link_detail);?>" target="_blank" ><?php if ($this->category[0]['category_logo'] == "") :?> <?php echo $this->translate("")?><?php else:?><?php echo "<img src='" . $this->category[0]['category_logo'] . "'  alt=''/>"?> <?php endif;?>  </a>
                <div style="clear:both"></div>   
                <p class="view_more">
                    <a href="<?php echo($this->content->link_detail);?>" target="_blank" ><?php echo $this->translate('Original Link') . '...';?></a>
                </p>
            </div>
            <div style="clear:both"></div>

        </li>
    </ul>																 
    <?php //echo $this->action("lists", "index", "Ultimatenews", array("type" => "Ultimatenews_content", "id" => $this->content->getIdentity()));?>
    <div id="share" style="margin-top:10px;">
        <span style="padding-bottom:0px;padding-right:10px;"><?php echo $this->htmlLink(Array('module' => 'core', 'controller' => 'report', 'action' => 'create', 'route' => 'default', 'subject' => "Ultimatenews_content_" . $this->content->getIdentity(), 'format' => 'smoothbox'), $this->translate("Report"), array('class' => 'smoothbox'));?></span>
        <span style="width:100px;padding-right:10px;cursor:pointer;background-image: url('application/modules/Ultimatenews/externals/images/print_icon.gif'); background-repeat:no-repeat;"  onclick="printpage();">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span><span class="st_facebook"></span><span class="st_twitter"></span><span class="st_sharethis" displayText="ShareThis"></span>
    </div>
</div>
<script type="text/javascript" src="http://w.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:'1aa52842-757c-4d8b-b509-ad6790dc26e6'});</script>
<script language="Javascript1.2">  
    function printpage() {
        window.print();
    }
</script>

