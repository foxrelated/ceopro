<!-- Base MasterSlider style sheet -->
<link rel="stylesheet" href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/style/masterslider.css" />

<!-- Master Slider Skin -->
<link href="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/skins/default/style.css" rel='stylesheet' type='text/css'>

<!-- MasterSlider Template Style -->
<link href='<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/style/ms-videogallery.css' rel='stylesheet' type='text/css'>

<!-- jQuery -->
<script src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/jquery.easing.min.js"></script>

<!-- Master Slider -->
<script src="<?php echo $this->layout()->staticBaseUrl?>application/modules/Ynultimatevideo/externals/masterslider/masterslider.js"></script>

<?php
$staticBaseUrl = $this->layout()->staticBaseUrl;
$this->headLink()
    ->appendStylesheet($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/mediaelementjs/mediaelementplayer.min.css');
$this->headScript()
    ->appendScript('window.MediaElementPlayer = null;')
    ->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/mediaelementjs/mediaelement-and-player.min.js')
    ->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/mediaelementjs/renderers/dailymotion.min.js')
    ->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/mediaelementjs/renderers/vimeo.min.js')
    ->appendFile($staticBaseUrl . 'application/modules/Ynultimatevideo/externals/mediaelementjs/renderers/facebook.min.js')
    ->appendScript('jQuery.noConflict();')
;
?>

<div class="ynultimatevideo_playlist_top_info">
    <span><?php echo $this->translate("Playing"); ?> :</span>
    <span id="current_video_title"></span>
    <a id="current_video_href" href="javascript:void(0);" target="_blank"><i class="fa fa-external-link"></i><?php echo $this->translate("view video"); ?></a>
</div>


<div id="ynultimatevideo_playlist_slideshow" class="ms-videogallery-template  ms-videogallery-vertical-template">
    <div id="ynultimatevideo_playlist_slideshow_masterslider" class="master-slider ms-skin-default">
        <a class="ynultimatevideo_playbutton ynultimatevideo_btn_playlist_pre" href="javascript:void(0)" onclick="ynultimatevideoPrev();"><i class="fa fa-angle-left"></i></a>
        <a class="ynultimatevideo_playbutton ynultimatevideo_btn_playlist_play" href="javascript:void(0)" onclick="ynultimatevideoPlay();"><i class="fa fa-play"></i></a>
        <a class="ynultimatevideo_playbutton ynultimatevideo_btn_playlist_next" href="javascript:void(0)" onclick="ynultimatevideoNext();"><i class="fa fa-angle-right"></i></a>
        <div class="ynultimatevideo_playlist_detail_actions">
            <a id="ynultimatevideo_continue_button" data-status="auto_play" href="javascript:void(0);" title="<?php echo $this->translate('Autoplay')?>" onclick="ynultimatevideoSwitch(this);" class="ynultimatevideo_status_button">
                <i class="fa fa-play-circle"></i>
            </a>
            <a id="ynultimatevideo_repeat_button" data-status="repeat" href="javascript:void(0);" title="<?php echo $this->translate('Repeat')?>" onclick="ynultimatevideoSwitch(this);" class="ynultimatevideo_status_button">
                <i class="fa fa-repeat"></i>
            </a>
            <a id="ynultimatevideo_shuffle_button" data-status="shuffle" href="javascript:void(0);" title="<?php echo $this->translate('Shuffle')?>" onclick="ynultimatevideoSwitch(this);" class="ynultimatevideo_status_button">
                <i class="fa fa-random"></i>
            </a>
            <span>
                <?php echo $this->translate(array('%s video', '%s videos', $this->playlist->getVideoCount()), $this->locale()->toNumber($this->playlist->getVideoCount())) ?>
            </span>
        </div>
        <?php foreach ($this->paginator as $video) : ?>
            <?php echo $this->partial('_video_playlist_slideshow.tpl', 'ynultimatevideo', array('video' => $video))?>
        <?php endforeach; ?>
    </div>
</div>

<?php
    /*
    the slider:
    - Next, prev are custom buttons. As we have repeate and random modes, slide transition is done by jump to specific slide
      when next/pre button is clicked, we check for mode before making slide change.
    - Slide history is push to stack, and prev go back in history, not prev slide
    the playlist:
    - media player is process based on current slide
    - when slide is changed we remove the player and player element of old slide
      after that and crete new video element for the new slide and start the player (why? see below)
    the video element:
    - we don't create the video element before hand, because the damn master slider auto processes video tags as video background,
      and mess up our element
    - therefore, we only output the source and dynamically create video element on the fly
    the video player:
    - the player only initialized if isPlaying state is on
    - if user play the video and change slide, next video will be autoplay, if not, video is not play we only slide through thumbnails
    - if player ended and continous play is on, isPlaying is set to on the next slide event is triggered, and the next video played
     */
?>
<script type="text/javascript">
    window.addEvent('domready', function() {
        var playerCookie = getCookie('ynultimatevideo_player_status');
        if (playerCookie) {
            var playerStatus = JSON.parse(playerCookie);
            jQuery(".ynultimatevideo_status_button").each(function(){
                if (playerStatus[jQuery(this).data('status')]) {
                    jQuery(this)[0].addClass('active');
                }
            });
        }
    });

    var $currentSlide;
    var isPlaying = false;

    function ynultimatevideoSwitch(ele) {
        if ($(ele).hasClass('active')) {
            $(ele).removeClass('active');
        } else {
            $(ele).addClass('active');
        }
        var status = getPlayingStatus();
        setCookie('ynultimatevideo_player_status', JSON.stringify(status));
    }

    function switchPlayState(state) {
//        console.log('switch play state', state);
        isPlaying = state;
    }

    function switchPlayButton(state) {
        if (state) {
            jQuery('.ynultimatevideo_btn_playlist_play').show();
        } else {
            jQuery('.ynultimatevideo_btn_playlist_play').hide();
        }
    }

    function getPlayingStatus() {
        var status = {
            auto_play: 0,
            repeat: 0,
            shuffle: 0
        };
        jQuery(".ynultimatevideo_status_button").each(function(){
            status[jQuery(this).data('status')] = jQuery(this)[0].hasClass('active') ? 1 : 0;
        });
        return status;
    }

    function generateRandom(current, min, max) {
        // playlist contain only 1 video
        if (max === 1) {
            return 0;
        }
        var gen = Math.floor(Math.random() * (max - min + 1)) + min;
        while (gen === current) {
            gen = Math.floor(Math.random() * (max - min + 1)) + min;
        }
        return gen;
    }

    jQuery(function() {
        var slider = new MasterSlider();
        var previousIndex = [0];

        slider.setup('ynultimatevideo_playlist_slideshow_masterslider', {
            width : 854,
            height : 476,
            space : 0,
            loop : false,
            view : 'basic',
            swipe : false,
            mouse : false,
            speed : 100,
            autoplay : false
        });

        slider.control('arrows');
        slider.control('thumblist', {autohide : false,  dir : 'v',width:300,height:80});

        slider.api.addEventListener(MSSliderEvent.CHANGE_START , function(){
            removePlayer($currentSlide);
//            removeAllPlayers();
            var current = slider.api.index();
//            console.log('change start', current);
            previousIndex.push(current);
        });

        slider.api.addEventListener(MSSliderEvent.CHANGE_END , function(){
            // update current slide
            $currentSlide = slider.api.view.currentSlide.$element;
            updateCurrentSlideInfo($currentSlide);
            initPlayer($currentSlide);
        });

        var removePlayer = function($slide) {
            if (!$slide) {
//                return console.log('remove nothing');
            } else {
                var videoId = $slide.find('.video_id').val();
                var playerId = jQuery('#player_' + videoId).closest('.mejs__container').attr('id');
                var player = mejs.players[playerId];
                player && player.remove();
                var videoDom = $slide.find('#player_' + videoId);
                videoDom && videoDom.remove();
//                return console.log('remove', videoId);
            }
        };

        var initPlayer = function($slide) {
            if (!$slide || !isPlaying) {
                switchPlayButton(true);
//                return console.log('init nothing');
            } else {
                switchPlayButton(false);
                // put video here ms-slide-bgvideocont
                // no, here ms-slide-bgcont
                var videoSrc = $slide.find('.video_src').val();
                var videoId = $slide.find('.video_id').val();
                var videoType = $slide.find('.video_type').val();
                var videoContainer = $slide.find('.ms-slide-bgcont');
                if (videoSrc) {
                    if (!jQuery('#player_' + videoId).length) {
                        if (videoType == 6) {
                            var videoDom = '<iframe id="player_'+ videoId +'" class="ynultimatevideo-player" width="100%" height="100%" src="' + videoSrc +  '?autoplay=1" frameborder="0"></iframe>';
                            videoContainer.prepend(videoDom);

                        } else {
                            var videoDom = jQuery('<video id="player_' + videoId + '" class="ynultimatevideo-player" src="' + videoSrc + '" width="100%" height="100%"></video>');
                            videoContainer.prepend(videoDom);
                            videoDom.mediaelementplayer({
                                success: function(mediaElement, originalNode) {
                                    window.setTimeout(function(){
                                        mediaElement.play();
                                    }, 500);
                                    mediaElement.addEventListener('play', function() {
                                        switchPlayState(true);
                                    });
                                    mediaElement.addEventListener('ended', function() {
                                        ynultimatevideoAutoNext();
                                    });
                                    mediaElement.addEventListener('pause', function() {
                                        // prevent pause after ended of vimeo
                                        if (mediaElement.currentTime < mediaElement.duration - 1) {
                                            switchPlayState(false);
                                        }
                                    });
                                }
                            });
                        }
                    }
                }
//                return console.log('init', videoId);
            }
        };

        window.ynultimatevideoPlay = function() {
            switchPlayState(true);
            initPlayer($currentSlide);
        };

        window.ynultimatevideoNext = function() {
            var min = 0;
            var total = slider.api.count();
            var current = slider.api.index();
            var status = getPlayingStatus();
            if (status.shuffle) {
                slider.api.gotoSlide(generateRandom(current, min, total));
            } else if (status.repeat && current === total - 1) {
                slider.api.gotoSlide(0);
            } else if (current < total - 1) {
                slider.api.next();
            }
        };

        window.ynultimatevideoPrev = function() {
            var pos = previousIndex.pop();
            pos = previousIndex.pop();
            if (pos !== null){
                slider.api.gotoSlide(pos);
            } else {
                slider.api.gotoSlide(0);
            }
        };

        var ynultimatevideoAutoNext = function() {
            var status = getPlayingStatus();
            if (status.auto_play) {
                switchPlayState(true);
                ynultimatevideoNext();
            }
        };

        var updateCurrentSlideInfo = function($slide){
//            var currentVideoId = $slide.find('.video_id').val();
            var currentVideoTitle = $slide.find('.title').val();
            var currentVideoHref = $slide.find('.href').val();
            // update title
            jQuery("#current_video_title").text(currentVideoTitle);
            // update href
            jQuery("#current_video_href").attr('href',currentVideoHref);
        };

        window.removeAllPlayers = function(){
            var p = mejs.players;
            for (var key in p) {
                if (p.hasOwnProperty(key)) {
                    p[key].remove();
                }
            }
            jQuery('.ynultimatevideo-player').remove();
        }
    });
</script>
