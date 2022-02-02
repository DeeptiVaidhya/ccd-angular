<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<div class="">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="x_panel">
                <div class="x_title">
                    <h3>List of participants</h3>
                    <a class="btn btn-pink btn-sm pull-right" href="#">Export</a>
                    <div class="clearfix"></div>
                </div>

                <div class="x_content">
                    <!-- start table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered dt-responsive datatable-list data-table" data-src="<?php echo base_url() . 'user/get-users-data'; ?>">
                            <thead>
                                <tr>
                                    <th width="5%">#</th>
                                    <th>Name</th>
                                    <th>Insurance Type</th>
                                    <th>Coverage Effective Date</th>
                                    <th>Coverage Expiration Date</th>
                                    <th>Co Insurance Percentage</th>
                                    <th>Individual Deductible</th>
                                    <th>Individual Deductible Paid</th>
                                    <th>Individual Max Out of Pocket</th>
                                    <th>Individual Max Out of Pocket Paid</th>
                                    <th>Family Deductible</th>
                                    <th>Family Deductible Paid</th>
                                    <th>Family Max Out of Pocket</th>
                                    <th>Family Max Out of Pocket Paid</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                    <!-- end table -->
                </div>
            </div>
        </div>
    </div>
</div>

