<?php

class Advgroup_Form_Edit extends Engine_Form
{
    public $_error = array();
    protected $_item;

    public function getItem()
    {
        return $this->_item;
    }

    public function setItem(Core_Model_Item_Abstract $item)
    {
        $this->_item = $item;
        return $this;
    }

    public function init()
    {
        $user = Engine_Api::_()->user()->getViewer();

        $this
            ->setTitle('Edit Group');

        $this->addElement('Text', 'title', array(
            'label' => 'Group Name',
            'allowEmpty' => false,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true),
                array('StringLength', false, array(1, 64)),
            ),
            'filters' => array(
                'StripTags',
                new Engine_Filter_Censor(),
            ),
        ));

        $this->addElement('Text', 'tags', array(
            'label' => 'Tags (Keywords)',
            'autocomplete' => 'off',
            'description' => 'Separate tags with commas.',
            'filters' => array(
                new Engine_Filter_Censor(),
            ),
        ));
        $this->tags->getDecorator("Description")->setOption("placement", "append");
        $upload_url = Zend_Controller_Front::getInstance() -> getRouter() -> assemble(array('action' => 'upload-photo'), 'group_general', true);
        $allowed_html = 'strong, b, em, i, u, strike, sub, sup, p, div, pre, address, h1, h2, h3, h4, h5, h6, span, ol, li, ul, a, img, embed, br, hr, object , param, iframe';
        $this->addElement('TinyMce', 'description', array(
            'label' => 'Description',
            'editorOptions' => array(
                'bbcode' => 1,
                'mode' => 'exact',
                'elements' => "description",
                'html' => 1,
                'plugins' => array(
                    'table', 'fullscreen', 'media', 'preview', 'paste',
                    'code', 'image', 'textcolor', 'jbimages', 'link','emoticons'
                ),

                'toolbar1' => array(
                    'undo', '|', 'redo', '|', 'removeformat', '|', 'pastetext', '|', 'code', '|', 'media', '|',
                    'image', '|', 'link', '|', 'jbimages', '|', 'fullscreen', '|', 'preview', 'emoticons'
                ),
                'upload_url' => $upload_url
            ),
            'required' => true,
            'allowEmpty' => false,
            'filters' => array(
                new Engine_Filter_Censor(),
                new Engine_Filter_Html(array('AllowedTags' => $allowed_html)))
        ));

        // Cover Photo
        $this->addElement('File', 'cover_thumb', array(
            'label' => 'Cover Photo'
        ));
        $this->cover_thumb->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement('File', 'photo', array(
            'label' => 'Profile Photo'
        ));
        $this->photo->addValidator('Extension', false, 'jpg,png,gif,jpeg');

        $this->addElement('Select', 'category_id', array(
            'label' => 'Category',
            'multiOptions' => array(
                '0' => ''
            ),
        ));

        $this->addElement('Dummy', 'location_map', array(
            'label' => 'Location',
            'decorators' => array(array(
                'ViewScript',
                array(
                    'viewScript' => '_location_search.tpl',
                    'class' => 'form element',
                )
            )),
        ));

        $this->addElement('hidden', 'location_address', array(
            'value' => '0',
            'order' => '97'
        ));

        $this->addElement('hidden', 'lat', array(
            'value' => '0',
            'order' => '98'
        ));

        $this->addElement('hidden', 'long', array(
            'value' => '0',
            'order' => '99'
        ));

        $this->addElement('Radio', 'search', array(
            'label' => 'Include in search results?',
            'multiOptions' => array(
                '1' => 'Yes, include in search results.',
                '0' => 'No, hide from search results.',
            ),
            'value' => '1',
        ));

        $this->addElement('Radio', 'auth_invite', array(
            'label' => 'Let members invite others?',
            'multiOptions' => array(
                'member' => 'Yes, members can invite other people.',
                'officer' => 'No, only officers can invite other people.',
            ),
            'value' => 'member',
        ));

        $this->addElement('Radio', 'approval', array(
            'label' => 'Approve members?',
            'description' => ' When people try to join this group, should they be allowed ' .
                'to join immediately, or should they be forced to wait for approval?',
            'multiOptions' => array(
                '0' => 'New members can join immediately.',
                '1' => 'New members must be approved.',
            ),
            'value' => '0',
        ));

        // Add subforms
        if (!$this->_item) {
            $customFields = new Advgroup_Form_Custom_Fields();
        } else {
            $customFields = new Advgroup_Form_Custom_Fields(array(
                'item' => $this->getItem()
            ));
        }
        if (get_class($this) == 'Advgroup_Form_Create') {
            $customFields->setIsCreation(true);
        }

        $this->addSubForms(array(
            'fields' => $customFields
        ));

        // Privacy
        $availableLabels = array(
            'everyone' => 'Everyone',
            'registered' => 'Registered Members',
            'member' => 'All Group Members',
            'officer' => 'Officers and Owner Only',
            'owner' => 'Owner Only',
        );


        // View
        $viewOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_view');
        $viewOptions = array_intersect_key($availableLabels, array_flip($viewOptions));

        if (!empty($viewOptions) && count($viewOptions) >= 1) {
            // Make a hidden field
            if (count($viewOptions) == 1) {
                $this->addElement('hidden', 'auth_view', array('value' => key($viewOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_view', array(
                    'label' => 'View Privacy',
                    'description' => 'Who may see this group?',
                    'multiOptions' => $viewOptions,
                    'value' => key($viewOptions),
                ));
                $this->auth_view->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Comment
        $commentOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_comment');
        $commentOptions = array_intersect_key($availableLabels, array_flip($commentOptions));

        if (!empty($commentOptions) && count($commentOptions) >= 1) {
            // Make a hidden field
            if (count($commentOptions) == 1) {
                $this->addElement('hidden', 'auth_comment', array('value' => key($commentOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_comment', array(
                    'label' => 'Comment Privacy',
                    'description' => 'Who may post on this group\'s wall?',
                    'multiOptions' => $commentOptions,
                    'value' => key($commentOptions),
                ));
                $this->auth_comment->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Photo
        $photoOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_photo');
        $photoOptions = array_intersect_key($availableLabels, array_flip($photoOptions));

        if (!empty($photoOptions) && count($photoOptions) >= 1) {
            // Make a hidden field
            if (count($photoOptions) == 1) {
                $this->addElement('hidden', 'auth_photo', array('value' => key($photoOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_photo', array(
                    'label' => 'Photo - Album Creation',
                    'description' => 'Who may upload photos/albums to this group?',
                    'multiOptions' => $photoOptions,
                    'value' => key($photoOptions),
                ));
                $this->auth_photo->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Event
        $eventOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_event');
        $eventOptions = array_intersect_key($availableLabels, array_flip($eventOptions));

        if (!empty($eventOptions) && count($eventOptions) >= 1) {
            // Make a hidden field
            if (count($eventOptions) == 1) {
                $this->addElement('hidden', 'auth_event', array('value' => key($eventOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_event', array(
                    'label' => 'Event Creation',
                    'description' => 'Who may create events for this group?',
                    'multiOptions' => $eventOptions,
                    'value' => key($eventOptions),
                ));
                $this->auth_event->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        $pollOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_poll');
        $pollOptions = array_intersect_key($availableLabels, array_flip($pollOptions));

        if (!empty($pollOptions) && count($pollOptions) >= 1) {
            // Make a hidden field
            if (count($pollOptions) == 1) {
                $this->addElement('hidden', 'auth_poll', array('value' => key($pollOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_poll', array(
                    'label' => 'Poll Creation',
                    'description' => 'Who may create polls for this group?',
                    'multiOptions' => $pollOptions,
                    'value' => key($pollOptions),
                ));
                $this->auth_poll->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        //Sub-Group
        $subGroupOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_sub_group');
        $subGroupOptions = array_intersect_key($availableLabels, array_flip($subGroupOptions));

        if (!empty($subGroupOptions) && count($subGroupOptions) >= 1) {
            // Make a hidden field
            if (count($subGroupOptions) == 1) {
                $this->addElement('hidden', 'auth_sub_group', array('value' => key($pollOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_sub_group', array(
                    'label' => 'Sub Group Creation',
                    'description' => 'Who may create sub group for this group?',
                    'multiOptions' => $subGroupOptions,
                    'value' => key($subGroupOptions),
                ));
                $this->auth_sub_group->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        //Video
        $videoOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_video');
        $videoOptions = array_intersect_key($availableLabels, array_flip($videoOptions));

        if (!empty($videoOptions) && count($videoOptions) >= 1) {
            // Make a hidden field
            if (count($videoOptions) == 1) {
                $this->addElement('hidden', 'auth_video', array('value' => key($videoOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_video', array(
                    'label' => 'Video Creation',
                    'description' => 'Who may create videos for this group?',
                    'multiOptions' => $videoOptions,
                    'value' => key($videoOptions),
                ));
                $this->auth_video->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        //Wiki
        $wikiOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_wiki');
        $wikiOptions = array_intersect_key($availableLabels, array_flip($wikiOptions));

        if (!empty($wikiOptions) && count($wikiOptions) >= 1) {
            // Make a hidden field
            if (count($wikiOptions) == 1) {
                $this->addElement('hidden', 'auth_wiki', array('value' => key($wikiOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_wiki', array(
                    'label' => 'Wiki Page Creation',
                    'description' => 'Who may create wiki pages for this group?',
                    'multiOptions' => $wikiOptions,
                    'value' => key($wikiOptions),
                ));
                $this->auth_wiki->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        //Music
        $musicOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_music');
        $musicOptions = array_intersect_key($availableLabels, array_flip($musicOptions));

        if (!empty($musicOptions) && count($musicOptions) >= 1) {
            // Make a hidden field
            if (count($musicOptions) == 1) {
                $this->addElement('hidden', 'auth_music', array('value' => key($musicOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_music', array(
                    'label' => 'Music Creation',
                    'description' => 'Who may create music albums for this group?',
                    'multiOptions' => $musicOptions,
                    'value' => key($musicOptions),
                ));
                $this->auth_music->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        //Folder
        $folderOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_folder');
        $folderOptions = array_intersect_key($availableLabels, array_flip($folderOptions));

        if (!empty($folderOptions) && count($folderOptions) >= 1) {
            // Make a hidden field
            if (count($folderOptions) == 1) {
                $this->addElement('hidden', 'auth_folder', array('value' => key($folderOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_folder', array(
                    'label' => 'Folder Creation',
                    'description' => 'Who may create folder for this group?',
                    'multiOptions' => $folderOptions,
                    'value' => key($folderOptions),
                ));
                $this->auth_folder->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        //File Uploading
        $fileuploadOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_file_upload');
        $fileuploadOptions = array_intersect_key($availableLabels, array_flip($fileuploadOptions));

        if (!empty($fileuploadOptions) && count($fileuploadOptions) >= 1) {
            // Make a hidden field
            if (count($fileuploadOptions) == 1) {
                $this->addElement('hidden', 'auth_file_upload', array('value' => key($fileuploadOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_file_upload', array(
                    'label' => 'File Uploading',
                    'description' => 'Who may upload file on this group?',
                    'multiOptions' => $fileuploadOptions,
                    'value' => key($fileuploadOptions),
                ));
                $this->auth_file_upload->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        //File Downloading
        $filedownloadOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_file_down');
        $filedownloadOptions = array_intersect_key($availableLabels, array_flip($filedownloadOptions));

        if (!empty($filedownloadOptions) && count($filedownloadOptions) >= 1) {
            // Make a hidden field
            if (count($filedownloadOptions) == 1) {
                $this->addElement('hidden', 'auth_file_down', array('value' => key($filedownloadOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_file_down', array(
                    'label' => 'File Downloading',
                    'description' => 'Who may download file from this group?',
                    'multiOptions' => $filedownloadOptions,
                    'value' => key($filedownloadOptions),
                ));
                $this->auth_file_down->getDecorator('Description')->setOption('placement', 'append');
            }
        }


        //Listing
        $listingOptions = (array)Engine_Api::_()->authorization()->getAdapter('levels')->getAllowed('group', $user, 'auth_listing');
        $listingOptions = array_intersect_key($availableLabels, array_flip($listingOptions));

        if (!empty($listingOptions) && count($listingOptions) >= 1) {
            // Make a hidden field
            if (count($listingOptions) == 1) {
                $this->addElement('hidden', 'auth_listing', array('value' => key($listingOptions)));
                // Make select box
            } else {
                $this->addElement('Select', 'auth_listing', array(
                    'label' => 'Listing Creation',
                    'description' => 'Who may create listings for this group?',
                    'multiOptions' => $listingOptions,
                    'value' => key($listingOptions),
                ));
                $this->auth_listing->getDecorator('Description')->setOption('placement', 'append');
            }
        }

        // Buttons
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'onclick' => 'removeSubmit()',
            'type' => 'submit',
            'ignore' => true,
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addElement('Cancel', 'cancel', array(
            'label' => 'cancel',
            'link' => true,
            'prependText' => ' or ',
            'decorators' => array(
                'ViewHelper',
            ),
        ));

        $this->addDisplayGroup(array('submit', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements',
                'DivDivDivWrapper',
            ),
        ));
    }
}
