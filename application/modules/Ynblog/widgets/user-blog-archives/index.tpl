<script type="text/javascript">
var dateAction =function(start_date, end_date){
    $('start_date').value = start_date;
    $('end_date').value = end_date;
    $('ynblog_filter_form').submit();
  }
</script>

<?php if (count($this->archive_list )):?>
   <ul class = "global_form_box" style="margin-bottom: 15px; ">
      <?php  $index = 0; foreach ($this->archive_list as $archive): $index ++; ?>
          <li style="font-weight: bold; padding: 5px 5px 5px 0px; ">
            <a href='javascript:void(0);' onclick='javascript:dateAction(<?php echo $archive['date_start']?>, <?php echo $archive['date_end']?>);' <?php if ($this->start_date==$archive['date_start']) echo " style='font-weight: bold;'";?>>
                  <?php echo $archive['label']?></a>
          </li>
      <?php endforeach; ?>
   </ul>
<?php endif; ?>