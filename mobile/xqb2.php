<?php

/**
 * ECSHOP 计划任务
 * ============================================================================
 * * 版权所有 2008-2015 秦皇岛商之翼网络科技有限公司，并保留所有权利。
 * 网站地址: http://www.68ecshop.com;
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！您只能在不用于商业目的的前提下对程序代码进行修改和
 * 使用；不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * $Author: derek $
 * $Id: user.php 17217 2011-01-19 06:29:08Z derek $
 */
define('IN_ECS', true);

require (dirname(__FILE__) . '/includes/init.php');

/* 载入语言文件 */
require_once (ROOT_PATH . 'languages/' . $_CFG['lang'] . '/user.php');

/*新版微信改动*/
function fh_data(){
	//全局变量@xqb 2017.7.25
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    //$data_start=strtotime(date('Y-m-d 00:00:00',time()));
    $data_start=strtotime("-100 day");
    $data_end=time();
    $lj100=$_CFG['100lj'];
    $fhsx=$_CFG['fhsx'];
    $fh100=$_CFG['100fh'];

    $infoq=$db->GetAll('SELECT * FROM ' . $ecs->table('users') . " WHERE 1  ");
    foreach ($infoq as &$val){
        $user_id=$val['user_id'];
        $ex_where1 = " and user_id=$user_id and shipping_time_end>=$data_start and shipping_time_end <=$data_end" ;
        $order_count['finished1'] = $db->GetOne('SELECT SUM(goods_amount)  FROM ' . $ecs->table('order_info') . " WHERE 1 $ex_where1 " );
        $sql1 = "SELECT * FROM " .$GLOBALS['ecs']->table('users'). " WHERE user_id = '$user_id'";
        $row1 = $GLOBALS['db']->getRow($sql1);

        if( $order_count['finished1']>=$lj100){
            $order1['fh_num']=$row1['fh_num']+$fh100;
        }else{
            $order1['fh_num']=$row1['fh_num']-$fh100;
        }
        if($order1['fh_num']>=$fhsx){
            $order1['fh_num']=$fhsx;
        }elseif ($order1['fh_num']<=0){
            $order1['fh_num']=0;
        }
        /*$GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $order1, "UPDATE", "user_id = $user_id");
        $ordertt1['user_id']=$user_id;
        $ordertt1['fh_num']=$fh100;
        $ordertt1['user_money']=$ordertt1['rank_points']=$ordertt['frozen_money']=$ordertt['pay_points']=0;
        $ordertt1['change_time']=time();
        $ordertt1['change_desc']='100天增加分红权';
        $ordertt1['change_type']=3;
        $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $ordertt1, 'INSERT');*/
        echo 'ok'.$order1['fh_num'];
    }

}

fh_data();
?>
