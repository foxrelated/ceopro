<?php foreach( $this->paginator as $photo ): ?>
    <?php echo $this->partial('_manage-photos-item.tpl', 'advalbum', array(
        'photo' => $photo,
        'album' => $this->album,
        'currentYear' => $this->current_year,
        'smallestYear' => $this->smallest_year,
        'colors' => $this->colors
    )); ?>
<?php endforeach; ?>

<?php
$currentPage = $this->paginator->getCurrentPageNumber();
$totalPage = $this->paginator->count();
?>
<?php if($currentPage < $totalPage): ?>
    <div id="ynadvalbum_canloadmore" current_page="<?php echo $currentPage + 1; ?>">
        <div id="ynadvalbum_loadmore"></div>
    </div>
<?php endif; ?>
