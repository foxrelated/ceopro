<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Advgroup
 * @author     YouNet Company
 */
class Advgroup_Plugin_Adapter_Youtube extends Advgroup_Plugin_Adapter_Abstract {

    public function extractCode() {
        $link = $this->_params['link'];
        $new_code = @pathinfo($link);
        $link = preg_replace("/#!/", "?", $link);

        // get v variable from the url
        $arr = array();
        $arr = @parse_url($link);
        $code = "code";
        $parameters = $arr["query"];
        parse_str($parameters, $data);
        $code = $data['v'];
        if ($code == "") {
            $code = $new_code['basename'];
        }

        return $code;
    }

    /**
     *
     * @return : false if the link is invalid, otherwise return an SimpleXMLElement object containing the video information 
     */
    public function isValid() {
        if (array_key_exists('code', $this->_params)) {
            $code = $this->_params['code'];
        }

        if (empty($code) && array_key_exists('link', $this->_params)) {
            $code = $this->extractCode();
            $this->_params['code'] = $code;
        }

        if ($code) {
            $url = "http://gdata.youtube.com/feeds/api/videos/$code";
            $data = @file_get_contents($url);
            if ($data == "Video not found") {
                return false;
            } else {
                $xmlData = @simplexml_load_string($data);
                return $xmlData;
            }
        }
        return false;
    }

    /**
     *
     * @return type 
     */
    public function fetchLink() {
        $data = $this->isValid();
        if ($data === false) {
            return false;
        } else {
            $this->_information = array();
            $this->_information['title'] = sprintf("%s", $data->title);
            $this->_information['content'] = sprintf("%s", $data->content);
            $this->_information['duration'] = sprintf("%s", $data->children('http://search.yahoo.com/mrss/')->group->content->attributes()->duration);
            $this->_information['description'] = sprintf("%s", $data->children('http://search.yahoo.com/mrss/')->group->description);
            $this->_information['large-thumbnail'] = sprintf("%s", $data->children('http://search.yahoo.com/mrss/')->group->children('http://search.yahoo.com/mrss/')
                    ->thumbnail[0]->attributes()->url);
        }
        return true;
    }

    public function getVideoThumbnailImage() {
        if (array_key_exists('code', $this->_params)) {
            $code = $this->_params['code'];
        } else {
            $code = $this->extractCode();
        }
        if ($code) {
            return "http://img.youtube.com/vi/$code/default.jpg";
        }
        return '';
    }

    public function getVideoLargeImage() {
        if (empty($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('large-thumbnail', $this->_information)) {
            return $this->_information['large-thumbnail'];
        }
    }

    public function getVideoDuration() {
        return $this->duration;
    }

    public function getVideoTitle() {
        return $this->title;
    }
    
    public function getVideoDescription() {
        return empty($this->_information['description'])?'':$this->_information['description'];
    }

    public function getEmbededCode($code = null) {
        if (!$code) {
            if (array_key_exists('code', $this->_params)) {
                $code = $this->_prams['code'];
            } else if (array_key_exists('link', $this->_params)) {
                $code = $this->extractCode();
            }
        }

        if ($code) {
            $url = "http://www.youtube.com/share_ajax?action_get_embed=1&video_id=$code";

            ## HTTPS url that you are targeting.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.23 (Windows NT 5.1; U; en)');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            ## Below two option will enable the HTTPS option.
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            $data = json_decode(curl_exec($ch));
            if (!isset($data->error)) {
                var_dump($data);
            }
        }
    }

    public function compileVideo($params) {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile'])?false:$params['mobile'];
        //560 x 340
        //legacy youtube embed code
        if (!$mobile) {
            $embedded = '
      <object width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '"">
      <param name="movie" value="https://www.youtube.com/v/' . $code . '&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1"/>
      <param name="allowFullScreen" value="true"/>
      <param name="allowScriptAccess" value="always"/>
      <embed src="https://www.youtube.com/v/' . $code . '&color1=0xb1b1b1&color2=0xcfcfcf&hl=en_US&feature=player_embedded&fs=1' . ($view ? "" : "&autoplay=1") . '" type="application/x-shockwave-flash" allowfullscreen="true" allowScriptAccess="always" width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '" wmode="transparent"/>
      <param name="wmode" value="transparent" />
      </object>';
        } else {
            $autoplay = !$mobile && !$view;
			$videoFrame = $video_id."_".$params['count_video'];
            $embedded = '
        <iframe
        title="YouTube video player"
        id="videoFrame' . $videoFrame . '"
        class="youtube_iframe' . ($view ? "_big" : "_small") . '"' .
                    /*
                      width="'.($view?"560":"425").'"
                      height="'.($view?"340":"344").'"
                     */'
        src="https://www.youtube.com/embed/' . $code . '?wmode=opaque' . ($autoplay ? "&autoplay=1" : "") . '"
        frameborder="0"
        allowfullscreen=""
        scrolling="no">
        </iframe>
        <script type="text/javascript">
          en4.core.runonce.add(function() {
            var doResize = function() {
              var aspect = 16 / 9;
              var el = document.id("videoFrame' . $videoFrame . '");
              var parent = el.getParent();
              var parentSize = parent.getSize();
              el.set("width", parentSize.x);
              el.set("height", parentSize.x / aspect);
            }
            window.addEvent("resize", doResize);
            doResize();
          });
        </script>
      ';
        }

        return $embedded;
    }

}
