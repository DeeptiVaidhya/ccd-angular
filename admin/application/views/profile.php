<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Profile</h3>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="col-md-2 col-sm-2 col-xs-12 profile_left text-center">
                        <h4><?php echo ucwords($login_user_detail->first_name . ' ' . $login_user_detail->last_name); ?></h4>
                    </div>
                    <div class="col-md-10 col-sm-10 col-xs-12">
                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Basic information</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br>
                                <form id="basicInfoForm" class="form-horizontal form-label-left" method="post" enctype='multipart/form-data'>

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">First Name</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" name="first_name" class="form-control" value="<?php echo $login_user_detail->first_name; ?>">
                                            <?php echo form_error('first_name'); ?> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Last Name</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" name="last_name" class="form-control" value="<?php echo $login_user_detail->last_name; ?>">
                                            <?php echo form_error('last_name'); ?> 
                                        </div>
									</div>
                                    <input type="hidden" name="action" value="basic_information" />
                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                            <button type="submit" class="btn btn-pink pull-right">Save</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <div class="x_panel">
                            <div class="x_title">
                                <h2>Log in details</h2>
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                            <div class="x_content">
                                <br>
                                <form id="loginDetailForm" class="form-horizontal form-label-left" method="post"> 

                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Username</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" name="username" class="form-control" value="<?php echo $login_user_detail->username; ?>">
                                            <?php echo form_error('username'); ?> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="text" name="email" class="form-control" value="<?php echo $login_user_detail->email; ?>">
                                            <?php echo form_error('email'); ?> 
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">New Password</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input  id="password" type="password" class="form-control" name="password">
                                            <?php echo form_error('password'); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-md-3 col-sm-3 col-xs-12">Retype New Password</label>
                                        <div class="col-md-9 col-sm-9 col-xs-12">
                                            <input type="password" class="form-control" name="confirm_password">
                                            <?php echo form_error('confirm_password'); ?>
                                        </div>
                                    </div>
                                    <input type="hidden" name="action" value="login_detail" />

                                    <div class="ln_solid"></div>
                                    <div class="form-group">
                                        <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                            <button type="submit" class="btn btn-pink pull-right">Save</button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


