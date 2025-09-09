@push('scripts')
<script type="text/javascript">
	function renew_investment(){
		Swal.fire({
			title: 'Do you want to proceed to renew this investment ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post_renewal();
			} 
		});
	}


	function post_renewal(){
		$.ajax({
			type      :      'POST',
			url       :      '/investment/post/renewal',
			data      :      {'id_investment' : ID_INVESTMENT},
			beforeSend :     function(){
				show_loader();
			},
			success   :      function(response){
				hide_loader();
				console.log({response});


				if(response.RESPONSE_CODE == "SUCCESS"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'success',
						showConfirmButton : false,
						timer : 2000
					}).then((result) => {
						window.location = `/investment/view/${response.NEW_ID_INVESTMENT}`;
					})
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});
				}

			},error: function(xhr, status, error) {
				hide_loader();
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}
		})
	}
</script>
@endpush