<style type="text/css">
    .tbl_loan_req_show tr>th{
        padding: 5px;
        padding-left: 5px;
        padding-right: 5px;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 14px;
    }
    .tbl_loan_req_show tr>td{
        padding: 3px;
        font-family: "Roboto", "Arial", "Helvetica Neue", sans-serif;
        font-size: 14px;
    }

</style>
<div id="div_loan_application_details_display" style="display:none">
    <div class="col-md-12 p-0">
        <fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
            <legend class="w-auto p-1">Net Pays</legend>
            <div class="col-md-8">
                <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                    <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req_show" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="table_header_dblue">Period Start</th>
                                <th class="table_header_dblue">Period End</th>
                                <th class="table_header_dblue">Amount</th>
                            </tr>
                        </thead>
                        <tbody id="net_body_summary">

                        </tbody>
                    </table>    
                </div>  
            </div>
        </fieldset>
    </div>
    <div class="col-md-12 p-0" id="div_other_lendings">
        <fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
            <legend class="w-auto p-1">Other Lending</legend>
            <div class="col-md-8">
                <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                    <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req_show" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="table_header_dblue">Name</th>
                                <th class="table_header_dblue">Date Started</th>
                                <th class="table_header_dblue">Date of Maturity</th>
                                <th class="table_header_dblue">Amount of Loan</th>
                            </tr>
                        </thead>
                        <tbody id="other_lending_body_summary">

                        </tbody>
                    </table>    
                </div>  
            </div>
        </fieldset>
    </div>
    <div class="col-md-12 p-0">
        <fieldset class="border" style="padding-left: 10px;padding-right: 10px;">
            <legend class="w-auto p-1">Comaker(s)</legend>
            <div class="col-md-8">
                <div class="table-responsive" style="max-height: calc(100vh - 200px);overflow-y: auto;margin-top: 5px !important;overflow-x: auto">
                    <table class="table table-bordered table-stripped table-head-fixed tbl_loan_req_show" style="white-space: nowrap;">
                        <thead>
                            <tr>
                                <th class="table_header_dblue" width="15px">No.</th>
                                <th class="table_header_dblue">Name</th>
                            </tr>
                        </thead>
                        <tbody id="comaker_body_summary">

                        </tbody>
                    </table>    
                </div>  
            </div>
        </fieldset>
    </div>
</div>