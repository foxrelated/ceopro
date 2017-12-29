<?php
if (isset($this->photo_id)) {
    echo $this->partial('_add-to-photo.tpl',
            array(
                    'album_id' => $this->album_id,
                    'photo_id' => $this->photo_id,
                    'is_login' => $this->is_login,
            		'has_virtual_album' => $this->has_virtual_album,
            		'is_virtual' => $this->is_virtual
            ));
} else {
    echo $this->partial('_add-to-album.tpl',
            array(
                    'album_id' => $this->album_id,
                    'is_login' => $this->is_login
            ));
}
?>