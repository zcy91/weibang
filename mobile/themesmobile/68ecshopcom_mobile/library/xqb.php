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
    $meiri=$_CFG['meiri'];
    $time=time();
    $data_end=strtotime(date('Y-m-d 00:00:00',time()));
    $data_start=$data_end-86400;
    $ex_where1 = " and change_time>=$data_start and change_time <=$data_end and change_type=99" ;
    $ex_where = " and gufennum>=1 and qixian >=6 and is_paid=1 and status=1 " ;
    $infogf=$db->GetOne('SELECT SUM(gufennum) FROM ' . $ecs->table('user_account') . " WHERE 1 $ex_where ");//获取所有股份
    $info=$db->GetOne('SELECT SUM(fh_num) FROM ' . $ecs->table('users') . " WHERE 1  ");//获取所有分红权
    $total_jifen=$db->GetOne('SELECT SUM(pay_points) FROM ' . $ecs->table('account_log') . " WHERE 1 $ex_where1 ");
    if($total_jifen>=300000){
        $fh=($total_jifen*0.145)*0.6/$info;
	}elseif ($total_jifen>=200000){
        $fh=($total_jifen*0.14)*0.6/$info;
	}elseif ($total_jifen>=100000){
        $fh=($total_jifen*0.135)*0.6/$info;
	}elseif ($total_jifen>=50000){
        $fh=($total_jifen*0.13)*0.6/$info;
	}elseif ($total_jifen>=20000){
        $fh=($total_jifen*0.125)*0.6/$info;
	}elseif ($total_jifen>=10000){
        $fh=($total_jifen*0.115)*0.6/$info;
    }elseif ($total_jifen>=9000) {
        $fh=($total_jifen*0.07)*0.6/$info;
    }else if ($total_jifen>=8000) {
        $fh=($total_jifen*0.045)*0.6/$info;
    }elseif ($total_jifen>=7000) {
        $fh=($total_jifen*0.03)*0.6/$info;
    }elseif ($total_jifen>=6000) {
        $fh=($total_jifen*0.02)*0.6/$info;
    }elseif ($total_jifen>=5000) {
        $fh=($total_jifen*0.01)*0.6/$info;//分红权分红
    }else{
        $fh=0;
    }
    if($total_jifen>=5000){
        if($infogf<=1000){
            $gffh= $total_jifen*0.4*0.2/1000;//股份分红
        }else{
            $gffh= $total_jifen*0.4*0.2/$infogf;
        }
    }else{
        $gffh=0;
    }

    $zdlirun=0.2*$total_jifen;
    if($total_jifen>=$meiri&&$zdlirun>1000){
        $infoq=$db->GetAll('SELECT * FROM ' . $ecs->table('users') . " WHERE 1  ");
        foreach ($infoq as &$val){
            $user_id1=$val['user_id'];
			if($val['fh_num']>=1){
                $order1['credit']=$val['credit']+($val['fh_num']*$fh);
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $order1, "UPDATE", "user_id = $user_id1");
                $ordertt1['user_id']=$user_id1;
                $ordertt1['credit']=$val['fh_num']*$fh;
                $ordertt1['user_money']=$ordertt1['rank_points']=$ordertt['frozen_money']=$ordertt['pay_points']=0;
                $ordertt1['change_time']=time();
                $ordertt1['change_desc']='每日分红';
                $ordertt1['change_type']=3;
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $ordertt1, 'INSERT');
			}
		}
	}file_put_contents(__DIR__.'/fhlog.txt','时间开始='.$data_start.'时间结束='.$data_end.'昨日积分='.$total_jifen.'总分红权='.$info.'最低金额'.$fh);
    $infoq=$db->GetAll('SELECT * FROM ' . $ecs->table('user_account') . " WHERE gufennum>=1 and qixian >=6 and is_paid=1 and status=1 order by paid_time desc ");
    foreach ($infoq as &$val){
        $user_id1=$val['user_id'];
        $data1id=$val['id'];
        if($val['qixian']==6){
            $endtime = strtotime("+6 month",$val['paid_time']);
            $bili=0.6;
        }else if($val['qixian']==12){
            $endtime = strtotime("+12 month",$val['paid_time']);
            $bili=0.3;
        }else if($val['qixian']==60){
            $endtime = strtotime("+60 month",$val['paid_time']);
            $bili=0.1;
        }
        if($endtime>=$time){

                $row1 = $GLOBALS['db']->getRow("SELECT * FROM " .$GLOBALS['ecs']->table('users'). " WHERE user_id = '$user_id1'");
                $rifan = round($val['amount']*$bili/100,2) ;//日返
                $data1['credit2']=$val['credit2']+$rifan;
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('user_account'), $data1, "UPDATE", "id = $data1id");
                $data2['credit2']=$row1['credit2']+($gffh * $val['gufennum'])+$rifan;//股份分红
                $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('users'), $data2, "UPDATE", "user_id = $user_id1");
                if($gffh>0){
                    $ordertt1['user_id']=$user_id1;
                    $ordertt1['credit2']=$gffh * $val['gufennum'];
                    $ordertt1['user_money']=$ordertt1['rank_points']=$ordertt['frozen_money']=$ordertt['pay_points']=0;
                    $ordertt1['change_time']=time();
                    $ordertt1['change_desc']='股份分红';
                    $ordertt1['change_type']=4;
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $ordertt1, 'INSERT');
                }
                if($rifan>0){
                    $ordertt2['user_id']=$user_id1;
                    $ordertt2['credit2']=$rifan;
                    $ordertt2['user_money']=$ordertt1['rank_points']=$ordertt['frozen_money']=$ordertt['pay_points']=0;
                    $ordertt2['change_time']=time();
                    $ordertt2['change_desc']='每日返还本金';
                    $ordertt2['change_type']=4;
                    $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('account_log'), $ordertt2, 'INSERT');
                }

            }


    }file_put_contents(__DIR__.'/gflog.txt','时间开始='.$data_start.'时间结束='.$data_end.'昨日积分='.$total_jifen.'总分股份='.$infogf.'最低金额'.$infogf);
    /*echo $total_jifen;echo $info;echo '@@'.$fh;*/
}

fh_data();
?>
