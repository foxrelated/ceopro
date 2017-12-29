<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Ynforum
 * @author     LuanND
 */
?>

<?php echo $this->partial('_forum_dashboard.tpl', 'ynforum', array('forum'=>$this -> forum, 'my_watch_topic' => 'active','callback_url'=>$this -> callback_url))?>  


<br />
<?php if (count($this->paginator)): ?>
    <form id='multidelete_form' method="post" action="<?php echo $this -> url(); ?>">
        <div class="table_scroll">
            <table class='admin_table'>
                <thead>
                    <tr>                                               
                        <th style="text-align: left;">
							<?php echo $this->translate("Post/Topic Title") ?>                           
                        </th>
                        <th class="ynforum_hide" style="text-align: left;">
                            <?php echo $this->translate("Content") ?>
                        </th> 
                        <th class="ynforum_hide" style="text-align: left;">
							<?php echo $this->translate("Views") ?>                            
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
                    <?php foreach ($this->paginator as $item): ?>
                         <tr <?php if(!$item->isViewedLastPost($this->viewer)):?>
	          					style="font-weight: bold;"
	         				<?php endif;?>>                         	
                            <td style="text-align: left;" >
                            	<?php echo $this->htmlLink($item->getHref(), $this->string()->truncate($item->getTitle(), 20))  ?></td>
                            <td class="ynforum_hide" style="text-align: left;" >
                            	<?php echo $this->string()->truncate($this->string()->stripTags($item->getDescription()), 200)  ?></td>
                            <td class="ynforum_hide" style="text-align: left;"><?php echo $item->view_count; ?></td>
                            <td class="ynforum_hide" style="text-align: left;"><?php echo $this->locale()->toDateTime($item->creation_date) ?></td>
                            <td style="text-align: left;">
                            	<?php 
								$forum = $item -> getParent();                            	
                            	if (!$item->isWatching($this->viewer->getIdentity(), $forum->getIdentity())) {
		                            echo $this->htmlLink($this->url(array('action' => 'watch', 'topic_id' => $item -> getIdentity(), 'watch' => '1', 'refeshed' => 1), 'ynforum_topic'), $this->translate('Watch topic'), 
		                                    array('class' => 'buttonlink smoothbox  icon_forum_topic_watch'));
		                        } else {
		                            echo $this->htmlLink($this->url(array('action' => 'watch', 'topic_id' => $item -> getIdentity(), 'watch' => '0', 'refeshed' => 1), 'ynforum_topic'), $this->translate('Stop watching topic'),
		                                    array('class' => 'buttonlink smoothbox  icon_forum_topic_unwatch'));
		                        }
                        
                        		?> 
                            </td>
                         </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <br />
        
    </form>
    <div>
        <?php echo $this -> paginationControl($this -> paginator); ?>
    </div>

<?php else: ?>
    <div class="tip">
        <span>
            <?php echo $this->translate("There are no posts posted by your members yet.") ?>
        </span>
    </div>
<?php endif; ?>
