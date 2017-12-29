<?php
class Ynblog_Form_Create extends Engine_Form
{
  public $_error = array();

/*----- Init Form Function -----*/
  public function init()
  {
    // Form Attributes
    $this->setTitle('Write New Entry')
      ->setDescription('Compose your new blog entry below, then click "Post Entry" to publish the entry to your blog.')
      ->setAttrib('name', 'ynblogs_create');

    // Get user and user level
    $user = Engine_Api::_()->user()->getViewer();
    $user_level = $user->level_id;

    // Title field
    $this->addElement('Text', 'title', array(
      'label' => 'Title',
      'allowEmpty' => false,
      'required' => true,
      'filters' => array(
        new Engine_Filter_Censor(),
        'StripTags',
        new Engine_Filter_StringLength(array('max' => '63'))
    )));

    // Tag field
	    $this->addElement('Text', 'tags',array(
      'label'=>'Tags (Keywords)',
      'autocomplete' => 'off',
      'description' => 'Separate tags with commas.',
      'filters' => array(
        new Engine_Filter_Censor(),
      ),
    ));
    $this->tags->getDecorator("Description")->setOption("placement", "append");
	
	$this->addElement('File', 'photo', array(
      'label' => 'Profile Photo'
    ));
    $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
    
    // Category field
    $cat_array = Engine_Api::_()->getItemTable('blog_category')->getCategoriesAssoc();
    $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => $cat_array
          ));

    //Mode field
    $this->addElement('Select', 'draft', array(
      'label' => 'Status',
      'multiOptions' => array(
          "0" => "Published",
          "1" => "Saved As Draft"
      ),
      'description' => 'If this entry is published, it cannot be switched back to draft mode.'
    ));
    $this->draft->getDecorator('Description')->setOption('placement', 'append');

    //Content field
    $allowed_html = Engine_Api::_()->authorization()->getPermission($user_level, 'blog', 'auth_html');
    $upload_url = Zend_Controller_Front::getInstance()->getRouter()->assemble(array('action' => 'upload-photo'), 'blog_general', true);

    $this->addElement('TinyMce', 'body', array(
      'disableLoadDefaultDecorators' => true,
      'editorOptions' => array(
          'bbcode' => 1,
          'html'   => 1,
          'theme_advanced_buttons1' => array(
              'undo', 'redo', 'cleanup', 'removeformat', 'pasteword',  '|',
              'media', 'image','link', 'unlink', 'fullscreen', 'preview', 'emotions', 'code',
          ),
          'theme_advanced_buttons2' => array(
              'fontselect', 'fontsizeselect', 'bold', 'italic', 'underline',
              'strikethrough', 'forecolor', 'backcolor', '|', 'justifyleft',
              'justifycenter', 'justifyright', 'justifyfull', '|', 'outdent', 'indent', 'blockquote',
          ),
          'plugins' => array(
		   		'table', 'fullscreen', 'media', 'preview', 'paste',
		   		'code', 'image', 'textcolor', 'jbimages','link', 'emoticons'
		  ),
   		  
	      'toolbar1' => array(
		      'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|', 
		      'image', '|', 'link', '|', 'jbimages', '|', 'fullscreen', '|', 'preview',  'emoticons'
		    ),         
          'upload_url' => $upload_url,
      ),
      'required'   => true,
      'allowEmpty' => false,
      'decorators' => array('ViewHelper'),
      'filters' => array(
        new Engine_Filter_Censor(),
        new Engine_Filter_Html(array('AllowedTags'=>$allowed_html))),
    ));

    // Search privacy field
    $this->addElement('Checkbox', 'search', array(
      'label' => 'Show this blog entry in search results',
      'value' => 1,
    ));

    // Prepare privacy options
    $availableLabels = array(
      'everyone'            => 'Everyone',
      'owner_network'       => 'Friends and Networks',
      'owner_member_member' => 'Friends of Friends',
      'owner_member'        => 'Friends Only',
      'owner'               => 'Just Me'
    );

    $viewOptions = (array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $user, 'auth_view');
    $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

    // View privacy field
    if( !empty($viewOptions) && count($viewOptions) >= 1 ) {
    $this->addElement('Select', 'auth_view', array(
      'label'        => 'View Privacy',
      'description'  => 'Who may see this blog entry?',
      'multiOptions' => $viewOptions,
      'value'        => 'everyone',
    ));
    $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
    }

    $commentOptions =(array) Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('blog', $user, 'auth_comment');
    $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

    // Comment
     if( !empty($commentOptions) && count($commentOptions) >= 1 ) {
    $this->addElement('Select', 'auth_comment', array(
      'label' => 'Comment Privacy',
      'description' => 'Who may post comments on this blog entry?',
      'multiOptions' => $commentOptions,
      'value' => 'everyone',
    ));
    $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
     }

    $captcha = Engine_Api::_()->getApi('settings', 'core')->getSetting('ynblog.captcha',0);
    // Element: captcha
    if($captcha){
      $this->addElement('captcha', 'captcha', Engine_Api::_()->core()->getCaptchaOptions(array(
      )));
    }
      
    // Submit Button
    $this->addElement('Button', 'submit', array(
      'label' => 'Post Entry',
      'type' => 'submit',
    ));
  }
/*----- Tags Filter Function -----*/
public function handleTags($blog_id, $tags){
      $tagTable = Engine_Api::_()->getDbtable('tags', 'ynblog');
      $tabMapTable = Engine_Api::_()->getDbtable('tagmaps', 'ynblog');
      $tagDup = array();
      foreach( $tags as $tag )
      {
        $tag = htmlspecialchars((trim($tag)));
        if (!in_array($tag, $tagDup) && $tag !="" && strlen($tag)< 20){
          $tag_id = $this->checkTag($tag);
          // check if it is new. if new, createnew tag. else, get the tag_id and insert
          if (!$tag_id){
            $tag_id = $this->createNewTag($tag, $blog_id, $tagTable);
          }

          $tabMapTable->insert(array(
            'blog_id' => $blog_id,
            'tag_id' => $tag_id
          ));
          $tagDup[] = $tag;
        }
        if (strlen($tag)>= 20){
          $this->_error[] = $tag;
        }
      }
   }

 /*----- Tags Checking Function -----*/
 public function checkTag($text){
    $table = Engine_Api::_()->getDbtable('tags', 'ynblog');
    $select = $table->select()->order('text ASC')->where('text = ?', $text);
    $results = $table->fetchRow($select);
    $tag_id = "";
    if($results) $tag_id = $results->tag_id;
    return $tag_id;
  }

  /*----- Tags Creation Function -----*/
  public function createNewTag($text, $blog_id, $tagTable){
    $row = $tagTable->createRow();
    $row->text =  $text;
    $row->save();
    $tag_id = $row->tag_id;

    return $tag_id;
  }

}