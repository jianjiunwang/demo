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

class Order extends Controller
{
    public function bet(){
        $param = request()->param();
        $uid= session('uid');
        //檢測是否登陸
        if(empty($uid)){
            $data = array("code"=>"","text"=>"帳戶未登入");//未登  
        }else{
            //檢測額度是否足夠
            $user = DB::name("members")->where("id",$uid)->find();
            $config = DB::name("system_config")->where("id",1)->find();
            sleep($user["delay"]);
            $str = explode("&",$param["data"]);
            $str1 = array();
            foreach($str as $val){
                $v = explode("=",$val);
                if($v[0]=="totalView"){
                    $v1 = explode("%24",$v[1]);
                    $v[1] = $v1[1];
                }
                $str1[$v[0]] = $v[1];
            }
            $totalbet = $this->totalv($str1);

            //當前期數
            $nowtime = date("Y-m-d H:i:s",time());
            $cy = DB::name("lottery_result_cy")->where("create_time","<=",$nowtime)->where("datetime",">",$nowtime)->find();
            $lbtime = strtotime($cy["datetime"])-10;
            if($user["bstatus"]=="1"){
                $data = array("code"=>"","text"=>"當前帳戶已被停壓");   //額度不足
            }elseif($user["money"]<$totalbet["totalbet"]){
                $data = array("code"=>"","text"=>"當前帳戶額度不足");   //額度不足
            }elseif($totalbet["totalbet"]==0){
                $data = array("code"=>"","text"=>"交易失敗，請選擇玩法");   //失敗
            }elseif($totalbet["totalbet"] < $config["sscmin"]){
                $text = "下注最低限額為".$config["sscmin"];
                $data = array("code"=>"","text"=>$text);   //查詢最低限額
            }elseif($totalbet["totalbet"] > $config["sscmax"]){
                $text = "下注最高限額為".$config["sscmax"];
                $data = array("code"=>"","text"=>$text);   //查詢最高限額
            }elseif(time()>$lbtime){
                $data = array("code"=>"","text"=>"交易失敗，已超過可購買時間");   //失敗
            }elseif($str1["draw_uid"] != $cy["qishu"]){
                $data = array("code"=>"","text"=>"交易失敗，請重整頁面後繼續購買");
            }else{
                if(array_key_exists("205_10_124", $str1)){//漲
                    $str1["bet_rate"] = "0.9";
                    $str1["quick_type"] = "G";
                    $str1["number"] = "0";
                    $this->inorder($str1);
                }
                if(array_key_exists("205_10_125", $str1)){//跌
                    $str1["bet_rate"] = "0.9";
                    $str1["quick_type"] = "D";
                    $str1["number"] = "0";
                    $this->inorder($str1);
                }
                if(array_key_exists("205_2_111", $str1)){//高
                    $str1["bet_rate"] = "0.9";
                    $str1["quick_type"] = "G";
                    $str1["number"] = "0";
                    $this->inorder($str1);
                }
                if(array_key_exists("205_2_112", $str1)){//低
                    $str1["bet_rate"] = "0.9";
                    $str1["quick_type"] = "D";
                    $str1["number"] = "0";
                    $this->inorder($str1);
                }
                if(array_key_exists("205_3_113", $str1)){//單
                    $str1["bet_rate"] = "0.9";
                    $str1["quick_type"] = "J";
                    $str1["number"] = "0";
                    $this->inorder($str1);
                }
                if(array_key_exists("205_3_114", $str1)){//雙
                    $str1["bet_rate"] = "0.9";
                    $str1["quick_type"] = "O";
                    $str1["number"] = "0";
                    $this->inorder($str1);
                }
                if(array_key_exists("205_41_168", $str1)){//反指標
                    $str1["bet_rate"] = "0.01";
                    $str1["quick_type"] = "FZB";
                    $t1 = date("Y-m-d H:i",time()).":00";
                    $t2 = date("s",time());
                    
                    $num = DB::name("lottery_result_hcy")->where("create_time",$t1)->find();
                    $t2s = "s".$t2;
                    $number = $num[$t2s];
                    $str1["number"] = $number;
                    $this->inorder($str1);
                }
                $text = "共: ".$totalbet["add"]." 筆單,總計:".$totalbet["totalbet"];
                $data = array("code"=>"1","text"=>$text);//成功
            }
        }
        return json($data);
    }
    private function totalv($str){
        $add = 0;
        if(array_key_exists("205_2_111", $str)){$add += 1;}
        if(array_key_exists("205_2_112", $str)){$add += 1;}
        if(array_key_exists("205_3_113", $str)){$add += 1;}
        if(array_key_exists("205_3_114", $str)){$add += 1;}
        if(array_key_exists("205_41_168", $str)){$add += 1;}
        if(array_key_exists("205_10_124", $str)){$add += 1;}
        if(array_key_exists("205_10_125", $str)){$add += 1;}
        if($add==0){
            $totalbet=0;
        }else{
            $totalbet = $add*$str["totalView"];
        }
        $val["add"] = $add;
        $val["totalbet"] = $totalbet;
        return $val;
    }
    private function inorder($data){
        $uid= session('uid');
        $user = DB::name("members")->where("id",$uid)->find();
        $in["user_id"] = $uid;
        $orderid = date("YmdHis",time()).rand(100000,999999);
        $in["order_num"] = $orderid;
        $in["Gtype"] = $data["game"];
        if($data["draw_uid"]){
            $in["lottery_number"] = $data["draw_uid"];
        }else{
            $lo = DB::name("lottery_result_hcy")->where("currency",$data["game"])->order('id desc')->limit(1)->find();
            $in["lottery_number"] = $lo['qishu'];
        }
        
        switch($data["game"]){
            case "BTCUSDT":$in["rtype_str"]="比特幣/美金";break;
            case "ETHUSDT":$in["rtype_str"]="以太幣/美金";break;
            case "DOTUSDT":$in["rtype_str"]="波卡幣/美金";break;
            case "XRPUSDT":$in["rtype_str"]="瑞波幣/美金";break;
            case "LTCUSDT":$in["rtype_str"]="萊特幣/美金";break;
            case "XMRUSDT":$in["rtype_str"]="門羅幣/美金";break;
            case "DASHUSDT":$in["rtype_str"]="達世幣/美金";break;
            case "QTUMUSDT":$in["rtype_str"]="量子鏈幣/美金";break;
            case "USDGBP":$in["rtype_str"]="美金/英鎊";break;
            case "USDEUR":$in["rtype_str"]="美金/歐元";break;
            case "USDCAD":$in["rtype_str"]="美金/加元";break;
            case "USDAUD":$in["rtype_str"]="美金/澳元";break;
        }
        $in["rtype"] = $data["quick_type"];
        $in["bet_info"] =  $data["number"];
        $in["bet_money"] = $data["totalView"];
        $in["win"] = 0;
        $in["bet_rate"] = $data["bet_rate"];
        $in["bet_time"] = date("Y-m-d H:i:s",time());
        $in["status"] = 0;
        DB::name("order_lottery")->insert($in);
        
        $afmoney = $user["money"]-$data["totalView"];
        $ins["order_num"] = $orderid;
        $ins["order_sub_num"] = $orderid;
        $ins["quick_type"] = $data["quick_type"];
        $ins["number"] = $data["number"];
        $ins["bet_money"] = $data["totalView"];
        $ins["bet_rate"] = $data["bet_rate"];
        $ins["win"] = $data["bet_rate"]*$data["totalView"];
        $ins["balance"] = $afmoney;
        $ins["status"] = "0";
        $ins["is_win"] = "0";
        DB::name("order_lottery_sub")->insert($ins);
        
        DB::name("members")->where("id",$uid)->setField("money",$afmoney);

        //money_log
        $log['user_id'] = $uid;
        $log['name'] = $user["name"];
        $log['order_num'] = $orderid;
        $log['about'] = $in["rtype_str"];
        $log['update_time'] = date("Y-m-d H:i:s",time());
        $log['type'] = '遊戲下注';
        $log['order_value'] = $data["totalView"];
        $log['assets'] = $user["money"];
        $log['balance'] = $afmoney;
        DB::name("money_log")->insert($log);
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