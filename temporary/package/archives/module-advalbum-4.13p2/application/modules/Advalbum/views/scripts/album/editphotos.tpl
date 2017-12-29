<?php
/**
 * SocialEngine
 *
 * @category   Application_Extensions
 * @package    Album
 * @copyright  Copyright 2006-2010 Webligo Developments
 * @license    http://www.socialengine.net/license/
 * @version    $Id: editphotos.tpl 7249 2010-09-01 04:15:19Z john $
 * @author     Sami
 */
?>
<?php
    $googleApi = Engine_Api::_()->getApi('settings', 'core') -> getSetting('yncore.google.api.key', 'AIzaSyBUNcrjiTBFrc5NS9b_yrmH6TNRRUkSQzM');
	$this->headScript()->appendFile('https://maps.googleapis.com/maps/api/js?libraries=places&key=' . $googleApi)
        ->appendFile($this->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/Autocompleter.Custom.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/Autocompleter.Local.js')
        ->appendFile($this->layout()->staticBaseUrl . 'application/modules/Advalbum/externals/scripts/Autocompleter.Request.js');
    $session = new Zend_Session_Namespace('mobile');
?>
<?php
$menu = $this->partial('_menu.tpl', array());
echo $menu;
?>

<div class="layout_middle">
    <h3 >
        <?php echo $this->translate('Manage Photos');?>
        - <span><?php echo $this->htmlLink($this->album->getHref(), $this->album->getTitle()) ?>
  (<?php echo $this->translate(array('%d advalbum_photo', '%s photos', $this->album->count()),$this->locale()->toNumber($this->album->count())) ?>)</span>
    </h3>

    <?php if ($this->paginator->count() > 0):?>
    <?php
    $currentYear = date('Y');
    $smallestYear = $currentYear - 99;
    $albumId = $this->album->getIdentity();
    ?>
    <form id="ynadvalbum_editphotos_form" class="global_form" action="<?php echo $this->escape($this->form->getAction()) ?>" method="post">
        <ul id="ynadvalbum_manage_photo-items" class="ynadvalbum_manage_photo-items clearfix">
            <div id="ynadvalbum_canloadmore" current_page="1">
                <div id="ynadvalbum_loadmore" style="background-image: url(application/modules/Advalbum/externals/images/load_more.gif);"></div>
            </div>
        </ul>

        <div class="ynadvalbum_manage_photo_parent_button">
            <button type="button" id="ynadvalbum_manage_photo_save-btn" onclick="return ynadvalbumSavePhotos();"><?php echo '<span class="ynicon yn-check"></span>'.$this->translate('Save changes')?></button>
            <button type="button" id="ynadvalbum_manage_photo_move-btn" onclick="return ynadvalbumMovePhotos();"><?php echo '<span class="ynicon yn-folder-open"></span>'.$this->translate('Move to other album')?></button>
            <button type="button" id="ynadvalbum_manage_photo_delete-btn" onclick="return ynadvalbumDeletePhotos();"><?php echo '<span class="ynicon yn-trash"></span>'.$this->translate('Delete selected items')?></button>
        </div>
    </form>
    <?php else :?>
    <div class="tip">
        <span><?php echo $this->translate("There is no photo in this album.");?></span>
    </div>
    <?php endif;?>

    <?php if( $this->paginator->count() > 0 ): ?>
    <br />
    <?php echo $this->paginationControl($this->paginator, null, array("paginator.tpl","advalbum")); ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
    window.addEvent('domready', function() {
        ynadvalbumLoadMorePhotos();
        window.onscroll = ynadvalbumOnLoadMorePhoto;
        var isMobile = '<?php echo $session-> mobile ?>';
        if (isMobile) {
            $('global_wrapper').grab($$('.ynadvalbum_manage_photo_parent_button')[0], 'after');
        }
    });

    var isLoading = false;
    var max_color = <?php echo $this->max_color ?>;
    var colors = <?php echo json_encode($this->colors) ?>;
    var single_color = (max_color == 1);

    function ynadvalbumManagePhotosEvents() {

        Smoothbox.bind('.smoothbox');

        $$('.ynadvalbum-manage-color-item').removeEvents('click').addEvent('click', function(){
            // check if this color is active and remove it from selection
            var colorBox = this.getParents('.ynadvalbum_manage_photo-info')[0].getElements('.ynadvalbum-manage-color-box')[0];
            var colorCurrent  = colorBox.get('value');
            var colorArr = [];
            if (colorCurrent) {
                colorArr = colorBox.get('value').split(',');
            }
            // not allow remove the last color
            if (this.hasClass('selected') && colorArr.length == 1) {
                return;
            }
            // single color mode
            if (single_color) {
                this.getSiblings('.ynadvalbum-manage-color-item').removeClass('selected');
                this.addClass('selected');
                colorArr = [this.get('color_name')];
            } else {
                if (!this.hasClass('selected') && colorArr.length >= max_color) {
                    return alert("<?php echo $this->translate('Maximum main colors is only ') . $this->max_color;?>");
                }
                var colorSelected = this.get('color_name');
                this.toggleClass('selected');
                if (this.hasClass('selected')){
                    colorArr.push(colorSelected);
                } else {
                    var index = colorArr.indexOf(colorSelected);
                    if (index > -1){
                        colorArr.splice(index, 1);
                    }
                }
            }

            colorBox.set('value', colorArr.join(','));
            if (colorArr.length >= max_color) {
                ynadvalbumSwitchColorPalettes(true, this);
            }
            if (colorArr.length) {
                this.getParents('.ynadvalbum_manage_photo-input-options').getElement('.yn-arr-down').setStyle('background-color', colors[colorArr[0]]);
            } else {
                this.getParents('.ynadvalbum_manage_photo-input-options').getElement('.yn-arr-down').setStyle('background-color', '');
            }
        });

        $$('.ynadvalbum-setting').removeEvents('click').addEvent('click', function() {
            this.getParent('.ynadvalbum_setting').getChildren('.ynadvalbum-setting-option').toggleClass('show');
            this.toggleClass('color');
        });
        $$('.ynadvalbum-setting').addEvent('outerClick', function() {
            this.getParent('.ynadvalbum_setting').getChildren('.ynadvalbum-setting-option').removeClass('show');
            this.removeClass('color');
        });

        $$('.ynadvalbum_item').removeEvents('click').addEvent('click', function() {
            var parent_class = this.get('tab_class');
            var parent = this.getParent('.ynadvalbum_manage_photo-info');
            var photo_id = parent.getParent('.ynadvalbum_manage_photo-item').get('id');
            var isActive = this.hasClass('active');
            this.getSiblings('.ynadvalbum_item').removeClass('active');
            this.getParent('.ynadvalbum_manage_photo-info').removeClass('ynadvalbum_show_datetime');
            this.getParent('.ynadvalbum_manage_photo-info').removeClass('ynadvalbum_show_local');
            this.getParent('.ynadvalbum_manage_photo-info').removeClass('ynadvalbum_show_name');
            this.getParent('.ynadvalbum_manage_photo-info').removeClass('ynadvalbum_show_color');
            if (!isActive) {
                this.getParent('.ynadvalbum_manage_photo-info').addClass(parent_class);
                if (this.hasClass('ynadvalbum_add-name')) {
                    ynadvalbumAddTagCompleter(photo_id);
                } else if (this.hasClass('ynadvalbum_add-location')) {
                    ynadvalbumAddGoogleCompleter(photo_id);
                }
            }
            this.toggleClass('active');
            if (this.hasClass('ynadvalbum_add-color')) {
                ynadvalbumSwitchColorPalettes(isActive, this);
            } else {
                ynadvalbumSwitchColorPalettes(true, this);
            }
        });

        $$('.ynadvalbum_check').removeEvents('click').addEvent('click', function() {
            $$('.ynadvalbum_make-cover').removeClass('background');
            this.getParents('.ynadvalbum_make-cover').addClass('background');
        });

        $$('.ynadvalbum_names_wrapper').removeEvents('click').addEvent('click', function() {
            this.getChildren('input')[0].focus();
        });

        $$('.ynadvalbum_manage_album-name').removeEvents('input').addEvent('input', function() {
            if (this.get('value').length > 5) {
                this.setAttribute( 'style', 'width: ' + this.get('value').length * 10 + 'px !important' );
            }
        });
    }

    function ynadvalbumSwitchColorPalettes(active, el) {
        var inputOptions = el.getParents('.ynadvalbum_manage_photo-input-options')[0];
        if (!active) {
            inputOptions.getElement('.ynadvalbum_manage_photo_color-patterns').addClass('ynadvalbum_patterns-show');
            inputOptions.getElement('.ynadvalbum_triangle_parent').addClass('ynadvalbum_triangle_parent_show');
        } else {
            inputOptions.getElement('.ynadvalbum_manage_photo_color-patterns').removeClass('ynadvalbum_patterns-show');
            inputOptions.getElement('.ynadvalbum_triangle_parent').removeClass('ynadvalbum_triangle_parent_show');
            inputOptions.getElement('.ynadvalbum_add-color').removeClass('active');
        }
    }

    function SortablesInstance() {
        var SortablesInstance;
        $$('.ynadvalbum_manage_photo-items > li').addClass('sortable');
        SortablesInstance = new Sortables($$('.ynadvalbum_manage_photo-items'), {
//            clone: true,
            constrain: true,
            handle: '.ynadvalbum_manage_photo-owner',
            onStart: function(e, clone) {
                e.addClass('ynadvalbum_drag');
            },
            onComplete: function(e) {
                var ids = [];
                $$('.ynadvalbum_manage_photo-items > li').each(function(el) {
                    ids.push(el.get('id'));
                });
                e.removeClass('ynadvalbum_drag');
                // Send request
                var url = '<?php echo $this->url(array('action' => 'order')) ?>';
                var request = new Request.JSON({
                    'url' : url,
                    'data' : {
                        format : 'json',
                        order : ids
                    }
                });
                request.send();
            }
        });
    }

    function ynadvalbumDeletePhotos() {
        var photo_ids = "";
        $$("input[name=ynadvalbum_photo_delete]:checked").each(function(el){
            photo_ids += el.get("value") + ',';
        });
        photo_ids = photo_ids.substring(0, photo_ids.length - 1);
        if (!photo_ids)
            return;
        var url = en4.core.baseUrl + 'albums/delete-photos/<?php echo $albumId ?>/photo_ids/' + photo_ids;
        Smoothbox.open(url);
        return false;
    }

    function ynadvalbumSavePhotos() {
        document.getElementById('ynadvalbum_editphotos_form').submit();
        return false;
    }
    function ynadvalbumMovePhotos() {
        var photo_ids = "";
        $$("input[name=ynadvalbum_photo_delete]:checked").each(function(el){
            photo_ids += el.get("value") + ',';
        });
        photo_ids = photo_ids.substring(0, photo_ids.length - 1);
        if (!photo_ids)
            return;
        var url = en4.core.baseUrl + 'albums/move-photos/<?php echo $albumId ?>/photo_ids/' + photo_ids;
        Smoothbox.open(url);
        return false;
    }

    function ynadvalbumOnLoadMorePhoto()
    {
        ynadvalbumRedrawButtons();
        var canLoadMore = $('ynadvalbum_canloadmore');
        if (!canLoadMore)
            return;

        if (typeof ($('ynadvalbum_loadmore').offsetParent) != 'undefined') {
            var elementPostionY = $('ynadvalbum_loadmore').offsetTop;
        } else {
            var elementPostionY = $('ynadvalbum_loadmore').y;
        }
        if (elementPostionY <= window.getScrollTop() + window.getSize().y - 60) {
            ynadvalbumLoadMorePhotos();
        }
    }

    function ynadvalbumRedrawButtons() {
        var $photoList = $('ynadvalbum_manage_photo-items'),
                buttons = $$('.ynadvalbum_manage_photo_parent_button');
        if (typeof ($photoList.offsetParent) != 'undefined') {
            var elementPostionY = $photoList.offsetTop;
        } else {
            var elementPostionY = $photoList.y;
        }
        var photoListBottom = elementPostionY + $photoList.getSize().y;

        if (photoListBottom <= window.getScrollTop() + (window.getSize().y - 62)) {
            buttons.setStyle('position','relative');
        } else {
            buttons.setStyle('position','fixed');
        }
    }

    function ynadvalbumLoadMorePhotos()
    {
        if (isLoading) return;
        isLoading = true;
        var canLoadMore = $('ynadvalbum_canloadmore');
//        canLoadMore.remove();
        var loadmoreIcon = $('ynadvalbum_loadmore');
        loadmoreIcon.show();
        var nextPage = parseInt(canLoadMore.get('current_page'));
        new Request.HTML({
            method: 'post',
            url: en4.core.baseUrl + 'albums/load-more-photos/<?php echo $albumId ?>',
            data: {
                format: 'html',
                page: nextPage
            },
            onSuccess: function (responseTree, responseElements, responseHTML, responseJavaScript) {
                canLoadMore.outerHTML = responseHTML;
                SortablesInstance();
                ynadvalbumManagePhotosEvents();
                isLoading = false;
                ynadvalbumOnLoadMorePhoto();
            }
        }).send();
    }

    function placeChanged(autocomplete, photo_id) {
        google.maps.event.addListener(autocomplete, 'place_changed', function(){
            var place = autocomplete.getPlace();
            if (!place.geometry) {
                return;
            }
            document.getElementById('advalbum_photo[' + photo_id + '][latitude]').value = place.geometry.location.lat();
            document.getElementById('advalbum_photo[' + photo_id + '][longitude]').value = place.geometry.location.lng();
        });
    }

    function ynadvalbumAddGoogleCompleter(photo_id)
    {
        var input = document.getElementById('advalbum_photo_' + photo_id + '-location');
        var autocomplete = new google.maps.places.Autocomplete(input);
        placeChanged(autocomplete, photo_id);
    }

    function ynadvalbumAddTagCompleter(photo_id)
    {
        new Autocompleter2.Request.JSON('ynadvalbum_name_' + photo_id, '<?php echo $this->url(array('module' => 'advalbum', 'controller' => 'index', 'action' => 'suggest-tag'), 'default', true) ?>', {
            'minLength': 1,
            'toElementName': 'advalbum_photo[' + photo_id + '][name]',
            'delay' : 250,
            'selectMode': 'pick',
            'autocompleteType'  : 'message',
            'multiple': false,
            'className': 'message-autosuggest',
            'filterSubset' : true,
            'tokenFormat' : 'object',
            'tokenValueKey' : 'label',
            'injectChoice': function(token){
                if(token.type == 'user'){
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices',
                        'html': token.photo,
                        'id':token.label
                    });
                    new Element('div', {
                        'html': this.markQueryValue(token.label),
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                }
                else {
                    var choice = new Element('li', {
                        'class': 'autocompleter-choices friendlist',
                        'id':token.label
                    });
                    new Element('div', {
                        'html': this.markQueryValue(token.label),
                        'class': 'autocompleter-choice'
                    }).inject(choice);
                    this.addChoiceEvents(choice).inject(this.choices);
                    choice.store('autocompleteChoice', token);
                }
            },
            onPush : function(){
//                if( document.getElementById('memberValues').value.split(',').length >= 20 ){
//                    document.getElementById('project_members').style.display = 'none';
//                }
                ynadvalbumRebuildTagList(photo_id);
            }
        });
    }

    function removeFromToValue(id, elementName) {
        // code to change the values in the hidden field to have updated values
        // when recipients are removed.
        var toValues = document.getElementById(elementName).value;
        var toValueArray = toValues.split(",");
        // remove names list
        var toTagValues = document.getElementById(elementName + '_tags').value;
        var toTagValueArray = toTagValues.split(",");

        var checkMulti = id.toString().search(/,/);
        var index = toValueArray.indexOf(id);

        // check if we are removing multiple recipients
        if (checkMulti!=-1){
            var recipientsArray = id.split(",");
            for (var i = 0; i < recipientsArray.length; i++){
                removeToValue(elementName, recipientsArray[i], toValueArray);
            }
        }
        else{
            removeToValue(elementName, id, toValueArray);
            removeToTagValue(elementName, index, toTagValueArray);
        }

        var parentEl = $(elementName).getParents('li.ynadvalbum_manage_photo-item');
        ynadvalbumRebuildTagList(parentEl.get('id'));
        // hide the wrapper for usernames if it is empty
        if (document.getElementById(elementName).value==""){
//            document.getElementById(elementName + '-wrapper').style.height = '0';
        }
    }

    function removeToValue(elementName, id, toValueArray){
        for (var i = 0; i < toValueArray.length; i++){
            if (toValueArray[i]==id) toValueIndex =i;
        }

        toValueArray.splice(toValueIndex, 1);
        document.getElementById(elementName).value = toValueArray.join();
    }

    function removeToTagValue(elementName, index, toTagValueArray){
        toTagValueArray.splice(index, 1);
        document.getElementById(elementName + '_tags').value = toTagValueArray.join();
    }

    function ynadvalbumRebuildTagList(photo_id)
    {
        var tagList = document.getElementById('advalbum_photo[' + photo_id + '][name]_tags').value;
        var tagEl = $('ynadvalbum_photo_' + photo_id + '_taglist');
        if (tagList) {
            var tagArray = tagList.split(",");
            var tagText = '<?php echo $this->translate("Tagged") ?>' + ': <span class="ynadvalbum_tag_first">' + tagArray[0] + '</span>';
            tagArray.shift();
            if (tagArray.length) {
                tagText += ' and ' + tagArray.length + '<span class="ynadvalbum_tag_second">' + ((tagArray.length = 1) ? ' other' : ' others') + '</span>';
            }
            tagEl.innerHTML = tagText;
        } else {
            tagEl.innerHTML = "";
        }
    }
</script>