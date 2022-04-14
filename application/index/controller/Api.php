<?php
namespace app\index\controller;
use think\Controller;
use think\Request;
use think\Url;
use think\Db;
use think\Session;
use app\common\Model\UserModel;
use app\common\Model\HcyModel;
use app\common\Model\CyModel;

class Api extends Controller
{
    //補歷史數據
    
    
    private function gethcy($cy,$qishu){
        $hcy = DB::name("lottery_result_hcy")->where("qishu",$qishu)->where("currency",$cy)->select();
        if(empty($hcy)){
            $val = 0;
        }else{
            $val = $hcy[0]["s59"];
        }
        return $val;
    }
    //过滤重复数据
    public function filteradmin(){
        $f = DB::name("lottery_result_admin_cy")->field('id,qishu')->group('qishu')->having('count(qishu)>1')->select();
        foreach($f as $k){
            $u = array();
            $fa = DB::name("lottery_result_admin_cy")->where("qishu",$k["qishu"])->order(
                'id asc')->select();
            if(!$fa[0]["BTCUSDT"]){
                if(!$fa[1]["BTCUSDT"]){
                    $fa[1]["BTCUSDT"] = $this->gethcy('BTCUSDT',$k["qishu"]);
                }
                $u["BTCUSDT"] = $fa[1]["BTCUSDT"];
            }
            if(!$fa[0]["ETHUSDT"]){
                if(!$fa[1]["ETHUSDT"]){
                    $fa[1]["ETHUSDT"] = $this->gethcy('ETHUSDT',$k["qishu"]);
                }
                $u["ETHUSDT"] = $fa[1]["ETHUSDT"];
            }
            if(!$fa[0]["DOTUSDT"]){
                if(!$fa[1]["DOTUSDT"]){
                    $fa[1]["DOTUSDT"] = $this->gethcy('DOTUSDT',$k["qishu"]);
                }
                $u["DOTUSDT"] = $fa[1]["DOTUSDT"];
            }
            if(!$fa[0]["XRPUSDT"]){
                if(!$fa[1]["XRPUSDT"]){
                    $fa[1]["XRPUSDT"] = $this->gethcy('XRPUSDT',$k["qishu"]);
                }
                $u["XRPUSDT"] = $fa[1]["XRPUSDT"];
            }
            if(!$fa[0]["LTCUSDT"]){
                if(!$fa[1]["LTCUSDT"]){
                    $fa[1]["LTCUSDT"] = $this->gethcy('LTCUSDT',$k["qishu"]);
                }
                $u["LTCUSDT"] = $fa[1]["LTCUSDT"];
            }
            if(!$fa[0]["XMRUSDT"]){
                if(!$fa[1]["XMRUSDT"]){
                    $fa[1]["XMRUSDT"] = $this->gethcy('XMRUSDT',$k["qishu"]);
                }
                $u["XMRUSDT"] = $fa[1]["XMRUSDT"];
            }
            if(!$fa[0]["DASHUSDT"]){
                if(!$fa[1]["DASHUSDT"]){
                    $fa[1]["DASHUSDT"] = $this->gethcy('DASHUSDT',$k["qishu"]);
                }
                $u["DASHUSDT"] = $fa[1]["DASHUSDT"];
            }
            if(!$fa[0]["QTUMUSDT"]){
                if(!$fa[1]["QTUMUSDT"]){
                    $fa[1]["QTUMUSDT"] = $this->gethcy('QTUMUSDT',$k["qishu"]);
                }
                $u["QTUMUSDT"] = $fa[1]["QTUMUSDT"];
            }
            if(!$fa[0]["USDGBP"]){
                if(!$fa[1]["USDGBP"]){
                    $fa[1]["USDGBP"] = $this->gethcy('USDGBP',$k["qishu"]);
                }
                $u["USDGBP"] = $fa[1]["USDGBP"];
            }
            if(!$fa[0]["USDEUR"]){
                if(!$fa[1]["USDEUR"]){
                    $fa[1]["USDEUR"] = $this->gethcy('USDEUR',$k["qishu"]);
                }
                $u["USDEUR"] = $fa[1]["USDEUR"];
            }
            if(!$fa[0]["USDCAD"]){
                if(!$fa[1]["USDCAD"]){
                    $fa[1]["USDCAD"] = $this->gethcy('USDCAD',$k["qishu"]);
                }
                $u["USDCAD"] = $fa[1]["USDCAD"];
            }
            if(!$fa[0]["USDAUD"]){
                    if(!$fa[1]["USDAUD"]){
                        $fa[1]["USDAUD"] = $this->gethcys('USDAUD',$k["qishu"]);
                    }
                    $u["USDAUD"] = $fa[1]["USDAUD"];
                }
            if($u){
                DB::name("lottery_result_admin_cy")->where("id",$fa[0]["id"])->update($u);
                DB::name("lottery_result_admin_cy")->where('qishu',$k["qishu"])->where("id","<>",$fa[0]["id"])->delete();
            }
        }
    }
	private function cy($cy){
	  switch($cy){
          case "BTCUSDT":$t ='btc_usdt';break;
          case "ETHUSDT":$t ='eth_usdt';break;
          case "DOTUSDT":$t ='dot_usdt';break;
          case "XRPUSDT":$t ='xrp_usdt';break;
          case "LTCUSDT":$t ='ltc_usdt';break;
          case "XMRUSDT":$t ='xmr_usdt';break;
          case "DASHUSDT":$t ='dash_usdt';break;
          case "QTUMUSDT":$t ='qtum_usdt';break;
          case "USDGBP":$t = '1';break;
          case "USDEUR":$t = '1';break;
          case "USDCAD":$t = '1';break;
          case "USDAUD":$t = '1';break;
      }
      if($t==1){
          $content=file_get_contents('https://tw.rter.info/capi.php');
          $capi=json_decode($content,true);
          $last = $capi[$cy]["Exrate"];
      }else{
          $url = 'http://api.bitkk.com/data/v1/ticker?market='.$t;
    	  $getdata = $this->curlfun($url);
    	  $res = json_decode($getdata, 1);
          $last = $res["ticker"]["last"];    
      }
      return $last;
	}


     public function gc(){
        $param = request()->param();
        $this->getcy($param["cy"]);
	}
	private function getcy($cy){
      sleep(50);
      $c1 = date("Y-m-d H:i:s",strtotime(date("Y-m-d H:i",time()).":00"));
      $c2 = date("Y-m-d H:i:s",strtotime($c1)+60);
      $c3 = date("Y-m-d H:i:s",strtotime($c1)-60);
 	   $c1qishu = Db::name("lottery_result_cy")->where("create_time",$c1)->find();
 	   $c2qishu = Db::name("lottery_result_cy")->where("create_time",$c2)->find();
      $info1 = DB::name("lottery_result_admin_cy")->where("qishu",$c1qishu["qishu"])->find();
      $info2 = DB::name("lottery_result_admin_cy")->where("qishu",$c2qishu["qishu"])->find();

      $cys = $this->cy($cy);
      if(empty($info1)){
          $lld1 = $cys.$c1qishu[$cy];
      }else{
          $lld1 = $cys.$info2[$cy];
      }

      $dt1 = array();
      $str = substr($c2qishu["qishu"],0,3);
	  $str1 = substr($c2qishu["qishu"],3)+1;
	  $qishu = $str.$str1;
      $in1["qishu"] = $c2qishu["qishu"];
      $in1["currency"] = $cy;
      $in1["create_time"] = $c2;
      
      if($cy=="USDGBP" || $cy=="USDEUR" || $cy=="USDCAD" || $cy=="USDAUD"){
          $in1["s0"] = $cys;
      }else{
          $dt1[0] = $in1["s0"] = $lld1;
      }
      for($i=0;$i<60;$i++){
          $cysrand = $cys*$this->getrand();
          $dt1[$i+1] = round(($cys+$cysrand).rand(10,99),4);
          $s = $i+1;
          $in1["s".$s] = round(($cys+$cysrand).rand(10,99),4);
          if($s==59){
              $val = DB::name("lottery_result_cy")->where("qishu",$c2qishu["qishu"])->value($cy);
              $in1["s59"] = substr($in1["s59"],0,-2).$val;
          }
      }
      $cq1 = DB::name("lottery_result_hcy")->where("qishu",$c2qishu["qishu"])->where("currency",$cy)->find();
      if(empty($cq1) && !empty($in1["s0"])){
          DB::name("lottery_result_hcy")->insert($in1);
          //寫入歷史
          $ckad = DB::name("lottery_result_admin_cy")->where("qishu",$c2qishu["qishu"])->find();
          if(!empty($ckad)){
              $up[$cy] = $in1["s59"]; 
              DB::name("lottery_result_admin_cy")->where("qishu",$c2qishu["qishu"])->update($up);
          }else{
              $ins["qishu"] = $c2qishu["qishu"];
              $ins["create_time"] = $c2qishu["create_time"];
              $ins["datetime"] = $c2qishu["datetime"];
              $ins["state"] = 1;
              $ins[$cy] = $in1["s59"];
              DB::name("lottery_result_admin_cy")->insert($ins);
          }
      }
	}	
    private function getrand(){
   	    $rand = rand(1,20);
   	    $jn = rand(1,2);
   	    if($rand<10){
   	        $rands = "0.0".$rand;
   	    }else{
   	        $rands = "0.".$rand;
   	    }
   	    if($jn==1){
   	        return $rands/100;    
   	    }else{
   	        return -$rands/100;
   	    }
    }
	public function kaipan(){
	    sleep(10);
	    $data = [];
        Db::startTrans();
        $date = date('md');
        $qishu =$date."0001";
        $num   = 60*24;
        $i       = 0;
          // $newdate  = strtotime(date("Y-m-d",time())." 00:00:00");
          $newdate = strtotime(date("Y-m-d",strtotime("+1 day"))." 00:00:00");
         $dates = strtotime(date("Y-m-d",time())." 00:00:00");
        while ($i<$num) {
            $i++;
            $data[$i]['qishu'] = str_pad($qishu+($i-1),8,0,STR_PAD_LEFT);
            $data[$i]['create_time'] = date("Y-m-d H:i:s",$dates+($i-1)*60);
            $data[$i]['datetime'] = date("Y-m-d H:i:s",$dates+$i*60);
            $data[$i]['state']    = 0;
            $data[$i]['BTCUSDT'] = rand(10,99);
            $data[$i]['ETHUSDT'] = rand(10,99);
            $data[$i]['DOTUSDT'] = rand(10,99);
            $data[$i]['XRPUSDT'] = rand(10,99);
            $data[$i]['LTCUSDT'] = rand(10,99);
            $data[$i]['XMRUSDT'] = rand(10,99);
            $data[$i]['DASHUSDT'] = rand(10,99);
            $data[$i]['QTUMUSDT'] = rand(10,99);
            $data[$i]['USDGBP'] = rand(10,99);
            $data[$i]['USDEUR'] = rand(10,99);
            $data[$i]['USDCAD'] = rand(10,99);
            $data[$i]['USDAUD'] = rand(10,99);
        }
        Db::name("lottery_result_cy")->where("state",0)->delete();
        $info = Db::name("lottery_result_cy")->insertAll($data);
        if($info !== false){
            Db::commit();
        }else{
            Db::rollback();
        }
	}

    //curl获取数据
	public function curlfun($url, $params = array(), $method = 'GET')
	{
		$header = array();
		$opts = array(CURLOPT_TIMEOUT => 10, CURLOPT_RETURNTRANSFER => 1, CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_HTTPHEADER => $header);
		/* 根据请求类型设置特定参数 */
		switch (strtoupper($method)) {
			case 'GET' :
				$opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
				$opts[CURLOPT_URL] = substr($opts[CURLOPT_URL],0,-1);
				
				break;
			case 'POST' :
				//判断是否传输文件
				$params = http_build_query($params);
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_POST] = 1;
				$opts[CURLOPT_POSTFIELDS] = $params;
				break;
			default :
		}
		/* 初始化并执行curl请求 */
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$data = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		
		if($error){
			$data = null;
		}
		return $data;
	}
}