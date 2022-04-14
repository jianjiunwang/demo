<?php


namespace app\ws;
use app\common\Model\UserModel;
use app\common\Model\HcyModel;
use app\common\Model\CyModel;
use think\worker\Server;
class Worker extends Server {
    protected $socket = 'websocket://0.0.0.0:2380';
    protected $option = ['name' => 'thinkphp', 'count' => 40, ];
    public function onConnect($connection) {
        $connection->onWebSocketConnect = function ($connection, $http_header) {
            //	session('uid',$_GET['uid']);
            // 给链接对象动态增加一个session属性
            $connection->session['draw_uid'] = null;
            $connection->session['uid'] = $_GET['uid'];
            $connection->session['session'] = $_GET['session'];
            // var_dump("hello");
        };
    }
    public function onMessage($connection, $data) {
        $type['101']['title']="比特幣/美金";
    	$type['101']['id']="BTCUSDT";
    	$type['101']['typeid']="a";
    	$type['102']['title']="以太幣/美金";
    	$type['102']['id']="ETHUSDT";
    	$type['102']['typeid']="b";
    	$type['103']['title']="波卡幣/美金";
    	$type['103']['id']="DOTUSDT";
    	$type['103']['typeid']="c";
    	$type['104']['title']="瑞波幣/美金";
    	$type['104']['id']="XRPUSDT";
    	$type['104']['typeid']="d";
    	$type['105']['title']="萊特幣/美金";
    	$type['105']['id']="LTCUSDT";
    	$type['105']['typeid']="e";
    	$type['106']['title']="門羅幣/美金";
    	$type['106']['id']="XMRUSDT";
    	$type['106']['typeid']="f";
    	$type['107']['title']="達世幣/美金";
    	$type['107']['id']="DASHUSDT";
	    $type['107']['typeid']="g";
	    $type['108']['title']="量子鏈幣/美金";
    	$type['108']['id']="QTUMUSDT";
	    $type['108']['typeid']="h";
	    $type['109']['title']="美金/英鎊";
    	$type['109']['id']="USDGBP";
	    $type['109']['typeid']="i";
	    $type['110']['title']="美金/歐元";
    	$type['110']['id']="USDEUR";
	    $type['110']['typeid']="j";
	    $type['111']['title']="美金/加元";
    	$type['111']['id']="USDCAD";
	    $type['111']['typeid']="k";
	    $type['112']['title']="美金/澳元";
    	$type['112']['id']="USDAUD";
	    $type['112']['typeid']="l";
        $data = json_decode($data, true);
        $ret['type'] = $data['type'];
        $ret['uid'] = $connection->session['uid'];
        
        //TODO :odds数组为界面投注比例修改
        if ($data['type'] == 'odds') {
            $ret['odds'] = ['205_2_111' => 1.9, '205_2_112' => 1.9, '205_3_113' => 1.9, '205_3_114' => 1.9, '205_10_124' => 1.9, '205_10_125' => 1.9, '205_41_168' => 1.01, ];
            $ret['now'] = time();
            $ret['time'] = date('Y-m-d H:i:s');
            $ret = json_encode($ret);
            $connection->send($ret);
        }
        //credit
        if ($data['type'] == 'credit') {
            $ret['code'] = 0;
            $id = $connection->session['uid'];
            $UserModel = new UserModel();
            $myself = $UserModel->where(['id' => $id])->find();
            //print_r($myself);
            $ret['data']['Wallet'] = $myself['money'];
            $ret['data']['logout'] = $myself['last_session2'];

            $session = $connection->session['session'];
            $ret["data"]["session"] = $connection->session['session'];

            if($myself['last_session'] != $session){
                $ret['data']['logout'] = '1';
            }

            $ret = json_encode($ret);
            $connection->send($ret);
        }
        if ($data['type'] == 'logout') {

        }

        //check
        if ($data['type'] == 'check') {
            $ret['hello'] = 5278;
            $ret['world'] = 5269;
            $ret = json_encode($ret);
            $connection->send($ret);
        }

        //config
        if ($data['type'] == 'config') {
            $game = str_replace("Bopt", "", $data['game']);
            foreach ($type as $k => $v) {
                if ($game == $type[$k]['typeid']) $game = $type[$k]['id'];
            }

            $ret['now'] = time();
            $ret['time'] = date('Y-m-d H:i:s');
            $CyModel = new CyModel();
            $myself = $CyModel->where('create_time', '<=', $ret['time'])->where('datetime', '>', $ret['time'])->find();
            $ret['draw_uid'] = $myself['qishu'];
            $ooo = (date('md') . '0000');
            $ret['opened'] = $myself['qishu'] - (int)$ooo;
            $ret['unopen'] = 1440 - (int)$ret['opened'];
            $ret['stime'] = strtotime($myself['create_time']);
            $ret['etime'] = strtotime($myself['datetime']);
            $ret['game'] = $game;
            $ret['all']['data'] = '';
            if ($connection->session['draw_uid'] == null || $connection->session['draw_uid'] != $ret['draw_uid']) {
                $HcyModel = new HcyModel();
                $hcy = $HcyModel->where('qishu', $ret['draw_uid'])->where('currency', $game)->find();
                
                //  var_dump($connection->session['draw_uid'] . ':' .$ret['draw_uid'].':'. $ret['uid'] . ':' . $game . (!$hcy ? ':没数据<br>' : ':有数据<br>'));
                //   var_dump($hcy);
                if (!$hcy) {
                    unset($ret['draw_uid']);
                    unset($ret['opened']);
                    unset($ret['unopen']);
                    unset($ret['stime']);
                    unset($ret['etime']);
                    
                } else {
                    
                    $connection->session['draw_uid'] = $ret['draw_uid'];
                    $time = strtotime($hcy['create_time']);
                    for ($i = 0; $i < 60; $i++) {
                        $ret['rate'][(int)$time + (int)$i + 60] = $ret['rate'][(int)$time + (int)$i] = $hcy['s' . $i];
                    }
                    
                    // foreach ($type as $k => $v) {
                    //     // $ret['all'][$k] = sprintf("%.2f", $myself[$v['id']]);
                    //     // $ret['all'][$k] = $myself[$v['id']];
                    //     // $ret['all'][$k] = "--";
                    //     $ret['all'][$k] = sprintf("%.2f", $myself[$v['id']]);
                        
                    //     if (!$ret['all'][$k]) $ret['all'][$k] = false;
                    // }

                    $array = array();
                    $dbname = array('BTCUSDT','ETHUSDT','DOTUSDT','XRPUSDT','LTCUSDT','XMRUSDT','DASHUSDT','QTUMUSDT','USDGBP','USDEUR','USDCAD','USDAUD');
                    
                    foreach($dbname as $key => $value){
                        $data = $HcyModel->field('id,qishu,create_time,currency',true)->where('qishu',$myself['qishu'])
                                ->where('currency',"$value")->findOrEmpty()->toArray();
                        $array["$value"] = $data;
                    }
                    
                    $ret['all']['data'] = $array;
                }
            }
            ksort($ret);
            $ret = json_encode($ret);
            $connection->send($ret);
        }
    }
}

