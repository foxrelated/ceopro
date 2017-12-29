<?php

class Advalbum_Form_Admin_Global extends Engine_Form
{
    public function init()
    {
        $settings = Engine_Api::_()->getApi('settings', 'core');

        $this
            ->setTitle('Global Settings')
            ->setDescription('These settings affect all members in your community.');


        $this->addElement('Radio', 'album_privacy', array(
            'label' => 'Global Privacy',
            'description' => 'Do not display albums and photos if they lack of permission. This will slow down performance.',
            'required' => true,
            'multiOptions' => array(
                1 => 'Yes',
                0 => 'No'
            ),
            'value' => $settings->getSetting('album.privacy', 0),
        ));


        $this->addElement('Text', 'album_max_file_size', array(
            'label' => 'Maximum Upload File Size',
            'description' => 'What is the maximum file size (in kilobytes) for uploaded photos? Leave 0 to set no limits.',
            'value' => 4096,
            'validators' => array(
                "Digits",
            ),
        ));

        $options = array(
            'crop' => 'Resize and crop to fit the thumbnail box',
            'resize' => 'Resize and keep the ratio',
        );

        $this->addElement('Select', 'album_thumbnailstyle', array(
            'label' => 'Thumbnail Style',
            'description' => 'Select the way you want to create the thumbails',
            'multiOptions' => $options,
            'value' => 'crop',
        ));

        $this->addElement('Text', 'album_default_photo_title', array(
            'label' => 'Default Photo Title',
            'description' => 'When the photo has no title, this default text will be used as photo title. If this value is set as empty and the photo has no title, system will show nothing.',
            'value' => '[Untitled]',
        ));

        // Add submit button
        $this->addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true,
        ));
    }
}