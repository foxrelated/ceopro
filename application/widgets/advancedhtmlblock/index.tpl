<style>
	.layout_advancedhtmlblock ul{
		list-style-type: disc;
	}
	.layout_advancedhtmlblock * > li {
		margin-left: 20px;
	}
	.layout_advancedhtmlblock img,
	.layout_advancedhtmlblock video,
	.layout_advancedhtmlblock iframe,
	.layout_advancedhtmlblock embed{
		width: 100%;
	}
</style>

<?php if (isset($this->title_data) && $this->title_data): ?>
	<h3><?php echo $this->title_data ?></h3>
<?php endif; ?>

<?php if($this->isTablet && !$this->notablet):?>
	<?php echo $this->tablet_data ?>

<?php elseif($this->isMobile && !$this->nomobile):?>
	<?php echo $this->mobile_data ?>

<?php elseif(!$this->nofullsite):?>
	<?php echo $this->body_data ?>
<?php endif;?>