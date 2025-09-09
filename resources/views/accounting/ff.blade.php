@extends('adminLTE.admin_template')
@section('content')
<style type="text/css">
table.pretty {
    width: 100%;
}

table.pretty th, table.pretty td {
    border: 1px solid gainsboro;
    padding: 0.2em;
}

table.pretty caption {
    font-style: italic;
    font-weight: bold;
    margin-left: inherit;
    margin-right: inherit;
}

table.pretty thead tr th {
    border-bottom: 2px solid;
    font-weight: bold;
    text-align: center;
}

table.pretty thead tr th.empty {
    border: 0 none;
}

table.pretty tfoot tr th {
    border-bottom: 2px solid;
    border-top: 2px solid;
    font-weight: bold;
    text-align: center;
}

table.pretty tbody tr th {
    text-align: center;
}

table.pretty tbody tr td {
    border-top: 1px solid;
    text-align: center;
}

table.pretty tbody tr.odd td {
    background: none repeat scroll 0 0 #EBF4FB;
}

table.pretty tbody tr.even td {
    background: none repeat scroll 0 0 #BCEEEE;
}

table.pretty thead tr th.highlightcol {
    border-color: #2E6E9E #2E6E9E gainsboro;
    border-style: solid;
    border-width: 2px 2px 1px;
}

table.pretty tfoot tr th.highlightcol {
    border-left: 2px solid #2E6E9E;
    border-right: 2px solid #2E6E9E;
}

table.pretty thead tr th.lefthighlightcol, table.pretty tbody tr td.lefthighlightcol, table.pretty tfoot tr th.lefthighlightcol {
    border-left: 2px solid #2E6E9E;
}

table.pretty thead tr th.righthighlightcol, table.pretty tbody tr td.righthighlightcol, table.pretty tfoot tr th.righthighlightcol {
    border-right: 2px solid #2E6E9E;
}

table.pretty thead tr th.lefthighlightcolheader, table.pretty tbody tr td.lefthighlightcolheader, table.pretty tfoot tr th.lefthighlightcolheader {
    border-left: 2px solid #2E6E9E;
}

table.pretty thead tr th.righthighlightcolheader, table.pretty tbody tr td.righthighlightcolheader, table.pretty tfoot tr th.righthighlightcolheader {
    border-right: 2px solid #2E6E9E;
}

.control-label{
	font-size: 13px;
}
#gg{
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif !important;
}

.form-row  label{
	margin-bottom: unset !important;
	font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
	font-size: 13px;
}
</style>
<div id="gg">
<div class="table-responsive">
	<table class="pretty displayschedule" id="repaymentschedule" style="margin-top: 20px;white-space: nowrap;">
		<colgroup span="3"></colgroup>
		<colgroup span="3">
			<col class="lefthighlightcol">
			<col>
			<col class="righthighlightcol">
		</colgroup>
		<colgroup span="3">
			<col class="lefthighlightcol">
			<col>
			<col class="righthighlightcol">
		</colgroup>
		<colgroup span="3"></colgroup>
		<thead>
			<tr>
				<th class="empty" scope="colgroup" colspan="5">&nbsp;</th>
				<th class="highlightcol" scope="colgroup" colspan="3">Loan Amount and Balance</th>
				<th class="highlightcol" scope="colgroup" colspan="3">Total Cost of Loan</th>
				<th class="empty" scope="colgroup" colspan="1">&nbsp;</th>
			</tr>
			<tr>
				<th scope="col">#</th>
				<th scope="col">Date</th>
				<th scope="col"># Days</th>
				<th scope="col">Paid By</th>
				<th scope="col"></th>
				<th class="lefthighlightcolheader" scope="col">Disbursement</th>
				<th scope="col">Principal Due</th>
				<th class="righthighlightcolheader" scope="col">Principal Balance</th>

				<th class="lefthighlightcolheader" scope="col">Interest Due</th>
				<th scope="col">Fees</th>
				<th class="righthighlightcolheader" scope="col">Penalties

				</th>
				<th scope="col">Total Due</th>
				<th scope="col">Total Paid</th>
				<th scope="col">Total Outstanding</th>
			</tr>
		</thead>
		<tbody>

			<tr>
				<td scope="row"></td>
				<td>2021-12-06</td>
				<td></td>
				<td><span style="color: #eb2442;"></span></td>
				<td>&nbsp;</td>
				<td class="lefthighlightcolheader">60,000</td>
				<td></td>
				<td class="righthighlightcolheader">60,000</td>
				<td class="lefthighlightcolheader"></td>
				<td>0</td>
				<td class="righthighlightcolheader"></td>
				<td>0</td>
				<td>0</td>
				<td></td>
			</tr>
			<tr>
				<td scope="row">1</td>
				<td>2022-01-06</td>
				<td>31</td>
				<td>
				</td>
				<td>
				</td>
				<td class="lefthighlightcolheader"></td>
				<td>10,000</td>
				<td class="righthighlightcolheader">50,000</td>
				<td class="lefthighlightcolheader">
					1,000
				</td>
				<td>0</td>
				<td class="righthighlightcolheader">0</td>
				<td>11,000</td>
				<td>0</td>
				<td>11,000</td>
			</tr>
			<tr>
				<td scope="row">2</td>
				<td>2022-02-06</td>
				<td>30</td>
				<td>
				</td>
				<td>
				</td>
				<td class="lefthighlightcolheader"></td>
				<td>10,000</td>
				<td class="righthighlightcolheader">40,000</td>
				<td class="lefthighlightcolheader">
					1,000
				</td>
				<td>0</td>
				<td class="righthighlightcolheader">0</td>
				<td>11,000</td>
				<td>0</td>
				<td>11,000</td>
			</tr>
			<tr>
				<td scope="row">3</td>
				<td>2022-03-06</td>
				<td>27</td>
				<td>
				</td>
				<td>
				</td>
				<td class="lefthighlightcolheader"></td>
				<td>10,000</td>
				<td class="righthighlightcolheader">30,000</td>
				<td class="lefthighlightcolheader">
					1,000
				</td>
				<td>0</td>
				<td class="righthighlightcolheader">0</td>
				<td>11,000</td>
				<td>0</td>
				<td>11,000</td>
			</tr>
			<tr>
				<td scope="row">4</td>
				<td>2022-04-06</td>
				<td>30</td>
				<td>
				</td>
				<td>
				</td>
				<td class="lefthighlightcolheader"></td>
				<td>10,000</td>
				<td class="righthighlightcolheader">20,000</td>
				<td class="lefthighlightcolheader">
					1,000
				</td>
				<td>0</td>
				<td class="righthighlightcolheader">0</td>
				<td>11,000</td>
				<td>0</td>
				<td>11,000</td>
			</tr>
			<tr>
				<td scope="row">5</td>
				<td>2022-05-06</td>
				<td>29</td>
				<td>
				</td>
				<td>
				</td>
				<td class="lefthighlightcolheader"></td>
				<td>10,000</td>
				<td class="righthighlightcolheader">10,000</td>
				<td class="lefthighlightcolheader">
					1,000
				</td>
				<td>0</td>
				<td class="righthighlightcolheader">0</td>
				<td>11,000</td>
				<td>0</td>
				<td>11,000</td>
			</tr>
			<tr>
				<td scope="row">6</td>
				<td>2022-06-06</td>
				<td>30</td>
				<td>
				</td>
				<td>
				</td>
				<td class="lefthighlightcolheader"></td>
				<td>10,000</td>
				<td class="righthighlightcolheader">0</td>
				<td class="lefthighlightcolheader">
					1,000
				</td>
				<td>0</td>
				<td class="righthighlightcolheader">0</td>
				<td>11,000</td>
				<td>0</td>
				<td>11,000</td>
			</tr>
		</tbody>
		<tfoot class="ui-widget-header">
			<tr>
				<th colspan="2">Total</th>
				<th>177</th>
				<th></th>
				<th></th>
				<th class="lefthighlightcolheader">60,000</th>
				<th>60,000</th>
				<th class="righthighlightcolheader">&nbsp;</th>
				<th class="lefthighlightcolheader">6,000</th>
				<th>0</th>
				<th class="righthighlightcolheader">0</th>
				<th>66,000</th>
				<th>0</th>
				<th>66,000</th>
			</tr>
		</tfoot>
	</table>
</div>
	<div class="row p-0" style="margin-top:20px">
		<div class="col-sm-3 p-1">
			<div class="form-group row p-0 div_collection">
				<label for="txt_amount" class="col-sm-4 control-label col-form-label" style="text-align: left">Amount:&nbsp;</label>
				<div class="col-sm-8">
					<input type="text" name="" class="form-control col_amount" id="txt_amount" value="0.00">
				</div> 
			</div>		
			<div class="form-group row p-0 div_collection">
				<label for="txt_discount" class="col-sm-4 control-label col-form-label" style="text-align: left">Discount:&nbsp;</label>
				<div class="col-sm-8">
					<input type="text" name="" class="form-control col_amount" id="txt_discount" value="0.00">
				</div> 
			</div>		
		</div>
	</div>
	<div class="row p-0" style="margin-top:20px">
		<div class="col-sm-9 p-1">
			<div class="form-row">
							<div class="form-group col-md-6">
								<label for="txt_contact_person">Contact Person First Name</label>
								<input type="text" class="form-control" id="txt_contact_person_first" placeholder="First Name" value="John1">
							</div>
							<div class="form-group col-md-6">
								<label for="txt_contact_person_last">Contact Person Last Name</label>
								<input type="text" class="form-control" id="txt_contact_person_last" placeholder="Last Name" value="Doe1">
							</div>
						</div>
		</div>
	</div>
</div>
@endsection