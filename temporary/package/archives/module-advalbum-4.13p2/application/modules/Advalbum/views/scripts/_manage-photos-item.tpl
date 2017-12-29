<?php
    $photo = $this->photo;
    $photoId = $photo->getIdentity();
    $elName = 'advalbum_photo[' . $photoId . ']';
    $album = $this->album;
    $albumId = $album->getIdentity();
    $currentYear = $this->currentYear;
    $smallestYear = $this->smallestYear;
    $tags = array();
    $tagIds = array();
    $tagNames = array();
    foreach ($photo->tags()->getTagMaps() as $tagmap)
    {
        $tagIds[] = $tagmap->tag_id;
        $tagNames[] = $tagmap -> getTitle();
        $tags[] = array(
            'id' => $tagmap->tag_id,
            'name' => $tagmap->getTitle()
        );
    }
?>
<li id="<?php echo $photoId ?>" class="ynadvalbum_manage_photo-item">
    <div class="ynadvalbum_manage_photo-parent">
        <div class="ynadvalbum_manage_photo-owner">
            <input type="checkbox" name="ynadvalbum_photo_delete" class="ynadvalbum_manage_photo-owner-checkbox" value="<?php echo $photoId ?>" id="advalbum_photo_1_<?php echo $photo->getIdentity(); ?>">
            <div class="ynadvalbum_setting">
                <span class="ynicon yn-gear ynadvalbum-setting"></span>
                <ul class="ynadvalbum-setting-option">
                    <li>
                        <?php echo $this->htmlLink(array(
                        'route' => 'album_photo_specific',
                        'action' => 'delete-photo',
                        'album_id' => $albumId,
                        'photo_id' => $photoId,
                        'format' => 'smoothbox'),
                        '<span class="ynicon yn-trash"></span> ' . $this->translate('Delete Photo'), array(
                        'class' => 'smoothbox'
                        )) ?>
                    </li>
                    <li>
                        <?php echo $this->htmlLink(array(
                        'route' => 'album_specific',
                        'action' => 'move-photos',
                        'album_id' => $albumId,
                        'photo_ids' => $photoId,
                        'format' => 'smoothbox'),
                        '<span class="ynicon yn-folder-open"></span> ' . $this->translate('Move to other album'), array(
                        'class' => 'smoothbox'
                        )) ?>
                    </li>
                </ul>
            </div>
            <span style="background-image: url(<?php echo $photo->getPhotoUrl("thumb.profile")?>);">
            <label for="advalbum_photo_1_<?php echo $photo->getIdentity(); ?>"></label>
            <div id="ynadvalbum_photo_<?php echo $photoId ?>_taglist" class="ynadvalbum_manage_photo_tag-names">
                <?php if(count($tagNames)): ?>
                    <?php echo $this->translate('Tagged:') ?>
                        <span class="ynadvalbum_tag_first"><?php echo $tagNames[0] ?></span>
                    <?php $other = $tagNames ?>
                    <?php array_shift($other) ?>
                    <?php if(count($other)): ?>
                        and
                        <span class="ynadvalbum_tag_second"><?php echo $this->translate(array('%d other', '%d others', count($other)), $this->locale()->toNumber(count($other))) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            </span>
            <a  class="" href="<?php echo $photo->getHref() ?>"></a>
        </div>
        <div class="ynadvalbum_manage_photo-info">
            <div>
                <div class="ynadvalbum_manage_photo-input-info">
                    <div id="<?php echo $elName ?>[name]-wrapper" class="ynadvalbum_names_wrapper clearfix">
                        <div id="<?php echo $elName ?>[name]-element" class="ynadvalbum_manage_photo_tagnames">
                            <?php foreach($tags as $tag): ?>
                                <span id="tospan_<?php echo $tag['name'] ?>_<?php echo $tag['id'] ?>" class="tag"><?php echo $tag['name'] ?> <a class='ynicon yn-del' href="javascript:void(0);" onclick="this.parentNode.destroy();removeFromToValue(<?php echo $tag['id'] ?>, 'advalbum_photo[<?php echo $photoId ?>][name]');"></a></span>
                            <?php endforeach; ?>
                                <input id="ynadvalbum_name_<?php echo $photoId ?>" class="ynadvalbum_placeholder_publish ynadvalbum_manage_album-name" type="text" placeholder="@">
                        </div>
                    <input type="hidden" name="<?php echo $elName ?>[name]" id="<?php echo $elName ?>[name]" value="<?php echo implode(',', $tagIds) ?>">
                    <input type="hidden" id="<?php echo $elName ?>[name]_tags" value="<?php echo implode(',', $tagNames) ?>">
                    </div>

                    <input id="advalbum_photo_<?php echo $photo->getIdentity() ?>-location" name="<?php echo $elName ?>[location]" class="ynadvalbum_placeholder_publish ynadvalbum_manage_album-local" type="text" placeholder="where was this taken?"  value="<?php echo $photo->getLocation() ?>">
                    <input type="hidden" name="<?php echo $elName ?>[latitude]" id="<?php echo $elName ?>[latitude]" value="<?php if (!empty($photo->latitude)) echo $photo->latitude ?>">
                    <input type="hidden" name="<?php echo $elName ?>[longitude]" id="<?php echo $elName ?>[longitude]" value="<?php if (!empty($photo->longitude)) echo $photo->longitude ?>">
                    <div class="ynadvalbum_placeholder_publish  ynadvalbum_manage_album-time clearfix">
                        <select name="<?php echo $elName ?>[day]" id="ynadvalbum_manage_day">
                            <option value="0">Day</option>
                            <?php for($i = 1; $i<=31; $i++): ?>
                            <option value="<?php echo $i ?>" <?php if(!empty($photo->taken_date) && ($i == date('j', strtotime($photo->taken_date)))) echo 'selected' ?>><?php echo $i ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="<?php echo $elName ?>[month]" id="ynadvalbum_manage_month">
                            <option value="0">Month</option>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                            <option value="<?php echo $m ?>" <?php if(!empty($photo->taken_date) && ($m == date('n', strtotime($photo->taken_date)))) echo 'selected' ?>><?php echo date('F', mktime(0,0,0,$m)) ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="<?php echo $elName ?>[year]" id="ynadvalbum_manage_year">
                            <option value="0">Year</option>
                            <?php for($y = $currentYear; $y >= $smallestYear; $y--): ?>
                            <option value="<?php echo $y ?>" <?php if(!empty($photo->taken_date) && ($y == date('Y', strtotime($photo->taken_date)))) echo 'selected' ?>><?php echo $y ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <ul class="ynadvalbum_manage_photo-input-options clearfix">
                    <li class="ynadvalbum_add-name ynadvalbum_item" tab_class="ynadvalbum_show_name" title="<?php echo $this->translate('Add tags');?>">
                        <span class="ynicon yn-user-plus"></span>
                    </li>
                    <li class="ynadvalbum_add-location ynadvalbum_item" tab_class="ynadvalbum_show_local" title="<?php echo $this->translate('Add location');?>">
                        <span class="ynicon yn-map-marker"></span>
                    </li>
                    <li class="ynadvalbum_add-datetime ynadvalbum_item" tab_class="ynadvalbum_show_datetime" title="<?php echo $this->translate('Add date');?>">
                        <span class="ynicon yn-calendar"></span>
                    </li>
                    <li class="ynadvalbum_add-color ynadvalbum_item" tab_class="ynadvalbum_show_color"  title="<?php echo $this->translate('Set main color');?>">
                        <span class="ynicon yn-arr-down" style="background: <?php echo $photo->getColorValue(); ?>"></span>
                        <div class="ynadvalbum_triangle_parent">
                            <div class="ynadvalbum_triangle_left"></div>
                            <div class="ynadvalbum_triangle_right"></div>
                        </div>
                    </li>
                    <li class="ynadvalbum_make-cover <?php echo ($this->album->photo_id == $photo->getIdentity()) ? 'background' : ''; ?>" >
                        <div>
                            <label  for="advalbum_photo_<?php echo $photo->getIdentity(); ?>"></label>
                            <input class="ynadvalbum_check" id="advalbum_photo_<?php echo $photo->getIdentity(); ?>" type="radio" name="cover" value="<?php echo $photo->getIdentity() ?>" <?php if( $this->album->photo_id == $photo->getIdentity()) echo 'checked' ?> />
                            <label class="ynadvalbum_manage_photo_cover-title"><?php echo $this->translate('Album Cover');?></label>
                        </div>
                    </li>
                    <ul class="ynadvalbum_manage_photo_color-patterns clearfix">
                        <?php foreach($this->colors as $name => $color): ?>
                        <li color_value="<?php echo $color ?>" color_name="<?php echo $name ?>" class="ynadvalbum-manage-color-item <?php if(in_array($name, $photo->getColors()) != false) echo 'selected' ?>" style="background-color: <?php echo $color ?>">
                            <span class="ynicon yn-check"></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </ul>
                <input type="hidden" class="ynadvalbum-manage-color-box" name="<?php echo $elName ?>[color]" value="<?php echo implode(',', $photo->getColors()) ?>">
                <div class="ynadvalbum_manage_photo-typing-info">
                    <div class="ynadvalbum_manage_photo-typing-info-title">
                        <label><?php echo $this->translate('Title: ')?></label>
                        <div class="ynadvalbum_input_parent">
                            <input type="text" name="<?php echo $elName ?>[title]" id="ynadvalbum_manage-title-input" value="<?php echo $photo->title ?>" maxlength="128">
                        </div>
                    </div>
                    <div class="ynadvalbum_manage_photo-typing-info-caption">
                        <label><?php echo $this->translate('Caption: ')?></label>
                        <div class="ynadvalbum_input_parent">
                            <textarea type="text" name="<?php echo $elName ?>[description]" id="ynadvalbum_manage-caption-input"><?php echo $photo->getDescription() ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</li>