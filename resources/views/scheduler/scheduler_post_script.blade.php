@push('scripts')
<script type="text/javascript">
	let TYPE = {{$SCHEDULER_PARAM['schedule_type'] ?? 0}};
	const REFERENCE = {{$SCHEDULER_PARAM['scheduler_reference'] ?? 0}};
	const BOOKS = {{$SCHEDULER_PARAM['books'] ?? 0}}
	const ID_SCHEDULER = {{$SCHEDULER_PARAM['id_scheduler'] ?? 0}};

	function show_scheduler_modal(){
		$('#scheduler_modal').modal('show');
	}

	$('#frm_post_schedule').submit(function(e){
		e.preventDefault();
		Swal.fire({
			title: 'Do you want to post this scheduler ?',
			icon: 'warning',
			showDenyButton: false,
			showCancelButton: true,
			confirmButtonText: `Save`,
		}).then((result) => {
			if (result.isConfirmed) {
				post_schedule();
			} 
		})	
	});

	function post_schedule(){

		var ajaxParam = {
			'date'  : $('#txt_sched_date').val(),
			'sched_type' : $('#sel_sched_type').val(),
			'REFERENCE' : REFERENCE,
			'BOOKS' : BOOKS,
			'TYPE' : TYPE,
			'stop_date' : $('#txt_stop_date').val(),
			'is_active' : $('#sel_status').val()
		};
		$.ajax({
			'type'       :       'POST',
			'url'        :       '/scheduler/post',
			'data'		 :       ajaxParam,
			beforeSend   :       function(){
								 show_loader();
			},
			success      :       function(response){
								console.log({response});
								hide_loader();
								if(response.RESPONSE_CODE == "ERROR"){
									Swal.fire({
										title: response.message,
										text : '',
										icon: 'warning',
										showCancelButton : false,
										showConfirmButton : false,
										timer : 3000
									});
								}else if(response.RESPONSE_CODE == "SUCCESS"){
									Swal.fire({
										title: response.message,
										text : '',
										text: '',
										icon: 'success',
										showCancelButton : true,
										cancelButtonText: 'Close',
										
										showDenyButton: false,
							
										showConfirmButton : false,   
										timer : 2500  

									}).then((result) => {
										location.reload();
									});
								}
			},error: function(xhr, status, error) {
				hide_loader()
				var errorMessage = xhr.status + ': ' + xhr.statusText;
				Swal.fire({
					title: "Error-" + errorMessage,
					text: '',
					icon: 'warning',
					confirmButtonText: 'OK',
					confirmButtonColor: "#DD6B55"
				});
			}

		});
		console.log({ajaxParam});
	}
</script>
@endpush