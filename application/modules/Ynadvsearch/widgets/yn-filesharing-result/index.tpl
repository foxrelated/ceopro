<?php
/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Ynfilesharing
 * @author     YouNet Company
 */
?>
<?php 
    $total_search_item = count($this->folders_search) + count($this->files);
    $total_item = count($this->files_total) + count($this->folders_total);
?>
<div class='count_results'>
    <span class="search_icon fa fa-search"></span>
    <span class="num_results"><?php echo $this->translate(array('%s Result', '%s Results', $total_search_item),$total_search_item)?></span>
    <span class="total_results">(<?php echo $total_item?>)</span>
    <span class="label_results"><?php echo $this->htmlLink(array('route' => 'ynfilesharing_general'), $this->label_content, array());?></span>
</div>
</br>
<?php
    echo $this->partial(
        '_browse_folders.tpl', 
        'ynfilesharing', 
        array(
            'subFolders' => $this->folders, 
            'files' => $this->files,
            'isViewMore' => $this->isViewMore,
            'params' => $this->params,
        )
    );
?>

<?php if ($this->canViewMore && !$this->isViewMore) : ?>
    <div class="ynfs_block">
        <a href="javascript:void(0)" class="buttonlink icon_viewmore" id="ynfs_viewmore">
            <?php echo $this->translate('View More')?>
        </a>
    </div>
<?php endif;?>
<script type="text/javascript">
    <?php if ($this->canViewMore) : ?>  
            $('ynfs_viewmore').removeEvents('click').addEvent('click', function(){
                var element = $$('.ynfs_browse_ul_list')[0];
                en4.core.request.send(new Request.HTML({
                    url : '<?php echo $this->url(array(), 'ynfilesharing_general')?>',
                    data : {
                        view_more : true,
                        format : 'html',
                        from_folder_id : '<?php echo $this->lastFolder->getIdentity()?>'
                    },
                    onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
                        element.set('html', element.get('html') + responseHTML);  
                        eval(responseJavaScript);                       
                    }
                }));
            });
    <?php else : ?>
            $('ynfs_viewmore').destroy();
    <?php endif;?>
</script>