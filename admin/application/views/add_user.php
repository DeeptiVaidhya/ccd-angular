<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>Add Professional</h3>
                    <ul class="nav navbar-right panel_toolbox">
                        <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                        </li>
                    </ul>
                    <div class="clearfix"></div>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <form id="registerForm" class="form-horizontal form-label-left" method="post">

                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">First Name</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="first_name" class="form-control" value="<?php echo set_value('first_name');?>">
                                        <?php echo form_error('first_name'); ?> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Last Name</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="last_name" class="form-control" value="<?php echo set_value('last_name');?>">
                                        <?php echo form_error('last_name'); ?> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Email</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="email" class="form-control" value="<?php echo set_value('email');?>">
                                        <?php echo form_error('email'); ?> 
                                    </div>
								</div>
								
								<div class="form-group">
                                    <label class="control-label col-md-3 col-sm-3 col-xs-12">Phone Number</label>
                                    <div class="col-md-9 col-sm-9 col-xs-12">
                                        <input type="text" name="phone_number" class="form-control" value="<?php echo set_value('phone_number');?>">
                                        <?php echo form_error('phone_number'); ?> 
                                    </div>
								</div>

                                <input type="hidden" name="user_type" value="2">

                                <div class="ln_solid"></div>
                                <div class="form-group">
                                    <div class="col-md-9 col-sm-9 col-xs-12 col-md-offset-3">
                                        <button type="submit" class="btn btn-primary pull-right">Save</button>
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
<div id="loading">
  <img id="loading-image" src="<?php echo assets_url('images/loading.gif') ?>" alt="Loading..." />
</div>

