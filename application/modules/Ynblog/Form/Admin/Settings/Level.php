<?php
class Ynblog_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // Form title and description
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("BLOG_FORM_ADMIN_LEVEL_DESCRIPTION");

    // View Settings
    $this->addElement('Radio', 'view', array(
      'label'        => 'Allow Viewing of Blogs?',
      'description'  => 'Do you want to let members view blogs? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
          2 => 'Yes, allow viewing of all blogs, even private ones.',
          1 => 'Yes, allow viewing of blogs.',
          0 => 'No, do not allow blogs to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

      // Create Settings
      $this->addElement('Radio', 'create', array(
        'label'        => 'Allow Creation of Blogs?',
        'description'  => 'Do you want to let members create blogs? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view blogs, but only want certain levels to be able to create blogs.',
        'multiOptions' => array(
            1 => 'Yes, allow creation of blogs.',
            0 => 'No, do not allow blogs to be created.'
        ),
        'value' => 1,
      ));

      // Edit Settings
      $this->addElement('Radio', 'edit', array(
        'label'        => 'Allow Editing of Blogs?',
        'description'  => 'Do you want to let members edit blogs? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow members to edit all blogs.',
            1 => 'Yes, allow members to edit their own blogs.',
            0 => 'No, do not allow members to edit their blogs.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Delete Settings
      $this->addElement('Radio', 'delete', array(
        'label'        => 'Allow Deletion of Blogs?',
        'description'  => 'Do you want to let members delete blogs? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
            2 => 'Yes, allow members to delete all blogs.',
            1 => 'Yes, allow members to delete their own blogs.',
            0 => 'No, do not allow members to delete their blogs.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Comment Settings
      $this->addElement('Radio', 'comment', array(
        'label'        => 'Allow Commenting on Blogs?',
        'description'  => 'Do you want to let members of this level comment on blogs?',
        'multiOptions' => array(
            2 => 'Yes, allow members to comment on all blogs, including private ones.',
            1 => 'Yes, allow members to comment on blogs.',
            0 => 'No, do not allow members to comment on blogs.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }

      // View Privacy
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label'        => 'Blog Entry Privacy',
        'description'  => 'Your members can choose from any of the options checked below when they decide who can see their blog entries. These options appear on your members\' "Add Entry" and "Edit Entry" pages. If you do not check any options, everyone will be allowed to view blogs.',
        'multiOptions' => array(
            'everyone'            => 'Everyone',
            'owner_network'       => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member'        => 'Friends Only',
            'owner'               => 'Just Me',
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));

      // Comment Privacy
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label'        => 'Blog Comment Options',
        'description'  => 'Your members can choose from any of the options checked below when they decide who can post comments on their entries. If you do not check any options, everyone will be allowed to post comments on entries.',
        'multiOptions' => array(
            'everyone'            => 'Everyone',
            'owner_network'       => 'Friends and Networks',
            'owner_member_member' => 'Friends of Friends',
            'owner_member'        => 'Friends Only',
            'owner'               => 'Just Me',
        ),
        'value' => array('everyone', 'owner_network', 'owner_member_member', 'owner_member', 'owner'),
      ));

      // Element: style
      $this->addElement('Radio', 'style', array(
        'label'        => 'Allow Custom CSS Styles?',
        'description'  => 'If you enable this feature, your members will be able to customize the colors and fonts of their blogs by altering their CSS styles.',
        'multiOptions' => array(
          1 => 'Yes, enable custom CSS styles.',
          0 => 'No, disable custom CSS styles.',
        ),
        'value' => 1,
      ));

      // HTML Allowed Tags
      $this->addElement('Text', 'auth_html', array(
        'label'       => 'HTML in Blog Entries?',
        'description' => 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: b, img, a, embed, font',
        'value'       => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'
      ));

      // Maximum Blogs Number
      $this->addElement('Text', 'max', array(
        'label'       => 'Maximum Allowed Blog Entries?',
        'description' => 'Enter the maximum number of allowed blog entries. The field must contain an integer between 1 and 1000, or 0 for unlimited.',
        'required'    => true,
        'onKeyPress'  => "return checkIt(event)",
        'maxlength'   => '4',
        'value'       => 0,
        'validators'  => array(
            array('Int', true),
            //new Engine_Validate_AtLeast(0),
        	array('Between', true, array(0, 1000, true)),
          ),
      ));
    }
  }
}