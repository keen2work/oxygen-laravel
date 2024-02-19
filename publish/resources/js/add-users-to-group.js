import $ from "jquery";

import validate from 'jquery-validation'
import select2 from 'select2';
select2();

$(document).ready(function () {

	var hideModalErrorMessage = function () {
		$('#modalErrorMessage').html('').hide();
	};
	var showLoadingIndicator = function () {
		$('#loadingIndicator').show();
	};
	var hideLoadingIndicator = function () {
		$('#loadingIndicator').hide();
	};

	// multiple drop-down selects
	$('.select2').select2();

	$('#usersForm').validate({
		rules: {
			'selectRoles[]': {
				required: true
			},
			'selectUsers[]': {
				required: true
			}
		},
		submitHandler: function (form) {
			hideModalErrorMessage();
			showLoadingIndicator();
			$.ajax({
				method: "post",
				url: "/account/groups/users",
				data: $(form).serialize()
			}).done(function ( data ) {
				// don't clear or hide the modal, since we're going to reload the page
				// clear the drop down
				// $('.select2').select2('val', []);
				// $('#userControlModal').modal('hide');

				// refresh the page
				window.location.reload();
			}).fail(function (xhr, status, err) {
				var message = 'An error occured. Please check your input and try again';
				if (xhr.responseJSON.message) {
					message = xhr.responseJSON.message;
				}
				$('#modalErrorMessage').html('<strong>Whoops!</strong> ' + message).show();
			}).always(function () {
				hideLoadingIndicator();
			});
		}
	});


	$('#userControlModal').on('show.bs.modal', function (event) {
		var button = $(event.relatedTarget);  // Button that triggered the modal
		var role_id = button.data('role_id'); // Extract info from data-* attributes

		var modal = $(this);

		// select the role by default
		$('#selectRoles').select2('val', role_id);

		modal.find('.modal-title').text('Add New Users to a Group');

		hideModalErrorMessage();
		hideLoadingIndicator();
	});

	$('#userControlModal').on('hidden.bs.modal', function (event) {
		// clear the errors when closing
		hideModalErrorMessage();
	});
});
