
var SampleDataViewers = function() {

		var openSample = function() {

			$("#sample-data-viewers").change(function() {

				if($(this).val() != '') window.open($(this).val(),'_blank');

			});

		}

    

   return {
        //main function to initiate the module
        init: function() {

           openSample();

        }

    };

}();

jQuery(document).ready(function() {
    SampleDataViewers.init();
});
