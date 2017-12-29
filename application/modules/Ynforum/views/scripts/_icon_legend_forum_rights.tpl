<ul class="forum_boxarea icon_legend">
    <div>
        <h3><?php echo $this->translate('Icon Legend and Forum Rights') ?></h3>
    </div>			
    <li class="advforum_iconlegend">		
        <div class="advforum_normalpost">
            <div class="forum_iconlegend">
                <div>
                    <div class="advforum_iconlegend_mean">
                        <i class="ynicon yn-text rep"></i>
                        <?php echo $this->translate('Topic has replies') ?> 
                    </div>
                </div>
                <div>
                    <div class="advforum_iconlegend_mean">
                        <i class="ynicon yn-text hot"></i>
                        <?php echo $this->translate('Hot topic') ?> 
                    </div>
                </div>
							<div>
								<div class="advforum_iconlegend_mean">
                                    <i class="ynicon yn-arrow-right"></i>
									<?php echo $this->translate('Topic unread') ?> 
								</div>
							</div>
                <div>
                    <div class="advforum_iconlegend_mean">
                        <i class="ynicon yn-text"></i>
                        <?php echo $this->translate('Topic doesn\'t have any replies') ?> 
                    </div>
                </div>						
                <div>
                    <div class="advforum_iconlegend_mean"> 
                        <i class="ynicon yn-lock"></i>
                        <?php echo $this->translate('Closed topic') ?> 
                    </div>
                </div>						
            </div>
            <div class="advforum_code_characters">
                <div> 
                    <span><?php echo $this->translate('BBCode') ?>&nbsp;</span>
                    <?php if ($this->allowBbcode) : ?>
                        <?php echo $this->translate('is opened') ?> 
                    <?php else : ?>
                        <?php echo $this->translate('is closed') ?> 
                    <?php endif; ?>
                </div>
                <div> 
                    <span><?php echo $this->translate('HTML') ?>&nbsp;</span> 
                    <?php if ($this->allowHtml) : ?>
                        <?php echo $this->translate('is opened') ?> 
                    <?php else : ?>
                        <?php echo $this->translate('is closed') ?> 
                    <?php endif; ?>
                </div>
            </div>
            <div class="advforum_iconlegend_permission">
                <div>
                    <?php if ($this->canPost) : ?>
                        <?php echo $this->translate('You have the permission to post or reply a topic') ?>
                    <?php else : ?>    
                        <?php echo $this->translate('You don\'t have permission to post or reply a topic') ?>
                    <?php endif; ?>
                </div>    
                <div>
                    <?php if ($this->canEdit) : ?>
                        <?php echo $this->translate('You have the permission to edit a topic') ?>
                    <?php else : ?>    
                        <?php echo $this->translate('You don\'t have permission to edit a topic') ?>
                    <?php endif; ?>
                </div>    
                <div>
                    <?php if ($this->canDelete) : ?>
                        <?php echo $this->translate('You have the permission to delete a topic') ?>
                    <?php else : ?>    
                        <?php echo $this->translate('You don\'t have the permission to delete a topic') ?>
                    <?php endif; ?>
                </div> 
                <div>
                    <?php if ($this->canApprove) : ?>
                        <?php echo $this->translate('You have the permission to approve a post') ?>
                    <?php else : ?>    
                        <?php echo $this->translate('You don\'t have the permission to approve a post') ?>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($this->canSticky) : ?>
                        <?php echo $this->translate('You have the permission to make a sticky on a topic') ?>
                    <?php else : ?>    
                        <?php echo $this->translate('You don\'t have the permission to make a sticky on a topic') ?>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($this->canClose) : ?>
                        <?php echo $this->translate('You have the permission to close a topic') ?>
                    <?php else : ?>    
                        <?php echo $this->translate('You don\'t have the permission to close a topic') ?>
                    <?php endif; ?>
                </div>
                <div>
                    <?php if ($this->canMove) : ?>
                        <?php echo $this->translate('You have the permission to move a topic') ?>
                    <?php else : ?>    
                        <?php echo $this->translate('You don\'t have the permission to move a topic') ?>
                    <?php endif; ?>
                </div>
            </div>						
        </div>					
    </li>			
</ul>	