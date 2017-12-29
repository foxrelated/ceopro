<!-- Header -->
<div class="generic_layout_container layout_top">
	<div class="generic_layout_container layout_middle">
		<h2>
			<?php echo $this->group->__toString();
				echo $this->translate(' &#187; Wikis');
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
			<?php if( $this->canCreate ): ?>
				<?php echo $this->htmlLink(array(
					'route' => 'ynwiki_general',
					'action' => 'create',
					'parent_type' =>'group',
					'subject_id' =>  $this->group->group_id,
				  ), $this->translate('Create New Space'), array(
					'class' => 'buttonlink icon_group_wiki_new'
				)) ?>
			<?php endif; ?>
		</div>
		
		<!-- Content -->
		<?php if ($this->pages->getTotalItemCount()> 0) : ?>
		<ul class="ynwiki_browse" style="padding-top: 10px;">
			<?php foreach( $this->pages as $item ): ?>
			<li>
				<div class='ynwiki_browse_photo'>
					<?php echo $this->htmlLink($item->getOwner()->getHref(), $this->itemPhoto($item->getOwner(), 'thumb.icon')) ?>
				</div>
				<div class='ynwiki_browse_info'>
					<p class='ynwiki_browse_info_title'>
						<?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?>
					</p>
					<p class='ynwiki_browse_info_date'>
						<?php echo $this->translate('Create by <b>%1$s</b> ', $this->htmlLink($item->getOwner()->getHref(), $item->getOwner()->getTitle(), array('target'=>'_top')));?>
						|
						<?php echo $this->timestamp($item->creation_date) ?>
						<?php $revision = $item->getLastUpdated();
						if($revision):  ?>
						|
						<?php $owner =  Engine_Api::_()->getItem('user', $revision->user_id);
							echo $this->translate(' Last updated by <b>%1$s</b> ',$this->htmlLink($owner->getHref(), $owner->getOwner()->getTitle(), array('target'=>'_top')));?>
						<?php echo $this->timestamp($revision->creation_date) ?>
						(<?php echo $this->htmlLink(array(
							'action' => 'compare-versions',
							'pageId' => $item->page_id,
							'route' => 'ynwiki_general',
							'reset' => true,
							), $this->translate("view change"), array(
						)) ?>)
						<?php endif;?>
					</p>
					<?php foreach($item->getBreadCrumNode() as $node): ?>
						<?php echo $this->htmlLink($node->getHref(), $node->title) ?>
						&raquo;
					<?php endforeach; ?>
					<?php echo $this->htmlLink($item->getHref(), $item->title) ?>
					
					<p class='ynwiki_browse_info_blurb'>
						<?php echo $this->string()->truncate($this->string()->stripTags($item->body), 300) ?>
					</p>
				</div>
			</li>
			<?php endforeach; ?>
		</ul>
		<br/>
		<div class ="ynvideo_pages">
			<?php echo $this->paginationControl($this->pages, null, null, array(
				'pageAsQuery' => true,
				'query' => $this->formValues,
			)); ?>
		</div>
		<?php else: ?>
		<div class="tip">
			<span>
				<?php echo $this->translate('You do not have any pages.');?>
			</span>
		</div>
		<?php endif; ?>	
	</div>
</div>