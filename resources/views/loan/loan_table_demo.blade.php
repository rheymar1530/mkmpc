

<!-- Main content -->
<!-- <div class="content">          -->
	<style type="text/css">
		.tbl_loan  tr>td,.tbl_loan  tr>th{

			vertical-align:top;
			font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
			font-size: 15px;
			/*font-weight: bold;*/
		}
		.xl-text{
			font-size: 18px !important;
		}
		.repayment-pad tr>td,.repayment-pad tr>th{
			padding:4px !important;
		}
		.loan-pad tr>td,.loan-pad tr>th{
			padding:1px;
			padding-right: 5px !important;
		}
		.border{
			border: 1px solid black !important;
		}
		.col_amount{
			text-align: right;
		}
		.center{
			text-align: center;
		}
		.border-top-bottom{
			border-top: 1px solid black !important;
			border-bottom: 1px solid black !important;
		}
		.border-bottom{
			border-bottom: 1px solid black !important;
			border-top: none !important;
		}
		.border-top{
			border-top: 1px solid black !important;

		}
		.no_border{
			border: none !important;
		}
		.col_amount:before{
			content: '₱';
		}
		.pad-left{
			padding-left: 10px !important;
		}

	</style>

	<div class="row">
		<div class="col-md-12">
			<table class="table tbl_loan loan-pad" style="white-space: nowrap;margin-bottom: unset!important;border: 1px solid black !important">
				<tbody><tr>
					<th class="no_border pad-left xl-text">Loan Service: INSTALLMENT LOAN 1</th>
					<th class="no_border xl-text">Terms: 24 Months</th>
				</tr>
				<tr>
					<th class="no_border pad-left xl-text">No of Repayments: 24</th>
					<th class="no_border xl-text">Interest: 3.00%</th>
				</tr>
			</tbody></table>
			<table class="table tbl_loan loan-pad" style="white-space: nowrap;margin-bottom: unset!important;margin-top: -3px;border: 1px solid black !important">
				<tbody><tr class="table_header_dblue">
					<th colspan="4" class="center">LOAN PROCEEDS COMPUTATION</th>
				</tr>
				<!-- LOAN AMOUNT -->
				<tr>
					<th class="border-top-bottom xl-text pad-left" colspan="3">Loan Amount</th>
					<th class="border-top-bottom col_amount xl-text" colspan="1">200,000.00</th>
				</tr>
				<tr>
					<th colspan="4" class="pad-left">Deductions:</th>
				</tr>	
				<!-- DEDUCTIONS LIST -->
				<tr>
					<td colspan="2" class="no_border pad-left">Sevice Fee (1.00%)</td>
					<td class="col_amount no_border">2,000.00</td>
					<td class="no_border"></td>
				</tr>	
				<!-- DEDUCTIONS LIST -->
				<tr>
					<td colspan="2" class="no_border pad-left">Notarial Fee</td>
					<td class="col_amount no_border">100.00</td>
					<td class="no_border"></td>
				</tr>	
				<!-- DEDUCTIONS LIST -->
				<tr>
					<td colspan="2" class="no_border pad-left">CBU (5.00%)</td>
					<td class="col_amount no_border">10,000.00</td>
					<td class="no_border"></td>
				</tr>	
				<!-- DEDUCTIONS LIST -->
				<tr>
					<td colspan="2" class="no_border pad-left">Loan Protection (1.50%)</td>
					<td class="col_amount no_border">3,000.00</td>
					<td class="no_border"></td>
				</tr>	
				<!-- DEDUCTIONS LIST -->
				<tr>
					<td colspan="2" class="no_border pad-left">CBU deficient amount</td>
					<td class="col_amount no_border">2,000.00</td>
					<td class="no_border"></td>
				</tr>	
				<!-- BALANCE FROM THE CURRENT LOAN -->
				<tr>
					<th colspan="4" class="border-top pad-left">Balance from the current loans:</th>
				</tr>
				<tr>
					<td class="no_border pad-left">Remaining balance</td>
					<td class="col_amount no_border">50,000.00</td>
					<td class="no_border" colspan="2"></td>
				</tr>	
				<tr>
					<td class="no_border pad-left">Rebates on loan protection</td>
					<td class="col_amount no_border">(500.00)</td>
					<td class="no_border" colspan="2"></td>
				</tr>
				<tr>
					<td class="no_border pad-left" colspan="2">Remaining balance</td>
					<td class="col_amount no_border">49,500.00</td>
					<td class="no_border"></td>
				</tr>
				<!-- TOTALS -->

				<tr>
					<th class="border-top-bottom xl-text pad-left" colspan="3">Total Deductions</th>
					<th class="border-top-bottom col_amount xl-text" colspan="1">66,600.00</th>
				</tr>	
				<!-- TOTALS -->
				<tr>
					<th class="border-top-bottom xl-text pad-left" colspan="3">Total Loan Proceeds</th>
					<th class="border-top-bottom col_amount xl-text" colspan="1">133,400.00</th>
				</tr>			
			</tbody></table>
			<table class="table tbl_loan repayment-pad" style="white-space: nowrap;margin-top: -3px;border: 1px solid black !important">
				<tbody><tr class="table_header_dblue">
					<th colspan="5" class="center">LOAN SCHEDULE</th>
				</tr>
				<tr>
					<th class="border center">No.</th>
					<th class="border center">Principal</th>
					<th class="border center">Interest</th>
					<th class="border center">Fees</th>
					<th class="border center">Total Amount Due</th>
				</tr>			
				<tr>
					<td class="border center">1</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">2</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">3</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">4</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">5</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">6</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">7</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">8</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">9</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">10</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">11</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">12</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">13</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">14</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">15</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">16</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">17</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">18</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">19</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">20</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">21</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">22</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">23</td>
					<td class="border col_amount">8,333.33</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.33</td>
				</tr>
				<tr>
					<td class="border center">24</td>
					<td class="border col_amount">8,333.41</td>
					<td class="border col_amount">6,000.00</td>
					<td class="border col_amount">0.00</td>
					<td class="border col_amount">14,333.41</td>
				</tr>
				<tr>
					<th class="border center xl-text">TOTAL</th>
					<th class="border col_amount xl-text">200,000.00</th>
					<th class="border col_amount xl-text">144,000.00</th>
					<th class="border col_amount xl-text">0.00</th>
					<th class="border col_amount xl-text">344,000.00</th>
				</tr>			
			</tbody></table>
		</div>
	</div>
	<!-- </div>  -->
	<!-- /.container-fluid -->
