
<div class="left_col scroll-view">
    <div class="navbar nav_title">
        <a href="<?php echo $base_url ?>dashboard" class="site_title"><div class="logo"> CancerCost<span>Detox</span></div></a>
        <a href="<?php echo $base_url ?>dashboard" class="site_title-sm"><div class="logo">CCD</div></a>
    </div>

    <div class="clearfix"></div>
    <?php $login_user_detail = $this->session->userdata('logged_in'); ?>
    <!-- menu profile quick info -->
    <div class="profile clearfix">
        <div class="profile_info">
            <span>Welcome,</span>
            <h2><?php echo ucwords($login_user_detail->first_name . ' ' . $login_user_detail->last_name); ?></h2>
        </div>
    </div>
    <!-- /menu profile quick info -->

    <!-- sidebar menu -->
    <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
        <div class="menu_section">
            <ul class="nav side-menu">
				<li><a href="<?php echo $base_url ?>user/list-users"><i class="fa fa-users"></i> Participants</a></li>
				<li><a href="<?php echo $base_url ?>auth/profile"><i class="fa fa-address-card"></i> Profile</a></li>
				<li><a href="<?php echo $base_url ?>auth/logout"><i class="fa fa-sign-out"></i> Logout</a></li>
            </ul>
        </div>
    </div>
    <!-- /sidebar menu -->
</div>
