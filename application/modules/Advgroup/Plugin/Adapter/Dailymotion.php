<?php

/**
 * YouNet Company
 *
 * @category   Application_Extensions
 * @package    Advgroup
 * @author     YouNet Company
 */
class Advgroup_Plugin_Adapter_Dailymotion extends Advgroup_Plugin_Adapter_Abstract {

    public function extractCode() {
        $link = $this->_params['link'];
        $params = @pathinfo($this->_params['link']);
        $arr = explode('_', $params['basename']);
        $code = $arr[0];

        return $code;
    }

    /**
     *
     * @return : false if the link is invalid, otherwise return an SimpleXMLElement object containing the video information 
     */
    public function isValid() {
        if ($this->_params['link'] || $this->_params['code']) {
            if (!array_key_exists('code', $this->_params) || !$this->_params['code']) {
                $code = $this->extractCode();
                $this->_params['code'] = $code;
            } else {
                $code = $this->_params['code'];
            }

            $url = "https://api.dailymotion.com/video/$code&fields=description,duration,embed_html,embed_url,id,thumbnail_large_url,thumbnail_medium_url,title";
            return $this->fetchURL($url);
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
            $properties = get_object_vars($data);
            foreach ($properties as $key => $value) {
                $this->_information[$key] = $value;
            }
        }
        return true;
    }

    public function getVideoThumbnailImage() {
        return $this->_information['thumbnail_large_url'];
    }

    public function getVideoLargeImage() {
        if (empty($this->_information)) {
            $this->fetchLink();
        }
        if (array_key_exists('thumbnail_large_url', $this->_information)) {
            return $this->_information['thumbnail_large_url'];
        }
    }

    public function getVideoDuration() {
        if (isset($this->_information['duration'])) {
            return $this->_information['duration'];    
        }
        return 0;
    }

    public function getVideoTitle() {
        return $this->title;
    }
    
    public function getVideoDescription() {
        return $this->description;
    }

    public function getEmbededCode() {
        $this->fetchLink();
        return $this->_information['embed_html'];
    }

    public function fetchURL($url) {
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
            return $data;
        }
    }

    public function compileVideo($params) {
        $video_id = $params['video_id'];
        $code = $params['code'];
        $view = $params['view'];
        $mobile = empty($params['mobile']) ? false : $params['mobile'];
        if (!isset($code)) {
            $code = $this->_params['code'];
        }
        if (!isset($code)) {
            $code = $this->extractCode($this->_params['link']);
        }
        
        if ($code) {
            $embeded = "<object width='560' height='425'>"
                . "<param name='movie' value='http://www.dailymotion.com/swf/video/$code?background=493D27&foreground=E8D9AC&highlight=FFFFF0'></param>"
                . "<param name='allowFullScreen' value='true'></param>"
                . "<param name='allowScriptAccess' value='always'></param>"
                . "<param name='wmode' value='opaque' />"
                . "<embed type='application/x-shockwave-flash' src='http://www.dailymotion.com/swf/video/$code?background=493D27&foreground=E8D9AC&highlight=FFFFF0' width='560' height='425' allowfullscreen='true' allowscriptaccess='always' wmode='transparent'></embed>"
                        . "</object>";
            return $embeded;
        } 
        throw new Exception("The code is not found" . var_dump($params));
    }

}