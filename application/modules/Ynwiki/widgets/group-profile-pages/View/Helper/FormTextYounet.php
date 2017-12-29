<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: FormText.php 9382 2011-10-14 00:41:45Z john $
 */


/**
 * Abstract class for extension
 */
// require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a "text" element
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Ynwiki_Widget_View_Helper_FormTextYounet extends Engine_View_Helper_FormText
{
    /**
     * Generates a 'text' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are used in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formTextYounet($name, $value = null, $attribs = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // name, value, attribs, options, listsep, disable

        // build the element
        $disabled = '';
        if ($disable) {
            // disabled
            $disabled = ' disabled="disabled"';
        }
        
        // input type (support for html5)
        $type = 'text';
        if( !empty($attribs['inputType']) ) {
          $type = $attribs['inputType'];
          unset($attribs['inputType']);
        }
        
        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }
        $this->view->headLink()->appendStylesheet($this->view->layout()->staticBaseUrl . '/application/modules/Ynwiki/externals/styles/main.css');      
        $this->view->headScript()
        ->appendFile($this->view->layout()->staticBaseUrl . 'externals/autocompleter/Observer.js')
        ->appendFile($this->view->layout()->staticBaseUrl . 'application/modules/Ynwiki/externals/scripts/Autocompleter.js')
        ->appendFile($this->view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Local.js')
        ->appendFile($this->view->layout()->staticBaseUrl . 'externals/autocompleter/Autocompleter.Request.js')
        ->appendScript("
  // Populate data
  var maxRecipients = 1;
  var to = {
    id : false,
    type : false,
    title : false
  };
  var isPopulated = false;
    
  function removeFromToValue(id) {
    // code to change the values in the hidden field to have updated values
    // when recipients are removed.
    var toValues = document.getElementById('toValues').value;
    var toValueArray = toValues.split(',');
    var toValueIndex = '';

    var checkMulti = id.search(/,/);

    // check if we are removing multiple recipients
    if (checkMulti!=-1){
      var recipientsArray = id.split(',');
      for (var i = 0; i < recipientsArray.length; i++){
        removeToValue(recipientsArray[i], toValueArray);
      }
    }
    else{
      removeToValue(id, toValueArray);
    }

    // hide the wrapper for usernames if it is empty
    if (document.getElementById('toValues').value==''){
      document.getElementById('toValues-wrapper').style.height = '0';
      document.getElementById('toValues-wrapper').style.display = 'none';
    }

    document.getElementById('to-wrapper').style.display = 'block';
    document.getElementById('title-wrapper').style.display = 'none';
  }

  function removeToValue(id, toValueArray){
    for (var i = 0; i < toValueArray.length; i++){
      if (toValueArray[i]==id) toValueIndex =i;
    }

    toValueArray.splice(toValueIndex, 1);
    document.getElementById('toValues').value = toValueArray.join();
  }

  en4.core.runonce.add(function() {
    if( !isPopulated ) { // NOT POPULATED
      new Autocompleter.Request.JSON('to', en4.core.baseUrl + 'wiki/suggest', {
        'minLength': 1,
        'delay' : 250,
        'selectMode': 'pick',
        'autocompleteType': 'message',
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
          if( document.getElementById('toValues').value.split(',').length >= maxRecipients ){
            //document.getElementById('to-wrapper').style.display = 'none';
            document.getElementById('toValues-wrapper').style.display = 'block';
          }
        }
      });
    } else { // POPULATED

      var myElement = new Element('span', {
        'id' : 'tospan' + to.id,
        'class' : 'tag tag_' + to.type,
        'html' :  to.title
      });
      document.getElementById('to-element').appendChild(myElement);
      document.getElementById('to-wrapper').style.height = 'auto';

      // Hide to input?
      document.getElementById('to').style.display = 'none';
      document.getElementById('toValues-wrapper').style.display = 'none';
    }
  });
  window.onload=function()
 {
      //document.getElementById('toValues-wrapper').style.display = 'none';
      //document.getElementById('toValues').style.display = 'none';
 }")
        ->appendFile($this->view->layout()->staticBaseUrl . 'externals/mdetect/mdetect' . ( APPLICATION_ENV != 'development' ? '.min' : '' ) . '.js')
        ->appendFile($this->view->layout()->staticBaseUrl . 'application/modules/Core/externals/scripts/composer.js');

        $xhtml = '<input type="' . $this->view->escape($type) . '"'
                . ' name="' . $this->view->escape($name) . '"'
                . ' id="' . $this->view->escape($id) . '"'
                . ' value="' . $this->view->escape($value) . '"'
                . $disabled
                . $this->_htmlAttribs($attribs)
                . $endTag;
        return $xhtml.='';
    }
}
