<?php
	$data = array(
		//Referece based (tbl_loan_fees)
		'cbu'=>2,
		'id_cbu_deficient'=>7,
		'id_loan_protection'=>11,
		'id_notarial_fee'=>3,
		'interest' => 12,
		'prime'=>13,

		//Loan Payments Variable (tbl_payent_type)
		'repayment_type_principal' => 24,
		'repayment_type_interest' => 25,
		'repayment_type_fees' => 26,
		'repayment_type_principal_previous' => 27,
		'repayment_type_interest_previous' => 28,
		'repayment_type_fees_previous' => 29,
		'default_cbu'=>30,
		
		'loan_surcharges' => 46,

		'school_name' => 'MAASIN KAWAYAN MULTI-PURPOSE COOPERATIVE',
		'coop_name' => 'MAASIN KAWAYAN MULTI-PURPOSE COOPERATIVE',
		'coop_abbr'=>env('APP_NAME'),
		'coop_district' => 'District of Maasin',
		'coop_address' => 'Thompson Street, Maasin, Iloilo',
		'fs_cda_reg_no'=> '9520-06000851',
		'fs_reg_date'=> 'October 20, 2009',
		'coop_email'=> 'lepsta1.cooperative@gmail.com',
		'coop_tin'=>'000230314',

		//Coop contact number
		'coop_contact'=>'(033) 330-8984/09981962336',
		'investment'=>56,
		'paid_interest_summary_account'=>"35",


		'default_bank'=>1,
		'default_bank_chart'=>77,


		// 'v_prepared_by'=>'NONALYN M. MANDATE',
		'v_prepared_by'=>'',

		'v_prepared_by_loan'=>'MIKKA CABIA AN',
		'v_prepared_by_exp'=>'JOSEPHINE S. MANERO',

		'v_checked_by'=>'JOSEPHINE S. MANERO',
		'v_approved_by'=>'JUAN M. RENTOY JR',
		'v_released_by'=>'AGNES V. AMORTE',
		'treasurer'=>'AGNES V. AMORTE',




		'loan_disbursement_prepared'=>'NONALYN M. MANDATE',
		'disbursement_prepapred'=>'AGNES V. AMORTE',

		
		//Loan Fees (previous payment and rebates)
		// 'previous_loan_payment' =>33,
		// 'previous_loan_rebates' => 34

		'check_on_hand_account'=>81
	);
	return $data


?>




