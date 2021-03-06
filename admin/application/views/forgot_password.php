<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title><?php echo $this->template->title->default($this->config->item('site_name')); ?></title>

        <link rel="stylesheet" href="<?php echo assets_url('css/font-awesome.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/bootstrap.min.css'); ?>"/>
        <link rel="stylesheet" href="<?php echo assets_url('css/toastr.css'); ?>"/>
      

        <!-- Custom Theme Style -->
        <link rel="stylesheet" href="<?php echo assets_url('css/theme.css'); ?>"/> 
        <script src="<?php echo assets_url('js/jquery-3.4.1.min.js'); ?>"></script>
        <script src="<?php echo assets_url('js/toastr.min.js'); ?>"></script>
         <script src="<?php echo assets_url('js/jquery.validate.js'); ?>"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                toastr.options = {"closeButton": true}
                var success = '<?php echo $this->session->flashdata("success"); ?>';
                var error = '<?php echo $this->session->flashdata("error"); ?>';
                if (success != '') {
                    toastr.success(success);
                } else if (error != '') {
                    toastr.error(error);
                }
                
                /* ------- Forgot form script for validation-------- */
                $("#forgotForm").validate({
                    rules: {
                        email: {required: true,email:true}
                    }
                });
            });

        </script>
    </head>
    <style>
        /*start login*/
        .login-page{margin: 35px auto;width: 400px;}
        .login-bg{background-color: #fff;border: 1px solid #e8e8e8;border-radius: 4px;padding: 0 10px 20px;}
        .login-page .col-md-12.form-group.has-feedback{padding-bottom: 10px;}
        .login-logo{text-align: center;padding-bottom: 20px;}
        .login-bg h3{color: #8d8d8d;font-size: 24px;font-weight: bold;margin: 20px 0;}
        /*end login*/
    </style>
    <body>
        <div class="login-page">
            <div class="login-logo">
                <img src="<?php echo assets_url('images/logo.png') ?>">
            </div>
            <div class="row login-bg">
                <div><h3 class="text-center">FORGOT PASSWORD</h3></div>
                <form class="form-horizontal form-label-left input_mask" method="post" action="" id="forgotForm">
                    <div class="col-md-12 col-sm-12 col-xs-12 form-group has-feedback">
                        <input class="form-control has-feedback-left" type="text" placeholder="Email" name="email">
                        <span aria-hidden="true" class="fa fa-envelope form-control-feedback left"></span>
                        <?php echo form_error('email'); ?>
                    </div>

                    <div class="clerfix"></div>
                    <div class="col-md-12 col-sm-12 col-xs-12 text-center">
                        <button class="btn btn-primary btn-block m-b-15" type="submit">Submit</button>
                        <a href="<?php echo base_url() . 'auth/login'; ?>" type="button">Sign in</a>
                    </div>
                </form>    
            </div>    
        </div>

    </body>
</html>
