<div>
<?php 
	if ($this->viewer->getIdentity() && $this->canAdd) {
		echo $this->htmlLink(
				$this->url(array('controller' => 'sponsor','action' => 'create', 'group_id' => $this->group->getIdentity(), 'tab' => $this->identity), 'group_extended'),
				$this->translate('Add Sponsor'),
				array(
					'class' => 'buttonlink smoothbox ynevent_sponsor_add'
				)
		);		
	}
?>
</div>
<br />

<?php if( $this->paginator->getTotalItemCount() > 0 ): ?>
	
<div class="advgroup_list-view">
	<div class="advgroup-tabs-content ynclearfix">
		<div class="tabcontent">
			<ul class="advgroup_sponsor_list generic_list_widget groups_browse">
			<?php foreach( $this->paginator as $sponsor ): ?>
				<li>
					<div class="list-view">
						<div class="photo">
							<span style="background:url(<?php echo ($sponsor->getPhotoUrl('thumb.normal')) ? ($sponsor->getPhotoUrl('thumb.normal')) :("application/modules/Advgroup/externals/images/no_sponsor.jpg") ; ?>) no-repeat center;"></span>
						</div>
						<div class="info advgroup_sponsor_content">
							<div class="title">
								<?php echo $sponsor->name; ?>
							</div>
							<div class="stats">
								<?php if ($sponsor->url != "") : ?>
								<?php if ($sponsor->url) {
										$pos = strpos($sponsor->url, "http");
										if ($pos === false){
											$sponsor->url = "http://" . $sponsor->url;
										}	
									}
								?>
								<div class="address">
									<?php echo $this->translate("More at"); ?>: 
									<a target="_blank" href="<?php echo $sponsor->url; ?>">
										<?php echo $sponsor->url; ?>
									</a>
								</div>
								<?php endif;?>
								<div class="content">	
									<?php echo $sponsor->description; ?>
								</div>
								
							</div>
						</div>

						<?php if ($this->viewer->getIdentity() && $this->canAdd) :?>
						<div class="advgroup_sponsor_action">
						<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'sponsor', 'action' => 'edit', 'id' => $sponsor->getIdentity(), 'tab' => $this->identity), $this->translate('Edit'), array(
						'class' => 'smoothbox',
						)) ?>
						| 
						<?php echo $this->htmlLink(array('route' => 'group_extended', 'controller' => 'sponsor', 'action' => 'delete', 'id' => $sponsor->getIdentity(), 'tab' => $this->identity), $this->translate('Delete'), array(
						'class' => 'smoothbox',
						)) ?>
						</div>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
			</ul>
		</div>
	</div>
</div>
<?php else: ?>
	<br />
	<div class="tip">
		<span>
			<?php echo $this->translate('No sponsors have been added in this group yet.');?>
		</span>
	</div>
<?php endif; ?>