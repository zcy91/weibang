<?php

/**
 * ECSHOP 会员帐目管理(包括预付款，余额)
 * ============================================================================
 * 版权所有 2005-2011 上海商派网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.ecshop.com；
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: liubo $
 * $Id: user_accountgf.php 17217 2011-01-19 06:29:08Z liubo $
*/

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 会员余额记录列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
    /* 权限判断 */
    admin_priv('surplus_manage');

    /* 指定会员的ID为查询条件 */
    $user_id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

    /* 获得支付方式列表 */
    $payment = array();
    $sql = "SELECT pay_id, pay_name FROM ".$ecs->table('payment').
           " WHERE enabled = 1 AND pay_code != 'cod' ORDER BY pay_id";
    $res = $db->query($sql);
    //启用股份
    $ex_where1 = " and qixian>=6 and gufennum >=1 and is_paid=1 and status=1" ;
    $qy_gufen=$db->GetOne('SELECT SUM(gufennum) FROM ' . $ecs->table('user_account') . " WHERE 1 $ex_where1 ");
    $qy_jine=$db->GetOne('SELECT SUM(amount) FROM ' . $ecs->table('user_account') . " WHERE 1 $ex_where1 ");
//总股份
    $ex_where2 = " and qixian>=6 and gufennum >=1 and is_paid=1" ;
    $total_gufen=$db->GetOne('SELECT SUM(gufennum) FROM ' . $ecs->table('user_account') . " WHERE 1 $ex_where2 ");
    $total_jine=$db->GetOne('SELECT SUM(amount) FROM ' . $ecs->table('user_account') . " WHERE 1 $ex_where2 ");
//关闭股份
    $ex_where3 = " and qixian>=6 and gufennum >=1 and is_paid=1 and status=0" ;
    $tg_gufen=$db->GetOne('SELECT SUM(gufennum) FROM ' . $ecs->table('user_account') . " WHERE 1 $ex_where3 ");
    $tg_jine=$db->GetOne('SELECT SUM(amount) FROM ' . $ecs->table('user_account') . " WHERE 1 $ex_where3 ");

    while ($row = $db->fetchRow($res))
    {
        $payment[$row['pay_name']] = $row['pay_name'];
    }

    /* 模板赋值 */
    if (isset($_REQUEST['process_type']))
    {
        $smarty->assign('process_type_' . intval($_REQUEST['process_type']), 'selected="selected"');
    }
    if (isset($_REQUEST['is_paid']))
    {
        $smarty->assign('is_paid_' . intval($_REQUEST['is_paid']), 'selected="selected"');
    }
    $smarty->assign('ur_here',       'VIP会员购买');
    $smarty->assign('id',            $user_id);
    $smarty->assign('payment_list',  $payment);
    $smarty->assign('action_link',   array('text' => '会员列表', 'href'=>'users.php?act=list'));

    $list = account_list();
    $smarty->assign('total_gufen',         $total_gufen);
    $smarty->assign('total_jine',       $total_jine);
    $smarty->assign('qy_gufen',         $qy_gufen);
    $smarty->assign('qy_jine',       $qy_jine);
    $smarty->assign('tg_gufen',         $tg_gufen);
    $smarty->assign('tg_jine',       $tg_jine);

    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);

    assign_query_info();
    $smarty->display('user_account_listrank.htm');
}

/*------------------------------------------------------ */
//-- 添加/编辑会员余额页面
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('surplus_manage'); //权限判断

    $ur_here  = ($_REQUEST['act'] == 'add') ? $_LANG['surplus_add'] : $_LANG['surplus_edit'];
    $form_act = ($_REQUEST['act'] == 'add') ? 'insert' : 'update';
    $id       = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $row1 = $GLOBALS['db']->getRow("SELECT * FROM " .$GLOBALS['ecs']->table('user_account'). " WHERE id = '$id'");
    //file_put_contents(__DIR__.'/aa1.php',json_encode($row1));
    if(empty($row1['tuigutime']) && $row1['status']==1){
        $data2['status']=0;
        $data2['tuigutime']=time();
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_account'), $data2, "UPDATE", "id = $id");
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'user_accountgf.php?act=gflist&' . list_link_postfix();

        sys_msg('退股成功', 0, $link);
    }else{
        $link[0]['text'] = $_LANG['back_list'];
        $link[0]['href'] = 'user_accountgf.php?act=gflist&' . list_link_postfix();

        sys_msg('该股份已经关闭', 0, $link);
    }

}elseif($_REQUEST['act'] == 'add'){
    admin_priv('surplus_manage'); //权限判断

    $ur_here  = ($_REQUEST['act'] == 'add') ? $_LANG['surplus_add'] : $_LANG['surplus_edit'];
    $form_act = ($_REQUEST['act'] == 'add') ? 'insert' : 'update';
    $id       = isset($_GET['id']) ? intval($_GET['id']) : 0;

    /* 获得支付方式列表, 不包括“货到付款” */
    $user_account = array();
    $payment = array();
    $sql = "SELECT pay_id, pay_name FROM ".$ecs->table('payment').
        " WHERE enabled = 1 AND pay_code != 'cod' ORDER BY pay_id";
    $res = $db->query($sql);

    while ($row = $db->fetchRow($res))
    {
        $payment[$row['pay_name']] = $row['pay_name'];
    }

    if ($_REQUEST['act'] == 'edit')
    {
        /* 取得余额信息 */
        $user_account = $db->getRow("SELECT * FROM " .$ecs->table('user_account') . " WHERE id = '$id'");

        // 如果是负数，去掉前面的符号
        $user_account['amount'] = str_replace('-', '', $user_account['amount']);

        /* 取得会员名称 */
        $sql = "SELECT user_name FROM " .$ecs->table('users'). " WHERE user_id = '$user_account[user_id]'";
        $user_name = $db->getOne($sql);
    }
    else
    {
        $surplus_type = '';
        $user_name    = '';
    }

    /* 模板赋值 */
    $smarty->assign('ur_here',          $ur_here);
    $smarty->assign('form_act',         $form_act);
    $smarty->assign('payment_list',     $payment);
    $smarty->assign('action',           $_REQUEST['act']);
    $smarty->assign('user_surplus',     $user_account);
    $smarty->assign('user_name',        $user_name);
    if ($_REQUEST['act'] == 'add')
    {
        $href = 'user_accountgf.php?act=gflist';
    }
    else
    {
        $href = 'user_accountgf.php?act=gflist&' . list_link_postfix();
    }
    $smarty->assign('action_link', array('href' => $href, 'text' => $_LANG['09_user_account']));

    assign_query_info();
    $smarty->display('user_account_listrank.htm');
}

/*------------------------------------------------------ */
//-- 添加/编辑会员余额的处理部分
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'insert' || $_REQUEST['act'] == 'update')
{
    /* 权限判断 */
    admin_priv('surplus_manage');

    /* 初始化变量 */
    $id           = isset($_POST['id'])            ? intval($_POST['id'])             : 0;
    $is_paid      = !empty($_POST['is_paid'])      ? intval($_POST['is_paid'])        : 0;
    $qixian      = !empty($_POST['qixian'])      ? intval($_POST['qixian'])        : 0;
    $amount       = !empty($_POST['amount'])       ? floatval($_POST['amount'])       : 0;
    $gufennum       = !empty($_POST['gufennum'])       ? floatval($_POST['gufennum'])       : 0;
    $process_type = !empty($_POST['process_type']) ? intval($_POST['process_type'])   : 0;
    $user_name    = !empty($_POST['user_id'])      ? trim($_POST['user_id'])          : '';
    $realname    = !empty($_POST['realname'])      ? trim($_POST['realname'])          : '';
    $mobile    = !empty($_POST['mobile'])      ? trim($_POST['mobile'])          : '';
    $idcard    = !empty($_POST['idcard'])      ? trim($_POST['idcard'])          : '';
    $admin_note   = '后台添加股份';
    $user_note    = '';
    $status    =1;
    $payment      = !empty($_POST['payment'])      ? trim($_POST['payment'])          : '';
    $user_id = $db->getOne("SELECT user_id FROM " .$ecs->table('users'). " WHERE user_id = '$user_name'");
    if (empty($gufennum) || empty($amount))
    {
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
        sys_msg('输入数值', 0, $link);
    }
    /* 此会员是否存在 */
    if ($user_id == 0)
    {
        $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
        sys_msg($_LANG['username_not_exist'], 0, $link);
    }

    /* 退款，检查余额是否足够 */
    if ($process_type == 1)
    {
        $user_account = get_user_surplus($user_id);

        /* 如果扣除的余额多于此会员拥有的余额，提示 */
        if ($amount > $user_account)
        {
            $link[] = array('text' => $_LANG['go_back'], 'href'=>'javascript:history.back(-1)');
            sys_msg($_LANG['surplus_amount_error'], 0, $link);
        }
    }

    if ($_REQUEST['act'] == 'insert')
    {
        /* 入库的操作 */
        if ($process_type == 1)
        {
            $amount = (-1) * $amount;
        }
        $sql = "INSERT INTO " .$ecs->table('user_account').
               " VALUES ('', '$user_id', '$gufennum','$_SESSION[admin_name]', '$amount', '".gmtime()."', '$qixian','".gmtime()."', '$admin_note', '$user_note', '$process_type', '$payment', '$is_paid','$status','$realname','$nobile','$idcard',0,0)";
        $db->query($sql);
        $id = $db->insert_id();
    }
    else
    {
        /* 更新数据表 */
        $sql = "UPDATE " .$ecs->table('user_account'). " SET ".
               "admin_note   = '$admin_note', ".
               "user_note    = '$user_note', ".
               "payment      = '$payment' ".
              "WHERE id      = '$id'";
        $db->query($sql);
    }

    // 更新会员余额数量
    if ($is_paid == 1)
    {
        $change_desc = $amount > 0 ? $_LANG['surplus_type_0'] : $_LANG['surplus_type_1'];
        $change_type = $amount > 0 ? ACT_SAVING : ACT_DRAWING;
        //log_account_change($user_id, $amount, 0, 0, 0, $change_desc, $change_type);
    }

    //如果是预付款并且未确认，向pay_log插入一条记录
    if ($process_type == 0 && $is_paid == 0)
    {
        include_once(ROOT_PATH . 'includes/lib_order.php');

        /* 取支付方式信息 */
        $payment_info = array();
        $payment_info = $db->getRow('SELECT * FROM ' . $ecs->table('payment').
                                    " WHERE pay_name = '$payment' AND enabled = '1'");
        //计算支付手续费用
        $pay_fee   = pay_fee($payment_info['pay_id'], $amount, 0);
        $total_fee = $pay_fee + $amount;

        /* 插入 pay_log */
        $sql = 'INSERT INTO ' . $ecs->table('pay_log') . " (order_id, order_amount, order_type, is_paid)" .
                " VALUES ('$id', '$total_fee', '" .PAY_SURPLUS. "', 0)";
        $db->query($sql);
    }

    /* 记录管理员操作 */
    if ($_REQUEST['act'] == 'update')
    {
        admin_log($user_name, 'edit', 'user_surplus');
    }
    else
    {
        admin_log($user_name, 'add', 'user_surplus');
    }

    /* 提示信息 */
    if ($_REQUEST['act'] == 'insert')
    {
        $href = 'user_accountgf.php?act=gflist';
    }
    else
    {
        $href = 'user_accountgf.php?act=gflist&' . list_link_postfix();
    }
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = $href;

    $link[1]['text'] = $_LANG['continue_add'];
    $link[1]['href'] = 'user_accountgf.php?act=add';

    sys_msg($_LANG['attradd_succed'], 0, $link);
}



/*------------------------------------------------------ */
//-- ajax帐户信息列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = account_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_account_listrank.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
/*------------------------------------------------------ */
//-- ajax删除一条信息
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'remove')
{
    /* 检查权限 */
    /* check_authz_json('surplus_manage');
     $id = @intval($_REQUEST['id']);

   /**/$row1 = $GLOBALS['db']->getRow("SELECT * FROM " .$GLOBALS['ecs']->table('user_account'). " WHERE id = '$id'");
     //file_put_contents(__DIR__.'/aa1.php',json_encode($row1));
    if(empty($row1['tuigutime']) && $row1['status']==1){
        $data2['status']=0;
        $data2['tuigutime']=time();
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_account'), $data2, "UPDATE", "id = $id");

    }
    $link[0]['text'] = $_LANG['back_list'];
    $link[0]['href'] = 'user_account.php?act=list&' . list_link_postfix();

    sys_msg($_LANG['attradd_succed']);
    //exit;
    /*if ($db->query($sql, 'SILENT'))
    {
       admin_log(addslashes($user_name), 'remove', 'user_surplus');
       $url = 'user_account.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);

       exit;
    }
    else
    {
        make_json_error($db->error());
    }*/
}

/*------------------------------------------------------ */
//-- 会员余额函数部分
/*------------------------------------------------------ */
/**
 * 查询会员余额的数量
 * @access  public
 * @param   int     $user_id        会员ID
 * @return  int
 */
function get_user_surplus($user_id)
{
    $sql = "SELECT SUM(user_money) FROM " .$GLOBALS['ecs']->table('account_log').
           " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}
/**
 * 查询会员余额的数量
 * @access  public
 * @param   int     $user_id        会员ID
 * @return  int
 */
function get_user_surplusc($user_id)
{
    $sql = "SELECT SUM(credit) FROM " .$GLOBALS['ecs']->table('account_log').
        " WHERE user_id = '$user_id'";

    return $GLOBALS['db']->getOne($sql);
}
/**
 * 更新会员账目明细
 *
 * @access  public
 * @param   array     $id          帐目ID
 * @param   array     $admin_note  管理员描述
 * @param   array     $amount      操作的金额
 * @param   array     $is_paid     是否已完成
 *
 * @return  int
 */
function update_user_account($id, $amount, $admin_note, $is_paid)
{
    $sql = "UPDATE " .$GLOBALS['ecs']->table('user_account'). " SET ".
           "admin_user  = '$_SESSION[admin_name]', ".
           "amount      = '$amount', ".
           "paid_time   = '".gmtime()."', ".
           "admin_note  = '$admin_note', ".
           "is_paid     = '$is_paid' WHERE id = '$id'";
    return $GLOBALS['db']->query($sql);
}

/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function account_list()
{
    $result = get_filter();
    if ($result === false)
    {
        /* 过滤列表 */
        $filter['user_id'] = !empty($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 0;
        $filter['keywords'] = empty($_REQUEST['keywords']) ? '' : trim($_REQUEST['keywords']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keywords'] = json_str_iconv($filter['keywords']);
        }

        $filter['process_type'] = isset($_REQUEST['process_type']) ? intval($_REQUEST['process_type']) : -1;
        $filter['payment'] = empty($_REQUEST['payment']) ? '' : trim($_REQUEST['payment']);
        $filter['is_paid'] = isset($_REQUEST['is_paid']) ? intval($_REQUEST['is_paid']) : -1;
        $filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'add_time' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);
        $filter['start_date'] = empty($_REQUEST['start_date']) ? '' : local_strtotime($_REQUEST['start_date']);
        $filter['end_date'] = empty($_REQUEST['end_date']) ? '' : (local_strtotime($_REQUEST['end_date']) + 86400);

        $where = " WHERE 1 and ua.vip=1 and ua.process_type=0 and is_paid=1";
        if ($filter['user_id'] > 0)
        {
            $where .= " AND ua.user_id = '$filter[user_id]' ";
        }
        if ($filter['process_type'] != -1)
        {
            $where .= " AND ua.process_type = '$filter[process_type]' ";
        }
        else
        {
            $where .= " AND ua.process_type " . db_create_in(array(SURPLUS_SAVE, SURPLUS_RETURN));
        }
        if ($filter['payment'])
        {
            $where .= " AND ua.payment = '$filter[payment]' ";
        }
        if ($filter['is_paid'] != -1)
        {
            $where .= " AND ua.is_paid = '$filter[is_paid]' ";
        }

        if ($filter['keywords'])
        {
            $where .= " AND u.user_name LIKE '%" . mysql_like_quote($filter['keywords']) . "%'";
            $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua, ".
                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
        }
        /*　时间过滤　*/
        if (!empty($filter['start_date']) && !empty($filter['end_date']))
        {
            $where .= "AND paid_time >= " . $filter['start_date']. " AND paid_time < '" . $filter['end_date'] . "'";
        }

        // 代码修改   By  www.68ecshop.com Start
//        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua, ".
//                   $GLOBALS['ecs']->table('users') . " AS u " . $where;
        $sql = "SELECT COUNT(*) FROM " .$GLOBALS['ecs']->table('user_account'). " AS ua LEFT JOIN ".
            $GLOBALS['ecs']->table('users') . " AS u ON ua.user_id = u.user_id " . $where;
        // 代码修改   By  www.68ecshop.com End
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        /* 分页大小 */
        $filter = page_and_size($filter);

        /* 查询数据 */
        $sql  = 'SELECT ua.*, u.user_name FROM ' .
            $GLOBALS['ecs']->table('user_account'). ' AS ua LEFT JOIN ' .
            $GLOBALS['ecs']->table('users'). ' AS u ON ua.user_id = u.user_id'.
            $where . "ORDER by " . $filter['sort_by'] . " " .$filter['sort_order']. " LIMIT ".$filter['start'].", ".$filter['page_size'];

        $filter['keywords'] = stripslashes($filter['keywords']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }

    $list = $GLOBALS['db']->getAll($sql);
    foreach ($list AS $key => $value)
    {
        $list[$key]['surplus_amount']       = price_format(abs($value['amount']), false);
        $list[$key]['add_date']             = local_date($GLOBALS['_CFG']['time_format'], $value['add_time']);
        $list[$key]['process_type_name']    = $GLOBALS['_LANG']['surplus_type_' . $value['process_type']];

        if($value['qixian']==6){
            $list[$key]['shijian']='半年';
            $list[$key]['tuigutime']             = local_date($GLOBALS['_CFG']['time_format'], strtotime("+6 month",$value['add_time']));
        }elseif($value['qixian']==12){
            $list[$key]['shijian']='一年';
            $list[$key]['tuigutime']             = local_date($GLOBALS['_CFG']['time_format'], strtotime("+12 month",$value['add_time']));
        }elseif($value['qixian']==1){
            $list[$key]['shijian']='1个月';
            $list[$key]['tuigutime']             = local_date($GLOBALS['_CFG']['time_format'], strtotime("+1 month",$value['add_time']));
        }elseif($value['qixian']==3){
            $list[$key]['shijian']='3个月';
            $list[$key]['tuigutime']             = local_date($GLOBALS['_CFG']['time_format'], strtotime("+3 month",$value['add_time']));
        }
        if($value['status']==1){
            $list[$key]['status']='启用';
        }else{
            $list[$key]['status']='到期';
        }
     }
    //file_put_contents(__DIR__.'/aa.php',json_encode($list));
    $arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>