(function($) {
    $(function() {


        if ($.fn.dataTable) {
            $.extend(true, $.fn.dataTable.defaults, {
                oLanguage: {
                    sProcessing: "<div class='loader-center'><img height='50' width='50' src='" + BASE_URL + "assets/images/loading.gif'></div>"
                },
                bProcessing: true,
                bServerSide: true,
                ordering: false,
                iDisplayLength: 10,
                responsive: true,
                bSortCellsTop: true,
                searching: false,
                aaSorting: [
                    [0, 'desc']
                ],
                bDestroy: true, //!!!--- for remove data table warning.
                aLengthMenu: [
                    [5, 10, 20, -1],
                    [5, 10, 20, "All"]
                ],
                aoColumnDefs: [{
                    bSortable: false,
                    aTargets: [-1]
                }],
                drawCallback: function(settings) {

                }
            });

            if ($('.data-table').length) {
                $('.data-table').each(function() {
                    var opts = {};
                    // var obj = $(this);
                    if ($(this).attr('data-src')) {
                        opts['sAjaxSource'] = $(this).attr('data-src');

                    } else if ($(this).attr('data-opts')) {
                        $.extend(opts, $.parseJSON($(this).attr('data-opts')));
                    }
                    // var classes_id = $(this).attr('data-classes_id');
                    // var course_id = $(this).attr('data-course_id');
                    if ($(this).attr('data-server_params')) {
                        var sparam = $.parseJSON($(this).attr('data-server_params'));
                        opts["fnServerParams"] = function(aoData) {
                            $(sparam).each(function() {
                                aoData.push(this);
                            });
                        }
                    }
                    $(this).DataTable(opts);
                });
            }
        }
    });
})(jQuery);