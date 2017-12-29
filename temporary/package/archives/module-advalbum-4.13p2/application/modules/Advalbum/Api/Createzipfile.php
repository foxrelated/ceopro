<?php

ob_start();

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(dirname(dirname(dirname(__FILE__))))));
require_once (APPLICATION_PATH . '/application/modules/Advalbum/externals/lib/zip_min.inc');

class Advalbum_Api_Createzipfile extends Core_Api_Abstract
{
	/*
	 * @todo
	 */
	public function downloadAlbum($album_id)
	{
		$apiSetting = Engine_Api::_() -> getApi('settings', 'core');

		$album = Engine_Api::_() -> getItem('advalbum_album', $album_id);

		if (!is_object($album))
		{
			exit();
		}

		if ($album->virtual)
		{
			$album_photos = $album -> getVirtualPhotos();
		}
		else
		{
			$album_photos = $album -> getCollectiblesPaginator();
		}
		
		if ($album_photos -> getTotalItemCount() <= 0)
		{
			exit();
		}

		$zip = new zipfile();

		$index = 0;

		$default_photo_title = $apiSetting -> getSetting('album_default_photo_title', Zend_Registry::get('Zend_Translate') -> _('[Untitled]'));
        $photo_title_arr = array();
		foreach ($album_photos as $photo)
		{

			if (!$photo -> file_id)
			{
				continue;
			}
			
			$file = Engine_Api::_() -> getApi('storage', 'storage') -> get($photo -> file_id, '');

			if (!$file)
			{
				continue;
			}
			
			if ($file -> service_id == 1)
			{
				$file_path = rtrim((constant('_ENGINE_SSL') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'], '/') . $file -> getHref();
			}
			else
			{
				$file_path = $file -> getHref();
			}
			// to remove params from file url
			$path_parts = pathinfo(parse_url($file_path, PHP_URL_PATH));
			$contents = file_get_contents($file_path);
			if ($contents === false)
			{
				continue;
			}

			// get photo title
			$view = Zend_Registry::get('Zend_View');
			$photo_title = trim($photo -> getTitle());
			$photo_title = str_replace(" ", "_", $photo_title);
			if (empty($photo_title))
			{
				$photo_title = Zend_Registry::get('Zend_Translate') -> _('[Untitled]');
			}
			if ($photo_title == $default_photo_title)
			{
				if ($index != 0)
				{
					$photo_title = $photo_title . '_' . $index;
				}
				$index++;
			}
            $photo_title_arr[] = $photo_title;
			$count_duplicate_title = array_count_values($photo_title_arr)[$photo_title];
			if($count_duplicate_title > 1) {
                $photo_title .= ' (' . $count_duplicate_title . ')';
            }
			$zip -> addFile($contents, $photo_title . '.' .  $path_parts['extension']);
		}
		
		$filename = $album -> getTitle();

		if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
			$filename = rawurlencode($filename);

		$filename .= '.zip';

		/*
		 header("Pragma: no-cache");
		 header('Content-Description: File Transfer');
		 header("Content-type: application/octet-stream");
		 // header("Content-type: application/zip");
		 header('Content-Disposition: attachment; filename="' . $filename . '"');
		 header('Content-Transfer-Encoding: binary');
		 header('Expires: 0');
		 header('Cache-Control: must-revalidate');
		 //header('Pragma: public');
		 */

		// http headers for zip downloads
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: public");
		header("Content-Description: File Transfer");
		header("Content-type: application/octet-stream");
		header("Content-Transfer-Encoding: binary");
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		ob_end_flush();

		//ob_clean();
		//ob_flush();
		echo $zip -> file();
	}

}
