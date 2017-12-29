
Composer.Plugin.Photo = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'photo',

  options : {
    title : 'Add Photo',
    lang : {},
    requestOptions : false
  },

  initialize : function(options) {
    this.elements = new Hash(this.elements);
    this.params = new Hash(this.params);
    this.parent(options);
  },

  attach : function() {
    this.parent();
    this.makeActivator();
    return this;
  },

  detach : function() {
    this.parent();
    return this;
  },

  activate : function() {
    if( this.active ) return;
    this.parent();

    this.makeBody();
    this.prepareSpecialAlbum();

    // init HTML5 upload
    this.elements.uploadContainer = new Element('div', {
      'id' : 'advalbum-post-upload',
    }).inject(this.elements.body);

    this.elements.fileList = new Element('div', {
      'class' : 'form-element',
      'html':
      '<div id="files_contain" class="files-contain">\n\
        <ul id="advalbum-preview-list">\n\
        </ul>\n\
        <div class="ynadvalbum_background_upload">\n\
        <span class="ynicon yn-photos-o"></span>\n\
        <span class="ynadvalbum_upload_text">'
          + this._lang("ynadvalbum_drag_drop_photos") +
        '<span>\n\
        </div>\n\
        </div>\n\
        <span class="btn fileinput-button btn-success" type="button">\n\
        \n\
        <label for="fileupload"><span class="ynicon yn-plus"></span>' + this._lang('Add Photos') + '</label>\n\
        <input id="fileupload" type="file" name="Filedata" accept="image/*" multiple>\n\
        </span>'
    }).inject(this.elements.uploadContainer);

    // create error file list
    this.elements.errorList = new Element('div', {
      'id': 'ynadvalbum_upload_error',
      'class': 'ynadvalbum_upload_error',
      'html':
      '<div><span>' + this._lang("upload_error") + '</span></div>\n\
      <ul id="ynadvalbum_upload_error_list" class="ynadvalbum_upload_error_list clearfix">\n\
      </ul>'
    }).inject(this.elements.uploadContainer);

    this.elements.menuClose = new Element('a', {
      'href' : 'javascript:void(0);',
      'class' : 'ynicon yn-del ynadvalbum-cancel-upload',
      'events' : {
        'click' : function(e) {
          e.stop();
          this.getComposer().deactivate();
        }.bind(this)
      }
    }).inject(this.elements.fileList);

    // create a blank form input to contain the photo_ids
    this.makeFormInputs();
    // clear photo_ids
    if(document.getElementsByName('attachment[photo_id]')[0] ) {
      document.getElementsByName('attachment[photo_id]')[0].value = '';
    }

    advalbumInitUploader();
  },

  deactivate : function() {
    if( !this.active ) return;
    this.parent();
  },

  makeFormInputs : function() {
    this.ready();
    this.parent({
      'photo_id' : this.params.photo_id
    });
  },

  // create wall album if not exist
  prepareSpecialAlbum: function() {
    request = new Request.JSON({
      'format': 'json',
      'url': en4.core.baseUrl + 'advalbum/ajax/prepare-special-album',
      'data': {
        'type': 'wall',
        'isAjax' : 1
      },
      'onSuccess': function (responseJSON) {
        return false;
      }
    });
    request.send();
  },

});
