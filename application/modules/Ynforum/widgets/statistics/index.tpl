<?php
/**
 * SocialEngine
 *
 * @category   Application_Extension
 * @package    Ynforum
 * @author     MinhNC
 */
?>

<ul class="forum_categories">
    <li>
        <ul class="forum_boxarea">
            <li class="forum_boxarea_header">				
                <h3 class="forum_boxarea_forums"> <?php echo $this->translate('Statistic - Forums') ?> </h3>				
            </li>
            <li class="forum_boxarea_body"> 
                <div class="statistic_ynforum_form">
                    <div class="forum_icon">
                        <a href="javascript:void(0);">
                            <i class="ynicon yn-bar-chart"></i>
                        </a>
                    </div>
                    <div class="forum_title">
                        <p> 
                            <span> <?php echo $this->translate('Topic(s):') ?> </span><?php echo $this->locale()->toNumber($this->topicCount)?>,
                            <span> <?php echo $this->translate('Post(s):') ?> </span><?php echo $this->locale()->toNumber($this->postCount)?>
                        </p>
                    </div>					
                </div>			
            </li>
        </ul>
    </li>	
</ul>	
