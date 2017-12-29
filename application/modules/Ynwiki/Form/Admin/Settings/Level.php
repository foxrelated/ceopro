<?php

class Ynwiki_Form_Admin_Settings_Level extends Authorization_Form_Admin_Level_Abstract
{
  public function init()
  {
    parent::init();

    // My stuff
    $this
      ->setTitle('Member Level Settings')
      ->setDescription("These settings are applied on a per member level basis. Start by selecting the member level you want to modify, then adjust the settings for that level below.");

    // Element: view
    $this->addElement('Radio', 'view', array(
      'label' => 'Allow Viewing of Wiki Page?',
      'description' => 'Do you want to let members view pages? If set to no, some other settings on this page may not apply.',
      'multiOptions' => array(
        2 => 'Yes, allow viewing of all pages, even private ones.',
        1 => 'Yes, allow viewing of pages.',
        0 => 'No, do not allow pages to be viewed.',
      ),
      'value' => ( $this->isModerator() ? 2 : 1 ),
    ));
    if( !$this->isModerator() ) {
      unset($this->view->options[2]);
    }

    if( !$this->isPublic() ) {

    if($this->isModerator())
    {
      // Element: createspase
      $this->addElement('Radio', 'createspase', array(
        'label' => 'Allow Creation of Space?',
        'description' => 'Do you want to let members create spaces? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view pages, but only want certain levels to be able to create spaces.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of spaces.',
          0 => 'No, do not allow spaces to be created.'
        ),
        'value' => 1,
      ));
    }
    else
    {
         // Element: createspase
      $this->addElement('Radio', 'createspase', array(
        'label' => 'Allow Creation of Space?',
        'description' => 'Do you want to let members create spaces? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view pages, but only want certain levels to be able to create spaces.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of spaces.',
          0 => 'No, do not allow spaces to be created.'
        ),
        'value' => 0,
      ));
    }
        
        // Element: create
      $this->addElement('Radio', 'create', array(
        'label' => 'Allow Creation of Wiki Page?',
        'description' => 'Do you want to let members create pages? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view pages, but only want certain levels to be able to create pages.',
        'multiOptions' => array(
          1 => 'Yes, allow creation of pages.',
          0 => 'No, do not allow pages to be created.'
        ),
        'value' => 1,
      ));
      
      // Element: create
      $this->addElement('Radio', 'restrict', array(
        'label' => 'Allow Restrict of Wiki Page?',
        'description' => 'Do you want to let members restrict pages? If set to no, some other settings on this page may not apply. This is useful if you want members to be able to view pages, but only want certain levels to be able to restrict pages.',
        'multiOptions' => array(
          2 => 'Yes, allow members to restrict all pages.',
          1 => 'Yes, allow members to restrict their own pages.',
          0 => 'No, do not allow members to restrict their pages.'
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ), 
      ));
      if( !$this->isModerator() ) {
        unset($this->restrict->options[2]);
      }

      // Element: edit
      $this->addElement('Radio', 'edit', array(
        'label' => 'Allow Editing of Pages?',
        'description' => 'Do you want to let members edit pages? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to edit all pages.',
          1 => 'Yes, allow members to edit their own pages.',
          0 => 'No, do not allow members to edit their pages.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->edit->options[2]);
      }

      // Element: delete
      $this->addElement('Radio', 'delete', array(
        'label' => 'Allow Deletion of Pages?',
        'description' => 'Do you want to let members delete pages? If set to no, some other settings on this page may not apply.',
        'multiOptions' => array(
          2 => 'Yes, allow members to delete all pages.',
          1 => 'Yes, allow members to delete their own pages.',
          0 => 'No, do not allow members to delete their pages.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->delete->options[2]);
      }

      // Element: comment
      $this->addElement('Radio', 'comment', array(
        'label' => 'Allow Commenting on Pages?',
        'description' => 'Do you want to let members of this level comment on pages?',
        'multiOptions' => array(
          2 => 'Yes, allow members to comment on all pages, including private ones.',
          1 => 'Yes, allow members to comment on pages.',
          0 => 'No, do not allow members to comment on pages.',
        ),
        'value' => ( $this->isModerator() ? 2 : 1 ),
      ));
      if( !$this->isModerator() ) {
        unset($this->comment->options[2]);
      }
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_restrict', array(
        'label' => 'Page Restrict Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can restrict their page entries. These options appear on your members\' \'Add Page\' and \'Edit Page\' pages. If you do not check any options, owner will be allowed to restrict pages.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'parent_member'       => 'Group Members (group wikis only)',
          'member'   => 'Wiki members',
          'ynwiki_list' 	  => 'Owner and Officer',
        ),
        'value' => array('everyone', 'registered', 'parent_member', 'member', 'ynwiki_list'),
      ));
      
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_view', array(
        'label' => 'Page Entry Privacy',
        'description' => 'Your members can choose from any of the options checked below when they decide who can see their page entries. These options appear on your members\' \'Add Page\' and \'Edit Page\' pages. If you do not check any options, owner will be allowed to view pages.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'parent_member'       => 'Group Members (group wikis only)',
          'member'   => 'Wiki members',
          'ynwiki_list' 	  => 'Owner and Officer',
        ),
        'value' => array('everyone', 'registered', 'parent_member', 'member', 'ynwiki_list'),
      ));
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_edit', array(
        'label' => 'Page Edit Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can edit their page entries. These options appear on your members\' \'Add Page\' and \'Edit Page\' pages. If you do not check any options, owner will be allowed to edit pages.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'parent_member'       => 'Group Members (group wikis only)',
          'member'   => 'Wiki members',
          'ynwiki_list' 	  => 'Owner and Officer',
        ),
        'value' => array('everyone', 'registered', 'parent_member', 'member', 'ynwiki_list'),
      ));
      // Element: auth_view
      $this->addElement('MultiCheckbox', 'auth_delete', array(
        'label' => 'Page Delete Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can delete their page entries. These options appear on your members\' \'Add Page\' and \'Edit Page\' pages. If you do not check any options, owner will be allowed to delete pages.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'parent_member'       => 'Group Members (group wikis only)',
          'member'   => 'Wiki members',
          'ynwiki_list' 	  => 'Owner and Officer',
        ),
        'value' => array('everyone', 'registered', 'parent_member', 'member', 'ynwiki_list'),
      ));

      // Element: auth_comment
      $this->addElement('MultiCheckbox', 'auth_comment', array(
        'label' => 'Page Comment Options',
        'description' => 'Your members can choose from any of the options checked below when they decide who can post comments on their pages. If you do not check any options, owner will be allowed to post comments on pages.',
        'multiOptions' => array(
          'everyone'            => 'Everyone',
          'registered'          => 'All Registered Members',
          'parent_member'       => 'Group Members (group wikis only)',
          'member'   => 'Wiki members',
          'ynwiki_list' 	  => 'Owner and Officer',
        ),
        'value' => array('everyone', 'registered', 'parent_member', 'member', 'ynwiki_list'),
      ));
      
      // Element: auth_html
      $this->addElement('Text', 'auth_html', array(
        'label' => 'HTML in Page Entries?',
        'description' => 'If you want to allow specific HTML tags, you can enter them below (separated by commas). Example: b, img, a, embed, font',
        'value' => 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr'
      ));
      
      $this->addElement('Text', 'auth_file', array(
      		'label' => 'File type is authorized ?',
      		'description' => 'If you want to allow specific file type, you can enter them below (separated by commas). Example: zip, rar, pdf, txt, html, php, tpl, doc, docx',
      		'value' => 'zip, rar, pdf, txt, html, php, tpl, doc, docx, xls, xlsx, pptx, exe, gif, png, jpg, jpeg, mp3, wav, xmind, mpeg, mpg, mpe, mov, avi, tar, tgz, tar.gz'
      ));
    }
  }
}