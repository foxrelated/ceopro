 <div id="print_view" style="text-align: center;">
    <div style="text-align: left;width: 500px; margin: 0 auto;;">  
       <div style="float: left; padding-right: 10px;">
            <?php echo $this->htmlLink($this->page->getOwner()->getHref(), $this->itemPhoto($this->page->getOwner(), 'thumb.icon')) ?>  
       </div>
       <div>
            <h3 style="margin-bottom: 1px;"><?php echo $this->htmlLink($this->page->getHref(), $this->page->title) ?> </h3>
                    <?php foreach($this->page->getBreadCrumNode() as $node): ?>
                        <?php echo $this->htmlLink($node->getHref(), $node->title) ?>
                        &raquo;
                    <?php endforeach; ?>
                    <?php echo $this->htmlLink($this->page->getHref(), $this->page->title) ?>
            <p style="padding-left: 58px;"> 
            <?php $creator =  Engine_Api::_()->getItem('user', $this->page->creator_id);
            echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($creator->getHref(), $creator->getTitle(), array('target'=>'_top')));?> | 
            <?php echo $this->timestamp($this->page->creation_date) ?> 
            <?php $revision = $this->page->getLastUpdated();
            if($revision):  ?>
              |
            <?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id); 
             echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?> 
             <?php echo $this->timestamp($revision->creation_date) ?>
              (<?php echo $this->htmlLink(array(
                      'action' => 'compare-versions',
                      'pageId' => $this->page->page_id,
                      'route' => 'ynwiki_general',
                      'reset' => true,
                    ), $this->translate("view change"), array(
                    )) ?>)
               <?php endif;?>  
            </p>
       </div>
       <br/>
       <div class="ynwiki_content">  
       <?php 
         echo $this->page->body;
       ?>
       </div>
      <?php 
        $menu = $this->partial('_childs.tpl', array('page'=>$this->page));  
        echo $menu;
        ?>
       <br/>
       <button onclick="window.print()"><?php echo $this->translate("Print")?></button>
    </div>
</div>   
<style type="">
.ynwiki_content p           
{ 
    margin: 0 0 0.7em; 
}
.ynwiki_content table       
{ 
    margin-bottom: 1.4em; 
    width:100%;
    border: 1px solid #C5C5C5;
    border-collapse: collapse;  
}
.ynwiki_content th          
{ 
    font-weight: bold; 
}
.ynwiki_content thead th    
{ 
    background: #c3d9ff; 
}
.ynwiki_content th,
.ynwiki_content td,
.ynwiki_content caption { padding: 4px 10px 4px 5px; }
.ynwiki_content tr
{
    background-color: #C5C5C5;
    border-bottom: 1px solid #EBEBEB;
    height: 28px;
    text-align: center;
}
.ynwiki_content tr + tr
{
    background-color: transparent;
}  
.ynwiki_content td 
{
   border-right : 1px solid #EBEBEB;
   text-align: center;    
}
ul.ynwiki_browse
{
  clear: both;
}
ul.ynwiki_browse span h3
{
  margin: 0;
}
ul.ynwiki_browse > li
{
  clear: both;
  padding: 0px 0px 15px 0px;
}
ul.ynwiki_browse > li + li
{
  border-top-width: 1px;
  padding-top: 15px;
}
ul.ynwiki_browse > li .ynwiki_browse_photo
{
  float: left;
  overflow: hidden;
  margin-right: 8px;
}
html[dir="rtl"] ul.ynwiki_browse > li .ynwiki_browse_photo
{
  float: right;
  margin-right: 0px;
  margin-left: 8px;
}
ul.ynwiki_browse > li .ynwiki_browse_options
{
  float: right;
  overflow: hidden;
  padding-left: 20px;
}
html[dir="rtl"] ul.ynwiki_browse > li .ynwiki_browse_options
{
  float: left;
  padding-left: 0px;
  padding-right: 20px;
}
ul.ynwiki_browse > li .ynwiki_browse_options > a
{
  clear: both;
  display: block;
  margin: 5px;
  font-size: .8em;
  padding-top: 2px;
  padding-bottom: 2px;
}
ul.ynwiki_browse > li .ynwiki_browse_info
{
  overflow: hidden;
}
ul.ynwiki_browse > li .ynwiki_browse_info_title
{
  font-weight: bold;
}
ul.ynwiki_browse > li .ynwiki_browse_info_date
{
  font-size: .8em;
  color: $theme_font_color_light;
}
ul.ynwiki_browse > li .ynwiki_browse_info_blurb
{
  margin-top: 5px;
}
</style>          