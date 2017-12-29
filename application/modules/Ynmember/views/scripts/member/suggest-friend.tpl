		<div>
<?php if($this->mode == 'ajax'):?>
	<?php foreach ($this->paginator as $u):?>
	<li class="ynmember-suggestion-item ynmember-clearfix" id="ynmember-suggestion-item-<?php echo $u->getIdentity();?>">
		<div class="ynmember-suggestion-item-photo">
			<?php echo $this->htmlLink($u->getHref(), $this->itemPhoto($u, 'thumb.icon'), array('class' => 'thumb')) ?>
		</div>
		<div  class="ynmember-suggestion-item-button">
			<button onclick="suggest(<?php echo $u->getIdentity();?>);"><?php echo $this->translate("Suggest Friend");?></button>
		</div>
		<div class="ynmember-suggestion-item-title">
			<?php echo $this->htmlLink($u->getHref(), $u->getTitle()) ?>
		</div>
	</li>
	<?php endforeach;?>
<?php else:?>
	<?php if (count($this->paginator)):?>
			<h3><?php echo $this->translate("Suggest friend for") . " " . $this->user->getTitle();?></h3>
			<h5 class="ynmember-suggestion-des"><?php echo 
			$this->translate("Does %s know any of your friends?", $this->user->getTitle());?>
			</h5>
			<div class="ynmember-suggestion-title-top">
				<input type="text" name="search_box" id="search_box" /> 
				<button class="ynmember-suggestion-search-btn" onclick="searchFriend();"><?php echo $this -> translate('Search');?></button>
			</div>
			<ul id="ynmember_suggestion_list">
			<?php foreach ($this->paginator as $u):?>
			<li class="ynmember-suggestion-item ynmember-clearfix" id="ynmember-suggestion-item-<?php echo $u->getIdentity();?>">
				<div class="ynmember-suggestion-item-photo">
					<?php echo $this->htmlLink($u->getHref(), $this->itemPhoto($u, 'thumb.icon'), array('class' => 'thumb', 'target'=> '_blank')) ?>
				</div>
				<div  class="ynmember-suggestion-item-button">
					<button onclick="suggest(<?php echo $u->getIdentity();?>);"><?php echo $this->translate("Suggest Friend");?></button>
				</div>
				<div class="ynmember-suggestion-item-title">
					<?php echo $this->htmlLink($u->getHref(), $u->getTitle(),  array('class' => 'thumb', 'target'=> '_blank')) ?>
				</div>
			</li>
	
	<?php endforeach;?>
	</ul>
		</div>
	
	<script type="text/javascript">
	var suggest = function(user_id){
		new Request.JSON({
			'method': 'get',
			'url' : '<?php echo $this->url(array('controller' => 'member', 'action' => 'suggest'), 'ynmember_extended',true) ?>',
			'data' : {
				'format' : 'json',
				'id' : user_id,
				'to' : <?php echo $this->user->getIdentity();?>,
				'from': <?php echo $this->viewer()->getIdentity();?>
			},
			'onSuccess' : function(json) {
				alert('<?php echo $this -> translate('sent suggestion successfully');?>');
				$("ynmember-suggestion-item-"+user_id).dispose();
			}
		}).send();
	};
	
	var searchFriend = function(){
		$("ynmember_suggestion_list").innerHTML = '';
		var req = new Request.HTML({
			url : en4.core.baseUrl + 'adv-members/member/suggest-friend/',
		    data : {
		    		'id'   : <?php echo $this->user->getIdentity();?>,
	                'text' : $('search_box').value,
	                'mode' : 'ajax'        
		      },
		   onSuccess : function(responseTree, responseElements, responseHTML, responseJavaScript) {
	           $("ynmember_suggestion_list").innerHTML = responseHTML;
		   }
		});    
	    en4.core.request.send(req, {
	      'element' : $('ynmember_suggestion_list')
	      //'updateHtmlMode' : 'prepend'           
	      }
	    );
	}
	</script>
	<?php else:?>
	<h3><?php echo $this->translate("No members for suggestion");?></h3>
	<?php endif;?>
<?php endif;?>