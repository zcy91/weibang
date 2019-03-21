<!-- $Id: comment_list.htm 14216 2008-03-10 02:27:21Z testyang $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<div class="form-div">
  <form action="javascript:searchComment()" name="searchForm">
    <img src="images/icon_search.gif" width="26" height="22" border="0" alt="SEARCH" />
    <?php echo $this->_var['lang']['search_comment']; ?> <input type="text" name="keyword" /> <input type="submit" class="Button" value="<?php echo $this->_var['lang']['button_search']; ?>" />
  </form>
</div>

<form method="POST" action="question_manage.php?act=batch_drop" name="listForm" onsubmit="return confirm_bath()">

<!-- start comment list -->
<div class="list-div" id="listDiv">
<?php endif; ?>

<table cellpadding="3" cellspacing="1">
  <tr>
    <th>
      <input onclick='listTable.selectAll(this, "checkboxes")' type="checkbox">
      <a href="javascript:listTable.sort('question_id'); "><?php echo $this->_var['lang']['record_id']; ?></a> <?php echo $this->_var['sort_comment_id']; ?></th>
    <th><a href="javascript:listTable.sort('user_name'); ">用户名</a><?php echo $this->_var['sort_user_name']; ?></th>
    <th><a href="javascript:listTable.sort('id_value'); ">咨询对象</a><?php echo $this->_var['sort_id_value']; ?></th>
    <th><a href="javascript:listTable.sort('ip_address'); ">IP地址</a><?php echo $this->_var['sort_ip_address']; ?></th>
    <th><a href="javascript:listTable.sort('add_time'); ">咨询时间</a><?php echo $this->_var['sort_add_time']; ?></th>
    <th>状态</th>
	<th>是否回复</th>
    <th><?php echo $this->_var['lang']['handler']; ?></th>
  </tr>
  <?php $_from = $this->_var['comment_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'comment');if (count($_from)):
    foreach ($_from AS $this->_var['comment']):
?>
  <tr>
    <td><input value="<?php echo $this->_var['comment']['question_id']; ?>" name="checkboxes[]" type="checkbox"><?php echo $this->_var['comment']['question_id']; ?></td>
    <td><?php if ($this->_var['comment']['user_name']): ?><?php echo $this->_var['comment']['user_name']; ?><?php else: ?>匿名用户<?php endif; ?></td>
    <td><a href="../goods.php?id=<?php echo $this->_var['comment']['id_value']; ?>" target="_blank"><?php echo $this->_var['comment']['title']; ?></td>
    <td><?php echo $this->_var['comment']['ip_address']; ?></td>
    <td align="center"><?php echo $this->_var['comment']['add_time']; ?></td>
    <td align="center"><?php if ($this->_var['comment']['status'] == 0): ?>隐藏<?php else: ?>显示<?php endif; ?></td>
	<td align="center"><?php echo $this->_var['comment']['is_reply']; ?></td>
    <td align="center">
      <a href="question_manage.php?act=reply&amp;id=<?php echo $this->_var['comment']['question_id']; ?>">查看详情</a> |
      <a href="javascript:" onclick="listTable.remove(<?php echo $this->_var['comment']['question_id']; ?>, '你确认要删除吗？')">移除</a>
    </td>
  </tr>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="10"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>

  <table cellpadding="4" cellspacing="0">
    <tr>
      <td>
      <div>
      <select name="sel_action">
        <option value="remove">删除咨询</option>
        <option value="allow">允许显示</option>
        <option value="deny">禁止显示</option>
      </select>
      <input type="hidden" name="act" value="batch" />
      <input type="submit" name="drop" id="btnSubmit" value="<?php echo $this->_var['lang']['button_submit']; ?>" class="button" disabled="true" /></div></td>
      <td align="right"><?php echo $this->fetch('page.htm'); ?></td>
    </tr>
  </table>

<?php if ($this->_var['full_page']): ?>
</div>
<!-- end comment list -->

</form>
<script type="text/javascript" language="JavaScript">
<!--
  listTable.recordCount = <?php echo $this->_var['record_count']; ?>;
  listTable.pageCount = <?php echo $this->_var['page_count']; ?>;
  cfm = new Object();
  cfm['allow'] = '你确定要允许显示所选咨询吗？';
  cfm['remove'] = '你确定要删除所选评论吗？';
  cfm['deny'] = '你确定要禁止显示所选咨询吗？';

  <?php $_from = $this->_var['filter']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('key', 'item');if (count($_from)):
    foreach ($_from AS $this->_var['key'] => $this->_var['item']):
?>
  listTable.filter.<?php echo $this->_var['key']; ?> = '<?php echo $this->_var['item']; ?>';
  <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>

  
  onload = function()
  {
      document.forms['searchForm'].elements['keyword'].focus();
      // 开始检查订单
      startCheckOrder();
  }
  /**
   * 搜索评论
   */
  function searchComment()
  {
      var keyword = Utils.trim(document.forms['searchForm'].elements['keyword'].value);
      if (keyword.length > 0)
      {
        listTable.filter['keywords'] = keyword;
        listTable.filter.page = 1;
        listTable.loadList();
      }
      else
      {
          document.forms['searchForm'].elements['keyword'].focus();
      }
  }
  

  function confirm_bath()
  {
    var action = document.forms['listForm'].elements['sel_action'].value;

    return confirm(cfm[action]);
  }
//-->
</script>
<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>