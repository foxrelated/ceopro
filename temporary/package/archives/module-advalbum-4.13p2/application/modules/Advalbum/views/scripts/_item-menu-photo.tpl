<?php
// @TODO: rework this list to an ajax action for better performance
$photo = $this->photo;
$album = $this->album;
$photoId = $photo->getIdentity();
$albumId = $album->getIdentity();
$view_mode = isset($this->view_mode) ? $this->view_mode : 'list';
$viewer = Engine_Api::_()->user()->getViewer();
$hasViewer = $viewer->getIdentity();
$isOwner = $photo->isOwner($viewer);

$buttons = array(
    'list' => array('add-to-virtual', 'make-profile', 'download-resized', 'download-fullsize', 'edit_title', 'change_date', 'change_location', 'set_album_cover', 'delete'),
    'manage' => array('add-to-virtual', 'make-profile', 'download-resized', 'download-fullsize', 'edit_title', 'change_date', 'change_location', 'set_album_cover', 'delete'),
//    'manage' => array('add-to-virtual', 'make-profile', 'download-resized', 'download-fullsize', 'delete'),
    'detail-popup' => array('report', 'rotate-left', 'rotate-right', 'make-profile', 'download-resized', 'download-fullsize', 'share', 'delete', 'url', 'html-code', 'forum-code', 'send-to-friend'),
    'detail' => array('report', 'rotate-left', 'rotate-right', 'delete', 'download-resized', 'download-fullsize', 'url', 'html-code', 'forum-code', 'send-to-friend'),
    'detail-second' => array('report', 'rotate-left', 'rotate-right', 'delete', 'download-resized', 'download-fullsize', 'url', 'html-code', 'forum-code', 'send-to-friend', 'add-tags', 'share', 'make-profile')
);
?>
<ul class="ynadvalbum_dropdown_items">
    <?php if (in_array('add-tags', $buttons[$view_mode]) && $album->authorization()->isAllowed($viewer, 'tag')): ?>
        <li class="ynadvalbum_dropdown_item">
            <?php echo $this->htmlLink('javascript:void(0);', '<span class="ynicon yn-user-plus"></span>' . $this->translate('Add tags'), array('onclick'=>'taggerInstance.begin();')) ?>
        </li>
    <?php endif; ?>

    <?php if (in_array('share', $buttons[$view_mode]) && $hasViewer): ?>
        <li class="ynadvalbum_dropdown_item">
            <?php echo $this->htmlLink(array(
            'module'=> 'activity',
            'controller' => 'index',
            'action' => 'share',
            'route' => 'default',
            'type' => 'advalbum_photo',
            'id' => $photo->getIdentity(),
            'format' => 'smoothbox'),
            '<span class="ynicon yn-share"></span>' . $this->translate("Share"), array('class' => 'smoothbox')); ?>
        </li>
    <?php endif; ?>

    <?php if (in_array('report', $buttons[$view_mode]) && $hasViewer): ?>
    <li class="ynadvalbum_dropdown_item">
    <?php echo $this->htmlLink(Array(
        'module'=>'core',
        'controller'=>'report',
        'action'=>'create',
        'route'=>'default',
        'subject'=>$photo->getGuid(),
        'format' => 'smoothbox'),
        '<span class="ynicon yn-warning-triangle"></span>' . $this->translate("Report"),
        array('class' => 'smoothbox')
    ); ?>
    </li>
    <?php endif; ?>

    <?php if (in_array('rotate-left', $buttons[$view_mode]) && $isOwner): ?>
    <li class="ynadvalbum_dropdown_item">
    <?php echo $this->htmlLink(array(
        'module' => 'advalbum',
        'controller' => 'photo',
        'action' => 'rotate',
        'route' => 'default',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        'dest'=>'left'),
        '<span class="ynicon yn-rotate-left"></span>' . $this->translate("Rotate Left"),
        array('class' => 'smoothbox')
    ); ?>
    </li>
    <?php endif; ?>

    <?php if (in_array('rotate-right', $buttons[$view_mode]) && $isOwner): ?>
    <li class="ynadvalbum_dropdown_item">
    <?php echo $this->htmlLink(array(
        'module' => 'advalbum',
        'controller' => 'photo',
        'action' => 'rotate',
        'route' => 'default',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        'dest'=>'right'),
        '<span class="ynicon yn-rotate-right"></span>' . $this->translate("Rotate Right"),
        array('class' => 'smoothbox')
    ); ?>
    </li>
    <?php endif; ?>

    <?php if(in_array('add-to-virtual', $buttons[$view_mode]) && $this->has_virtual_album): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'route' => 'album_photo_specific',
        'action' => 'add-to-virtual',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        'format' => 'smoothbox'),
        '<span class="ynicon yn-plus"></span>' . $this->translate('Add to virtual album'), array(
        'class' => 'ynadvalbum_add_to_menu_item smoothbox'
        )) ?>
    </li>
    <?php endif; ?>

    <?php if(in_array('make-profile', $buttons[$view_mode]) && $hasViewer): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array('route' => 'user_extended',
        'module' => 'user',
        'controller' => 'edit',
        'action' => 'external-photo',
        'photo' => $photo->getGuid(),
        'format' => 'smoothbox'),
        '<span class="ynicon yn-photo-o"></span>' . $this->translate('Make profile picture'),
        array('class' => 'ynadvalbum_add_to_menu_item smoothbox')
        ) ?>
    </li>
    <?php endif; ?>

    <?php if (in_array('download-resized', $buttons[$view_mode])): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'route' => 'album_photo_specific',
        'action' => 'download-photo',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        'photo_type' => 'profile'
        ),
        '<span class="ynicon yn-photo-download-o"></span>' . $this->translate('Download resized photo'), array(
        'class' => 'ynadvalbum_add_to_menu_item'
        )) ?>
    </li>
    <?php endif; ?>

    <?php if (in_array('download-fullsize', $buttons[$view_mode])): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'route' => 'album_photo_specific',
        'action' => 'download-photo',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        ),
        '<span class="ynicon yn-photo-download-o"></span>' . $this->translate('Download full size photo'), array(
        'class' => 'ynadvalbum_add_to_menu_item'
        )) ?>
    </li>
    <?php endif; ?>

    <?php if (in_array('download', $buttons[$view_mode])): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'route' => 'album_photo_specific',
        'action' => 'download-photo',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        ),
        '<span class="ynicon yn-photo-download-o"></span>' . $this->translate('Download'), array(
        'class' => 'ynadvalbum_add_to_menu_item',
        'id' => 'ynadvalbum_addTo_downloadfullphoto'
        )) ?>
    </li>
    <?php endif; ?>

    <?php if(in_array('delete', $buttons[$view_mode]) && $photo->isDeletable()): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'route' => 'album_photo_specific',
        'action' => 'delete-photo',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        'format' => 'smoothbox'),
        '<span class="ynicon yn-trash"></span>'.$this->translate('Delete this photo'), array(
                    'class' => 'ynadvalbum_add_to_menu_item smoothbox'
                )) ?>
        </li>
    <?php endif; ?>
    <?php if(in_array('edit_title', $buttons[$view_mode]) && $isOwner): ?>
        <li class="ynadvalbum_dropdown_item">
            <?php echo $this->htmlLink(array(
                'route' => 'album_photo_specific',
                'action' => 'edit-title',
                'album_id' => $albumId,
                'photo_id' => $photoId,
                'type' => 'title',
                'format' => 'smoothbox'),
                '<span class="ynicon yn-text-edit"></span>'.$this->translate('Edit Title'), array(
                    'class' => 'ynadvalbum_add_to_menu_item smoothbox'
                )) ?>
        </li>
    <?php endif; ?>
    <?php if(in_array('change_date', $buttons[$view_mode]) && $isOwner): ?>
        <li class="ynadvalbum_dropdown_item">
            <?php echo $this->htmlLink(array(
                'route' => 'album_photo_specific',
                'action' => 'edit-title',
                'album_id' => $albumId,
                'photo_id' => $photoId,
                'type' => 'taken_date',
                'format' => 'smoothbox'),
                '<span class="ynicon yn-calendar"></span>'.$this->translate('Change Date'), array(
                    'class' => 'ynadvalbum_add_to_menu_item smoothbox'
                )) ?>
        </li>
    <?php endif; ?>
    <?php if(in_array('change_location', $buttons[$view_mode]) && $isOwner): ?>
        <li class="ynadvalbum_dropdown_item">
            <?php echo $this->htmlLink(array(
                'route' => 'album_photo_specific',
                'action' => 'change-location',
                'album_id' => $albumId,
                'photo_id' => $photoId,
                'format' => 'smoothbox'),
                '<span class="ynicon yn-map-marker"></span>'.$this->translate('Change Location'), array(
                    'class' => 'ynadvalbum_add_to_menu_item smoothbox'
                )) ?>
        </li>
    <?php endif; ?>
    <?php if(in_array('set_album_cover', $buttons[$view_mode]) && $isOwner): ?>
        <li class="ynadvalbum_dropdown_item">
            <?php echo $this->htmlLink(array(
                'route' => 'album_photo_specific',
                'action' => 'set-album-cover',
                'album_id' => $albumId,
                'photo_id' => $photoId,
                'format' => 'smoothbox'),
                '<span class="ynicon yn-photos"></span>'.$this->translate('Set Album Cover'), array(
        'class' => 'ynadvalbum_add_to_menu_item smoothbox'
        )) ?>
    </li>
    <?php endif; ?>

    <?php if (in_array('url', $buttons[$view_mode])): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php
            $url = $this->url(array(
            'module' => 'advalbum',
            'controller' => 'photo',
            'action' => 'export-url',
            'mode' => 'url',
            'album_id' => $albumId,
            'photo_id' => $photoId
            ),'default', true);
        ?>
        <a href="javascript:void(0)" onclick="showSmoothbox('<?php echo $url ?>');">
            <span class="ynicon yn-link"></span><?php echo $this->translate('URL') ?>
        </a>
    </li>
    <?php endif; ?>

    <?php if (in_array('html-code', $buttons[$view_mode])): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php
            $url = $this->url(array(
            'module' => 'advalbum',
            'controller' => 'photo',
            'action' => 'export-url',
            'mode' => 'html',
            'album_id' => $albumId,
            'photo_id' => $photoId
            ),'default', true);
        ?>
        <a href="javascript:void(0)" onclick="showSmoothbox('<?php echo $url ?>');">
            <span class="ynicon yn-code"></span><?php echo $this->translate('HTML code') ?>
        </a>
    </li>
    <?php endif; ?>

    <?php if (in_array('forum-code', $buttons[$view_mode])): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php
            $url = $this->url(array(
            'module' => 'advalbum',
            'controller' => 'photo',
            'action' => 'export-url',
            'mode' => 'forum',
            'album_id' => $albumId,
            'photo_id' => $photoId
            ),'default', true);
        ?>
        <a href="javascript:void(0)" onclick="showSmoothbox('<?php echo $url ?>');">
            <span class="ynicon yn-comment"></span><?php echo $this->translate('Forum code') ?>
        </a>
    </li>
    <?php endif; ?>

    <?php if (in_array('send-to-friend', $buttons[$view_mode])): ?>
    <li class="ynadvalbum_dropdown_item">
        <?php echo $this->htmlLink(array(
        'module' => 'advalbum',
        'controller' => 'photo',
        'action' => 'send-to-friends',
        'album_id' => $albumId,
        'photo_id' => $photoId,
        'format' => 'smoothbox'),
        '<span class="ynicon yn-user"></span>'.$this->translate('Send to friend'), array(
        'class' => 'ynadvalbum_add_to_menu_item smoothbox'
        )) ?>
    </li>
    <?php endif; ?>
</ul>