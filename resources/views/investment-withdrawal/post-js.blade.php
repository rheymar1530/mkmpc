@push('scripts')
<script type="text/javascript">
	
	function post(){

		Swal.fire({
			title: 'Do you want to save this?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post_data();
			} 
		})
	}
	function post_data(){

		var post_data = [];
		$('tr.c-withdrawal-form').each(function(){
			var $temp = {};
			$temp['id_investment'] = $(this).attr('data-id');
			$temp['amount'] = decode_number_format($(this).find('input.text-with-amt').val());

			post_data.push($temp);
		});



		$.ajax({
			type         :      'GET',
			url          :      '/investment-withdrawal/post',
			data         :      {'post_data' : post_data,
			'opcode' : opcode,
			'id_investment_withdrawal_batch' : ID_INVESTMENT_WITHDRAWAL_BATCH},



			beforeSend   :      function(){
				show_loader();
				$('.mandatory').removeClass('mandatory');
			},
			success      :      function(response){
				console.log({response});
				hide_loader();
				if(response.RESPONSE_CODE == "SUCCESS"){

					var html_swal = '';
					var link = "/investment-withdrawal/view/"+response.id_investment_withdrawal_batch+"?href="+encodeURIComponent('<?php echo $back_link;?>');

					html_swal = "<a href='"+link+"'>Withdrawal ID# "+response.id_investment_withdrawal_batch+"</a>";

					Swal.fire({
						title: "Investment Withdrawal Successfully Posted",
						html : html_swal,
						text: '',
						icon: 'success',
						showCancelButton : true,
						confirmButtonText: 'Create Another Withdrawal',
						cancelButtonText: 'Back to List of Withdrawal',
						showDenyButton: false,

						showConfirmButton : true,     
						allowEscapeKey : false,
						allowOutsideClick: false
					}).then((result) => {
						if(result.isConfirmed) {
							window.location = "/investment-withdrawal/create?href="+encodeURIComponent('<?php echo $back_link;?>');
						}else{
							window.location = '<?php echo $back_link;?>';
						}
					});	
				}else if(response.RESPONSE_CODE == "ERROR"){
					Swal.fire({
						title: response.message,
						text: '',
						icon: 'warning',
						showCancelButton : false,
						showConfirmButton : false,
						timer : 2500
					});
					
					if(response.INVALID_INPUTS != undefined){
						$.each(response.INVALID_INPUTS,function(i,item){
							// $(`tr.c-withdrawal-form[data-id="${item.id_investment}"]`).addClass('mandatory');
							$(`tr.c-withdrawal-form[data-id="${item.id_investment}"]`).find('input.text-with-amt').addClass('mandatory');
						});
					}
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
		console.log({post_data});
	}

	$(document).on("focus",".class_amount",function(){
		var val = $(this).val();
		if(val == '' || val == 'NaN'){
			val = '0.00';
		}
		$(this).val(decode_number_format(val)); 
	})
	$(document).on("blur",".class_amount",function(){
		var val = $(this).val();
		if(!$.isNumeric(val)){
			val = 0;
		}
		$(this).val(number_format(parseFloat(val)));
	});
</script>

@endpush