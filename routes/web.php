<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
// Route::get('/login','LoginController@login');
Route::get('/test-or-print','RepaymentBulkController@TestORPrint');
Route::get('/accrued-post','LoanAccruedController@getAccruedLoans');

Route::get('/test_mail','LoanApprovalController@test_mail');

Route::get('/close_tab', ['uses' => 'SOAController@close_tab', 'as' => 'close_tab']);
Route::get('/repayment-structure','SyncLoan@restructureLoanPayment');

Route::get('/blk',function(){
	return view('blk');
});

Route::get('/',function(){
	return redirect('/login');
});

Route::get('/login_t/{type}',function($type){
	$view = ($type==1)?'login3':'login-card2';
	return view("adminLTE.$view");
});

Route::get('/sync-loan','SyncLoan@generateLoanTable');
Route::get('/sync-loan-repayment','SyncLoan@generateRepayment');
Route::get('/sync-loan/post','SyncLoan@post');
Route::Get('/sync-all','SyncLoan@SyncLoanBeg');

Route::get('/missing-or','Test@MissingOR');

Route::get('/registration/ndSaX4TnAToXdP3MOeXMfKkD5xNSqvD7y4qw3jflaOKKlm0cyNM7dvXzTAnXnKa5NYUTP3s9qSJYPKeGFO6ollEzxvEhGWc5OXYKPa7AcPXKZMjlcMSMC0v7','AccountRegistrationController@registration_form');
Route::get('/registration/search-member','AccountRegistrationController@search_member');
Route::get('/registration/parse-member','AccountRegistrationController@parseMember');
Route::post('/registration/post','AccountRegistrationController@post');
Route::get('/registration/set-password','AccountRegistrationController@SetPasswordView');
Route::post('PostPasswordRegister', ['uses' => 'AccountRegistrationController@PostPasswordRegister', 'as' => 'PostPasswordRegister']);

Route::get('/forgot-password','AccountRegistrationController@ForgotPasswordForm');
Route::post('/forgot-password/post_request','AccountRegistrationController@post_request');
Route::get('/forgot-password-form','AccountRegistrationController@ForgotPasswordInputs');
Route::post('PostPasswordReset', ['uses' => 'AccountRegistrationController@PostPasswordReset', 'as' => 'PostPasswordReset']);



Route::get('/t_print','ATMSwiperController@t');

Route::get('/rep/test-mail','RepaymentController@test_mail');
Route::get('login', ['uses' => 'LoginController@login', 'as' => 'login']);
Route::post('Postlogin', ['uses' => 'LoginController@postLogin', 'as' => 'postLogin']);
Route::group(['middleware' => 'session'], function(){


	Route::get('/sync-loan-index','SyncLoan@LoanSyncIndex');
	Route::get('/sync-loan/parse-member-loan','SyncLoan@ParseMemberLoan');

	Route::get('logout', ['uses' => 'LoginController@getLogout', 'as' => 'getLogout']);

	Route::post('/admin/post/user_settings','UserAdminController@post_settings');

	Route::get('/admin','DashboardController@dashboard');
	Route::get('/redirect/error','DashboardController@redirect_error');
	Route::get('index','LoginController@test_index');
	Route::get('/dashboard','LoginController@dashboard');


	Route::get('user/index','UserAdminController@index');
	Route::get('user/add','UserAdminController@add');
	Route::get('user/edit','UserAdminController@edit');
	Route::post('/admin/post_user','UserAdminController@post');
	Route::get('/profile','UserAdminController@profile');
	Route::post('/user/post_user_update','UserAdminController@post_user_update');
	Route::get('/user/get_member_details','UserAdminController@get_member_details');

	Route::get('/admin/menu_management','MenuManagementController@index');
	Route::post('/admin/menu_management/arrange','MenuManagementController@arrange');
	Route::post('/admin/post_menu','MenuManagementController@post_menu');
	Route::post('/admin/menu/delete','MenuManagementController@delete_menu');

	Route::get('/admin/privilege/index','PrivilegeAdminController@index');
	Route::get('/admin/privilege/add','PrivilegeAdminController@add');

	Route::post('/admin/privilege/post','PrivilegeAdminController@post');
	Route::get('/admin/privilege/edit','PrivilegeAdminController@edit');
	Route::post('/switch_privileges','PrivilegeAdminController@switch');

	//Charts of Account
	Route::get('/charts_of_account','ChartofAccountController@index');
	Route::post('/charts_of_account/post','ChartofAccountController@post');
	Route::get('/charts_of_account/load_chart','ChartofAccountController@view_chart');

	//Members
	Route::get('/member/create','MemberController@add_member');
	Route::get('/member/list','MemberController@index');
	Route::post('/member/post','MemberController@post');
	Route::get('/member/view/{id_member}','MemberController@view_member');
	Route::post('/member/post_status','MemberController@post_status');
	Route::get('/parse-member-cbu','MemberController@getMemberCBU');

	//Select Maintenance
	Route::get('/maintenance/{type}','SelectMaintenanceController@index');
	Route::post('/maintenance/post/{type}','SelectMaintenanceController@post');
	Route::get('/maintenance/view/{type}','SelectMaintenanceController@view');
	Route::post('/maintenance/delete/{type}','SelectMaintenanceController@delete');

	//Cash Receipt
	Route::get('/cash_receipt/add','CashReceiptController@add');
	Route::get('/search_member','CashReceiptController@search_member');
	Route::post('/cash_receipt/post','CashReceiptController@post');
	Route::get('/cash_receipt/view/{id_cash_receipt}','CashReceiptController@view');
	Route::get('/cash_receipt','CashReceiptController@index');
	Route::get('/cash_receipt/print','CashReceiptController@print');
	Route::post('/cash_receipt/cancel','CashReceiptController@cancel');
	Route::get('/cash_receipt_voucher/print/{id_cash_receipt_voucher}','CashReceiptController@printCRV');

	//Bank Transaction
	Route::get('/bank_transaction','BankTransactionController@index');
	Route::get('/bank_transaction/create','BankTransactionController@create');
	Route::post('/bank_transaction/post','BankTransactionController@post');
	Route::get('/bank_transaction/view/{id_bank_transaction}','BankTransactionController@view');
	Route::post('/bank_transaction/cancel','BankTransactionController@cancel');


	//Loan Service
	Route::get('/loan_service','LoanServiceController@index');
	Route::get('/loan_service/create','LoanServiceController@create');
	Route::get('/loan_service/post','LoanServiceController@post');
	Route::get('/loan_service/view/{id_loan_service}','LoanServiceController@view');
	Route::get('/parseChargesDetails','LoanServiceController@parseCharges');
	Route::get('/search_loan_service','LoanServiceController@search_loan_service');
	Route::get('/loan_service/parseTermsCondition','LoanServiceController@parseTermsCondition');
	


	//Charges

	Route::get('/charges','ChargesController@index');
	Route::get('/charges/create','ChargesController@create');
	Route::post('/charges/post','ChargesController@post');
	Route::get('/charges/view/{id_charges_group}','ChargesController@view');

	Route::get('/seach_charges','ChargesController@seach_charges');

	//Loan Application
	Route::get('/loan','LoanApplicationController@index');
	Route::get('/loan/active/{type}','LoanApplicationController@generate_active_loan');
	Route::get('/loan/validate/active','LoanApplicationController@validate_summary');
	Route::get('/loan/application/create','LoanApplicationController@create');
	Route::get('/loan/application/view/{loan_token}','LoanApplicationController@view_loan_application');
	Route::get('/search_comakers','LoanApplicationController@search_comaker');
	Route::get('/search_member/loan_application','LoanApplicationController@search_member');

	
	Route::get('/loan/calculate','LoanApplicationController@calculate');
	Route::get('/loan/table_show','LoanApplicationController@table_loan_frame');
	Route::get('/loan/get_loan_service_details','LoanApplicationController@get_loan_service_details');
	Route::post('/loan/post','LoanApplicationController@post');
	Route::post('/loan/application/cancel','LoanApplicationController@cancel_loan_application');

	

	Route::get('/loan/application/approval/{loan_token}','LoanApprovalController@view_approval');
	Route::post('/loan/application/loan_approval','LoanApprovalController@loan_approval');
	Route::get('/loan/repayment_transaction/{loan_token}','LoanApprovalController@repayment_transactions_frame');

	Route::get('/loan/print_application_waiver/{loan_token}','LoanApprovalController@print_application_waiver');

	Route::get('/loan/parseActiveLoans','LoanApplicationController@parseActiveLoans');
	Route::get('/loan/ParseLoanServiceAvail','LoanApplicationController@ParseLoanServiceAvail');


	//Loan RPT
	Route::get('/loan/released-rpt','LoanRPTController@index');
	Route::get('/loan/released-rpt/export','LoanRPTController@export');


	//Repayment
	Route::get('/repayment/create','RepaymentController@create');
	Route::get('/repayment/member/loan_dues','RepaymentController@get_loan_due');
	Route::get('/repayment/post','RepaymentController@post');
	Route::get('/repayment/view/{token}','RepaymentController@view');
	Route::get('/repayment','RepaymentController@index');
	Route::get('/repayment/repayment/get_due_dates','RepaymentController@due_dates');
	Route::get('/repayment/check_or','RepaymentController@check_or');
	Route::post('/repayment/post_or','RepaymentController@post_or');
	Route::get('/repayment/print_or/{id_repayment_transaction}','RepaymentController@print_repayment_or');
	Route::get('/repayment/summary/{transaction_date}/{transaction_type}','RepaymentController@repayment_summary');
	Route::post('/repayment/cancel','RepaymentController@cancel_repayment');

	Route::get('repayment/validate/summary','RepaymentController@validate_summary');


	Route::get('/test/post','RepaymentController@post');

	//Change
	Route::get('/change','ChangeController@index');
	Route::get('/change/create','ChangeController@create');
	Route::get('/change/{type}/{id_repayment_change}','ChangeController@view');
	Route::get("/change/parse_change",'ChangeController@parse_change');
	Route::post('/change/post','ChangeController@post');
	Route::post('/change/post/status','ChangeController@post_status');
	Route::get('/change/summary','ChangeController@ChangeSummary');
	Route::get('/change/summary/created/{date_start}/{date_end}/{cancel}','ChangeController@ChangeCreatedSummary');

	//Journal Voucher
	Route::get('/journal_voucher','JournalVoucherController@index');
	Route::get('/journal_voucher/create','JournalVoucherController@create');
	Route::get('/journal_voucher/view/{id_journal_voucher}','JournalVoucherController@view');
	Route::get('/journal_voucher/parse_address','JournalVoucherController@parse_address');
	Route::post('/journal_voucher/post','JournalVoucherController@post');
	Route::get('/journal_voucher/reversal/content','JournalVoucherController@reversal_content');
	Route::get('/journal_voucher/print/{id_journal_voucher}','JournalVoucherController@printJV');
	Route::post('/journal_voucher/cancel','JournalVoucherController@cancel');

	Route::get('/search_jv_reference','JournalVoucherController@search_jv_reference');


	//CDV
	Route::get('/cdv/{type}/create','CashDisbursementController@create');
	Route::post('/cdv/{type}/post','CashDisbursementController@post');
	Route::get('/cdv/{type}/view/{id_cash_disbursement}','CashDisbursementController@view');
	Route::get('/cdv/{type}','CashDisbursementController@index');
	Route::post('/cdv/cancel','CashDisbursementController@cancel');

	//Employee
	Route::get('/search_employee','EmployeeController@search_employee');
	Route::get('/employee','EmployeeController@index');
	Route::get('/employee/create','EmployeeController@create');
	Route::post('/employee/post','EmployeeController@post');
	Route::get('/employee/view/{id_employee}','EmployeeController@view');
	Route::get('/employee/sync_member','EmployeeController@sync_member');
	Route::get('/employee/get_details','EmployeeController@get_employee_details');

	//Supplier
	Route::get('/supplier','SupplierController@index');
	Route::get('/supplier/create','SupplierController@create');
	Route::post('/supplier/post','SupplierController@post');
	Route::get('/supplier/view/{id_supplier}','SupplierController@view');

	//Payroll
	Route::get('/payroll','PayrollController@index');
	Route::get('/payroll/create','PayrollController@create');
	Route::post('/payroll/post','PayrollController@post');
	Route::get('/payroll/view/{id_payroll}','PayrollController@view');
	Route::get('/payroll/master_list','PayrollController@master_list');
	Route::get('/payroll/excel_summary/{id_payroll}','PayrollController@PayrollSummaryExcel');
	Route::get('/payroll/print_summary/{id_payroll}','PayrollController@PrintPayrollSummary');
	Route::get('/payroll/print_payroll_payslip/{id_payroll}','PayrollController@print_payroll_payslip');
	Route::post('/payroll/post/cancel','PayrollController@post_cancel');
	Route::get('/payroll/check_status','PayrollController@check_status');



	//Asset
	// Route::get('/asset','AssetController@index');
	Route::get('/asset/asset_adjustment/add','AssetController@adjustment');
	Route::get('/asset/asset_purchase/add','AssetController@asset_purchase');
	Route::get('/asset/get_cdv','AssetController@get_cdv');
	Route::get('/asset/print_sticker/{id_asset}','AssetController@print_sticker');
	Route::get('/asset/parse/monthly_dep','AssetController@viewMonthlyDep');
	
	Route::get('/asset/parse/cdv_list','AssetController@parseCDVList');
	Route::get('/asset/parse/account_details','AssetController@parseChartDetails');
	Route::get('/asset/{type}/{id_asset}','AssetController@view_asset');
	Route::post('/asset/post/cancel','AssetController@post_cancel');
	Route::get('/asset','AssetController@asset_index');


	// ASSET MAINTENANCE
	Route::get('/asset_maintenance','AssetMaintenanceController@index');
	Route::post('/asset_maintenance/post','AssetMaintenanceController@post');

	//ASSET DISPOSAL
	Route::get('/asset_disposal','AssetDisposalController@index');
	Route::get('/asset_disposal/create','AssetDisposalController@create');
	Route::get('/asset/search_asset','AssetDisposalController@search_asset');
	Route::get('/asset_disposal/parseDetails','AssetDisposalController@parse_asset_details');
	Route::get('/asset_disposal/post','AssetDisposalController@post');
	Route::get('/asset_disposal/search_crv','AssetDisposalController@search_or');
	Route::get('/asset_disposal/crv/details','AssetDisposalController@parseCRVDetails');
	Route::get("/asset_disposal/view/{id_asset_disposal}",'AssetDisposalController@view');
	Route::post('/asset_disposal/post/cancel','AssetDisposalController@post_cancel');
	Route::get('/asset_disposal/refresh_asset_table','AssetDisposalController@refresh_table');
	
	// POST
	Route::get('/post/asset','AssetController@post');


	//General Ledger
	Route::get('/accounting/{type}','GeneralLedgerController@index');
	Route::get('/accounting/{type}/export','GeneralLedgerController@exportGL');

	// Route::get('/general_ledger/export','GeneralLedgerController@exportGL');

	Route::get('/cash_disbursement/print/{id_cash_disbursement}','CashDisbursementController@print');

	//ATM SWIPE
	Route::get('/atm_swipe','ATMSwiperController@index');
	Route::get('/atm_swipe/create','ATMSwiperController@create');
	Route::post('/atm_swipe/post','ATMSwiperController@post');
	Route::get('/atm_swipe/view/{id_atm_swipe}','ATMSwiperController@view');
	Route::post('/atm_swipe/cancel','ATMSwiperController@cancel');
	Route::get('/atm_swipe/entry/{id_asset}','ATMSwiperController@print_entry');
	Route::get('/atm_swipe/update_records','ATMSwiperController@update_records');
	Route::get('/atm_swipe/print_form/{id_atm_swipe}','ATMSwiperController@print_atm_swipe_form');
	Route::get('/atm_swipe/swipe_summary/{start_date}/{end_date}','ATMSwiperController@atm_swipe_summary');

	Route::get('/financial_statement/{types}','FinancialStatementController@index');
	Route::get('/financial_statement/{types}/export','FinancialStatementController@export');

	//INVESTMENT PRODUCT 
	Route::get('/investment_product','InvestmentProductController@index');
	Route::get('/investment_product/create','InvestmentProductController@create');
	Route::get('/investment_product/post','InvestmentProductController@post');
	Route::get('/investment_product/view/{id_investment_product}','InvestmentProductController@view');
	

	// INVESTMENT
	Route::get('/investment','InvestmentController@index');
	Route::get('/investment/create','InvestmentController@create');
	Route::get('/investment/compute','InvestmentController@compute');
	Route::get('/investment/compute_frame','InvestmentController@compute_frame');
	Route::get('/invest/parseTerms','InvestmentController@parseTerms');
	Route::get('/investment/check_or','InvestmentController@check_or');
	Route::post('/investment/post_or','InvestmentController@post_or');
	
	Route::get('/investment/edit/{id_investment}','InvestmentController@edit');
	Route::get('/investment/view/{id_investment}','InvestmentController@view');

	Route::get('/investment/post','InvestmentController@post');
	Route::get('/investment/update_status','InvestmentController@update_investment_status');
	Route::get('/investment/show-withdrawal/{id_investment}','InvestmentController@parseWithdrawalSummary');

	Route::post('/investment/post-close-request','InvestmentController@force_withdraw');
	Route::post('/investment/post/renewal','InvestmentController@RenewInvestment');


	// TERM CONDITION
	Route::get('/term-condition/create','TermConditionController@create');
	Route::get('/term-condition/post','TermConditionController@post');
	Route::get('/term-condition/view/{id}','TermConditionController@view');
	Route::get('/term-condition','TermConditionController@index');



	//Savings
	Route::get('/savings/test','SavingsController@test');

	// Route::get('/test',function(){
	// 	return view('test');
	// });
	Route::get('/test','RepaymentController@create');
	Route::get('/tat/test/{id_repayment_transaction}','RepaymentController@GenerateRepaymentCashReceiptData');

	Route::get('/cbu','CBUController@index');
	Route::get('/cbu/account-export','CBUController@CBUAccountExport');

	Route::get('/admin/test',function(){
		return view('cash_receipt.print_test');
	});

	Route::get('/hash_password/{input}','MemberController@hash');

	Route::get('/search_supplier','SupplierController@search_supplier');

	Route::get('/test','TestController@test');

	Route::get('/expand',function(){
		return view('expand');
	});

	Route::get('/cbu/report','CBUController@CBUReportController');
	Route::get('/cbu/report-export-excel','CBUController@CBUReportExportExcel');

	// Transaction History
	Route::get('/voucher_summary','TransactionSummaryController@index');
	Route::get('/voucher_summary/export','TransactionSummaryController@export');


	Route::get('/journal/report/{type}','JournalReportsController@index');
	Route::get('/journal/report/{type}/export','JournalReportsController@export');

	Route::get('/generate_payments','RepaymentController@generate_payments');


	Route::get('/depreciation/scheduler','DepreciationSchedulerController@generateJV');

	//PAID INTEREST
	Route::get('/summary/paid_interest','PaidInterestSummaryController@index');
	Route::get('/summary/paid_interest/export','PaidInterestSummaryController@export');
	Route::get('/summary/paid_interest/export-excel','PaidInterestSummaryController@export_excel');


	//CBU MONTHLY
	Route::get('/cbu/monthly','CBUController@CBUMonthlyIndex');
	Route::get('/cbu/monthly/export','CBUController@CBUMonthlyexport');
	Route::get('/cbu/monthly/export-excel','CBUController@CBUMonthlyexportExcel');

	//My Payments
	Route::get('/payments/{id_repayment_transaction}','MyPaymentsController@view');
	Route::get('/payments','MyPaymentsController@index');

	//My Dues
	Route::get('/my_dues','MyDuesController@index');

	// Cash flow
	// Route::get('/cash_flow','CashFlowController@index');
	Route::get('/cash_flow/export','CashFlowController@export');

	Route::get('/scheduler','EntrySchedulerController@index');

	// Changes in Equity
	Route::get('/changes_equity/export','ChangesEquityController@export');

	Route::get('/dashboard2','AdminDashboard2Controller@index');

	Route::get('/admin_dashboard/{type}','AdminDashboardController@index');
	Route::get('/admin_dashboard/top/parse','AdminDashboardController@parse_top');

	//CBU WITHDRAWAL
	Route::get('/cbu_withdraw','CBUWithdrawalController@index');
	Route::get('/cbu_withdraw/create','CBUWithdrawalController@create');
	Route::post('/cbu_withdraw/post','CBUWithdrawalController@post');
	Route::get('/cbu_withdraw/view/{id_cbu_withdrawal}','CBUWithdrawalController@view');
	Route::post('/cbu_withdraw/post_status','CBUWithdrawalController@post_status');
	Route::get('/cbu_withdraw/get_member_cbu','CBUWithdrawalController@get_member_cbu');
	Route::get('/cbu_withdraw/export/{date_start}/{date_end}','CBUWithdrawalController@export_cbu_withdrawal');

	//INVESTMENT WITHDRAWAL
	Route::get('/investment-withdrawal','InvestmentWithdrawalController@index');
	Route::get('/investment-withdrawal/create','InvestmentWithdrawalController@create');
	Route::get('/investment-withdrawal/post','InvestmentWithdrawalController@post');
	Route::get('/investment-withdrawal/view/{id_investment_withdrawal}','InvestmentWithdrawalController@view');
	Route::post('/investment-withdrawal/update_status','InvestmentWithdrawalController@post_status');
	Route::post('/investment-withdrawal/cancel-ind','InvestmentWithdrawalController@cancel_ind');
	Route::get('/investment-withdrawal/batch-summary/{id_batch}','InvestmentWithdrawalController@batch_summary');

	//BUDGET
	Route::get('/chart/budget','ChartBudgetController@index');
	Route::post('/chart/budget/post','ChartBudgetController@post');


	//Prime Report
	Route::get('/prime/report','PrimeController@PrimeReport');
	Route::get('/prime/index','PrimeController@index');
	Route::get('/prime/account-export','PrimeController@PrimeAccountExport');
	Route::get('/prime/report-export-excel','PrimeController@PrimeReportExportExcel');

	Route::get('/prime/monthly','PrimeController@PrimeMonthlyIndex');
	Route::get('/prime/monthly/export','PrimeController@PrimeMonthlyexport');
	Route::get('/prime/monthly/export-excel','PrimeController@PrimeMonthlyexportExcel');

	// Prime Withdraw
	Route::get('/prime_withdraw','PrimeWithdrawalController@index');
	Route::get('/prime_withdraw/create','PrimeWithdrawalController@create');
	Route::get('/prime_withdraw/post','PrimeWithdrawalController@post');
	Route::get('/prime_withdraw/get_member_prime','PrimeWithdrawalController@get_member_prime');
	Route::get('/prime_withdraw/view/{id_prime_withdrawal}','PrimeWithdrawalController@view');
	Route::get('/prime_withdraw/post_status','PrimeWithdrawalController@post_status');
	Route::get('/prime_withdraw/export/{date_start}/{date_end}','PrimeWithdrawalController@export_prime_withdrawal');
	Route::get('/prime_withdraw/export-batch/{id_batch}','PrimeWithdrawalController@batch_summary');

	Route::post('/prime_withdraw/individual/cancel','PrimeWithdrawalController@cancel_prime_individual');


	//Overdue notif
	Route::get('/overdue/show','DueNotificationController@show_overdue');
	Route::get('/overdue/insert','DueNotificationController@insert');
	Route::get('/overdue/push','DueNotificationController@PushMail');

	Route::get('/overdue/post/push-notif','DueNotificationController@PushNotif');
	Route::get('/overdue/test-dispatch','DueNotificationController@Dispatcher');
	Route::post('/overdue/post-cancel','DueNotificationController@cancel_notif');
	

	// SCHEDULER
	Route::get('/scheduler/test-post','SchedulerController@test_post');
	Route::get('/scheduler/execute-task','SchedulerController@execute_task');
	Route::post('/scheduler/post','SchedulerController@post_scheduler');
	Route::get('/scheduler/index','SchedulerController@index');
	Route::get('/scheduler/view-details','SchedulerController@view_details');
	Route::get('/scheduler/create','SchedulerController@create');
	Route::get('/scheduler/view/{id_scheduler}','SchedulerController@view');
	Route::get('/search_reference','SchedulerController@search_reference');


	//Manager Certification
	Route::get('/manager-certification/create','ManagerCertificationController@create');
	Route::post('/manager-certification/post','ManagerCertificationController@post');
	Route::get('/manager-certification/view/{id_manager_certification}','ManagerCertificationController@view');
	Route::get('/manager-certification/edit/{id_manager_certification}','ManagerCertificationController@edit');
	Route::get('/manager-certification','ManagerCertificationController@index');
	Route::get('/manager-certification/print/{id_manager_certification}','ManagerCertificationController@print');

	//Loan Payment Check
	Route::get('/repayment-check','RepaymentCheckController@index');
	Route::get('/repayment-check/create','RepaymentCheckController@create');
	Route::get('/repayment-check/post','RepaymentCheckController@post');
	Route::get('/repayment-check/edit/{id_repayment_check}','RepaymentCheckController@edit');
	Route::get('/repayment-check/view/{id_repayment_check}','RepaymentCheckController@view');
	Route::post('/repayment-check/post-status','RepaymentCheckController@post_status');


	//Bulk Loan Payment
	Route::get('/bulk-repayment','BulkRepaymentController@index');
	Route::get('/bulk-repayment/view/{id_repayment}','BulkRepaymentController@view');
	Route::get('/bulk-repayment/create','BulkRepaymentController@create');
	Route::get('/bulk-repayment/edit/{id_repayment}','BulkRepaymentController@edit');

	Route::post('/bulk-repayment/post','BulkRepaymentController@post');
	Route::get('/bulk-repayment/updateStatus','BulkRepaymentController@updateStatus');
	 

	//Loan Payment Statement
	Route::get('/repayment-statement','RepaymentStatementController@index');
	Route::get('/repayment-statement/create','RepaymentStatementController@create');
	Route::get('/repayment-statement/view/{id}','RepaymentStatementController@view');
	Route::get('/repayment-statement/edit/{id}','RepaymentStatementController@edit');

	Route::get('/repayment-statement/post','RepaymentStatementController@post');
	Route::get('/repayment-statement/post-amount','RepaymentStatementController@postAmount');
	Route::get('/repayment-statement/print/{type}/{id}','RepaymentStatementController@PrintStatement');
	Route::get('/repayment-statement/post_status','RepaymentStatementController@postStatus');


	//Loan Payment Bulk
	Route::get('/repayment-bulk','RepaymentBulkController@index');
	Route::get('/repayment-bulk/create','RepaymentBulkController@create');
	Route::get('/repayment-bulk/post','RepaymentBulkController@post');
	Route::get('/repayment-bulk/updateStatus','RepaymentBulkController@updateStatus');
	Route::get('/repayment-bulk/view/{id}','RepaymentBulkController@view');
	Route::get('/repayment-bulk/edit/{id}','RepaymentBulkController@edit');
	Route::get('/repayment-bulk/member-loan','RepaymentBulkController@ParseMemberLoans');
	Route::get('/repayment-bulk/parse-statement','RepaymentBulkController@ParseStatements');
	Route::get('/repayment-bulk/get-statements','RepaymentBulkController@getStatement');
	Route::get('/repayment-bulk/export/{id}','RepaymentBulkController@print');
	Route::get('/repayment-bulk/print-or/{id_repayment}','RepaymentBulkController@PrintRepaymentOR');


	Route::get('/repayment-report','RepaymentReportController@index');
	Route::get('/repayment-report/export/{type}','RepaymentReportController@export');


	//Check Deposit
	Route::get('/check-deposit/create','CheckDepositController@create');
	Route::get('/check-deposit/post','CheckDepositController@post');
	Route::get('/check-deposit/edit/{id_check_deposit}','CheckDepositController@edit');
	Route::get('/check-deposit/view/{id_check_deposit}','CheckDepositController@view');
	Route::get('/check-deposit','CheckDepositController@index');
	Route::get('/check-deposit/postStatus','CheckDepositController@postStatus');
	Route::get('/check-deposit/print/{id_check_deposit}','CheckDepositController@print');

	//Change Payable
	Route::get('/change-payable','ChangePayableController@index');
	Route::get('/change-payable/create','ChangePayableController@create');
	Route::get('/change-payable/parseRepayment','ChangePayableController@ParseRepaymentDetails');
	Route::get('/change-payable/post','ChangePayableController@post');
	Route::get('/change-payable/edit/{id_change_payable}','ChangePayableController@edit');
	Route::get('/change-payable/print/{id_change_payable}','ChangePayableController@print');
	Route::get('/change-payable/view/{id_change_payable}','ChangePayableController@view');
	Route::post('/change-payable/postStatus','ChangePayableController@postStatus');

	//Loan Deliquent
	Route::get('/loan-deliquent','LoanDeliquentController@index');
	Route::get('/loan-deliquent/export/{type}','LoanDeliquentController@export');

	//Loan Overdue
	Route::get('/loan-overdue','LoanOverdueController@index');
	Route::get('/loan-overdue/export/{type}','LoanOverdueController@export');


	Route::get('/treasurer-report','TreasurerReportController@index');
	Route::get('/treasurer-report/export/{type}','TreasurerReportController@export');

	//Surcharge Maintenance
	Route::get('/surcharge-maintenance','LoanAccruedController@SurchargeMaintenance');
	Route::get('/parse-surcharge','LoanAccruedController@parseSurcharge');

});


Route::get('/algorithm/6832','Test@algorithm_6832');
Route::get('/algorithm/alg','Test@request_number');

Route::get('/generate_loan_table','PushOldRecordController@generateLoanTable');

Route::get('/update_repayment_entry','PushOldRecordController@UpdateRepaymentEntry');
Route::get('/update_loan_entry','PushOldRecordController@UpdateLoanEntry');
Route::get('/update_asset','PushOldRecordController@updateAsset');


Route::get('/dummy/test','DummyDataController@test');



//SYNCING OF DATA

Route::get('/sync_member','PushOldRecordController@push_member');

Route::get('/sync/investment','SyncInvestmentController@sync');


Route::get('/generate-surcharge','LoanAccruedController@getOverDueLoans');
Route::get('/surcharge-maintenance/post','LoanAccruedController@postSurchargeTable');