<style type="text/css">
    ul.frontend_table{
        border: 2px solid #dcdcdc;
        margin-top: 5px;
        margin-bottom: 15px;
        font-size: 13px;
        font-weight: bold;
    }
    ul.frontend_table .checkbox{
        float: right; 
    }

    ul.frontend_table li{
        padding: 5px;
        border-bottom: 1px solid #dcdcdc;
    }

    ul.frontend_table li a{

        width: 85%;
        display: inline-block;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    div.select_all{
        clear: both;
        display: block;
        padding: 4px;
        margin-top: 10px;
        font-size: 14px;
        font-weight: bold;
    }

    div.select_all .checkbox{
        float: right;
    }

</style>

<?php if ($this->error) : ?>
<div class="import-message">
    <p><?php echo $this->message?></p>
    <button onclick="parent.Smoothbox.close();"><?php echo $this->translate('Close')?></button>
</div>    
<?php elseif (count($this->listings)) : ?>
<form id='multiselect_form' class="global_form_popup" method="post">
    <h3><?php echo $this->translate(array('%s listing is imported from %s.', '%s listings is imported from %s.', count($this->listings)), count($this->listings), $this->item)?></h3>

    <ul class="frontend_table">
        
        <?php foreach ($this->listings as $listing) :?>
        <li>
            <span><?php echo $this->htmlLink($listing->getHref(), $listing->getTitle(), array('target' => '_blank'))?></span>
        </li>
        <?php endforeach;?>     
        
    </ul>
    
    <div style="float:right;display: block; text-align: right">
        <span><a href="javascript:void(0)" onclick="parent.Smoothbox.close();"><?php echo $this->translate('Close')?></a></span>
    </div>
</form>
<?php else: ?>
<div class="import-message">
    <p><?php echo $this->translate('Cannot found any listings imported from %s.', $this->item)?></p>
    <button onclick="parent.Smoothbox.close();"><?php echo $this->translate('Close')?></button>
</div>    
<?php endif;?>