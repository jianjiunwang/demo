@extends('wap.layouts.main')
@extends('wap.layouts.header')
@section('content')
    <link type="text/css" rel="stylesheet" href="{{ asset('/wap/soccerskin/style/withdrewal_deposits.css') }}"
        media="screen">
    <script type="text/javascript" src="/wap/js/mmenu.all.min.js"></script>
    <script id="jquery_183" type="text/javascript" class="library" src="http://runjs.cn/js/sandbox/jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="{{ asset('/wap/js/jquery.date.js') }}"></script>
    <script>
        $(document).ready(function() {
            $("#side_menu_btn").click(function() {
                $("#side_menu_bg").toggle('slide');
                $("#side_menu").toggle('slide');
            });
            $("#side_menu_bg").click(function() {
                $("#side_menu_bg").toggle('slide');
                $("#side_menu").toggle('slide');
            });
            $('.detail_cube').click(function() {
                if ($(this).find(".detail_cube_in").css("display") == "none") {
                    $(this).find('.detail_cube_in').show();
                    $(this).find('.detail_cube_a').css('transform', 'rotate(180deg)');
                } else {
                    $(this).find('.detail_cube_in').hide();
                    $(this).find('.detail_cube_a').css('transform', 'rotate(0deg)');
                }
            });
        });
    </script>
    <style>
        #menu,
            {
            display: none;
        }

        .acc .myLv.lv0 {
            background-image: url(../wap/soccerskin/img/diamond{{ $_member->level }}.png);
        }

        ul,
        li {
            list-style: none;
            padding: 0
        }

        .right_game_a {
            background: #fff
        }

        .order_frame {
            overflow-x: hidden;
            overflow-y: hidden;
            height: 100%;
        }

        .right_side {
            float: none;
            width: 93%;
            m: 20px 0 55px;
            margin: 20px 0 55px !important;
        }

        .gft {
            background: #eee !important;
        }

        .item_list {
            width: 100% !important;
            color: #000 !important;
            font-size: 18px !important;
        }

        .game_form td {
            color: #000 !important;
        }

        #menu {
            display: block !important;
            position: fixed !important;
        }

        .head_banner {
            z-index: 10;
        }

        .menu_btn.menu_btn--home {
            top: 8px;
        }

        .content-1,
        .content-2,
        .content-3,
        .content-4 {
            opacity: 1;
            top: 100px;
            padding-top: 25px
        }

        .content {
            padding: 60px 15px;
        }

        .g_b {
            margin-top: 0;
            margin-bottom: 10px
        }

        .categoryTab.on {
            background: #3078eb;
            color: #fff !important
        }

        .categoryTab::before {
            content: "";
            position: absolute;
            display: block;
            width: 0px;
            height: 15px;
            background-color: #707070;
            right: 0;
            top: 12px;
        }
		                .top_box {
                        width: 100%;
                        display: table;
                        margin-bottom: 10px;
                    }

                    .top_box ul {
                        background: #fff;
                        width: 48%;
                        float: left;
                        margin: 0 1%;
                        border-radius: 5px;
                        border: 1px solid #ccc;
                    }

                    .top_box li {
                        text-align: center;
                        width: 100%;
                    }

                    .top_box li:nth-child(1) {
                        border-radius: 5px 5px 0 0;
                        background: linear-gradient(180deg, #daa858 0%, #ae8b00 100%) !important;
                        color: #fff;
                        line-height: 25px;
                    }

                    .top_box li {
                        line-height: 30px
                    }
    </style>
    <script type="text/javascript" src="/wap/js/mmenu.all.min.js"></script>
    <script id="jquery_183" type="text/javascript" class="library" src="http://runjs.cn/js/sandbox/jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="{{ asset('/wap/js/jquery.date.js') }}"></script>

    <div class="m_container">
        <div class="card_cube head_banner fixed-top">
            <div class="acc_money">
                <div class="acc"><span data-role="user-lv" class="myLv lv0"></span><span
                        data-role="username">{{ $_member->name }}</span></div>
                <div class="money pointer-event">
                    <div class="switchTag"></div>
                    <div class="carousel-inner">
                        <div class="moneyBox item carousel-item active">
                            <span style=" padding-left:10px"> <img src="{{ asset('../wap/soccerskin/img/coins1.png') }}"
                                    style="width:16px; "></span>
                            <span id="tgWallet" class="amounts">${{ $_member->money }}</span>
                        </div>
                        <div class="moneyBox item carousel-item">
                            <span>总资产</span>
                            <span id="totalAssetsView" class="amounts" data-role="wallet"
                                data-type="total">0.00</span>
                            <button class="openWallets" onclick="openMoney()"></button>
                        </div>
                    </div>
                    <ul id="memberMoney" class="totalMoneyList">
                        <li><span>TG钱包</span><span class="txt-right">${{ $_member->money }}</span></li>
                    </ul>
                    <input type="hidden" id="memberQuota" value="0.00">
                </div>
            </div>
        </div>
        <div class="title_background">
            <button class="announcement_btn" onclick="titleannice()" style="display: none;"></button>
            <div class="titleSwitch">
                <marquee id="marqinfo" direction="left" style="display: none;"></marquee>
                <div id="markettitle" class="title">BDX公告
                </div>
            </div>
        </div>
        <section class="tabs">
			
			  @foreach ($game_notice as $notice)
			<div class="detail_box">
                   
                            <ul class="detail_cube">
                                <li class="item_tt">{{ $notice->title }}</li>
                                

                                <li>
                                    <ul class="detail_cube_in">
                                        <li>{{ $notice->content }}</li>
                                     
                                      
                                    </ul>
                                </li>
                                <div class="detail_cube_a"></div>
                            </ul>
                
                     
                </div>
			
                            @endforeach
			
			
            
        </section>
    </div>
@endsection
