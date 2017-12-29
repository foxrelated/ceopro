<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: _composePhoto.tpl 7244 2010-09-01 01:49:53Z john $
 * @author     Sami
 */
?>

<?php
$staticBaseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
->prependStylesheet($staticBaseUrl . 'application/modules/Advalbum/externals/styles/upload_photo/bootstrap.min.css')
->prependStylesheet($staticBaseUrl . 'application/modules/Advalbum/externals/styles/upload_photo/jquery.fileupload.css')
;

$this->headScript()
->appendFile($staticBaseUrl . 'application/modules/Advalbum/externals/scripts/jquery-1.10.2.min.js')
->appendScript('jQuery.noConflict();')
->appendFile($staticBaseUrl . 'application/modules/Advalbum/externals/scripts/composer_photo.js')
->appendFile($staticBaseUrl . 'application/modules/Advalbum/externals/scripts/js/vendor/jquery.ui.widget.js')
->appendFile($staticBaseUrl . 'application/modules/Advalbum/externals/scripts/js/jquery.iframe-transport.js')
->appendFile($staticBaseUrl . 'application/modules/Advalbum/externals/scripts/js/jquery.fileupload.js')
;
?>

<script type="text/javascript">
  en4.core.runonce.add(function() {
    var type = 'wall';
    if (composeInstance.options.type) type = composeInstance.options.type;
    composeInstance.addPlugin(new Composer.Plugin.Photo({
              title : '<?php echo $this->string()->escapeJavascript($this->translate('Add Photo')) ?>',
              lang : {
                'Add Photos' : '<?php echo $this->string()->escapeJavascript($this->translate(' Browse photos to upload')) ?>',
        'ynadvalbum_drag_drop_photos' : '<?php echo $this->string()->escapeJavascript($this->translate('Drag and drop some photos from computer to upload or click on button to select multi photos')) ?>',
                'Select File' : '<?php echo $this->string()->escapeJavascript($this->translate('Select File')) ?>',
                'cancel' : '<?php echo $this->string()->escapeJavascript($this->translate('cancel')) ?>',
                'Loading...' : '<?php echo $this->string()->escapeJavascript($this->translate('Loading...')) ?>',
                'upload_error' : '<?php echo $this->string()->escapeJavascript($this->translate('Please check these files again because of having any problems in uploading process')) ?>',
              'Unable to upload photo. Please click cancel and try again': '<?php echo $this->string()->escapeJavascript($this->translate('Unable to upload photo. Please click cancel and try again')) ?>'
            },
            requestOptions : {
//            'url'  : en4.core.baseUrl + 'advalbum/album/compose-upload/type/'+type
    }
  }));
  });

  function advalbumInitUploader() {
    // Change this to the location of your server-side upload handler:
    var count = 0,
        acceptFileTypes = /(\.|\/)(gif|jpe?g|png)$/i,
        fileErrorList = jQuery('#ynadvalbum_upload_error_list'),
        previewList = jQuery('#advalbum-preview-list'),
        url = '<?php echo $this->url(array('module' => 'advalbum', 'controller' => 'album', 'action' => 'compose-upload', 'format' => 'json'), 'default')?>',
        previewText = '<li class="ynadvalbum_preview_item ynadvalbum_preview_loading">'
            + '<a class="ynicon yn-del ynadvalbum-preview-remove" href="javascript:void(0);" onclick="advalbumRemovePreview(this)"></a>'
            + '<div class="ynadvalbum_item_progress_bar-parent">'
            + '<div class="ynadvalbum_item_progress_bar-background">'
            + '<div class="ynadvalbum_item_progress_bar"></div>'
            + '</div>'
            + '</div>'
            + '</li>';
    // init file uploader
    jQuery('#fileupload').fileupload({
      url: url,
      dataType: 'json',
      dropZone: '#advalbum-post-upload',
      add: function(e, data)
      {
        var fileList = jQuery('#advalbum-post-upload').children('.form-element').get(0);
        fileList.addClass('ynadvalbum_has_photo clearfix');
        if (data.files) {
          // get the file
          var item = data.files[0];
          // check for file extensions
          if (!(acceptFileTypes.test(item.type) || acceptFileTypes.test(item.name))) {
            return;
          }
          // create file reader to get data url for preview
          var reader = new FileReader();
          data.$previewElement = jQuery(previewText);
          reader.onload = function(e) {
            data.$previewElement.css('background-image', 'url(' + e.target.result + ')');
            previewList.append(data.$previewElement);
          };
          reader.readAsDataURL(item);
        }
        // process upload
        data.submit();
      },
      done: function (e, data)
      {
        var file = data.result;
        if(file.status)
        {
          data.$previewElement.find('.ynadvalbum_item_progress_bar-parent').hide();
          data.$previewElement.removeClass('ynadvalbum_preview_loading');
          data.$previewElement.attr('photo_id', file.photo_id);
//          data.$previewElement.css('background-image', 'url("' + file.src + '")').show();
          data.$previewElement.find('.ynadvalbum-preview-remove').show();

          advalbumUpdatePhotosOrder();
          advalbumSortPhotoList();
        } else {
          // upload error
          var errorElement = jQuery.parseHTML('<li class="ynadvalbum_error_item" title="' + data.files[0].name + ': ' + file.error + '">' + data.files[0].name + '</li>');
          jQuery('#ynadvalbum_upload_error').show();
          fileErrorList.append(errorElement);
          data.$previewElement.remove();
        }

        // update photo list style
        advalbumUpdatePhotoList();
      },
      progress: function(e, data)
      {
        var progress = parseInt(data.loaded / data.total * 100, 10);
        var progressBar = data.$previewElement.find('.ynadvalbum_item_progress_bar');
        progressBar.css(
            'width',
            progress + '%'
        );
      }
    }).prop('disabled', !jQuery.support.fileInput)
            .parent().addClass(jQuery.support.fileInput ? undefined : 'disabled');

    // events for drag focus
    jQuery(document).bind('dragover', function (e) {
      var dropZone = jQuery('#advalbum-post-upload'),
              timeout = window.dropZoneTimeout;
      var dropZoneParent = dropZone.children('.form-element');
      if (timeout) {
        clearTimeout(timeout);
      }
      var found = false,
              node = e.target;
      do {
        if (node === dropZone[0]) {
          found = true;
          break;
        }
        node = node.parentNode;
      } while (node != null);
      if (found) {
        dropZoneParent.addClass('ynadvalbum_drag_photo_focus');
      } else {
        dropZoneParent.removeClass('ynadvalbum_drag_photo_focus');
      }
      window.dropZoneTimeout = setTimeout(function () {
        window.dropZoneTimeout = null;
        dropZoneParent.removeClass('ynadvalbum_drag_photo_focus');
      }, 100);
    });
  }

  function advalbumRemovePreview(el){
    var parent = jQuery(el).parent('.ynadvalbum_preview_item');
    var photo_id = parent.attr('photo_id');
    fileids = document.getElementsByName('attachment[photo_id]')[0];
    fileids.value = fileids.value.replace(photo_id, '');
    parent.remove();
    advalbumUpdatePhotoList();
  }

  function advalbumSortPhotoList() {
    var SortablesInstance;
    $$('#advalbum-preview-list > li').addClass('sortable');
    SortablesInstance = new Sortables($$('#advalbum-preview-list'), {
      clone: true,
      constrain: true,
      opacity: 0.5,
      onStart: function(e, clone) {
        e.addClass('ynadvalbum_drag');
        clone.addClass('ynadvalbum_clone');
        clone.getChildren('.ynadvalbum-preview-remove').hide();
      },
      onComplete: function (e) {
        e.removeClass('ynadvalbum_drag');
        advalbumUpdatePhotosOrder();
      }
    });
  }

  function advalbumUpdatePhotosOrder() {
    var ids = [];
    $$('#advalbum-preview-list > li').each(function (el) {
      if (el.get('photo_id'))
        ids.push(el.get('photo_id').match(/\d+/)[0]);
    });
    var vArray = ids;
    var photo_ids = '';
    for (i = 0; i < (vArray.length); i++) {
      photo_ids = photo_ids + vArray[i] + " ";
    }
    fileids = document.getElementsByName('attachment[photo_id]')[0];
    fileids.value = photo_ids;
  }

  function advalbumUpdatePhotoList() {
    var hasPhoto = jQuery('#advalbum-preview-list').children().length;
    var fileList = jQuery('#advalbum-post-upload').children('.form-element').get(0);
    if (hasPhoto) {
      fileList.addClass('ynadvalbum_has_photo clearfix');
    } else {
      fileList.removeClass('ynadvalbum_has_photo clearfix');
    }
  }
</script>