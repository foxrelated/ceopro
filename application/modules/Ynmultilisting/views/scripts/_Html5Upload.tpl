 <?php
	$staticBaseUrl = $this->layout()->staticBaseUrl;
 	$this->headLink() ->prependStylesheet('application/modules/Ynmultilisting/externals/styles/bootstrap.min.css')
		->prependStylesheet($staticBaseUrl . 'application/modules/Ynmultilisting/externals/styles/upload_photo/jquery.fileupload.css');

	$this->headScript()
  		->appendFile($staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/jquery-1.10.2.min.js')
		->appendScript('jQuery.noConflict();')
  		->appendFile($staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/js/vendor/jquery.ui.widget.js')
  		->appendFile($staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/js/jquery.iframe-transport.js')
		->appendFile($staticBaseUrl . 'application/modules/Ynmultilisting/externals/scripts/js/jquery.fileupload.js')
		->appendFile('//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js')
;
 ?>
 <div id="file-wrapper">
 	<div class="form-label">&nbsp;</div>
  <div class="form-element">
  	<div style="padding-bottom: 15px"><?php echo $this->translate('_STORAGE_UPLOAD_DESCRIPTION'); ?></div>
	<!-- The fileinput-button span is used to style the file input field as button -->
  <span class="btn fileinput-button btn-success" type="button">
      <i class="ynicon yn-plus"></i>
      <span><?php echo $this->translate("Add Photos")?></span>
      <!-- The file input field used as target for the file upload widget -->
      <input id="fileupload" type="file" name="files[]" multiple>
  </span>
  
  <button type="button" class="btn btn-danger delete" onclick="clearList();">
    <i class="ynicon yn-trash"></i>
      <span><?php echo $this->translate("Clear List")?></span>
  </button>

  <!-- The global progress bar -->
  <div class="progress-contain progress-contain-mobile clearfix">
      <div id="progress" class="progress" >
          <div class="progress-bar progress-bar-success"></div>
      </div>
      <span id="progress-percent" style="margin-top: 10px;"></span>
  </div>

  <!-- The container for the uploaded files -->
  <div class="files-contain">
    <ul id="files" class="files"></ul>
  </div>
 </div>
</div>
<script>

jQuery(function ()
{
    // Change this to the location of your server-side upload handler:
    var count = 0;
    var url = '<?php echo $this->url(array('controller' => 'photo', 'action' => 'upload-photo'), 'ynmultilisting_extended')?>';
    jQuery('#fileupload').fileupload({
        url: url,
        dataType: 'json',
        done: function (e, data)
        {
    		$('files').style.display = 'block';
            jQuery('.files-contain').addClass('success');
            jQuery.each(data.result.files, function (index, file)
            {
            	var text = "";
            	var ele = jQuery('<li/>');
            	ele.attr('id', count);
            	if(file.status)
            	{
            		text = '<a class="file-remove" onclick = "removeFile('+ count +', ' + file.photo_id + ')" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
            		text += '<span class="file-name">' + file.name + '</span>';
            		ele.addClass('file-success');
            		$('html5uploadfileids').value = $('html5uploadfileids').value + ' ' + file.photo_id;
            		if ($$('.file-success').length > 0)
                        ele.html(text).insertAfter('.file-success:last');
                    else
                        ele.html(text).prependTo('#files');
            	}
            	else
            	{
            		text = '<a class="file-remove" onclick = "removeFile('+ count +', 0)" href="javascript:;" title="<?php echo $this->translate("Click to remove this entry.")?>"><?php echo $this->translate("Remove")?></a>';
            		if(file.name)
            			text += '<span class="file-name">' + file.name + '</span>';
            		text += '<span class="file-info"><span>' + file.error +'</span></span>';
            		 if ($$('.file-unsuccess').length > 0)
                        ele.html(text).insertAfter('.file-unsuccess:last');
                    else
                        ele.html(text).appendTo('#files');
              }
            });
            $('submit-wrapper').style.display = 'block';
            count ++;
        },
        progressall: function (e, data)
        {
        	 $('progress').style.display = 'block';
            var progress = parseInt(data.loaded / data.total * 100, 10);
            jQuery('#progress .progress-bar').css(
                'width',
                progress + '%'
            );
            jQuery('#progress-percent').css('display', 'inline-block').text(
                progress + '%'
            );
        }
    }).prop('disabled', !jQuery.support.fileInput)
        .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');
});
function deletePhoto(photo_id)
{
	new Request.JSON({
        url: '<?php echo $this->url(array('controller'=>'photo','action'=>'delete-photo'), 'ynmultilisting_extended') ?>',
        data: {
          'format': 'json',
          'photo_id': photo_id
        }
      }).send();
}
function removeFile(count, photo_id)
{
	jQuery('#' + count).remove();
	if(photo_id)
	{
		 deletePhoto(photo_id);
		$('html5uploadfileids').value = $('html5uploadfileids').value.replace(photo_id, '');
	}
	if($('html5uploadfileids').value.trim() == "")
	{
		$('files').style.display = 'none';
		$('progress').style.display = 'none';
		$('progress-percent').innerHTML = '';
		$('submit-wrapper').style.display = 'none';
		$('html5uploadfileids').value = '';
	}
	return false;
}
function clearList()
{
	var ids = $('html5uploadfileids').value;
	var arr = ids.split(" ");
	for(var i = 0; i < arr.length; i ++)
	{
		if(arr[i] > 0)
			deletePhoto(arr[i]);
	}
	$('files').style.display = 'none';
	jQuery('#files').text('');
	$('html5uploadfileids').value = '';
	$('submit-wrapper').style.display = 'none';
	$('progress').style.display = 'none';
	$('progress-percent').innerHTML = '';
	return false;
}
</script>
