<?php echo $this -> partial('_listingResume.tpl', array(
    'idName' => $this -> identity,
    'class_mode' => "ynresume-layout-content-grid-view",
    'mode_enabled' => array('grid'),
    'resumeIds' => $this -> resumeIds,
    'resumes' => $this -> resumes,
//    'isWidget' => false,
));?>

