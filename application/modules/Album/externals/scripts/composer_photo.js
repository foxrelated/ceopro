
/* $Id: composer_photo.js 10262 2014-06-05 19:47:29Z lucas $ */



(function() { // START NAMESPACE
var $ = 'id' in document ? document.id : window.$;



Composer.Plugin.Photo = new Class({

  Extends : Composer.Plugin.Interface,

  name : 'photo',

  options : {
    title : 'Add Photo',
    lang : {},
    requestOptions : false,
    fancyUploadEnabled : true,
    fancyUploadOptions : {}
  },
  allowToSetInInput: true,
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

    this.makeMenu();
    this.makeBody();

    // Generate form
    var fullUrl = this.options.requestOptions.url;
    this.elements.form = new Element('form', {
      'id' : 'compose-photo-form',
      'class' : 'compose-form',
      'method' : 'post',
      'action' : fullUrl,
      'enctype' : 'multipart/form-data'
    }).inject(this.elements.body);

    this.elements.formInput = new Element('input', {
      'id' : 'compose-photo-form-input',
      'class' : 'compose-form-input',
      'type' : 'file',
      'name' : 'Filedata',
      'events' : {
        'change' : this.doRequest.bind(this)
      }
    }).inject(this.elements.form);

    var hasFlash = false;
    try {
      var flashObj = new ActiveXObject('ShockwaveFlash.ShockwaveFlash');
      if( flashObj ) {
        hasFlash = true;
      }
    } catch (e) {
      if( navigator.mimeTypes
            && navigator.mimeTypes['application/x-shockwave-flash'] != undefined
            && navigator.mimeTypes['application/x-shockwave-flash'].enabledPlugin ) {
        hasFlash = true;
      }
    }

    // Try to init fancyupload
    if( this.options.fancyUploadEnabled && this.options.fancyUploadOptions && hasFlash ) {
      this.elements.formFancyContainer = new Element('div', {
        'styles' : {
          'display' : 'none',
          'visibility' : 'hidden'
        }
      }).inject(this.elements.body);

      // This is the status
      this.elements.formFancyStatus = new Element('div', {
        'html' : 
'<div style="display:none;">\n\
  <div class="demo-status-overall" id="demo-status-overall" style="display:none;">\n\
    <div class="overall-title"></div>\n\
    <img src="" class="progress overall-progress" />\n\
  </div>\n\
  <div class="demo-status-current" id="demo-status-current" style="display:none;">\n\
    <div class="current-title"></div>\n\
    <img src="" class="progress current-progress" />\n\
  </div>\n\
  <div class="current-text"></div>\n\
</div>'
      }).inject(this.elements.formFancyContainer);

      this.elements.scrollContainer = new Element('div', {
        'class': 'scrollbars',
        'styles' : {
          'width' : this.elements.body.getSize().x + 'px',
        }
      }).inject(this.elements.formFancyContainer);
      // This is the list
      this.elements.formFancyList = new Element('ul', {
        'class': 'compose-photos-fancy-list',
      }).inject(this.elements.scrollContainer);

      // This is the browse button
      this.elements.formFancyFile = new Element('div', {
        'id' : 'compose-photo-form-fancy-file',
        'class' : '',
      }).inject(this.elements.scrollContainer);

      new Element('a', {
        'class' : 'buttonlink',
        'html' : this._lang('Select File')
      }).inject(this.elements.formFancyFile);

      this.elements.scrollContainer.scrollbars({
        scrollBarSize:5,
        fade:true
      });
      var self = this;
      var opts = $merge({
        policyFile: ('https:' == document.location.protocol ? 'https://' : 'http://')
            + document.location.host
            + en4.core.baseUrl + 'cross-domain',
        url : fullUrl,
        appendCookieData: true,
        multiple: this.options.fancyUploadOptions.limitFiles != 1,
        typeFilter: {
          'Images (*.jpg, *.jpeg, *.gif, *.png)': '*.jpg; *.jpeg; *.gif; *.png'
        },
        target : this.elements.formFancyFile,
        container : self.elements.body,
        // Events
        onLoad: function() {
          self.elements.formFancyContainer.setStyle('display', '');
          self.elements.formFancyContainer.setStyle('visibility', 'visible');
          self.elements.form.destroy();
          this.target.addEvents({
            click: function() {
              return false;
            },
            mouseenter: function() {
              this.addClass('hover');
            },
            mouseleave: function() {
              this.removeClass('hover');
              this.blur();
            },
            mousedown: function() {
              this.focus();
            }
          });
        },
        onSelectSuccess: function(files) {
          if (self.options.fancyUploadOptions.limitFiles == 1) {
            self.elements.formFancyFile.setStyle('display', 'none');
          }

          self.elements.formFancyList.style.display = 'inline-block';
          self.getComposer().getMenu().setStyle('display', 'none');
          self.allowToSetInInput = false;

          files.each(function(file) {
            file.element.addClass('compose-photo-preview');
            file.element.getElement('.file-remove').set('html', 'X');
            file.preview = new Element('span', {
              'class' : 'compose-photo-preview-image compose-photo-preview-loading',
            }).inject(file.info, 'before');

            var overlay = new Element('span', {
              'class' : 'compose-photo-preview-overlay',
            }).inject(file.element);
            file.element.getElement('.file-remove').inject(overlay);
          });
          self.updateScrollBar();
          var scrollbarContent = self.elements.formFancyList.getParent('.scrollbar-content');
          scrollbarContent.scrollTo(self.elements.formFancyFile.getPosition().x, scrollbarContent.getScroll().y);
          this.start();
        },
        onFileRemove: function(file) {
          if(file.rawParams && file.rawParams.photo_id) {
            self.removePhoto(file.rawParams.photo_id);
          }
         (function() {self.updateScrollBar();}).delay(1000);
        },
        onFileSuccess: function(file, response) {
          var json = new Hash(JSON.decode(response, true) || {});
          self.doProcessResponse(json, file);
        },
        onFail: function() {
          self.elements.formFancyContainer.destroy();
        },
        onComplete: function() {
          self.allowToSetInInput = true;
        },
      }, this.options.fancyUploadOptions);

      try {
        this.elements.formFancyUpload = new FancyUpload2(this.elements.formFancyStatus, this.elements.formFancyList, opts);
      } catch( e ) {
        //if( $type(console) ) console.log(e);
      }
    }
  },

  updateScrollBar: function () {
    var height = this.elements.formFancyFile.offsetHeight;
    if( height == 0 ) {
      height = 106;
    }
    var scrollbarContent = this.elements.formFancyList.getParent();
    scrollbarContent.setStyle('height', height + 20);
    var li = this.elements.formFancyList.getElements('li');
    scrollbarContent.setStyle('width', ((li[0].getSize().x + 11) * li.length) + this.elements.formFancyFile.getSize().x + 10);
    this.elements.scrollContainer.retrieve('scrollbars').updateScrollBars();
    scrollbarContent.getParent().setStyle('overflow', 'hidden');
  },

  deactivate : function() {
    if( !this.active ) return;
    this.parent();
  },

  doRequest : function() {
    this.elements.iframe = new IFrame({
      'name' : 'composePhotoFrame',
      'src' : 'javascript:false;',
      'styles' : {
        'display' : 'none'
      },
      'events' : {
        'load' : function() {
          if (window._composePhotoResponse) {
            this.doProcessResponse(window._composePhotoResponse);
          }
          window._composePhotoResponse = false;
        }.bind(this)
      }
    }).inject(this.elements.body);

    window._composePhotoResponse = false;
    this.elements.form.set('target', 'composePhotoFrame');

    // Submit and then destroy form
    this.elements.form.submit();
    this.elements.form.destroy();

    // Start loading screen
    this.makeLoading();
  },

  doProcessResponse : function(responseJSON, file) {
	  if( typeof responseJSON == 'object' && typeof responseJSON.error != 'undefined' ) {
		  if( this.elements.loading ) {
			  this.elements.loading.destroy();
		  }
		  this.elements.body.empty();
		  return this.makeError(responseJSON.error, 'empty');
	  }

    // An error occurred
    if( ($type(responseJSON) != 'hash' && $type(responseJSON) != 'object') || $type(responseJSON.src) != 'string' || $type(parseInt(responseJSON.photo_id)) != 'number' ) {
      this.elements.loading ? this.elements.loading.destroy() : '';
      this.elements.body.empty();
      if( responseJSON.error == 'Invalid data' ) {
        this.makeError(this._lang('The image you tried to upload exceeds the maximum file size.'), 'empty');
      } else {
        this.makeError(this._lang(responseJSON.error), 'empty');
      }
      return;
      //throw "unable to upload image";
    }

    // Success
    file = file || {};
    file.rawParams = responseJSON;
    this.setPhotoId(responseJSON.photo_id);
    this.elements.preview = Asset.image(responseJSON.src, {
      'id' : 'compose-photo-preview-image',
      'class' : 'compose-preview-image',
      'onload' : (function() {
        this.doImageLoaded(file);
      }.bind(this))
    });
  },

  doImageLoaded : function(file) {
    //compose-photo-error
    if($('compose-photo-error')){
      $('compose-photo-error').destroy();
    }

    if( this.elements.loading ) this.elements.loading.destroy();
    if( this.elements.formFancyContainer ) {
      file.preview.removeClass('compose-photo-preview-loading');
      file.preview.setStyle('backgroundImage', 'url(' + this.elements.preview.src + ')' );
    } else {
      this.elements.preview.erase('width');
      this.elements.preview.erase('height');
      this.elements.preview.inject(this.elements.body);
    }
    if(this.allowToSetInInput) {
      this.makeFormInputs();
    }
  },

  removePhoto: function(removePhotoId) {
    this.setPhotoId(removePhotoId, 'remove');
    var photo_id = this.setPhotoId(removePhotoId, 'remove');
    photo_id.erase(removePhotoId);
    if (photo_id.length === 0) {
      this.getComposer().deactivate();
      this.activate();
      return;
    }
    this.makeFormInputs();
  },

  setPhotoId: function (photoId, action) {
    var photo_id =  this.params.get('photo_id') || [];
    if (action === 'remove') {
      photo_id.erase(photoId);
    } else {
      photo_id.push(photoId);
    }
    this.params.set('photo_id', photo_id);
    return photo_id;
  },

  makeFormInputs : function() {
    this.ready();
    if( this.elements.has('attachmentFormPhoto_id') ) {
      return this.setFormInputValue('photo_id', this.getPhotoIdsString());
    }

    this.parent({
      'photo_id': this.getPhotoIdsString()
    });
  },

  getPhotoIdsString : function() {
    var photo_id_str = '';
    this.params.photo_id.each(function(value) {
      photo_id_str += value + ',';
    });
    return photo_id_str.substr(0, photo_id_str.length-1);
  }
});



})(); // END NAMESPACE
