var TablePDNAOut = function () {

    var handleTable = function () {

        var table = $('#tablePDNAD');

        var oTable = table.dataTable({

            "lengthMenu": [
                [5, 10, 20, -1],
                [5, 10, 20, "All"] // change per page values here
            ],

            // set the initial value
            "pageLength": -1,

            "language": {
                "lengthMenu": " _MENU_ records"
            },
            "columnDefs": [{ // set default column settings
                'orderable': true,
                'targets': [0]
            }, {
                "searchable": true,
                "targets": [0]
            }],
            "order": [
                [5, "asc"]
            ] , // set first column as a default sort by asc
        	"initComplete": function (settings, json) {
				$('#loading-datatable').hide();
				$('#tablePDNAD').show();
			}
        });


    }

    return {

        //main function to initiate the module
        init: function () {
            handleTable();
        }

    };

}();

jQuery(document).ready(function() {
    TablePDNAOut.init();
    //console.log($('#tablePDNAD').html());
});
