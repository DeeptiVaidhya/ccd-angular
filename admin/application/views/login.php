<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="<?php echo assets_url('images/favicon.ico'); ?>">
        <title><?php echo $this->template->title->default($this->config->item('site_name')); ?> Login</title>

        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/toastr.css'); ?>"/>

        <!-- Custom Theme Style -->
        <link rel="stylesheet" href="<?php echo assets_url('css/theme.css'); ?>"/> 
        <script src="<?php echo assets_url('js/jquery-3.4.1.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/toastr.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/toastr.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/jquery.validate.js'); ?>"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                toastr.options = {closeButton: true}
                var success = '<?php echo $this->session->flashdata("success"); ?>';
                var error = '<?php echo $this->session->flashdata("error"); ?>';
                if (success != '') {
                    toastr.success(success);
                } else if (error != '') {
                    toastr.error(error);
                }
                /* ------- Login form script for validation-------- */
                $("#loginForm").validate({
                    rules: {
                        password: {required: true},
                        username: {required: true}
                    }
                });
            });

        </script>
    </head>
    <style>
        /*start login*/
        
        .login-bg{background-color: #fff;width: 400px;margin: 35px auto;border: 1px solid #e8e8e8;border-radius: 4px;padding: 0 10px 20px;}
        .login-page .col-md-12.form-group.has-feedback{padding-bottom: 10px;}
        .login-logo{text-align: center;padding-bottom: 20px;}
        .logo {
            text-align: center;
            letter-spacing: 1px;
            opacity: 1;
            font-size: 20px;
            line-height: 72px;
            font-family: "Montserrat-Regular";
            font-weight: 400;
            color: #fff;
            background: #093C74 0% 0% no-repeat padding-box;
        }
        .logo span {
            font-family: "Montserrat-SemiBold";
            font-weight: 600;
        }
        
        .login-bg h3{color: #8d8d8d;font-size: 24px;font-weight: bold;margin: 20px 0;}
        /*end login*/
    </style>
    <body>
        <div class="login-page">
            <div class="login-logo">
                <div class="logo"> CancerCost<span>Detox</span></div>
            </div>
            <div class="row login-bg">
                <div><h3 class="text-center">LOGIN</h3></div>
                <form id="loginForm" class="form-horizontal form-label-left input_mask" method="post" action="" autocomplete="off">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                        <input class="form-control has-feedback-left" type="text" placeholder="Username" name="username" value="<?php set_value('username'); ?>">
                        <span aria-hidden="true" class="fa fa-user form-control-feedback left"></span>
                        <?php echo form_error('username'); ?>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                        <input class="form-control has-feedback-left" type="password" placeholder="Password" name="password" value="<?php set_value('password'); ?>">
                        <span aria-hidden="true" class="fa fa-lock form-control-feedback left"></span>
                        <?php echo form_error('password'); ?>
                    </div>
                    <div class="clerfix"></div>
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <button class="btn btn-pink btn-block m-b-15" type="submit">Log in</button>
                        <a href="<?php echo base_url() . 'auth/forgot-password'; ?>" type="button">Forgot password?</a>
                    </div>
                </form>    
            </div>    
        </div>
    </body>
</html>
