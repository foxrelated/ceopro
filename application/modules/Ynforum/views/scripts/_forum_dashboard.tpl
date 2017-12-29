
<h3><?php echo $this->translate('User Dashboard')?></h3>
<div class="ynforum_header"	>
	<div class="tabs navigation-over-list ynforum_intergrate_menus">
	    <ul >	
	    	<li >
		        <a class="buttonlink icon_back" href="<?php echo base64_decode($this->callback_url)?>"><?php echo $this -> translate("Back To Forum");?></a>
		    </li>	    
		    <li class="<?php echo $this->signature?>">
	        	<a href="<?php echo $this -> url(array('callback_url' => $this->callback_url), 'ynforum_dashboard', true)?>"><?php echo $this -> translate("Forum Signature"); ?></a>
		    </li>	
		    <li class="<?php echo $this->manage_attachments?>">
		        <a href="<?php echo $this -> url(array('action' => 'manage-attachments', 'callback_url' => $this->callback_url), 'ynforum_dashboard', true)?>"><?php echo $this -> translate("Manage Attachments"); ?></a>
		    </li>
	    	<li class="<?php echo $this->my_watch_topic?>">
	        	<a href="<?php echo $this -> url(array('action' => 'my-watch-topic', 'callback_url' => $this->callback_url), 'ynforum_dashboard', true)?>"><?php echo $this -> translate("My Watched Topics"); ?></a>
	    	</li>
		   
		</ul>
	</div>
</div>