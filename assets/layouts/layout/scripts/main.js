
function openTermsOfUse() {
	$('#modalTerms').modal({ show: 'true' });

	$.ajax({
		type: "POST",
		url: "/applib/getTermsOfUse.php",
		data:"id=1",
		success: function(data) {
			$('#modalTerms .modal-body .container-terms').html(data);
		}
	});
}


jQuery(document).ready(function() {
	// Optimalisation: Store the references outside the event handler:
    var $window = $(window);
	
	var menu_toggler = false;

    function checkWidth() {
        if(!menu_toggler) {
			var windowsize = $window.width();
			if (windowsize < 1400) {
				$('body').addClass('page-sidebar-closed');
				$('ul.page-sidebar-menu').addClass('page-sidebar-menu-closed');
				$('.beta-short').show();
				$('.beta-long').hide();
				if ($.cookie) {
					$.cookie('sidebar_closed', '1');
				}	
			}else {
				$('body').removeClass('page-sidebar-closed');
				$('ul.page-sidebar-menu').removeClass('page-sidebar-menu-closed');
				$('.beta-short').hide();
				$('.beta-long').show();
				if ($.cookie) {
					$.cookie('sidebar_closed', '0');
				}	
			}
		}else{
			menu_toggler = false;
		}
    }
    // Execute on load
    checkWidth();
    // Bind event listener
    $(window).resize(checkWidth);

	$('.menu-toggler.sidebar-toggler').on('click', function() {
		
		if($('ul.page-sidebar-menu').hasClass('page-sidebar-menu-closed')) {
			$('.beta-short').hide();
			$('.beta-long').show();
		} else {
			$('.beta-short').show();
			$('.beta-long').hide();
		}

		menu_toggler = true;
	});

	// LOGOUT
	$('#logout-button').on('click', function() {

		if($("#type-of-user").val() == 3) {
		
			$('#modalLogoutGuest').modal({ show: 'true' });

		} else {

			App.blockUI({
						boxed: true,
				message: 'Logging out...'
					});

			$.ajax({
				type: "POST",
				//url: "/applib/logout.php",
				url: "/applib/logoutToken.php",
				data:"id=1",
				success: function(data) {
					d = data.replace(/(\r\n|\n|\r|\t)/gm,"");
					if(d == '1'){
						setTimeout(function(){ location.href = '/'; }, 1000);	
					}else{
						App.unblockUI();
					}
				}
			});

		}
	
	});


	$('#modalLogoutGuest').on('click', '.btn-ok', function(e) {

		$('#modalLogoutGuest').modal('hide');

		App.blockUI({
						boxed: true,
				message: 'Logging out...'
					});

			$.ajax({
				type: "POST",
				//url: "/applib/logout.php",
				url: "/applib/logoutToken.php",
				data:"id=1",
				success: function(data) {
					d = data.replace(/(\r\n|\n|\r|\t)/gm,"");
					if(d == '1'){
						setTimeout(function(){ location.href = '/'; }, 1000);	
					}else{
						App.unblockUI();
					}
				}
			});
	});

	
});


