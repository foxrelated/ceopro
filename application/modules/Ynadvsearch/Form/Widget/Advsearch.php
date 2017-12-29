<?php

class Ynadvsearch_Form_Widget_Advsearch extends Engine_Form
{
	public function init()
	{
		$max = new Engine_Form_Element_Select("max");
		$max->setLabel('Number of Results')
		->setMultiOptions(array(
		1 => 1,
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => 6,
		7 => 7,
		8 => 8,
		9 => 9,
		10 => 10,
		));
		$align = new Engine_Form_Element_Select("align");
		$align->setLabel('Set Alignment of Search Result Dropbox')
		->setMultiOptions(array(
              '1' => 'Right',
              '0' => 'Left',
		))
		->setValue('0');

	 	$this->addElement('Text', 'title', array(
          'style' => 'display: none;',
          'order' => -100,
	   	  'decorators' => array(
	        'Label'
	      ),
        ));
		$this->getElement('title')->getDecorator('Label')->setOption('style', 'display: none;');
		$this->addElement($max);
		$this->addElement($align);
		$this->addElement('Select', 'nomobile', array(
          'order' => 100000 - 5,
          'style' => 'display: none;',
	   	  'decorators' => array(
	        'Label'
	      ),
          'multiOptions' => array(
            '1' => 'Yes, do not display on mobile site.',
            '0' => 'No, display on mobile site.',
          ),
          'value' => '0',
        ));
        $this->getElement('nomobile')->getDecorator('Label')->setOption('style', 'display: none;');
	}
	function checkModuleExist($module_name)
	{
		// check module exist
		$modulesTable = Engine_Api::_()->getDbtable('modules', 'core');
		$mselect = $modulesTable->select()
		->where('enabled = ?', 1)
		->where('name  = ?', $module_name);
		if(count($modulesTable->fetchAll($mselect)) <= 0)
		{
			return false;
		}
		return true;
	}
}

