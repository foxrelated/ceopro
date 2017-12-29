<?php

class Ynadvsearch_Model_DbTable_Modules extends Engine_Db_Table
{

    protected $_name = 'ynadvsearch_modules';

    protected $_rowClass = 'Ynadvsearch_Model_Module';

    protected $_primary = 'name';

    public function addModule ($module)
    {
        $select = $this->select()->where('name = ?', $module->name);
        $result = $this->fetchRow($select);
        if ($result) {
            $result->available = $module->enabled;
            $result->save();
        } else {
            $mod = $this->createRow();
            $mod->name = $module->name;
            $mod->title = $module->title;
            $mod->enabled = 0;
            $mod->available = $module->enabled;
            $mod->save();
        }
    }

    public function getModuleTitleByName ($name)
    {
        $select = $this->select()->where('name = ?', $name);
        $result = $this->fetchRow($select);
        return $result->title;
    }

    public function getModules ()
    {
        $select = $this->select()->where('available = 1');
        return $this->fetchAll($select);
    }

    public function updateEnabledModule ($moduleName, $enable)
    {
        $select = $this->select()->where('name = ?', $moduleName);
        $result = $this->fetchRow($select);
        $result->enabled = $enable;
        $result->save();
    }

    public function getEnabledModules ()
    {
        $moduleTable = Engine_Api::_()->getDbtable('modules', 'core');
        $moduleName = $moduleTable->info('name');
        $modName = $this->info('name');
        $select = $this->select()->from($modName, array('*',
            new Zend_Db_Expr(
                "(CASE WHEN $moduleName.name = 'user' THEN 1 ELSE $moduleName.name END)") .
            " as order_field"));
        $select->setIntegrityCheck(false)
            ->joinLeft($moduleName, "$moduleName.name = $modName.name", null)
            ->where("$moduleName.enabled = 1")
            ->where("$modName.enabled = 1")
            ->where("$modName.available = 1")
            ->order(
                "order_field");

        $results = $this->fetchAll($select);
        return $results;
    }

    public function getTypes ($module)
    {
        $manifest = Zend_Registry::get('Engine_Manifest');
        foreach ($manifest as $mod => $mani) {
            if ($mod == $module) {
                if (! isset($mani['items']))
                    continue;
                $results = $mani['items'];
            }
        }
        if (@$results) {
            return $results;
        } else {
            return array();
        }
    }

    public function getAllEnabledTypes ($modules = null)
    {
        if (! $modules) {
            $modules = $this->getEnabledModules();
        }
        $results = array();
        $manifest = Zend_Registry::get('Engine_Manifest');
        foreach ($modules as $module) {
            foreach ($manifest as $mod => $mani) {
                if ($mod == $module->name) {
                    if (! isset($mani['items']))
                        continue;
                    $results[] = $mani['items'];
                }
            }
        }
        return $results;
    }
}
