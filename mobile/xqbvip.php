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
    date_default_timezone_set("Asia/Shanghai");
    //全局变量@xqb 2017.7.25每日分红
    $user = $GLOBALS['user'];
    $_CFG = $GLOBALS['_CFG'];
    $_LANG = $GLOBALS['_LANG'];
    $smarty = $GLOBALS['smarty'];
    $db = $GLOBALS['db'];
    $ecs = $GLOBALS['ecs'];
    $time=time();

    $ex_where2 = " and is_vip=0 " ;
    $ex_where1 = " and is_vip=1 and user_rank_points >0 and pass_time>0" ;
    $info=$db->GetAll('SELECT user_id,is_vip,user_rank_points,pass_time FROM ' . $ecs->table('users') . " WHERE 1 $ex_where1 ");//获取所有分红权
    foreach ($info as &$val){

        if($val['pass_time']<=$time){

            $user_id1=$val['user_id'];
            $pass_time=$val['pass_time'];
            $ex_where2 = " and user_id=$user_id1 and tuigutime =$pass_time " ;
            $row1 = $GLOBALS['db']->getRow("SELECT * FROM " .$GLOBALS['ecs']->table('user_account'). " WHERE 1 $ex_where2");

            if($row1){
                $data1['status']=0;
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_account'), $data1, "UPDATE", "id = $row1[id]");
                $data2['user_rank_points']=0;
                $data2['is_vip']=0;
                $data2['pass_time']=0;
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $data2, "UPDATE", "user_id = $user_id1");
            }

        }

    }


}

fh_data();
?>
