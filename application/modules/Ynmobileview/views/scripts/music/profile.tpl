<?php	
// this is done to make these links more uniform with other viewscripts
$playlist = $this->playlist;
$songs    = Engine_Api::_()->ynmobileview()->getservicesongs($playlist);
?>
<?php echo $this->partial('_mainPlayer.tpl', array('album'=>$playlist, 'songs' => $songs)) ?>

