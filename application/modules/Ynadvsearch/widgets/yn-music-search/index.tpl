<?php
$this->headScript()
       ->appendFile($this->baseUrl() . '/application/modules/Mp3music/externals/scripts/music_function.js');   
       ?> 
 <script type="text/javascript">
 //<![CDATA[
 var search_params = <?php echo Zend_Json::encode($this->params) ?> ;
  function do_search() 
  {
        var key_search = $("title").value;
        key_search = key_search.replace(/^\s+|\s+$/g,'');
        if (key_search == "")
        {
            alert ('<?php echo $this->translate("Please enter keyword!") ?>');
            $("title").focus();
            return false;
        }
        else
        {
            $('frm_search').submit();
            return true;
        }
  }
  function active_search(obj)
  {
        var s_name = obj.getSelected()[0].get('data-type');
        if(s_name == "artist")
        {   
            <?php $allow_artist = Engine_Api::_()->getApi('settings', 'core')->getSetting('mp3music.artist', 1);
             if($allow_artist):?>
                s_name = "owner";
            <?php endif;?>
        }   
        $("type_search").value=s_name;
    }
    //]]>
</script>             
<ul class="global_form_box ynadvsearch-music-from ynadvsearch-clearfix" style="margin-bottom: 1em;">
    <div class="ynadvsearch-music">
        <select onchange="active_search(this)">
            <option data-type='all'  <?php if($this->params['search'] == "all"): echo 'selected="selected"'; endif; ?> "><?php echo $this->translate('All'); ?></option>
            <option data-type='songs'  <?php if($this->params['search'] == "songs" || $this->params['search'] == ""): echo 'selected="selected"'; endif; ?> "><?php echo $this->translate('Song'); ?></option>
            <option data-type='album' <?php if($this->params['search'] == "album"): echo 'selected="selected"'; endif; ?> "><?php echo $this->translate('Album'); ?></option>
            <option data-type='singer' <?php if($this->params['search'] == "singer"): echo 'selected="selected"'; endif; ?> "><?php echo $this->translate('Singer'); ?></option>
            <option data-type='artist' <?php if($this->params['search'] == "artist" || $this->params['search'] == "owner"): echo 'selected="selected"'; endif; ?> "><?php echo $this->translate('Artist'); ?></option>
            <option data-type='playlists'  <?php if($this->params['search'] == "playlists"): echo 'selected="selected"'; endif; ?> "><?php echo $this->translate('Playlist'); ?></option>
        </select>
      </div> 
    <div class="ynadvsearch-music-input">
       <form name="frm_search" id = "frm_search" method="get" onsubmit="return do_search();" action ="<?php echo $this->url(array(),'default') ?>mp3-music">
           <input required style="width: 40%; margin-right: 0.5em" type="text" id="title"  name="title" value="<?php echo $this->params['title'];?>" onkeydown="javascript:getKeyDown(event);"/>
           <input type="hidden" id="type_search" name="search" value="<?php echo $this->params['search'];?>"/>
           <button name=""  type="submit"><?php echo $this->translate('Search'); ?> </button>
       </form>    
    </div> 
    <div >
    </div>
</ul>