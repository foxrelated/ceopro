<script type="text/javascript">
    var count = 0;
    function setCookie(cname, cvalue, exdays) {
        var d = new Date();
        d.setTime(d.getTime() + (exdays*24*60*60*1000));
        var expires = "expires="+d.toUTCString();
        document.cookie = cname + "=" + cvalue + "; " + expires;
    }

    function getCookie(cname) {
        var name = cname + "=";
        var ca = document.cookie.split(';');
        for(var i=0; i<ca.length; i++) {
            var c = ca[i];
            while (c.charAt(0)==' ') c = c.substring(1);
            if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
        }
        return "";
    }
    function deleteCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:01 GMT;';
    }
    function uncheckAll()
    {
        var checkboxes = $$('td.ynblog_check input[type=checkbox]');
        checkboxes.each(function(item){
            item.checked = false;
        });
        $$('th.export_table_short input[type=checkbox]')[0].checked = false;
        deleteCookie("cbcookie");
        count = 0;
    }
    var select_blog = function(item, id)
    {
        var ids = getCookie("cbcookie");
        ids = ids.split(",");
        if(item.checked == true)
        {
            ids.push(id);
            count ++;
        }
        else
        {
            for(var i = ids.length; i--;) {
                if(ids[i] == id) {
                    ids.splice(i, 1);
                }
            }
            count --;
        }
        if(count == $$('td.ynblog_check input[type=checkbox]').length) {
            $$('th.export_table_short input[type=checkbox]')[0].checked = true;
        }
        else
        {
            $$('th.export_table_short input[type=checkbox]')[0].checked = false;
        }
        setCookie("cbcookie",ids,1);
    }

    window.addEvent('domready', function(){
        var selectedIds = getCookie("cbcookie");
        if(selectedIds=="")
        {
            var ids = [];
            setCookie("cbcookie",ids,1);
        }
        var checkboxes = $$('td.ynblog_check input[type=checkbox]');
        checkboxes.each(function(item){
            if(selectedIds.indexOf(item.value)>=0)
            {
                item.checked = true;
                count++;
            }
        });
        if(count == $$('td.ynblog_check input[type=checkbox]').length) {
            $$('th.export_table_short input[type=checkbox]')[0].checked = true;
        }
    });

    en4.core.runonce.add(function(){
        $$('th.export_table_short input[type=checkbox]').addEvent('click', function(){
            var selectedIds = getCookie("cbcookie");
            selectedIds = selectedIds.split(",");
            var checked = $(this).checked;
            var checkboxes = $$('td.ynblog_check input[type=checkbox]');
            checkboxes.each(function(item){
                item.checked = checked;
                if((checked == true) && (selectedIds.indexOf(item.value)<0))
                {
                    selectedIds.push(item.value);
                    count++;

                }
                if((checked == false) && (selectedIds.indexOf(item.value)>=0))
                {
                    for(var i = selectedIds.length; i--;) {
                        if(selectedIds[i] == item.value) {
                            selectedIds.splice(i, 1);
                        }
                    }
                    count--;

                }
            });
            setCookie("cbcookie",selectedIds,1);
        })
    });
    function actionSelected(exportTo){
        $('action_selected').action = en4.core.baseUrl +'blogs/export/export';
        var selectedIds = getCookie("cbcookie");
        if(selectedIds == "") {
            alert("<?php echo $this->translate('Please choose the blogs to export!')?>")
            return;
        }
        $('ids').value = selectedIds;
        $('exportTo').value = exportTo;
        $('action_selected').submit();
        deleteCookie("cbcookie");
        uncheckAll();
    }
</script>
<div class="headline">
    <h2>
        <?php echo $this->translate('Blogs');?>
    </h2>
    <div class="tabs">
        <?php
	      // Render the menu
	      echo $this->navigation() -> menu()
        -> setContainer($this->navigation)
        -> render();
        ?>
    </div>
</div>
<?php if($this->paginator->getTotalItemCount() > 0):?>
<div style="overflow: auto">
    <table class='export_table'>
        <thead>
        <tr>
            <th class='export_table_short'><input type='checkbox' class='checkbox'/></th>
            <th><?php echo $this->translate("Title") ?></th>
            <th><?php echo $this->translate("Description") ?></th>
            <th><?php echo $this->translate("Date") ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($this->paginator as $item): ?>
        <tr>
            <td class="ynblog_check"><input type='checkbox' class='checkbox' value="<?php echo $item->blog_id ?>" onchange="select_blog(this,<?php echo $item->blog_id?>);"/></td>
            <td><?php echo $this->htmlLink($item->getHref(), $item->getTitle()) ?></td>
            <td><?php echo $this -> string()->truncate(strip_tags($item->body,200));?></td>
            <td><?php echo $item->creation_date ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<form id='action_selected' method='post' action=''>
    <input type="hidden" id="ids" name="ids" value=""/>
    <input type="hidden" id="exportTo" name="exportTo" value=""/>
</form>


<?php echo $this->paginationControl($this->paginator, null, null, array(
'pageAsQuery' => true,
'query' => $this->formValues,
)); ?>

<div class='buttons'>
    <button onclick="javascript:uncheckAll();" type='button'>
        <?php echo $this->translate("Uncheck All") ?>
    </button>

    <button onclick="javascript:actionSelected('wordpress');" type='button'>
        <?php echo $this->translate("Export to Wordpress") ?>
    </button>

    <button onclick="javascript:actionSelected('tumblr');" type='button'>
        <?php echo $this->translate("Export to Tumblr") ?>
    </button>

    <button onclick="javascript:actionSelected('blogger');" type='button'>
        <?php echo $this->translate("Export to Blogger") ?>
    </button>
</div>
<?php else: ?>
<div class="tip">
    <span>
      <?php echo $this->translate("There are no blog entries.") ?>
    </span>
</div>
<?php endif; ?>
