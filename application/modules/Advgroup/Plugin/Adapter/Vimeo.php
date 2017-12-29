<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Advgroup
 * @author     YouNet Company
 */
class Advgroup_Plugin_Adapter_Vimeo extends Advgroup_Plugin_Adapter_Abstract {
    public function extractCode() {
        $params = @pathinfo($this->_params['link']);
        $code = $params['basename'];
        
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
            $url = "http://vimeo.com/api/v2/video/$code.xml";
            $data = @simplexml_load_file($url);
            $id = count($data->video->id);
            if ($id == 0)
                return false;            
            return $data;
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
            foreach ($data->video->children() as $element) {
                $this->_information[$element->getName()] = sprintf("%s", $element);
            }
        }
        return true;
    }

    public function getVideoThumbnailImage() {
        if (!isset ($this->_information) && is_array($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('thumbnail_medium', $this->_information)) {
            return $this->_information['thumbnail_medium'];
        }
    }

    public function getVideoDuration() {
        return $this->_information['duration'];
    }

    public function getVideoTitle() {
        return $this->_information['title'];
    }
    
    public function getVideoDescription() {
        return $this->description;
    }

    public function getVideoLargeImage() {
        if (empty($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('thumbnail_large', $this->_information)) {
            return $this->_information['thumbnail_large'];
        }
    }
    
    public function compileVideo($params) {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile'])?false:$params['mobile'];
        
        //640 x 360
        if (!$mobile) {
            $embedded = '
      <object width="' . ($view ? "560" : "425") . '" height="' . ($view ? "340" : "344") . '"">
      <param name="allowfullscreen" value="true"/>
      <param name="allowscriptaccess" value="always"/>
      <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' . $code . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />
      <embed src="https://vimeo.com/moogaloop.swf?clip_id=' . $code . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1' . ($view ? "" : "&amp;autoplay=1") . '" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="' . ($view ? "640" : "400") . '" height="' . ($view ? "360" : "230") . '" wmode="transparent"/>
      <param name="wmode" value="transparent" />
      </object>';
        } else {
            $autoplay = !$mobile && !$view;
			$videoFrame = $video_id."_".$params['count_video'];
            $embedded = '
        <iframe
        title="Vimeo video player"
        id="videoFrame' . $videoFrame . '"
        class="vimeo_iframe' . ($view ? "_big" : "_small") . '"' .
                    'src="https://player.vimeo.com/video/' . $code . '?title=0&amp;byline=0&amp;portrait=0&amp;wmode=opaque' . ($autoplay ? "&amp;autoplay=1" : "") . '"
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