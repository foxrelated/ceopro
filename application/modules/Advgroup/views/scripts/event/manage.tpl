<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->group->__toString();
				echo $this->translate(' &#187; Events');
			?>
		</h2>
	</div>
</div>
<div class="generic_layout_container layout_main advgroup_list">
	<div class="generic_layout_container layout_right">
		<!-- Search Form -->
		<div class="poll_search_form">
			<?php echo $this->form->render($this);?>
		</div>
	</div>
	<div class="generic_layout_container layout_middle">
		<!-- Menu Bar -->
		<div class="group_discussions_options">
			<?php echo $this->htmlLink(array('route' => 'group_profile', 'id' => $this->group->getIdentity()), $this->translate('Back to Group'), array(
				'class' => 'buttonlink icon_back'
			)) ?>
			<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller'=>'event','action'=>'list','subject' => $this->subject()->getGuid()), $this->translate('Browse Events'), array(
				'class' => 'buttonlink icon_group_event active'
			)) ?>
			<?php if( $this->canCreate ): ?>
			<?php echo $this->htmlLink(array(
				'route' => 'event_general',
				'action' => 'create',
				'parent_type' =>'group',
				'subject_id' =>  $this->group->group_id,
			), $this->translate('Create New Event'), array(
				'class' => 'buttonlink icon_group_event_new'
			)) ?>
			<?php endif; ?>
		</div>
		
		<!-- Content -->
		<?php if ($this->paginator->getTotalItemCount() > 0): ?>
		<ul class='ynevents_browse events_browse'>
			<h3>
            <?php
            $totalEvent = $this->paginator->getTotalItemCount();
				echo $this->translate(array('%1$s event', '%1$s events', $totalEvent), $this->locale()->toNumber($totalEvent));
            ?>
			</h3>
			<br/>
			<?php foreach ($this->paginator as $event): ?>
			<li>
				<div class="ynevents_photo">
					<?php echo $this->htmlLink($event->getHref(), $this->itemPhoto($event, 'thumb.normal')) ?>
				</div>
				<div class="ynevents_options events_options">
					<?php if ($this->viewer() && $event->isOwner($this->viewer())): ?>
					<?php
						echo $this->htmlLink(array(
							'route' => 'event_specific', 
							'action' => 'edit', 
							'event_id' => $event->getIdentity(),
							'parent_type' =>'group',
							'subject_id' =>  $this->group->getGuid(),
						), 	$this->translate('Edit Event'), array(
							'class' => 'buttonlink icon_event_edit'
						))
					?>
					<?php
						echo $this->htmlLink(array('route' => 'default', 'module' => 'event', 'controller' => 'event', 'action' => 'delete', 'event_id' => $event->getIdentity(), 'format' => 'smoothbox'), $this->translate('Delete Event'), array(
						'class' => 'buttonlink smoothbox icon_event_delete'
						));
					?>
					<?php endif; ?>
					<?php if ($this->viewer() && !$event->membership()->isMember($this->viewer(), null)): ?>
					<?php
						echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'join', 'event_id' => $event->getIdentity()), $this->translate('Join Event'), array(
						'class' => 'buttonlink smoothbox icon_event_join'
						))
					?>
					<?php elseif ($this->viewer() && $event->membership()->isMember($this->viewer()) && !$event->isOwner($this->viewer())): ?>
					<?php
						echo $this->htmlLink(array('route' => 'event_extended', 'controller' => 'member', 'action' => 'leave', 'event_id' => $event->getIdentity()), $this->translate('Leave Event'), array(
						'class' => 'buttonlink smoothbox icon_event_leave'
						))
					?>
					<?php endif; ?>

					<?php
					$date = date('y-m-d H:m:s');
					// echo $date;die();
					$start = $event->starttime;
					if (strtotime($start) > strtotime($date)) {
						echo $this->partial('_remind_dropdown.tpl','ynevent', array('event' => $event));
					}
					?>
				</div>
				<div class="ynevents_info events_info">
					<div class="ynevents_title">
						<h3><?php echo $this->htmlLink($event->getHref(), $event->getTitle()) ?></h3>
					</div>
					<div class="ynevents_members">
						<?php echo $this->locale()->toDateTime($event->starttime) ?>
					</div>
					<div class="ynevents_members">
						<?php echo $this->translate(array('%s guest', '%s guests', $event->membership()->getMemberCount()), $this->locale()->toNumber($event->membership()->getMemberCount())) ?>
						<?php echo $this->translate('led by') ?>
						<?php echo $this->htmlLink($event->getOwner()->getHref(), $event->getOwner()->getTitle()) ?>
					</div>
					<?php if ($event->repeat_group > 0) : ?>
					  <div class="ynevents_members ynevents_title_repeat_type">
							<?php
							$nextEvent = $event->getNextEvent();
							if (is_object($nextEvent)) : ?>
								<img class="ynevent_arrow" src="application/modules/Ynevent/externals/images/types/event.png" style="margin-top: 3px;"/>
								<a href="<?php echo $nextEvent->getHref();?>" title="<?php echo $this->translate('go to next event'); ?>" target="blank" style="margin-right: 10px;"><?php echo $this->translate('Next event'); ?></a>
							<?php endif;?>   
							<span title="<?php echo $this->translate('repeat type');?>"><?php echo $repeateType[$event->repeat_type]; ?></span>       	
					  </div>
					  <?php endif;?>
					<div class="ynevents_desc events_desc">
						<?php
							if(trim($event->brief_description) != "")
								echo $event->brief_description;
							else 
								echo $event->getDescription() ?>
					</div>
				</div>
			</li>
        <?php endforeach; ?>
		</ul>
		<br/>
		<div class="ynvideo_pages">
		<?php
			echo $this->paginationControl($this->paginator, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			));
		?>
		</div>

		<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('You do not have any events.'); ?>
			</span>
		</div>
		<?php endif; ?>  
		
	</div>
</div>
<script type="text/javascript">
    window.addEvent('domready', function()
    {
	    $$('.ynevents_options events_options select').each(
	    function(el)
	    {       
		    el.removeEvents().addEvent('change', function()
		    {
			    var event_id = el.get('id');
			    var remain_time = el.value;
			                        
			    (new Request.JSON({
			   
			    'method' : 'get',
			    'url' : '<?php echo $this->url(array('module' => 'ynevent', 'controller' => 'index', 'action' => 'remind'), 'default', true) ?>',
			        'data' : {
			        'format' : 'json',
			        'remain_time' : remain_time,
			        'event_id': event_id
		    		}                  
						})).send();
				});
			});
	});
  en4.core.runonce.add(function()
  {
   if($('text'))
    {
      new OverText($('text'), {
        poll: true,
        pollInterval: 500,
        positionOptions: {
          position: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          edge: ( en4.orientation == 'rtl' ? 'upperRight' : 'upperLeft' ),
          offset: {
            x: ( en4.orientation == 'rtl' ? -4 : 4 ),
            y: 2
          }
        }
      });
    }
  });
</script>
<!-- Content -->
 