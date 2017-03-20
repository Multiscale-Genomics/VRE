var TableDatatablesRepository = function () {

    var handleTable = function () {

        var table = $('#table-repository');

        var oTable = table.dataTable({

            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],

            // set the initial value
            "pageLength": 5,

            "language": {
                "lengthMenu": " _MENU_ records"
            },
            "columnDefs": [{ // set default column settings
                'orderable': true,
                'targets': [0]
            }, {
                "searchable": true,
                "targets": [0]
            }, {
                "searchable": false,
                "targets": [6, 7]
            }],
            "order": [
                [5, "desc"]
            ], // set first column as a default sort by asc
        	"initComplete": function (settings, json) {
				$('#loading-datatable').hide();
				$('#table-repository').show();
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
	TableDatatablesRepository.init();
});
