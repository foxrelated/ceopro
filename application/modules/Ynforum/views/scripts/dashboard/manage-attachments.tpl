<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     LuanND
 */
?>
<script type = "text/javascript">
       
    
    function multiDelete()
    {
    	var checkboxes = $$('td.checksub input[type=checkbox]:checked')[0];
		
		if (!checkboxes) {
		  alert("Please select a item to delete.");
		  return false;
		}
        return confirm("<?php echo $this->translate('Are you sure you want to delete the selected attachments?'); ?>");
    }

    en4.core.runonce.add(function()
    {
		$$('th.admin_table_short input[type=checkbox]').addEvent('click', function(){ 
			$$('td.checksub input[type=checkbox]').each(function(i){
	 			i.checked = $$('th.admin_table_short input[type=checkbox]')[0].checked;
			});
		});
		$$('td.checksub input[type=checkbox]').addEvent('click', function(){
			var checks = $$('td.checksub input[type=checkbox]');
			var flag = true;
			for (i = 0; i < checks.length; i++) {
				if (checks[i].checked == false) {
					flag = false;
				}
			}
			if (flag) {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = true;
			}
			else {
				$$('th.admin_table_short input[type=checkbox]')[0].checked = false;
			}
		});
	});
</script>


<?php echo $this->partial('_forum_dashboard.tpl', 'ynforum', array('forum'=>$this -> forum, 'manage_attachments' => 'active','callback_url'=>$this -> callback_url))?>  

<div class='admin_search'>   
    <?php echo $this -> form -> render($this); ?>
</div>

<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this -> url(); ?>" onSubmit="return multiDelete()">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>
                        <th class='admin_table_short'><input  type='checkbox' class='checkbox' /></th>                       
                        <th style="text-align: left;">
							<?php echo $this->translate("Post/Topic Title") ?>                           
                        </th>
                        <th class="ynforum_hide" style="text-align: left;">
                            <?php echo $this->translate("Content") ?>
                        </th> 
                        <th class="ynforum_hide" style="text-align: left;">
                            <?php echo $this->translate("Attachments") ?>
                        </th> 
                        
                        <th class="ynforum_hide" style="text-align: left;">                            
                            <?php echo $this->translate("Date") ?>                           
                        </th>                       
                        <th style="text-align: left;">
                            <?php echo $this->translate("Options") ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($this->paginator as $item): 
                        $post = Engine_Api::_()->getItemTable('ynforum_post')->find($item->post_id)->current();
                        if($post):
                    ?>
                         <tr>
                            <td class="checksub"><input type='checkbox' class='checkbox' name='delete_<?php echo $item -> attachment_id; ?>' value='<?php echo $item -> attachment_id; ?>' /></td>
                                                    
                            <td style="text-align: left;"><?php
                             echo $this->htmlLink($post->getHref(), $this->string()->truncate($post->title, 20))  ?></td>
                            <td class="ynforum_hide" style="text-align: left;"><?php echo $this->string()->truncate($this->string()->stripTags($post->getDescription()), 200)  ?></td>
                            
                            <td class="ynforum_hide" style="text-align: left;"><?php echo $this->string()->truncate($item->title, 20) ?></td>                            
                            
                            <td class="ynforum_hide" style="text-align: left;"><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                            <td style="text-align: left;">
                                <?php echo $this->htmlLink($this->url(array('action' => 'edit', 'post_id' => $post -> getIdentity()), 'ynforum_post'), $this->translate('Edit'), 
                                            array('class' => 'buttonlink icon_forum_post_edit'));?>
                                |
                                <?php  echo $this->htmlLink($this->url(array('post_id' => $post -> getIdentity(),'action' => 'deleteattachment'), 'ynforum_dashboard'), 
                                $this->translate('Delete'), array('class' => 'buttonlink smoothbox icon_forum_post_delete'));?>
                            </td>
                         </tr>
                    <?php endif; endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />

        <div class='buttons'>
            <button type='submit'><?php echo $this->translate("Delete Selected") ?></button>
        </div>
    </form>

    <br />

    <div>
        <?php echo $this -> paginationControl($this -> paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no attachments yet.") ?>
        </span>
    </div>
<?php endif; ?>
