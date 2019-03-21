<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
  <form action="javascript:searchUser()" name="searchForm">
    <img src="images/icon_search.gif" width="25" height="22" border="0" alt="SEARCH" />
    会员名称 <input type="text" name="keyword" size="10" />
      <select name="process_type" style="display: none">
        <option value="0">类型</option>
        <option value="0" <?php echo $this->_var['process_type_0']; ?>>充值</option>
        <option value="1" <?php echo $this->_var['process_type_1']; ?>>体现</option>
      </select>
      <select name="payment">
      <option value="">支付方式</option>
      <?php echo $this->html_options(array('options'=>$this->_var['payment_list'])); ?>
      </select>
      <select name="is_paid" style="display: none">
        <option value="1">到款状态</option>
        <option value="0" <?php echo $this->_var['is_paid_0']; ?>>未确认</option>
        <option value="1" <?php echo $this->_var['is_paid_1']; ?>>已完成</option>
        <option value="2"><?php echo $this->_var['lang']['cancel']; ?></option>
      </select>
      <input type="submit" value="<?php echo $this->_var['lang']['button_search']; ?>" class="button" />
   <!-- <span>总股份：<?php echo $this->_var['total_gufen']; ?>个 &nbsp; </span><span>总金额：<?php echo $this->_var['total_jine']; ?>元</span>&nbsp;
    <span>启用：<?php echo $this->_var['qy_gufen']; ?>个 &nbsp; </span><span>启用：<?php echo $this->_var['qy_jine']; ?>元</span>&nbsp;
    <span>关闭：<?php echo $this->_var['tg_gufen']; ?>个 &nbsp; </span><span>关闭：<?php echo $this->_var['tg_jine']; ?>元</span>-->
  </form>
</div>

<form method="POST" action="" name="listForm">
<!-- start user_deposit list -->
<div class="list-div" id="listDiv">
<?php endif; ?>
<table cellpadding="3" cellspacing="1">
  <tr>
    <th><a href="javascript:listTable.sort('user_name', 'DESC'); ">会员名称</a><?php echo $this->_var['sort_user_name']; ?></th>
    <th style="display: none"><a href="javascript:listTable.sort('process_type', 'DESC'); ">类型</a><?php echo $this->_var['sort_process_type']; ?></th>
    <!--<th><a href="javascript:listTable.sort('gufennum', 'DESC'); ">股份数量</a><?php echo $this->_var['sort_process_type']; ?></th>-->
    <th><a href="javascript:listTable.sort('amount', 'DESC'); ">金额</a><?php echo $this->_var['sort_amount']; ?></th>

    <th><a href="javascript:listTable.sort('payment', 'DESC'); ">支付方式</a><?php echo $this->_var['sort_payment']; ?></th>
    <th><a href="javascript:listTable.sort('is_paid', 'DESC'); ">到款状态</a><?php echo $this->_var['sort_is_paid']; ?></th>
    <!--<th><a href="javascript:listTable.sort('realname', 'DESC'); ">姓名</a><?php echo $this->_var['sort_is_paid']; ?></th>
    <th><a href="javascript:listTable.sort('mobile', 'DESC'); ">联系方式</a><?php echo $this->_var['sort_is_paid']; ?></th>
    <th><a href="javascript:listTable.sort('idcard', 'DESC'); ">身份证</a><?php echo $this->_var['sort_is_paid']; ?></th>-->
    <th><a href="javascript:listTable.sort('add_time', 'DESC'); ">操作日期</a><?php echo $this->_var['sort_add_time']; ?></th>
    <th><a href="javascript:listTable.sort('qixian', 'DESC'); ">会员期限</a><?php echo $this->_var['sort_add_time']; ?></th>
    <th><a href="javascript:listTable.sort('status', 'DESC'); ">会员状态</a><?php echo $this->_var['sort_add_time']; ?></th>
    <th><a href="javascript:listTable.sort('tuigutime', 'DESC'); ">到期时间</a><?php echo $this->_var['sort_add_time']; ?></th>
    <!--<th>已返本金</th>
    <th>退股</th>-->
  </tr>
  <?php $_from = $this->_var['list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['item']):
?>
  <tr>
    <td><?php if ($this->_var['item']['user_name']): ?><?php echo $this->_var['item']['user_name']; ?><?php else: ?><?php echo $this->_var['lang']['no_user']; ?><?php endif; ?></td>

    <td align="center" style="display: none"><?php echo $this->_var['item']['process_type_name']; ?>类型</td>
   <!-- <td align="center"><?php echo $this->_var['item']['gufennum']; ?></td>-->
    <td align="right"><?php echo $this->_var['item']['surplus_amount']; ?></td>

    <td><?php if ($this->_var['item']['payment']): ?><?php echo $this->_var['item']['payment']; ?><?php else: ?>N/A<?php endif; ?></td>
    <td align="center"><?php if ($this->_var['item']['is_paid']): ?>已完成<?php else: ?><?php echo $this->_var['lang']['unconfirm']; ?><?php endif; ?></td>
    <!--<td align="center"><?php echo $this->_var['item']['realname']; ?></td>
    <td align="center"><?php echo $this->_var['item']['mobile']; ?></td>
    <td align="center"><?php echo $this->_var['item']['idcard']; ?></td>-->
    <td align="center"><?php echo $this->_var['item']['add_date']; ?></td>
    <td align="center"><?php echo $this->_var['item']['shijian']; ?></td>
    <td align="center"><?php echo $this->_var['item']['status']; ?></td>
    <td align="center"><?php echo $this->_var['item']['tuigutime']; ?></td>
   <!-- <td align="center"><?php echo $this->_var['item']['credit2']; ?></td>
    <td align="center">
    <?php if ($this->_var['item']['is_paid']): ?>
      <a href="user_accountgf.php?act=edit&id=<?php echo $this->_var['item']['id']; ?>" title="<?php echo $this->_var['lang']['surplus']; ?>"><img src="images/icon_edit.gif" border="0" height="16" width="16" /></a>
    <?php else: ?>
    <a href="user_account.php?act=check&id=<?php echo $this->_var['item']['id']; ?>" title="<?php echo $this->_var['lang']['check']; ?>"><img src="images/icon_view.gif" border="0" height="16" width="16" />
    <&lt;!&ndash;a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['item']['id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="<?php echo $this->_var['lang']['drop']; ?>" ><img src="images/icon_drop.gif" border="0" height="16" width="16" /></a>&ndash;&gt;
    <?php endif; ?>
    </td>-->
  </tr>
  <?php endforeach; else: ?>
  <tr>
    <td class="no-records" colspan="8"><?php echo $this->_var['lang']['no_records']; ?></td>
  </tr>
  <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
</table>
<table id="page-table" cellspacing="0">
<tr>
  <td>&nbsp;</td>
  <td align="right" nowrap="true">
  <?php echo $this->fetch('page.htm'); ?>
  </td>
</tr>
</table>
<?php if ($this->_var['full_page']): ?>
</div>
<!-- end user_deposit list -->
</form>

<script type="text/javascript" language="JavaScript">
listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
<?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
<?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

<!--

onload = function()
{
    // 开始检查订单
    startCheckOrder();
}
/**
 * 搜索用户
 */
function searchUser()
{
    listTable.filter['keywords'] = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
    listTable.filter['process_type'] = document.forms['searchForm'].elements['process_type'].value;
    listTable.filter['payment'] = Utils.trim(document.forms['searchForm'].elements['payment'].value);
    listTable.filter['is_paid'] = document.forms['searchForm'].elements['is_paid'].value;
    listTable.filter['page'] = 1;
    listTable.loadList();
}
//-->
</script>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>