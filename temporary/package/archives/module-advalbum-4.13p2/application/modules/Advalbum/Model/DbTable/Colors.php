<?php
class Advalbum_Model_DbTable_Colors extends Engine_Db_Table
{
	protected $_rowClass = 'Advalbum_Model_Color';
	
	public function setDefault()
	{
		$colors = $this->fetchAll($this->select());
		foreach ($colors as $color)
		{
			$color->hex_value = $color->default_hex_value;
			$color->save();	
		}
	}
	
	public function getColorArray()
	{
		$colors = $this->fetchAll($this->select());
		$result = array();
		foreach ($colors as $color)
		{
			$result [$color->getTitle()] = $color->hex_value;				
		}
		return $result;
	}
	
	public function getColorIds()
	{
		$colors = $this->fetchAll($this->select());
		$result = array();
		foreach ($colors as $color)
		{
			$result [$color->title] = $color->getIdentity();
		}
		return $result;
	}
	
	public function getAllColors()
	{
		$colors = $this->fetchAll($this->select());
		return $colors;
	}
	
	public function getColorAssoc()
	{
		$colorList = $this -> getAllColors();
		$colors = array();
	    foreach ($colorList as $color){
	    	$colors[$color->getTitle()] = $color->getTitle();
	    }
	    return $colors;
	}
}