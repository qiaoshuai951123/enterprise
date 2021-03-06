<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:74:"/www/wwwroot/ccc.flv.pet/public/../application/index/view/login/index.html";i:1582203465;}*/ ?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>登录</title>
    <link type="text/css" rel="stylesheet" href="/static/index/css/login.css" />
    <script type="text/javascript" src="/static/index/js/jquery-1.11.1.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var height=$(document).height();
            $('.main').css('height',height);
        })
    </script>
    <style>
        .verify{
            width: 95px;
            height: 40px;
        }
        input{
            outline: none;
        }
        input:focus{
            border: 1px solid #0D99DB;
        }
    </style>
</head>

<body>
<div class="main">
    <div class="main0">
        <div class="main_left">
            <img src="/static/index/img/login-image-3.png" class="theimg"/>
            <img src="/static/index/img/login-image-2.png" class="secimg"/>
            <img src="/static/index/img/login-image-1.png" class="firimg"/>
        </div>
        <div class="main_right">
            <div class="main_r_up">
                <div class="pp">登录</div>
            </div>
            <div class="sub"><p>还没有账号？<a href="<?php echo url('login/zhuce'); ?>"><span class="blue">立即注册</span></a></p></div>
            <div class="txt">
                <span style="letter-spacing:8px;">用户名:</span>
                <input name="user_name" type="text" class="txtphone" placeholder="请输入用户名"/>
            </div>
            <div class="txt">
                <span style="letter-spacing:4px;">登录密码:</span>
                <input name="user_pwd" type="password" class="txtphone" placeholder="请输入登录密码"/>
            </div>
            <div class="txt">
                <span style=" float:left;letter-spacing:8px;">验证码:</span>
                <input name="vercode" type="text" class="txtyzm" placeholder="请输入页面验证码"/>
                <img src="<?php echo captcha_src(); ?>" class="verify" onclick="javascript:this.src='<?php echo captcha_src(); ?>?rand='+Math.random()" id="capimg">
            </div>
            <div class="xieyi">
                <input name="remember" type="checkbox" value="1" checked="checked"/>
                记住我
            </div>
            <div class="xiayibu">登录</div>
        </div>
        <script>
            $('.xiayibu').click(function () {
                var user_name = $('input[name=user_name]').val();
                var user_pwd = $('input[name=user_pwd]').val();
                var vercode = $('input[name=vercode]').val();
                var remember = $('input[name=remember]:checked').val();

                $.post("<?php echo url('login/login_action'); ?>",{user_name:user_name,user_pwd:user_pwd,vercode:vercode,remember:remember},function (res) {
                    console.log(res)
                    if (res.icon == 6){
                        alert(res.msg);
                        window.location.href = "<?php echo url('users/users'); ?>";
                    }else{
                        $('#capimg').attr('src', this.src = '<?php echo captcha_src(); ?>?rand=' + Math.random());
                        alert(res.msg);
                    }
                },'json');
            });
        </script>
    </div>
</div>

<div class="footer">
    <div class="footer0">
<!--        <div class="footer_l">使用条款 | 隐私保护</div>-->
<!--        <div class="footer_r">© 2015 （杭州）金融信息服务有限公司    杭ICP备0000000号</div>-->
    </div>
</div>
</body>
</html>
