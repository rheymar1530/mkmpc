<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\JVModel;

class DepreciationSchedulerController extends Controller
{

	public function generateJV(){
		return $this->populate_data('2022-10-31');

	}

	public function populate_data($depreciation_date){
		$dep_data = DB::select("SELECT ai.id_chart_account,ca.account_code,ca.description,SUM(depreciation_amount*getAssetQuantityAsOf(ai.asset_code,0,adm.depreciation_date)) as amount,adm.depreciation_date,getAssetQuantityAsOf(ai.asset_code,0,adm.depreciation_date) AS QTY FROM asset_depreciation_month as adm 
			LEFT JOIN asset_item as ai on ai.id_asset_item = adm.id_asset_item
			LEFT JOIN chart_account as ca on ca.id_chart_account = ai.id_chart_account
			LEFT JOIN asset on asset.id_asset = ai.id_asset
			where adm.depreciation_date <= ? and asset.status <> 10 and adm.id_journal_voucher =0
			GROUP BY ai.id_chart_account,depreciation_date
			ORDER BY depreciation_date,ai.id_chart_account;",[$depreciation_date]);


		$g = new GroupArrayController();

		$depreciations = $g->array_group_by($dep_data,['depreciation_date']);

		if(count($depreciations) > 0){
			foreach($depreciations as $dt=>$items){

				$dep_date = date_format(date_create($dt),"m/d/Y");
				$dep_date2 = strtoupper(date_format(date_create($dt),"F Y"));

				
				$total = 0;
				//get total
				foreach($items as $item){
					$total += $item->amount;
				}

				DB::table('journal_voucher')
				->insert([
					'jv_type'=>1,
					'date'=>$dt,
					'type'=>9,
					'payee_type'=>4,
					'payee'=>'SMESTCCO',
					'description' => "ASSET DEPRECIATION FOR THE MONTH OF $dep_date2",
					'id_branch' => 1,
					'total_amount'=>$total,
					'status'=>0

				]);

				$id_journal_voucher = DB::table('journal_voucher')->where('type',9)->max('id_journal_voucher');
				$select = array();
				foreach($items as $item){
					$amt = $item->amount;
					$id_ca = $item->id_chart_account;

					array_push($select,"SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,$amt as debit,0 as credit,'' as details,0 as reference,1 as ordering
						FROM chart_account as ca WHERE id_chart_account = if($id_ca=13,63,65)
						UNION ALL
						SELECT $id_journal_voucher as id_journal_voucher,ca.id_chart_account,ca.account_code,ca.description,0 as debit,$amt as credit,'' as detais,0 as reference,2 as ordering
						FROM chart_account as ca WHERE id_chart_account = if($id_ca=13,11,10)");

				}

				DB::select("	
					INSERT INTO journal_voucher_details (id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference)
					SELECT id_journal_voucher,id_chart_account,account_code,description,debit,credit,details,reference FROM(".implode(" UNION ALL ",$select).") as g ORDER BY ordering,g.id_chart_account");

					DB::select("UPDATE asset_depreciation_month as adm 
					LEFT JOIN asset on asset.id_asset = adm.id_asset
					SET adm.id_journal_voucher = ?
					where adm.depreciation_date <= ? and asset.status <> 10 and adm.id_journal_voucher =0;",[$id_journal_voucher,$dt]);

				JVModel::setIsCash($id_journal_voucher);
			}
		}
		return "SUCCESS";		
	}
}
