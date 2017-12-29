<?php
class Ynmember_Plugin_Task_CheckFeatureMember extends Core_Plugin_Task_Abstract
{
	public function execute()
	{
		$userTbl = Engine_Api::_()->getItemTable('user');
		$userTblName = $userTbl->info('name');
		$featureTbl = Engine_Api::_()->getItemTable('ynmember_feature');
		$featureTblName = $featureTbl ->info('name');
		$select = $userTbl -> select() -> setIntegrityCheck(false)
		-> from ($userTblName)
		-> joinLeft($featureTblName, "{$userTblName}.`user_id` = {$featureTblName}.`user_id`", array("{$featureTblName}.active"))
		-> where("{$userTblName}.`enabled` = 1") -> where("{$userTblName}.`verified` = 1") -> where("{$userTblName}.`approved` = 1")
		-> where("{$featureTblName}.`active` = 1")
		-> order(new Zend_Db_Expr(('rand()')));
		$users = $userTbl -> fetchAll($select);
		foreach($users as $user)
		{
			$feature_row = Engine_Api::_() -> getItemTable('ynmember_feature') -> getFeatureRowByUserId($user -> getIdentity());
			$expiration_date = strtotime($feature_row -> expiration_date);
			//check end date
			if($expiration_date > 0)
			{
				$current_date = $today = strtotime(date("Y-m-d H:i:s"));
				if($current_date > $expiration_date)
				{
					$feature_row -> active = 0;
					$feature_row -> save();
				}
			}
		}
	}
}
