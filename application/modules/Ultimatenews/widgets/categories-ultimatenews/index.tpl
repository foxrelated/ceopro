<ul>
<?php 
      foreach($this->categories as $cat): ?>
            <li title="<?php //echo $cat->category_name; ?>">
	            <a href="<?php echo $this->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=>$cat->category_id ),'ultimatenews_categoryparent')?>">
	            <?php echo strlen($this->translate($cat->category_name))>22?substr($this->translate($cat->category_name),0,19).'...':$this->translate($cat->category_name); ?>
	            </a>
            </li>
      <?php endforeach;?>
	   		<li title="<?php echo $this->translate("Other") ?>">
	            <a  href="<?php echo $this->url(array('controller' => 'index', 'action'=>'contents', 'categoryparent'=> 0),'ultimatenews_categoryparent')?>">
	            	<?php echo $this->translate('Other');?>
	            </a>
	        </li>
</ul>