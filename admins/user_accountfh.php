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
 * $Id: user_account.php 17217 2011-01-19 06:29:08Z liubo $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/* act操作项的初始化 */
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'fhlist';
}
else
{
    $_REQUEST['act'] = trim($_REQUEST['act']);
}

/*------------------------------------------------------ */
//-- 会员余额记录列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'fhlist')
{
    /* 权限判断 */
    admin_priv('surplus_manage');
//获取信息列表
    $list = get_pzd_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);
    $smarty->assign('full_page',    1);

//跳转页面
    assign_query_info();
    $smarty->display('user_account_fhlist.htm');
}/*------------------------------------------------------ */
//-- ajax帐户信息列表
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    $list = get_pzd_list();
    $smarty->assign('list',         $list['list']);
    $smarty->assign('filter',       $list['filter']);
    $smarty->assign('record_count', $list['record_count']);
    $smarty->assign('page_count',   $list['page_count']);

    $sort_flag  = sort_flag($list['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);

    make_json_result($smarty->fetch('user_account_fhlist.htm'), '', array('filter' => $list['filter'], 'page_count' => $list['page_count']));
}
/*------------------------------------------------------ */
//-- 会员余额函数部分
/*------------------------------------------------------ */
/**
 *
 *
 * @access  public
 * @param
 *
 * @return void
 */
function get_pzd_list()
{
    $sql = "select COUNT(*) from " . $GLOBALS['ecs']->table('account_log') . " where change_type=3";
    // 代码修改   By  www.68ecshop.com End
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    /* 分页大小 */
    $filter = page_and_size($filter);
/* 获活动数据 */

$sql ="select  user_id,credit,change_time,change_desc from ". $GLOBALS['ecs']->table('account_log'). " where change_type=3 order by log_id desc" ." LIMIT ". $filter['start'] .", " . $filter['page_size'];
$filter['keywords'] = stripslashes($filter['keywords']);
set_filter($filter, $sql);

    $list = $GLOBALS['db']->getAll($sql);
    foreach ($list AS $key => $value)
    {
        $user_id1=$value['user_id'];

            $hah=$GLOBALS['db']->getRow("select user_name from " . $GLOBALS['ecs']->table('users') . " where user_id =".$user_id1);
            $list[$key]['name']=$hah['user_name'];

        $list[$key]['add_date']             = local_date($GLOBALS['_CFG']['time_format'], $value['change_time']);

    }
    $arr = array('list' => $list, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);

    return $arr;
}

?>