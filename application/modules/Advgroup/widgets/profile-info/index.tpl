<ul style="margin: 10px 0 25px; word-wrap: break-word;">
<?php if( '' !== ($description = $this->group->description) ): ?>
<li style="margin: 10px 0 25px; word-wrap: break-word;" class="yntinymce">
	<?php echo $description; ?>
</li>
<?php endif; ?>  
<li class="advgroup_widget_cover_custom_fields">
    <?php if($this->fieldStructure):?>
     <?php echo $this->fieldValueLoop($this->group, $this->fieldStructure); ?>
<?php endif;?>
</li>
</ul>