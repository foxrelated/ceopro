<?php

class Ynfbpp_Form_Admin_Settings_Global extends Engine_Form
{
    protected $_params = array();

    public function getParams() {
        return $this -> _params;
    }

    public function setParams($params) {
        $this -> _params = $params;
    }

    public function init()
    {

        $this -> setTitle('Global Settings') -> setDescription('These settings affect all members in your community.');

        $settings = Engine_Api::_() -> getApi('settings', 'core');
        $params = $this->getParams();

        $this -> addElement('Text', 'ynfbpp_time_open', array(
            'label' => 'Opening Delay Time',
            'description' => 'Timeout before popup open in millisecond (100-1000)',
            'required'=>true,
            'validators'=>array('Int',array('Between',false,array(100,1000))),
            'value' => $settings -> getSetting('ynfbpp.time.open', 300),
        ));
        $this -> addElement('Text', 'ynfbpp_time_close', array(
            'label' => 'Closing Delay Time',
            'description' => 'Timeout before popup close in millisecond (100-1000)',
            'value' => $settings -> getSetting('ynfbpp.time.close', 300),
            'required'=>true,
            'validators'=>array('Int',array('Between',false,array(100,1000))),
        ));

        $this->addElement('Text','ynfbpp_ignore_classes',array(
            'label'=>'Ignore CSS class',
            'description'=>'Disable popup when mouse within element has following CSS class, separate by comma.',
            'value' => $settings -> getSetting('ynfbpp.ignore.classes', ''),
        ));

        $this->addElement('Radio','ynfbpp_enable_thumb',array(
            'label'=>'Enable Thumbnails',
            'description'=>'Enable popup when mouseover on thumbnail',
            'multiOptions'=>array(
                1=>'Yes',
                0=>'No'
            ),
            'required'=>true,
            'value' => $settings -> getSetting('ynfbpp.enable.thumb', 1),
        ));

        $this->addElement('Radio','ynfbpp_enabled_admin',array(
            'label'=>'Enable Back End',
            'description'=>'Enable profile popup at admin control panel',
            'multiOptions'=>array(
                '1'=>'Yes',
                'O'=>'No'
            ),
            'required'=>true,
            'value' => $settings -> getSetting('ynfbpp.enabled.admin', 0),
        ));

        $this ->addElement('heading', 'looks-n-feels', array(
            'label' => 'Profile Popup Configuration',
        ));

        $this->addElement('Heading', 'guide_picture', array(
            'value' => '<img src="application/modules/Ynfbpp/externals/images/guide_picture.jpg" />'
        ));

        $this->guide_picture->removeDecorator('label');

        $colorSettings = array(
            'popup_backgroundcolor' => 'Background Color',
            'popup_footercolor' => 'Footer Color',
            'popup_bordercolor' => 'Border Color',
        );

        $defaultColor = array(
            'popup_backgroundcolor' => '#FFFFFF',
            'popup_footercolor' => '#F8F8F8',
            'popup_bordercolor' => '#EAEAEA',
        );

        foreach ($colorSettings as $key => $value) {
            $color = $settings->getSetting('ynfbpp_'.$key, $defaultColor[$key]);
            if (isset($params[$key])) {
                $color = $params[$key];
            }
            $this->addElement('Heading', 'ynfbpp_'.$key, array(
                'label' => $value,
                'value' => '<input value="'.$color.'" type="color" id="'.$key.'" name="'.$key.'"/>'
            ));
        }

//        $borderWeight = array();
//        for ($i =0; $i<51; $i++){
//            $borderWeight[$i] = $i . ' px';
//        }

//        $borderDash = array(
//            'none' => ucwords('none'),
//            'hidden' => ucwords('hidden'),
//            'dotted' => ucwords('dotted'),
//            'dashed' => ucwords('dashed'),
//            'solid' => ucwords('solid'),
//            'double' => ucwords('double'),
//            'groove' => ucwords('groove'),
//            'ridge' => ucwords('ridge'),
//            'inset' => ucwords('inset'),
//            'outset' => ucwords('outset'),
//        );

//        $this->addElement('Select','ynfbpp_popup_borderweight',array(
//            'label'=>'Border Weight',
//            'multiOptions'=>$borderWeight,
//            'required'=>true,
//            'value' => $settings -> getSetting('ynfbpp.popup.borderweight', 1),
//        ));
//        $this->addElement('Select','ynfbpp_popup_borderdash',array(
//            'label'=>'Border Dash',
//            'multiOptions'=>$borderDash,
//            'required'=>true,
//            'value' => $settings -> getSetting('ynfbpp.popup.borderdash', 1),
//        ));

        $this ->addElement('heading', 'preview', array(
            'label' => '',
            'description' => "<a href='javascript:void(0);' onclick='ynfbppAdminPreview(this)'>Preview</a>"
        ));


        $this ->addElement('dummy', 'preview_popup', array(
            'label' => '',
            'description' => '',
            'decorators' => array(
                array(
                    'ViewScript',
                    array(
                        'viewScript' => '_preview-popup.tpl'
                    )
                )
            )
        ));

        $this->preview->getDecorator('Description')->setEscape(false);

        // Add submit button
        $this -> addElement('Button', 'submit', array(
            'label' => 'Save Changes',
            'type' => 'submit',
            'ignore' => true
        ));
    }
}
