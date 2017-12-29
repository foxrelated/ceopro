<?php
$baseUrl = $this->layout()->staticBaseUrl;
$this->headLink()->appendStylesheet($baseUrl . 'application/modules/Ynmember/externals/styles/ui-redmond/jquery-ui-1.8.18.custom.css');

$this->headScript()
	->appendFile($baseUrl . 'application/modules/Ynmember/externals/scripts/jquery-1.10.2.min.js')
	->appendFile($baseUrl . 'application/modules/Ynmember/externals/scripts/jquery-ui-1.11.4.min.js');
?>

<div class="ynmember-birthday-input">
	<span class="ynicon yn-calendar"></span>
	<input type="text" class="date_picker input_small" value="<?php echo sprintf("%s/%s/%s", $this->pickedMonth, $this->pickedDay, $this->pickedYear);?>" />
</div>

<script type="text/javascript">
	window.addEvent('domready', function(){
		var current = new Date();
		var yearRange = (current.getFullYear() - 100) +':' + (current.getFullYear());
		jQuery('.date_picker').datepicker({
			firstDay: 1,
			dateFormat: 'yy-mm-dd',
			showOn: "button",
			buttonImage: '',
			changeMonth: true,
			changeYear: true,
			yearRange: yearRange,
			buttonImageOnly: true,
			buttonText: '',
			onSelect: function(dateText, date){
				window.location= "<?php echo $this->url(array('controller' =>'member', 'action' => 'birthday'), 'ynmember_extended', true);?>?date=" + date.selectedDay + "&month=" + (date.selectedMonth + 1) + "&year=" + date.selectedYear;
            }
        });
    });
</script>

<?php if( count($this->paginator) > 0 ): ?>
<h3>
	<?php echo $this->translate("Members have birthday on"); ?>
	<?php echo sprintf(" %s/%s/%s", $this->pickedMonth, $this->pickedDay, $this->pickedYear);?>
</h3>

<ul class="ynmember-member-items ynmember-clearfix">
	<?php foreach($this->paginator as $user) :?>
	<li>
		<div class="ynmember-member-item">
			<div class="ynmember-member-item-option">
				<span class="ynmember-member-item-more-btn"><span class="ynicon yn-gear"></span></span>
				<div class="ynmember-member-item-option-hover">
					<?php echo $this->action('render', 'menu', 'ynmember', array('id' => $user->getIdentity()));?>
					<?php Engine_Api::_()->core()->clearSubject();?>
				</div>
			</div>
			<div class="ynmember-member-item-avatar">
				<?php $background_image = Engine_Api::_()->ynmember()->getMemberPhoto($user);?>
				<?php echo $this->htmlLink($user->getHref(), '<span alt="'.$user->getTitle().'" class="ynmember-profile-image" style="background-image:url('.$background_image.');"></span>', array('title'=>$user->getTitle())) ?>
				
				<!-- add friend button -->
				<?php $canAdd = Engine_Api::_()->ynmember()->canAddFriendButton($user);?>
				<?php if(is_array($canAdd)):?>
					<a href="<?php echo $this->url($canAdd['params'], $canAdd['route'], array());?>" class="smoothbox ynmember-btn-addfriend">
						<span class="ynicon yn-plus"></span>
		            </a>
				<?php endif;?>
			</div>
			<div class="ynmember-member-item-info">
				<div class="ynmember-member-item-title">
					<?php
				         $onlineTable = Engine_Api::_() -> getDbtable('online', 'user');
				         $step = 900;
				         $select = $onlineTable -> select() -> where('user_id=?', (int)$user -> getIdentity()) -> where('active > ?', date('Y-m-d H:i:s', time() - $step));
				         $online = $onlineTable -> fetchRow($select);
				         if(is_object($online)): ?>
				     		<span class="ynmember-item-status online"></span>
				        <?php else:?>
				            <span class="ynmember-item-status off"></span>
				        <?php endif;?>
					<span><a href='<?php echo $user->getHref();?>'><?php echo $user->getTitle();?></a></span>					
				</div>
				<div class="ynmember-member-item-more">
					<!-- studyplace -->
					<span><span class="ynicon yn-graduation-cap"></span> <?php echo $this-> translate("Studied at");?> 
					<?php
						$studyPlacesTbl = Engine_Api::_()->getDbTable('studyplaces', 'ynmember');
						$studyplaces = $studyPlacesTbl -> getCurrentStudyPlacesByUserId($user -> getIdentity());
						if ($studyplaces) :?>
						<?php
							$str_studyplace = "";
							if($studyplaces -> isViewable())
							{
								$str_studyplace = "<a target='_blank' href='https://www.google.com/maps?q={$studyplaces->latitude},{$studyplaces->longitude}'>{$studyplaces->name}</a>";
							}
						?>
							 <?php echo $str_studyplace;?> 
						<?php endif;?>	
					</span>	
					<!-- workplace -->
					<span><span class="ynicon yn-briefcase"></span> <?php echo $this-> translate("Works at");?> 
					<?php
						$workPlacesTbl = Engine_Api::_()->getDbTable('workplaces', 'ynmember');
						$workplaces = $workPlacesTbl -> getCurrentWorkPlacesByUserId($user -> getIdentity());
						if ($workplaces) :?>
						<?php
							$str_workplace = "";
							if($workplaces -> isViewable())
							{
								$str_workplace = "<a target='_blank' href='https://www.google.com/maps?q={$workplaces->latitude},{$workplaces->longitude}'>{$workplaces->company}</a>";
							}
						?>
							 <?php echo $str_workplace;?> 
						<?php endif;?>	
					</span>	
					<!-- living places -->		
					<span><span class="ynicon yn-map-marker"></span> <?php echo $this-> translate("Live in");?> 
					<?php
						$livePlacesTbl = Engine_Api::_()->getDbTable('liveplaces', 'ynmember');
						$liveplaces = $livePlacesTbl -> getLiveCurrentPlacesByUserId($user -> getIdentity());
						$lives = array();
						foreach ($liveplaces as $live)
						{
							if($live -> isViewable())
							{
								$lives[] = "<a target='_blank' href='https://www.google.com/maps?q={$live->latitude},{$live->longitude}'>{$live->location}</a>";
							}
						}
						$lives = implode(", ", $lives);
						if($lives) :?>
						 	<?php echo $lives; ?> 
						<?php endif;?>		
					</span>	
					<!-- groups -->
					<?php
						if (Engine_Api::_()->hasModuleBootstrap('group') || Engine_Api::_()->hasModuleBootstrap('advgroup'))
						{
							$groupTbl = Engine_Api::_()->getItemTable('group');
							$membership = (Engine_Api::_()->hasModuleBootstrap('advgroup'))
								? Engine_Api::_()->getDbtable('membership', 'advgroup')
								: Engine_Api::_()->getDbtable('membership', 'group');
						
							$select = $membership->getMembershipsOfSelect($user);
							$groups = $groupTbl->fetchAll($select);
						}
						?>
					<span><span class="ynicon yn-user-group"></span> <span><?php echo $this-> translate("Groups:");?> </span>
					<?php if (count($groups)):?>
							<?php foreach ($groups as $group) :?>
								 <a href="<?php echo $group->getHref();?>"><?php echo $group->getTitle();?></a>
								<?php break;?>
							<?php endforeach;?>
							<?php if (count($groups) > 1) : ?>
								<?php echo $this-> translate("and");?> <a class="smoothbox" href="<?php echo $this->url(array('controller' => 'review', 'action' => 'user-group', 'id' => $user -> getIdentity()),'ynmember_extended');?>"><?php echo $this -> translate(array("%s other", "%s others" , (count($groups) - 1 )), (count($groups) - 1))?></a>
							<?php endif;?>
						<?php endif;?>
					</span>	
				</div>

				<div class="ynmember-review-item-rating">
					<?php echo $this->partial('_review_rating_big.tpl', 'ynmember', array('rate_number' => $user -> rating));?>
            	</div>
			</div>
		</div>
	</li>
	<?php endforeach;?>
</ul>

<div id='paginator'>
	<?php if( $this->paginator->count() > 1 ): ?>
	     <?php echo $this->paginationControl($this->paginator, null, null, array(
	            'pageAsQuery' => true,
	            'query' => $this->formValues,
	          )); ?>
	<?php endif; ?>
</div>
<?php else: ?>
    <div class="tip">
		<span>
			<?php echo $this->translate('There are no members have birthday today.') ?>
		</span>
    </div>
<?php endif; ?>

<script type="text/javascript">

	(function($,$$){
		var events;
		var check = function(e){
			var target = $(e.target);
			var parents = target.getParents();
			events.each(function(item){
				var element = item.element;
				if (element != target && !parents.contains(element))
					item.fn.call(element, e);
			});
		};
		Element.Events.outerClick = {
			onAdd: function(fn){
				if(!events) {
					document.addEvent('click', check);
					events = [];
				}
				events.push({element: this, fn: fn});
			},
			onRemove: function(fn){
				events = events.filter(function(item){
					return item.element != this || item.fn != fn;
				}, this);
				if (!events.length) {
					document.removeEvent('click', check);
					events = null;
				}
			}
		};
	})(document.id,$$);

		window.addEvent('domready', function(){
		$$('.ynmember-member-item-more-btn').addEvent('outerClick', function(){
			this.getParent('.ynmember-member-item-option').removeClass('ynmember-member-item-option-show');
		});
		$$('.ynmember-member-item-more-btn').removeEvent('click').addEvent('click', function(){
			var popup = this.getParent('.ynmember-member-item-option');
			popup.toggleClass('ynmember-member-item-option-show');

			setTimeout(function(){
				var layout_parent = popup.getParent('.layout_middle');
				if (!layout_parent) layout_parent = popup.getParent('#global_content');
				var y_position = popup.getPosition(layout_parent).y;
				var p_height = layout_parent.getHeight();
				var c_height = popup.getElement('.ynmember-member-item-option-hover').getHeight();
				console.log(c_height);
				if (p_height - y_position < (c_height + 60)) {
					layout_parent.addClass('popup-padding-bottom');
					var margin_bottom = parseInt(layout_parent.getStyle('padding-bottom').replace(/\D+/g, ''));
					layout_parent.setStyle('padding-bottom', (margin_bottom + c_height + 60 + y_position - p_height) + 'px');
				}
			}, 350);
		});
	})

</script>