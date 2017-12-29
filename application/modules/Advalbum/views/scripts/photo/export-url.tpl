<div class="global_form_popup">
	<div>
		<div>
			<h3><?php echo $this->translate($this->title)?></h3>
			<div class="form-elements">
			<textarea cols="50" rows="4"><?php echo trim($this->htmlCode);?></textarea>
			</div>
			<div class="ynadvalbum_photo_export_url_btn-close">
			    <a href="javascript:void(0);" onclick="parent.Smoothbox.close();">
			        <button><?php echo $this->translate('Close') ?></button>
			    </a>
			</div>
		</div>
	</div>
</div>
