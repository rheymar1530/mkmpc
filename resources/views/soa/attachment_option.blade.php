<style type="text/css">
	.modal-conf {
	  	/*max-width: 60% !important;
	  	min-width: 60% !important;*/
	}
	#modal-preview-data{
		font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	}
	#modal-preview-data input{
		font-size: 12px;
		height: 30px !important;
	}

.inputGroup {
  background-color: #fff;
  display: block;
  /*margin: 10px 0;*/
  position: relative;
}
.inputGroup label {
  padding: 8px 10px;
  width: 100%;
  display: block;
  text-align: left;
  color: #3C454C;
  cursor: pointer;
  position: relative;
  z-index: 2;
  transition: color 200ms ease-in;
  overflow: hidden;
}

.inputGroup label:before {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  content: "";
  /*background-color: #5562eb;*/
  background: #5562eb linear-gradient(180deg,#3ab0c3,#5562eb) repeat-x!important;


  position: absolute;
  left: 50%;
  top: 50%;
  transform: translate(-50%, -50%) scale3d(1, 1, 1);
  transition: all 300ms cubic-bezier(0.4, 0, 0.2, 1);
  opacity: 0;
  z-index: -1;
}
.inputGroup label:after {
  width: 32px;
  height: 32px;
  content: "";
  border: 2px solid #D1D7DC;
  background-color: #fff;
  background-image: url("data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
  background-repeat: no-repeat;
  background-position: 2px 3px;
  border-radius: 50%;
  z-index: 2;
  position: absolute;
  right: 30px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  transition: all 200ms ease-in;
}
.inputGroup input:checked ~ label {
  color: #fff;
}
.inputGroup input:checked ~ label:before {
  transform: translate(-50%, -50%) scale3d(56, 56, 1);
  opacity: 1;
}
.inputGroup input:checked ~ label:after {
  background-color: #54E0C7;
  border-color: #54E0C7;
}
.inputGroup input {
  width: 32px;
  height: 32px;
  order: 1;
  z-index: 2;
  position: absolute;
  right: 30px;
  top: 50%;
  transform: translateY(-50%);
  cursor: pointer;
  visibility: hidden;
}

.option_form {
  padding: 0 16px;
  max-width: 550px;
  margin: 50px auto;
  font-size: 18px;
  font-weight: 600;
  line-height: 36px;
}




</style>
<div class="modal fade bd-example-modal-xl" id="modal-attachment-option">
	<div class="modal-dialog modal-conf">
		<div class="modal-content" style="padding:10px">
			
			<form id="frm_confirm_request">
				<div class="modal-body">		
					<h4>Attachment Option</h4>	
					<hr>
					<span style="color:red;"><i>* Please Select atleast 1</i></span>
					<form class="form option_form">
						<input type="hidden" name="txt_token__" id="txt_token__">
						<div class="inputGroup">
							<input id="option_id_receiver" name="option_id_receiver" type="checkbox"/>
							<label for="option_id_receiver">Receiver ID</label>
						</div>

						<div class="inputGroup">
							<input id="option_pod" name="option_pod" type="checkbox"/>
							<label for="option_pod">POD</label>
						</div>

						<div class="inputGroup">
							<input id="option_signature" name="option_signature" type="checkbox"/>
							<label for="option_signature">Signature</label>
						</div>

						<button type="button" class="btn bg-gradient-primary" style="float:right;" id="btn_download_attachment"><i class="fas fa-download"></i>&nbsp;Download Attachment</button>
					</form>
				</div>
<!-- 				<div class="modal-footer">
					<div class="float-right">
						<button type="submit" class="btn btn-primary">Save changes</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div> -->
			</form>
		</div>
	</div>
</div>
@push('scripts')
	<script type="text/javascript">
		$('#btn_download_attachment').click(function(){
			var attachment_option = {
				'id_receiver' : ($('#option_id_receiver').prop('checked'))?1:0,
				'pod' : ($('#option_pod').prop('checked'))?1:0,
				'signature' : ($('#option_signature').prop('checked'))?1:0
			}
			var tot = attachment_option['id_receiver'] + attachment_option['pod'] + attachment_option['signature'];

			if(tot == 0){
        Swal.fire({
          position: 'center',
          icon: 'warning',
          title: 'Please Select at least one POD attachment',
          showConfirmButton: false,
          timer: 1500
        }).then(function() {
             
        })
        return;
			}

			var link_parameter = $.param(attachment_option);

			window.location = 'soa_attachment/'+$('#txt_token__').val()+'?'+link_parameter
			console.log({link_parameter});
		})
	</script>
@endpush