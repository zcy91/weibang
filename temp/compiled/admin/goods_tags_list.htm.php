<!-- $Id: brand_list.htm 15898 2009-05-04 07:25:41Z liuhui $ -->

<?php if ($this->_var['full_page']): ?>
<?php echo $this->fetch('pageheader.htm'); ?>
<?php echo $this->smarty_insert_scripts(array('files'=>'../js/utils.js,listtable.js')); ?>
<form method="post" action="" name="listForm" onsubmit="return confirmSubmit(this)">
<div class="list-div" id="listDiv">
<?php endif; ?>

  <table cellpadding="3" cellspacing="1">
    <tr>
      <th>标签名称</th>
      <th>商品名称</th>
      <th width="100">审核通过</th>
      <th width="100"><?php echo $this->_var['lang']['handler']; ?></th>
    </tr>
    <?php $_from = $this->_var['item_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'value');if (count($_from)):
    foreach ($_from AS $this->_var['value']):
?>
    <tr>
      <td><?php echo $this->_var['value']['tag_name']; ?></td>
      <td><?php echo $this->_var['value']['goods_name']; ?></td>
      <td align="center"><img src="images/<?php if ($this->_var['value']['state']): ?>yes<?php else: ?>no<?php endif; ?>.gif" onclick="listTable.toggle(this, 'toggle_state', <?php echo $this->_var['value']['tag_id']; ?>)" /></td>
      <td align="center">
        <a href="javascript:;" onclick="listTable.remove(<?php echo $this->_var['value']['tag_id']; ?>, '<?php echo $this->_var['lang']['drop_confirm']; ?>')" title="移除"><?php echo $this->_var['lang']['remove']; ?></a> 
      </td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td class="no-records" colspan="4"><?php echo $this->_var['lang']['no_records']; ?></td></tr>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
  </table>

<?php if ($this->_var['full_page']): ?>
<!-- end brand list -->
</div>
</form>

<?php echo $this->fetch('pagefooter.htm'); ?>
<?php endif; ?>