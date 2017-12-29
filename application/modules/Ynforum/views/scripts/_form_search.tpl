<form class="frm-search" id="global_search_advforum_form" action="<?php echo $this->url(array('action' => 'search'), 'ynforum_forum') ?>/" method="get">
    <div class="advforum_search">
        <input type='image' class='advforum_bt_search' name='submit'  size='17' src="<?php echo $this->baseUrl('application/modules/Ynforum/externals/images/advforum_search.png') ?>" />
        <?php 
            echo $this->formText('title', $this->title, array(
                'id' => 'global_advforum_search_field', 
                'size' => 20, 
                'maxlength' => 180, 
                'placeholder' => $this->translate('Search in forum')));
        ?>
        <div class="form_search_element">
            <?php echo $this->formCheckbox('search_in_subforums', 1, 
                array('checked' => $this->searchInSubForums == 1)) ?>
            <label for="search_in_subforums"><?php echo $this->translate('Search in the sub-forums')?></label>
        </div>    
    </div>
</form>			