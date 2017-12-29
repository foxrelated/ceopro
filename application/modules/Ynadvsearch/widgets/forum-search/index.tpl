<div id="ynadvsearch_forum_search">         
<form id="filter_form" method="post" action='<?php echo $this->url(array('controller' => 'search', 'action'=>'forum-search'),'ynadvsearch_extended') ?>'>
    <input type="text" id="search" name="search" placeholder="<?php echo $this->translate('Search Forum Topics')?>" value="<?php echo (isset($this->values['search'])) ? $this->values['search'] : '';?>"/>
    <button type="submit"><?php echo $this->translate('Search')?></button>
</form>
</div>