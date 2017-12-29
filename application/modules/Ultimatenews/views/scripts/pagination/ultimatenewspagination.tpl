<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Blog
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: blogpagination.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Jung
 */
?>
<?php if ($this->pageCount > 1):?>
    <ul class="paginationControl" align="center">
        <?php /* Previous page link */?>
        <?php if (isset($this->previous)):?>
        	<li>
            <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->previous;?>)"><?php echo $this->translate('&#171; Previous');?></a>
            </li>
        <?php endif;?>
        <?php $length = count($this->pagesInRange);
        $count = 0;?>
        <?php foreach ($this->pagesInRange as $page):?>
        	<li <?php if ($page == $this->current): echo "class = 'selected'"; endif;?>>
            <?php $count++;?>
                <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $page;?>)"><?php echo $page;?></a>
           </li>
        <?php endforeach;?>

        <?php /* Next page link */?>
        <?php if (isset($this->next)):?>
        	<li>
            <a href="javascript:void(0)" onclick="javascript:pageAction(<?php echo $this->next;?>)"><?php echo $this->translate('Next &#187;');?></a>
    		</li>
    	<?php endif;?>

    </ul>
<?php endif;?>