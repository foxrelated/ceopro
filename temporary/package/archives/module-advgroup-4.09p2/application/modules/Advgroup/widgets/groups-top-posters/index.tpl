<?php if(count($items = $this->items)>0):?>
<ul class="generic_list_widget" style="background:none; overflow: hidden; margin-bottom: 10px;">
   <?php foreach($items as $item):
        $poster = Engine_Api::_()->user()->getUser($item->user_id);
    ?>
      <li>
        <div class="photo">
            <?php echo $this->htmlLink($poster->getHref(),$this->itemPhoto($poster, 'thumb.icon')) ?>
        </div>
        <div class="info">
            <b style="word-wrap:break-word"> <?php 	$poster_name = Engine_Api::_()->advgroup()->subPhrase($poster->getTitle(),28);
						echo $this->htmlLink($poster->getHref(), $poster_name)?> </b>
            <br/>
            <?php echo $this->translate(array('%s post','%s posts',$item->post_count),$item->post_count);?>
        </div>
      </li>
    <?php endforeach;?>
</ul>
<?php endif;?>