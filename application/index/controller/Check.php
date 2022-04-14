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

class Check extends Controller
{
    
    public function settle(){//結算
	    sleep(52);
	    //查詢未結訂單期号
	    $qishu = DB::name("order_lottery")->where("status",0)->value('lottery_number');
	    //查询是否有当期数据
	    $qi = DB::name("lottery_result_hcy")->where("qishu",$qishu)->find();
	    if(!empty($qi)){
    	    //查詢未結訂單
    	    $od = DB::name("order_lottery")->where("lottery_number",$qishu)->select();
    	    for($i=0;$i<count($od);$i++){
    	        $cys = $od[$i]["Gtype"];
    	        $tqi = DB::name("lottery_result_hcy")->where("qishu",$qishu)->where("currency",$cys)->find();
    	        //下注類型
    	        switch($od[$i]["rtype"]){
    	            case "G":case "D":
    	                $re = $this->GD($od[$i]["rtype"],$tqi["s59"]);
    	                break;
    	            case "J":case "O":
    	                $re = $this->JO($od[$i]["rtype"],$tqi["s59"]);
    	                break;
    	            case "FZB":
    	                $re = $this->FZB($od[$i]["bet_info"],$tqi["s59"]);
    	                break;
    	        }
    	        //更新状态，金额
    	        $usermoney = DB::name("members")->where("id",$od[$i]["user_id"])->value("money");
    	        if($re==1){
    	            $bmoney = $od[$i]["bet_money"]+($od[$i]["bet_money"]*$od[$i]["bet_rate"]);
    	        }else{
    	            $bmoney = 0;
    	        }
    	        $nmoney = $bmoney+$usermoney;
    	        DB::name("members")->where("id",$od[$i]["user_id"])->setField("money",$nmoney);
    	        $up["status"] = $ups["status"] = 1;
    	        $up["win"] = $bmoney;
				$up["result"] = $tqi["s59"];  //插入該期結果
    	        DB::name("order_lottery")->where("id",$od[$i]["id"])->update($up);
    	        DB::name("order_lottery_sub")->where("order_num",$od[$i]["order_num"])->update($ups);
    	    }
    	    //写入admin
	    }
	}
	private function GD($t,$cy){//高低
	    $val = substr($cy, -1);
	    if($t=="G"){
	        if($val>=5){
	            return 1;
	        }else{
	            return 2;
	        }
	    }else{
	        if($val<5){
	            return 1;
	        }else{
	            return 2;
	        }
	    }
	}
	private function JO($t,$cy){//單雙
	    $val = substr($cy, -1);
	    if($t=="J"){
	        if($val%2!=0){
	            return 1;
	        }else{
	            return 2;
	        }
	    }else{
	        if($val%2==0){
	            return 1;
	        }else{
	            return 2;
	        }
	    }
	}
	private function FZB($lcy,$cy){//反指標
	    $v1 = substr($lcy, -2);
	    $v2 = substr($cy,-2);
	    if($v1 != $v2){
	        return 1;
	    }else{
	        return 2;
	    }
	}
}