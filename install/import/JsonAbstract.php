<?php

abstract class Install_Import_JsonAbstract extends Install_Import_Abstract
{
  protected $_fromFile;

  protected $_fromFileAlternate;

  protected $_doReverseData = false;

  public function setFromFile($fromFile)
  {
    $this->_fromFile = $fromFile;
    return $this;
  }

  public function getFromFile()
  {
    if( null === $this->_fromFile ) {
      throw new Engine_Exception('No from file');
    }
    return $this->_fromFile;
  }

  public function getFromFileAbs()
  {
    $file = $this->getFromPath() . DIRECTORY_SEPARATOR . $this->getFromFile();
    if( !file_exists($file) ) {
      throw new Engine_Exception(sprintf('From file "%s" does not exist', $file));
    }
    return $file;
  }

  public function setFromFileAlternate($fromFileAlternate)
  {
    $this->_fromFileAlternate = $fromFileAlternate;
    return $this;
  }

  public function getFromFileAlternate()
  {
    if( null === $this->_fromFileAlternate ) {
      throw new Engine_Exception('No from file slternate');
    }
    return $this->_fromFileAlternate;
  }

  public function getFromFileAlternateAbs()
  {
    $file = $this->getFromPath() . DIRECTORY_SEPARATOR . $this->getFromFileAlternate();
    if( !file_exists($file) ) {
      throw new Engine_Exception(sprintf('From file "%s" does not exist', $file));
    }
    return $file;
  }

  public function run()
  {
    $this->_message('(START)', 2);
    $memory = memory_get_usage(true);

    $fromFileAbs = null;
    try {
      $fromFileAbs = $this->getFromFileAbs();
    } catch( Exception $e ) {
      try {
        $fromFileAbs = $this->getFromFileAlternateAbs();
      } catch( Exception $e ) {
        $this->_warning(sprintf('%s, skipping import', $e->getMessage()), 0);
        $this->_message('(END)', 2);
        return;
      }
    }

    $toDb = $this->getToDb();
    $toTable = $this->getToTable();

    // Check table exists
    $sql = 'SHOW TABLES LIKE ' . $toDb->quote($toTable);
    $ret = $toDb->query($sql)->fetchColumn();
    if( !$ret ) {
      $this->_message(sprintf('Table "%s" does not exist, skipping import', $toTable), 0);
      $this->_message('(END)', 2);
      return;
    }

    // stats
    $cTotal = 0;
    $cSuccess = 0;
    $cFail = 0;

    // Import
    $fromData = file_get_contents($fromFileAbs);
    $fromData = trim($fromData, " \n\r\t()");
    // That was not nice, Ning
    $fromData = str_replace(chr(148), '"', $fromData);

    // Hack to fix bug?
    $fromData = str_replace('}{', '},{', $fromData);
    // This was not nice either, Ning
    $fromData = str_replace('}]{', '},{', $fromData);

    // Debug
    /*
    include_once 'OFC/JSON_Format.php';
    echo "<pre>";
    print_r(json_format($fromData));
    die();
     * 
     */

    // Decode
    $fromData = Zend_Json::decode($fromData);
    if( !is_array($fromData) ) {
      throw new Engine_Exception('Data could not be decoded');
    }

    if ($this->_doReverseData) {
      $fromData = array_reverse($fromData);
    }

    $currentIndex = 0;
    if(($cache = $this->getCache()) instanceof Zend_Cache_Core ) {
      $cacheCurrentIndex = $cache->load('currentIndex', true);
      if ($cacheCurrentIndex) {
        $currentIndex = $cacheCurrentIndex;
      }
    }

    foreach( $fromData as $fromKey => $fromDatum ) {

      $cTotal++;
      if ($currentIndex > $fromKey) {
          continue;
      }

      try {

        $newData = $this->_translateRow($fromDatum, $fromKey);

        if( false !== $newData ) {
          $toDb->insert($toTable, $newData);
        }

        $cSuccess++;

      } catch( Exception $e ) {

        $message = $e->getMessage();

        //if( !empty($fromData) ) {
        //  $message .= ' | ' . Zend_Json::encode($fromData);
        //}

        if( !empty($newData) ) {
          $message .= ' | ' . Zend_Json::encode($newData);
        }

        $this->_message($message, 0);

        $cFail++;

      }

      if( ($cache = $this->getCache()) instanceof Zend_Cache_Core ) {
       $cache->save($cTotal, 'currentIndex');
      }
    }

    if( ($cache = $this->getCache()) instanceof Zend_Cache_Core ) {
      $cache->save(0, 'currentIndex');
    }
    $this->_message(sprintf('Total: %d', $cTotal), 1);
    $this->_message(sprintf('Success: %d', $cSuccess), 1);
    $this->_message(sprintf('Failure: %d', $cFail), 1);
    $this->_message('(END)', 2);
    $memoryUsed = memory_get_usage(true) - $memory;
    $this->_message(sprintf('Additional memory usage: %d bytes', $memoryUsed), 1);
  }
}
