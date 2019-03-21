<!-- $Id -->
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js')); ?>

<script type="text/javascript" src="../js/calendar.php"></script>
<link href="../js/calendar/calendar.css" rel="stylesheet" type="text/css" />
<div class="main-div">
<form name="theForm" method="post" action="" onsubmit="return validate()">
  <table cellspacing="1" cellpadding="3" width="100%">

    <tr>
      <td class="label"><?php echo $this->_var['lang']['label_user_rank_www_com']; ?></td>
      <td><select name="user_rank" id="user_rank">
        <option value="0" selected><?php echo $this->_var['lang']['all_users_www_com']; ?></option>
        <?php echo $this->html_options(array('options'=>$this->_var['user_ranks'])); ?>
      </select></td>
    </tr>

    <tr>
      <td class="label" ><?php echo $this->_var['lang']['label_reg_time_www_com']; ?></td>
      <td >
	  <input type="text" name="start_time" maxlength="60" size="20" readonly="readonly" id="start_time_id" />
      <input name="start_time_btn" type="button" id="start_time_btn" onclick="return showCalendar('start_time_id', '%Y-%m-%d %H:%M', '24', false, 'start_time_btn');" value="选择" class="button"/>
      ~      
      <input type="text" name="end_time" maxlength="60" size="20" readonly="readonly" id="end_time_id" />
      <input name="end_time_btn" type="button" id="end_time_btn" onclick="return showCalendar('end_time_id', '%Y-%m-%d %H:%M', '24', false, 'end_time_btn');" value="选择" class="button"/>  
	  </td>
    </tr>
	
    <tr>
      <td class="label"><?php echo $this->_var['lang']['user_point_gt_www_com']; ?></td>
      <td><input name="pay_points_gt" type="text" id="goods_num" value="" /></td>
    </tr>
   
    <tr>
      <td class="label"><?php echo $this->_var['lang']['user_point_lt_www_com']; ?></td>
      <td><input name="pay_points_lt" type="text" id="rows_num" value="" /></td>
    </tr>
  
  

    <tr>
      <td colspan="2" align="center">
	  <input type="hidden" name="act" value="act_export_excel">
	  <input type="submit" class="button" name="btn_user_export" value="<?php echo $this->_var['lang']['button_submit']; ?>"    />        </td>
      </tr>
    
  </table>
</form>
</div>

<script type="text/javascript" src="js/validator.js"></script>
<script language="JavaScript">
function validate()
{
      var validator = new Validator('theForm');
	  if (document.forms['theForm'].elements['pay_points_gt'].value)
      {
          validator.isInt('pay_points_gt', user_point_gt_not_int, false);
      }
	  if (document.forms['theForm'].elements['pay_points_lt'].value)
      {
          validator.isInt('pay_points_lt', user_point_lt_not_int, false);
      }
	  if ((document.forms['theForm'].elements['start_time'].value) && (document.forms['theForm'].elements['end_time'].value))
      {
          validator.islt('start_time', 'end_time', reg_time_not_lt);
      }
	  return validator.passed();
}
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>