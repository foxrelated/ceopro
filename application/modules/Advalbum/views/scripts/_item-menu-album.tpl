<?php
$album = $this->album;
$albumId = $this->album->getIdentity();
?>

<ul class="ynadvalbum_dropdown_items">
    <?php if (isset($this->view_mode) && $this->view_mode = 'detail'): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php
            $url = $this->url(array(
            'module' => 'advalbum',
            'controller' => 'album',
            'action' => 'export-slideshow',
            'album_id' => $albumId,
            ),'default', true);
        ?>
        <a href="javascript:void(0)" onclick="showSmoothbox('<?php echo $url ?>');">
            <span class="ynicon yn-code"></span><?php echo $this->translate('Share Slideshow') ?>
        </a>
    </li>
    <?php endif; ?>
    <?php if (isset($this->total_photos) && $this->total_photos): ?>
    <li class="ynadvalbum_dropdown_item ynadvalbum_album-download">
        <?php echo $this->htmlLink(array('route' => 'album_specific',
        'action' => 'download',
        'album_id' => $albumId),
        '<span class="ynicon yn-photo-download-o"></span>'.$this->translate('Download Album'),
        array(
        'class' => 'buttonlink icon_photos_download'
        )); ?>
    </li>
    <?php endif; ?>
    <?php if (isset($this->manage) && $this->manage): ?>
    <?php if (!$album->virtual): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
            'route' => 'album_general',
            'action' => 'upload',
            'album_id' => $albumId),
            '<span class="ynicon yn-photo-plus-o"></span>'.$this->translate('Add More Photos'),
        array(
        )) ?>
    </li>

    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'route' => 'album_specific',
        'action' => 'editphotos',
        'album_id' => $albumId),
        '<span class="ynicon yn-photos-o"></span>' . $this->translate("Manage photos"),
        array(
            'class' => 'ynadvalbum_add_to_menu_item'
        )) ?>
    </li>
    <?php endif; ?>

    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'route' => 'album_specific',
        'action' => 'edit',
        'album_id' => $albumId),
        '<span class="ynicon yn-gear"></span>'.$this->translate('Edit settings'),
        array(
            'class' => 'ynadvalbum_add_to_menu_item'
        )) ?>
    </li>

    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
            'route' => 'album_specific',
            'action' => 'delete',
            'album_id' => $albumId,
            'format' => 'smoothbox'),
            '<span class="ynicon yn-trash"></span>'.$this->translate('Delete album'),
            array(
                'class' => 'ynadvalbum_add_to_menu_item smoothbox'
        )) ?>
    </li>
    <?php endif; ?>
</ul>
