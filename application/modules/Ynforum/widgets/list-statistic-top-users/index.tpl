<?php
/**
 * SocialEngine
 *
 * @category   Application_Extension
 * @package    Ynforum
 * @author     DangTH
 */
?>
<div class="forum_latest_thanked">
    <div class="forum_latest_thanked_title clearfix">  
        <select id="forum_statistic_select_widget">
            <option value="thanked" selected="selected"> <?php echo $this->translate('Thanked') ?> </option>
            <option value="posted"> <?php echo $this->translate('Posted') ?> </option>				  
        </select>
        <h3><?php echo $this -> translate("Top User"); ?></h3>        			
    </div>
    <ul id="forum_select_thanked_widget">
        <?php foreach ($this->thankedSignatures as $i => $signature): ?>
            <li>
                <div class="numeric"><?php echo $i + 1?></div>
                <div class="thanked_item">
                    <div><?php echo $this->locale()->toNumber($signature->thanked_count)?></div>
                    
                    <?php 
                        $user = Engine_Api::_()->user()->getUser($signature->user_id);
                        echo '<span>'.$this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), 
                           array('title' => $this->string()->stripTags($user->getTitle()))).'</span>';
                        echo $this->htmlLink($user->getHref(), 
                            $this->string()->truncate($this->string()->stripTags($user->getTitle()), 18), 
                            array('title' => $this->string()->stripTags($user->getTitle())));
                    ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
    <ul id="forum_select_post_widget" style="display:none">
        <?php foreach ($this->postedSignatures as $i => $signature): ?>
            <li>
                <div class="numeric"><?php echo $i + 1?></div>
                <div class="thanked_item">
                    <div><?php echo $this->locale()->toNumber($signature->approved_post_count)?></div>                                
                    <?php 
                        $user = Engine_Api::_()->user()->getUser($signature->user_id);
                        echo '<span>'.$this->htmlLink($user->getHref(), $this->itemPhoto($user, 'thumb.icon'), 
                            array('title' => $this->string()->stripTags($user->getTitle()))).'</span>';
                        echo $this->htmlLink($user->getHref(), 
                            $this->string()->truncate($this->string()->stripTags($user->getTitle()), 18), 
                            array('title' => $this->string()->stripTags($user->getTitle()))); 
                    ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script language="javascript" type="text/javascript">
    $('forum_statistic_select_widget').addEvent('change', function(event) {
        $('forum_select_thanked_widget').hide();
        $('forum_select_post_widget').hide();
        if (this.value == 'thanked') {
            $('forum_select_thanked_widget').show();
        } else {
            $('forum_select_post_widget').show();    
        }
    });
</script>