-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: maasin_aug
-- ------------------------------------------------------
-- Server version	8.0.36

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Dumping events for database 'maasin_aug'
--

--
-- Dumping routines for database 'maasin_aug'
--
/*!50003 DROP FUNCTION IF EXISTS `calculateChargeAmountPer` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `calculateChargeAmountPer`($amount decimal(10,2),$interest_rate decimal(10,2),$charge_percentage decimal(10,2),$fee_base INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    -- $fee_base value [1 = TOTAL PRINCIPAL, 2= TOTAL INTEREST, 3 = TOTAL PRINCIPAL + TOTAL INTEREST]
	IF $fee_base = 3
    THEN
		SET result = ($amount + ($amount * ($interest_rate/100))) * ($charge_percentage/100);
		
	ELSEIF $fee_base = 2
    THEN
		SET result = $amount * ($interest_rate/100) * ($charge_percentage/100);
	ELSE
		SET result = $amount * ($charge_percentage/100);
	END IF;
		
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `ChargeRange` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `ChargeRange`($id_charges INT,$principal_amount decimal(10,2),$default_amount decimal(10,2)) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    
	SELECT value INTO result FROM charges_range as cr
	WHERE cr.id_charges = $id_charges AND $principal_amount >= cr.minimum AND $principal_amount <= if(cr.maximum=0,9999999999999999,cr.maximum)
	ORDER BY id_charges_range DESC limit 1;	

  RETURN ifnull(result,$default_amount);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `FormatName` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `FormatName`($first_name varchar(100),$middle_name varchar(100),$last_name varchar(100),$suffix varchar(45)) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
	SELECT concat($last_name,if($suffix='','',concat(' ',$suffix,' ')),', ',$first_name,' ',if($middle_name <> '',UPPER(concat(LEFT($middle_name,1),'.')),'')) INTO result;
      
    return result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getAssetQuantity` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getAssetQuantity`($asset_code VARCHAR(45),$id_asset_disposal INT) RETURNS int
BEGIN
    DECLARE result  INT(11);
    set result = 0;
	
SELECT SUM(quantity) INTO result FROM (
    SELECT ifnull(quantity,0) as quantity
    FROM asset_item as ai
    LEFT JOIN asset a on a.id_asset = ai.id_asset
    WHERE ai.asset_code = $asset_code
    UNION ALL
    SELECT ifnull(quantity,0)*-1 as quantity
    FROM asset_disposal_item as ad
    LEFT JOIN asset_disposal a on a.id_asset_disposal = ad.id_asset_disposal
    WHERE ad.asset_code = $asset_code and a.status <> 10 and ad.id_asset_disposal <> $id_asset_disposal
) as t;
    
  
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getAssetQuantityAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getAssetQuantityAsOf`($asset_code VARCHAR(45),$id_asset_disposal INT,$disposal_date DATE) RETURNS int
BEGIN
    DECLARE result  INT(11);
    set result = 0;
	
SELECT SUM(quantity) INTO result FROM (
    SELECT ifnull(quantity,0) as quantity
    FROM asset_item as ai
    LEFT JOIN asset a on a.id_asset = ai.id_asset
    WHERE ai.asset_code = $asset_code
    UNION ALL
    SELECT ifnull(quantity,0)*-1 as quantity
    FROM asset_disposal_item as ad
    LEFT JOIN asset_disposal a on a.id_asset_disposal = ad.id_asset_disposal
    WHERE ad.asset_code = $asset_code and a.status <> 10 and ad.id_asset_disposal <> $id_asset_disposal AND a.date <= $disposal_date
) as t;
    
  
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getCashAdvanceBalance` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCashAdvanceBalance`($id_cash_disbursement int,$id_employee int,$id_payroll int) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;

			select (debit-getCashAdvanceLessTotal(cd.id_cash_disbursement,$id_payroll)) into result from cash_disbursement as cd
            LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
            LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
            WHERE id_employee = $id_employee and ca.is_cash_advance = 1 and cdd.id_cash_disbursement <> 0 and cd.id_cash_disbursement =$id_cash_disbursement;
    
  
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getCashAdvanceBalanceJV` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCashAdvanceBalanceJV`($id_journal_voucher int,$id_employee int,$id_payroll int) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;

			select (debit-getCashAdvanceLessTotalJV(jv.id_journal_voucher,$id_payroll)) into result from journal_voucher as jv
            LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
            LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
            WHERE id_employee = $id_employee and ca.is_cash_advance = 1 and jvd.id_journal_voucher <> 0 and jv.id_journal_voucher =$id_journal_voucher;
    
  
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getCashAdvanceLessTotal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCashAdvanceLessTotal`($id_cash_disbursement int,$id_payroll int ) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;
    
	SELECT SUM(deducted) INTO result FROM (
	SELECT ifnull(SUM(amount),0) as deducted FROM payroll_ca 
    LEFT JOIN payroll as p on p.id_payroll = payroll_ca.id_payroll
	where payroll_ca.id_cash_disbursement = $id_cash_disbursement
	AND payroll_ca.id_payroll <> $id_payroll AND p.status <> 10
	UNION ALL
	SELECT ifnull(SUM(jvd.credit),0) from journal_voucher as jv
	LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
	LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
	WHERE ca.is_cash_advance = 1 and id_cdv = $id_cash_disbursement) as kk;    
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getCashAdvanceLessTotalJV` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCashAdvanceLessTotalJV`($id_journal_voucher int,$id_payroll int ) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;
    
	SELECT SUM(deducted) INTO result FROM (
	SELECT ifnull(SUM(amount),0) as deducted FROM payroll_ca 
    LEFT JOIN payroll as p on p.id_payroll = payroll_ca.id_payroll
	where payroll_ca.id_journal_voucher = $id_journal_voucher
	AND payroll_ca.id_payroll <> $id_payroll AND p.status <> 10
	UNION ALL
	SELECT ifnull(SUM(jvd.credit),0) from journal_voucher as jv
	LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
	LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
	WHERE ca.is_cash_advance = 1 and id_journal_voucher_reference = $id_journal_voucher) as kk;    
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getCashAdvanceTotalLess` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCashAdvanceTotalLess`($id_cash_disbursement int,$id_payroll int ) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;
    
	SELECT SUM(deducted) INTO result FROM (
	SELECT ifnull(SUM(amount),0) as deducted FROM payroll_ca 
    LEFT JOIN payroll as p on p.id_payroll = payroll_ca.id_payroll
	where payroll_ca.id_cash_disbursement = $id_cash_disbursement
	AND payroll_ca.id_payroll <> $id_payroll AND p.status <> 10
	UNION ALL
	SELECT ifnull(SUM(jvd.credit),0) from journal_voucher as jv
	LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
	LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
	WHERE ca.is_cash_advance = 1 and id_cdv = $id_cash_disbursement) as kk;    
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getCashAdvanceTotalPaid` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getCashAdvanceTotalPaid`($id_cash_disbursement int,$id_payroll int ) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;
    
	SELECT SUM(deducted) INTO result FROM (
	SELECT ifnull(SUM(amount),0) as deducted FROM payroll_ca 
    LEFT JOIN payroll as p on p.id_payroll = payroll_ca.id_payroll
	where payroll_ca.id_cash_disbursement = $id_cash_disbursement
	AND payroll_ca.id_payroll <> $id_payroll AND p.status <> 10
	UNION ALL
	SELECT ifnull(SUM(jvd.credit),0) from journal_voucher as jv
	LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
	LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
	WHERE ca.is_cash_advance = 1 and id_cdv = $id_cash_disbursement) as kk;    
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `GETCHARGESGROUP` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `GETCHARGESGROUP`($id_charges_group int) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
/**
SELECT group_concat(charge_description separator ' !! ') INTO result FROM (
SELECT concat(fees.name,' - ',if(id_fee_calculation=1,concat(format(charges.value,2),'% of ',cb.description),format(value,2)),if(charges.is_deduct=1,' | Deducted to loan | ',if(non_deduct_option is null,'',if(non_deduct_option=1,' | Fixed Amount',' | Divided Equally'))),if(charges.application_fee_type=1,'',if(charges.application_fee_type=2,'First Loan','Renewal'))) as charge_description FROM charges
LEFT JOIN loan_fees as fees on fees.id_loan_fees = charges.id_loan_fees
LEFT JOIN calculated_fee_base as cb on cb.id_calculated_fee_base = charges.id_calculated_fee_base
WHERE charges.id_charges_group = $id_charges_group) as t;*/

SELECT group_concat(charge_description separator ' !! ') INTO result FROM (
SELECT concat(fees.name,' - ',
CASE WHEN id_fee_calculation=1
THEN if(charges.with_range =1,concat(MIN(cr.value),'% to ',MAX(cr.value),'%'),concat(format(charges.value,2),'% of ',cb.description))
ELSE if(charges.with_range =1,concat(FORMAT(MIN(cr.value),2),' to ',FORMAT(MAX(cr.value),2)), format(charges.value,2)) END
,if(charges.is_deduct=1,' | Deducted to loan | ',if(non_deduct_option is null,'',if(non_deduct_option=1,' | Fixed Amount',' | Divided Equally'))),if(charges.application_fee_type=1,'',if(charges.application_fee_type=2,'First Loan','Renewal'))) as charge_description FROM charges
LEFT JOIN loan_fees as fees on fees.id_loan_fees = charges.id_loan_fees
LEFT JOIN calculated_fee_base as cb on cb.id_calculated_fee_base = charges.id_calculated_fee_base
LEFT JOIN charges_range as cr on cr.id_charges = charges.id_charges
WHERE charges.id_charges_group = $id_charges_group
GROUP BY charges.id_charges) as t;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getDueRepaymentSummary` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getDueRepaymentSummary`($type_in int,$id_loan int,$transaction_date DATE,$due_date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
    IF $type_in = 1 /***PRINCIPAL**/ 
		THEN
        SELECT SUM(paid) INTO result FROM (
			SELECT lt.repayment_amount-SUM(if((rt.transaction_date < $transaction_date AND rt.status <> 10),paid_principal,0)) as paid FROM loan_table as lt
			LEFT JOIN repayment_loans as rl on rl.id_loan =lt.id_loan and lt.term_code = rl.term_code
			LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
			where lt.id_loan = $id_loan and lt.due_date <= $due_date
			GROUP BY lt.term_code) as t;
	ELSEIF $type_in = 2 /**INTEREST*/ 
		THEN
        SELECT SUM(paid) INTO result FROM (
			SELECT lt.interest_amount-SUM(if((rt.transaction_date < $transaction_date AND rt.status <> 10),paid_interest,0)) as paid FROM loan_table as lt
			LEFT JOIN repayment_loans as rl on rl.id_loan =lt.id_loan and lt.term_code = rl.term_code
			LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
			where lt.id_loan = $id_loan and lt.due_date <= $due_date
			GROUP BY lt.term_code) as t;
	ELSEIF $type_in = 3 /**FEES*/ 
		THEN
        SELECT SUM(paid) INTO result FROM (
			SELECT lt.fees-SUM(if((rt.transaction_date < $transaction_date AND rt.status <> 10),paid_fees,0)) as paid FROM loan_table as lt
			LEFT JOIN repayment_loans as rl on rl.id_loan =lt.id_loan and lt.term_code = rl.term_code
			LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
			where lt.id_loan = $id_loan and lt.due_date <= $due_date
			GROUP BY lt.term_code) as t;
	ELSEIF $type_in = 4 /**Surcharge*/ 
		THEN
        SELECT SUM(paid) INTO result FROM (
			SELECT lt.surcharge-SUM(if((rt.transaction_date < $transaction_date AND rt.status <> 10),paid_surcharge,0)) as paid FROM loan_table as lt
			LEFT JOIN repayment_loans as rl on rl.id_loan =lt.id_loan and lt.term_code = rl.term_code
			LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
			where lt.id_loan = $id_loan and lt.due_date <= $due_date
			GROUP BY lt.term_code) as t;
	END IF;
    
	
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getEmployeeCashAdvance` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getEmployeeCashAdvance`($id_employee int,$id_reference int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
select ifnull(SUM(debit-credit),0) INTO result from cash_disbursement as cd
LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
WHERE id_employee = $id_employee and ca.is_cash_advance = 1 and cdd.id_cash_disbursement <> $id_reference;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getFeesBalanceAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getFeesBalanceAsOf`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(int_fees),0) INTO result FROM (
	SELECT lt.fees-SUM(ifnull(rl.paid_fees,0)) as int_fees FROM loan_table as lt
	LEFT JOIN repayment_loans as rl on rl.term_code = lt.term_code AND lt.id_loan = rl.id_loan and rl.status <> 10
	WHERE lt.due_date <= LAST_DAY($date) and lt.id_loan = $id_loan and lt.is_paid <> 1
	GROUP BY lt.term_code) as k;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getInterestBalanceAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getInterestBalanceAsOf`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
  SELECT ifnull(SUM(int_balance),0) INTO result FROM (
  SELECT lt.interest_amount-SUM(ifnull(rl.paid_interest,0)) as int_balance FROM loan_table as lt
  LEFT JOIN repayment_loans as rl on rl.term_code = lt.term_code AND lt.id_loan = rl.id_loan and rl.status <> 10
  WHERE lt.due_date <= LAST_DAY($date) and lt.id_loan = $id_loan and lt.is_paid <> 1
  GROUP BY lt.term_code) as k;

 /*****and lt.is_paid <> 1*****/
  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getInterestPrincipalAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getInterestPrincipalAsOf`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(principal_balance),0) INTO result FROM (
	SELECT lt.repayment_amount-SUM(ifnull(rl.paid_principal,0)) as principal_balance FROM loan_table as lt
	LEFT JOIN repayment_loans as rl on rl.term_code = lt.term_code AND lt.id_loan = rl.id_loan and rl.status <> 10
	WHERE lt.due_date <= LAST_DAY($date) and lt.id_loan = $id_loan and lt.is_paid <> 1
	GROUP BY lt.term_code) as k;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getInvestmentTotalWithdraw` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getInvestmentTotalWithdraw`($id_investment int,$id_investment_withdrawal int,$confirmed_only int,$type int) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;
    
    SELECT 
    CASE WHEN $type=1 THEN SUM(principal+interest)
		 WHEN $type=2 THEN SUM(principal)
         WHEN $type=3 THEN SUM(interest) end
     INTO result FROM investment_withdrawal as iw
    WHERE iw.status <> 10 AND iw.id_investment = $id_investment AND iw.id_investment_withdrawal <> $id_investment_withdrawal
    AND if($confirmed_only=1,iw.status=1,1);


  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanBalance` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanBalance`($id_loan int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    

  WITH loan as (
    SELECT loan.id_loan,SUM(repayment_amount+interest_amount+fees+surcharge) as loan_amount
    FROM loan
    LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
    WHERE loan.id_loan = $id_loan
  )
  SELECT loan.loan_amount-ifnull(SUM(paid_principal+paid_interest+paid_fees+paid_surcharge),0) INTO result
  FROM loan
  LEFT JOIN repayment_loans as rl on rl.id_loan = loan.id_loan AND rl.status <> 10;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanBalanceAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanBalanceAsOf`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT getLoanOverallBalance($id_loan,1)+ifnull(SUM(int_balance),0)+ifnull(SUM(int_fees),0)+ifnull(SUM(int_surcharge),0) 
    INTO result FROM (
	SELECT 
	lt.interest_amount-SUM(ifnull(rl.paid_interest,0)) as int_balance,
    lt.fees-SUM(ifnull(rl.paid_fees,0)) as int_fees,
    lt.surcharge-SUM(ifnull(rl.paid_surcharge,0)) as int_surcharge
    FROM loan_table as lt
	LEFT JOIN repayment_loans as rl on rl.term_code = lt.term_code AND lt.id_loan = rl.id_loan and rl.status <> 10
	WHERE lt.due_date <= DATE_FORMAT($date,'%Y-%m-30') and lt.id_loan = $id_loan and lt.is_paid <> 1
	GROUP BY lt.term_code) as k;
     

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanOverallBalance` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanOverallBalance`($id_loan int,$type_ int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
    
    IF $type_ = 1
    THEN
	SELECT SUM(repayment_amount)-getLoanOverallPaid($id_loan,$type_) INTO result
	FROM loan_table as lt
	WHERE lt.id_loan = $id_loan;
	ELSEIF $type_ = 2
    THEN
	SELECT SUM(interest_amount)-getLoanOverallPaid($id_loan,$type_) INTO result
	FROM loan_table as lt
	WHERE lt.id_loan = $id_loan;
    ELSEIF $type_ = 3
    THEN
	SELECT SUM(fees)-getLoanOverallPaid($id_loan,$type_) INTO result
	FROM loan_table as lt
	WHERE lt.id_loan = $id_loan;    
    ELSE
	SELECT SUM(surcharge)-getLoanOverallPaid($id_loan,$type_) INTO result
	FROM loan_table as lt
	WHERE lt.id_loan = $id_loan;  
    END IF;

	RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanOverallPaid` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanOverallPaid`($id_loan int,$type_ int ) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;
    
    
    IF $type_ = 1
    THEN
    SELECT SUM(paid_principal) INTO result FROM repayment_transaction as rt
	LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
	WHERE rl.id_loan = $id_loan and rt.status <> 10;
	ELSEIF $type_ = 2
    THEN
	SELECT SUM(paid_interest) INTO result FROM repayment_transaction as rt
	LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
	WHERE rl.id_loan = $id_loan and rt.status <> 10;   
	ELSEIF $type_ = 3
    THEN
	SELECT SUM(paid_interest) INTO result FROM repayment_transaction as rt
	LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
	WHERE rl.id_loan = $id_loan and rt.status <> 10;   
    ELSE
 	SELECT SUM(paid_surcharge) INTO result FROM repayment_transaction as rt
	LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
	WHERE rl.id_loan = $id_loan and rt.status <> 10;      
    END IF;
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanRebates` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanRebates`($id_loan VARCHAR(11),$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result  DECIMAL(10,2);
    set result = 0;
    
   SELECT ROUND((loan_protection_amount/terms)*(terms-COUNT(*)),2) INTO result
	FROM loan 
	LEFT JOIN loan_table as lt on lt.id_loan  = loan.id_loan and lt.due_date <= $date
	where loan.id_loan = $id_loan
	GROUP BY loan.id_loan;
	

    
  
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanServiceInterest` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanServiceInterest`($id_loan_loan_service int,$start_date DATE,$end_date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;

SELECT SUM(paid_interest) INTO result 
FROM repayment_transaction as rt
LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction =rt.id_repayment_transaction
LEFT JOIN loan as l on l.id_loan = rl.id_loan
WHERE rt.status <> 10 and rt.transaction_date >= $start_date and rt.transaction_date <= $end_date and l.id_loan_service = $id_loan_loan_service;

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanServiceName` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanServiceName`($payment_type int,$loan_service TEXT,$terms INT) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
	
    IF $payment_type =2 OR $terms = 1
    THEN
    SET result = $loan_service;
    ELSE
    SET result = concat($loan_service,' | ',$terms,' MONTHS');
    END IF;
  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTermCode` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTermCode`($id_loan int) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
    
	SELECT term_code INTO result FROM loan_table
	WHERE id_loan = $id_loan and is_paid in (0,2)
	ORDER BY count LIMIT 1;
    


  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalDeductedInterest` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalDeductedInterest`($id_loan int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT value INTO result 
    FROM loan_charges where id_loan = $id_loan AND id_loan_fees=12;
     

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPayment` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPayment`($id_loan int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(paid_principal+paid_interest+paid_fees+paid_surcharge),0) INTO result FROM repayment_loans
    WHERE id_loan = $id_loan and status < 10;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPaymentAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPaymentAsOf`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(rl.paid_principal+rl.paid_interest+rl.paid_fees+rl.paid_surcharge),0) INTO result 
    FROM repayment_loans as rl
    LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
    WHERE rl.id_loan = $id_loan and rl.status < 10 AND rt.transaction_date <= $date;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPaymentAsOfWithDue` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPaymentAsOfWithDue`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
    
  SELECT ifnull(SUM(rl.paid_principal+rl.paid_interest+rl.paid_fees+rl.paid_surcharge),0) INTO result 
    FROM repayment_loans as rl
    LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
    LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND rl.term_code = lt.term_code
    WHERE rl.id_loan = $id_loan and rl.status < 10 AND rt.transaction_date <= $date AND lt.due_date <= $date;
    
  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPaymentLedger` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPaymentLedger`($type int,$id_loan int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
    IF $type= 1
    THEN
		SELECT ifnull(SUM(LoanPayinterest+LoanPayPrincipal),0) INTO result FROM dummy_loan_ledger as dll
        WHERE dll.id_parent_dummy_loan = $id_loan;
    ELSE
		SELECT ifnull(SUM(Amountpaid),0) INTO result FROM dummy_loan_ledger as dll
        WHERE dll.id_parent_dummy_loan = $id_loan;
    END IF;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPaymentMonth` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPaymentMonth`($id_loan int,$start_date date,$end_date date) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
SELECT ifnull(SUM(paid_principal+paid_interest+paid_fees+paid_surcharge),0) INTO result FROM repayment_transaction as rt
LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction =rt.id_repayment_transaction
LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
WHERE rt.transaction_date >= $start_date AND rt.transaction_date <= $end_date and rt.status <> 10 AND lt.due_date <= $end_date AND rl.id_loan = $id_loan
GROUP BY rl.id_loan
HAVING SUM(paid_principal+paid_interest+paid_fees+paid_surcharge) > 0;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPaymentNotBeg` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPaymentNotBeg`($id_loan int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
SELECT IFNULL(SUM(paid_principal+paid_interest+paid_fees+paid_surcharge),0) INTO result
FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
where rl.id_loan = $id_loan AND rt.status <> 10 AND (rt.id_cash_receipt_voucher+rt.id_journal_voucher) > 0;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPaymentType` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPaymentType`($id_loan int,$type INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(if($type=1,paid_principal,if($type=2,paid_interest,if($type=3,paid_fees,paid_surcharge)))),0) INTO result FROM repayment_loans
    WHERE id_loan = $id_loan and status < 10 ;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalPaymentTypeEx` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalPaymentTypeEx`($id_loan int,$type INT,$id_repayment_transaction INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(if($type=1,paid_principal,if($type=2,paid_interest,if($type=3,paid_fees,paid_surcharge)))),0) INTO result FROM repayment_loans
    WHERE id_loan = $id_loan and status < 10 and repayment_loans.id_repayment_transaction <> $id_repayment_transaction;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalTermPayment` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalTermPayment`($id_loan int,$term_code VARCHAR(45)) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(paid_principal+paid_interest+paid_fees+paid_surcharge),0) INTO result FROM repayment_loans
    WHERE id_loan = $id_loan and term_code = $term_code and status < 10;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalTermPaymentType` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalTermPaymentType`($id_loan int,$term_code VARCHAR(45),$type INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(if($type=1,paid_principal,if($type=2,paid_interest,if($type=3,paid_fees,paid_surcharge)))),0) INTO result FROM repayment_loans
    WHERE id_loan = $id_loan and term_code = $term_code and status < 10 ;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getLoanTotalTermPaymentTypeEx` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getLoanTotalTermPaymentTypeEx`($id_loan int,$term_code VARCHAR(45),$type INT,$id_repayment_transaction INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(if($type=1,paid_principal,if($type=2,paid_interest,if($type=3,paid_fees,paid_surcharge)))),0) INTO result FROM repayment_loans
    WHERE id_loan = $id_loan and term_code = $term_code and status < 10 and repayment_loans.id_repayment_transaction <> $id_repayment_transaction;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getMonth` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getMonth`($month_in int) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
    
	IF $month_in = 1
    THEN
		SET result = "January";
	ELSEIF $month_in = 2 
    THEN
		SET result = "February";
	ELSEIF $month_in = 3
    THEN
		SET result = "March";
	ELSEIF $month_in = 4 
    THEN
		SET result = "April";
	ELSEIF $month_in = 5 
    THEN
		SET result = "May";
	ELSEIF $month_in = 6 
    THEN
		SET result = "June";
	ELSEIF $month_in = 7 
    THEN
		SET result = "July";
	ELSEIF $month_in = 8
    THEN
		SET result = "August";
	ELSEIF $month_in = 9 
    THEN
		SET result = "September";
	ELSEIF $month_in = 10 
    THEN
		SET result = "October";
	ELSEIF $month_in = 11 
    THEN
		SET result = "November";
	ELSEIF $month_in = 12
    THEN
		SET result = "December";
	ELSE
		SET result = null;
	END IF;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getMonthLapsed` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getMonthLapsed`($id_loan int,$date DATE) RETURNS int
BEGIN
    DECLARE result INT(11);
    
	SELECT COUNT(*) INTO result
    FROM loan_table
    WHERE id_loan = $id_loan AND due_date <= $date;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getPOPRICEExpiry` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getPOPRICEExpiry`($id_charges_group int) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
    
    SELECT group_concat(charge_description separator '\n') INTO result FROM (
SELECT concat('* ',fees.name,' - ',if(id_fee_calculation=1,concat(format(charges.value,0),'% of ',cb.description),format(value,2)),if(charges.is_deduct=1,' - Deducted to loan (',' ('),if(charges.application_fee_type=1,'First Loan','Renewal'),')') as charge_description FROM charges
LEFT JOIN loan_fees as fees on fees.id_loan_fees = charges.id_loan_fees
LEFT JOIN calculated_fee_base as cb on cb.id_calculated_fee_base = charges.id_calculated_fee_base
WHERE charges.id_charges_group = $id_charges_group) as t;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getPrevLoanID` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getPrevLoanID`($id_loan int,$id_member int) RETURNS int
BEGIN
    DECLARE result INT;
    set result = 0;
    
    SELECT id_loan INTO result FROM loan 
    where loan_status = 1 and id_member = $id_member and id_loan <> $id_loan;
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getPrincipalBalanceAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getPrincipalBalanceAsOf`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
  SELECT ifnull(SUM(principal_balance),0) INTO result FROM (
  SELECT lt.repayment_amount-SUM(ifnull(rl.paid_principal,0)) as principal_balance FROM loan_table as lt
  LEFT JOIN repayment_loans as rl on rl.term_code = lt.term_code AND lt.id_loan = rl.id_loan and rl.status <> 10
  WHERE lt.due_date <= LAST_DAY($date) and lt.id_loan = $id_loan and lt.is_paid <> 1
  GROUP BY lt.term_code) as k;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getRepaymentBalanceDueDate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getRepaymentBalanceDueDate`($id_loan int,$due_date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(balance),0) INTO result FROM (
	SELECT due_date,total_due-SUM(paid_principal+paid_interest+paid_fees+paid_surcharge) as balance
	FROM loan_table as lt 
	LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND rl.term_code = lt.term_code and rl.status < 10
	WHERE lt.id_loan = $id_loan AND lt.due_date <= $due_date 
	GROUP BY lt.term_code) as balance_table;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getRepaymentBulkChange` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getRepaymentBulkChange`($id_repayment int,$id_change_payable int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
    SELECT ifnull(SUM(cpd.amount),0) INTO result FROM change_payable as cp
    LEFT JOIN change_payable_details as cpd on cpd.id_change_payable = cp.id_change_payable
    WHERE  cp.id_repayment = $id_repayment AND cp.id_change_payable <> $id_change_payable AND cp.status <> 10;
    

  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getRepaymentNumPaymentDueDate` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getRepaymentNumPaymentDueDate`($id_loan int) RETURNS int
BEGIN
    DECLARE result INT;
    set result = 0;
    
	SELECT count(*) INTO result FROM loan_table where is_paid = 1 AND id_loan = $id_loan;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getRepaymentStatementPaid` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getRepaymentStatementPaid`($id_repayment int,$id_loan int,$id_repayment_statement int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
  SELECT ifnull(SUM(paid_principal+paid_interest+paid_fees+paid_surcharge),0) INTO result
  FROM repayment_loans as rl
  LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
  WHERE rl.id_loan = $id_loan AND rt.id_repayment = $id_repayment AND rt.id_repayment_statement = $id_repayment_statement
  GROUP BY rl.id_loan;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getRepaymentTransactionIDLoans` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getRepaymentTransactionIDLoans`($id_repayment_transacation int) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
    set result = '';
    
	SELECT group_concat(id_loan) INTO result FROM (
	SELECT distinct id_loan FROM repayment_loans where id_repayment_transaction = $id_repayment_transacation AND (paid_principal+paid_interest+paid_fees) > 0) as t;
    

  RETURN ifnull(result,'');
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getSatementLoanPayment` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getSatementLoanPayment`($id_repayment_statement int,$id_repayment int,$id_loan int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
  SELECT SUM(paid_principal+paid_interest+paid_fees+paid_surcharge) INTO result
    FROM repayment_transaction as rt
  LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
  WHERE rt.id_repayment_statement = $id_repayment_statement AND rt.status <> 10 AND rt.id_repayment <> $id_repayment
    AND rl.id_loan = $id_loan;
    
  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getSatementPayment` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getSatementPayment`($id_repayment_statement int,$id_repayment int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
  SELECT SUM(paid_principal+paid_interest+paid_fees+paid_surcharge) INTO result
    FROM repayment_transaction as rt
  LEFT JOIN repayment_loans as rl on rl.id_repayment_transaction = rt.id_repayment_transaction
  WHERE rt.id_repayment_statement = $id_repayment_statement AND rt.status <> 10 AND rt.id_repayment <> $id_repayment;
    
  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getStatementPayment` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getStatementPayment`($id_repayment_statement int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT SUM(total_loan_payment) INTO result FROM repayment_transaction
	WHERE id_repayment_statement = $id_repayment_statement AND status <> 10;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getSurchargeBalanceAsOf` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getSurchargeBalanceAsOf`($id_loan int,$date DATE) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(int_fees),0) INTO result FROM (
	SELECT lt.surcharge-SUM(ifnull(rl.paid_surcharge,0)) as int_fees FROM loan_table as lt
	LEFT JOIN repayment_loans as rl on rl.term_code = lt.term_code AND lt.id_loan = rl.id_loan and rl.status <> 10
	WHERE lt.due_date <= LAST_DAY($date) and lt.id_loan = $id_loan and lt.is_paid <> 1
	GROUP BY lt.term_code) as k;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getTotalAmortization` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getTotalAmortization`($id_loan int,$date DATE) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
    
    SELECT ROUND(repayment_amount+interest_amount,0) INTO result
    FROM loan_table
    WHERE id_loan = $id_loan AND due_date <= $date AND accrued = 0
    LIMIT 1;
    
    RETURN if(FLOOR(result) =result,result,FLOOR(result)+1);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getTotalChange` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getTotalChange`($id_repayment_transaction int,$id_repayment_change int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(amount),0) INTO result FROM repayment_change
    WHERE id_repayment_transaction = $id_repayment_transaction and id_repayment_change <> $id_repayment_change
    AND status <> 10;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getTotalDueAsOfEx` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getTotalDueAsOfEx`($id_loan int,$date DATE,$id_repayment_transaction INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT ifnull(SUM(amount),0) INTO result FROM (
	SELECT lt.id_loan,lt.term_code,lt.repayment_amount + lt.interest_amount+lt.fees+lt.surcharge-SUM(ifnull(rl.paid_principal+rl.paid_interest+rl.paid_fees+rl.paid_surcharge,0)) as amount FROM loan_table as lt
	LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND rl.term_code = lt.term_code AND rl.status <> 10 AND rl.id_repayment_transaction <> $id_repayment_transaction
	WHERE lt.id_loan = $id_loan and lt.due_date <= $date
	GROUP BY lt.term_code) as g;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getTotalDueAsOfRepaymentEx` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getTotalDueAsOfRepaymentEx`($id_loan int,$date DATE,$id_repayment INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
 /** SELECT ifnull(SUM(amount),0) INTO result  FROM (
  SELECT lt.id_loan,lt.term_code,lt.repayment_amount + lt.interest_amount+lt.fees+lt.surcharge-SUM(ifnull(rl.paid_principal+rl.paid_interest+rl.paid_fees+rl.paid_surcharge,0)) as amount FROM loan_table as lt
  LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan AND rl.term_code = lt.term_code AND rl.status <> 10
    LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction =rl.id_repayment_transaction AND (rt.id_repayment <> $id_repayment  OR rt.id_repayment is null)
  WHERE lt.id_loan = $id_loan and lt.due_date <= $date
  GROUP BY lt.term_code) as g;**/

WITH loan_total as (
	SELECT SUM(repayment_amount+interest_amount+fees+surcharge) as loan_amount,id_loan FROM loan_table
	WHERE  id_loan =$id_loan AND due_date <= $date),
payments as (
	SELECT rl.id_loan,SUM(paid_principal+paid_fees+paid_interest+paid_surcharge) as payment
    FROM repayment_loans as rl
    LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
    LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
    WHERE rl.id_loan = $id_loan AND rt.status <> 10 AND lt.due_date <= $date  AND NOT (rt.id_repayment <=> $id_repayment)
)
SELECT loan_amount-ifnull(payment,0) INTO result FROM loan_total
LEFT JOIN payments as p on p.id_loan = loan_total.id_loan;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getTotalDueTypeRepaymentX` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getTotalDueTypeRepaymentX`($id_loan int,$date DATE,$id_repayment INT,$type_ INT) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
/*
SELECT 
SUM(CASE WHEN $type_ = 1 THEN paid_principal
WHEN $type_ = 2 THEN paid_interest
WHEN $type_ = 3 THEN paid_fees
WHEN $type_ = 4 THEN paid_surcharge
END) INTO result
FROM repayment_loans as rl
LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rl.id_loan = $id_loan AND rt.status <> 10 AND lt.due_date <= $date AND NOT (rt.id_repayment <=> $id_repayment);
*/
WITH loan_total as (
	SELECT 
	SUM(CASE WHEN $type_ = 1 THEN repayment_amount
	WHEN $type_ = 2 THEN interest_amount
	WHEN $type_ = 3 THEN fees
	WHEN $type_ = 4 THEN surcharge
    END) as loan_amount
    ,id_loan FROM loan_table
	WHERE  id_loan =$id_loan AND due_date <= $date),
payments as (
SELECT 
	SUM(CASE WHEN $type_ = 1 THEN paid_principal
	WHEN $type_ = 2 THEN paid_interest
	WHEN $type_ = 3 THEN paid_fees
	WHEN $type_ = 4 THEN paid_surcharge
	END) as payment,rl.id_loan
	FROM repayment_loans as rl
	LEFT JOIN loan_table as lt on lt.id_loan = rl.id_loan AND lt.term_code = rl.term_code
	LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
	WHERE rl.id_loan = $id_loan AND rt.status <> 10 AND lt.due_date <= $date AND NOT (rt.id_repayment <=> $id_repayment)
)
SELECT loan_amount-ifnull(payment,0) INTO result FROM loan_total
LEFT JOIN payments as p on p.id_loan = loan_total.id_loan;

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `getTotalLoanOffset` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `getTotalLoanOffset`($id_loan int) RETURNS decimal(10,2)
BEGIN
    DECLARE result DECIMAL(10,2);
    set result = 0;
    
	SELECT SUM(amount-rebates) INTO result
    FROM loan_offset
    WHERE id_loan = $id_loan;
  RETURN ifnull(result,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `isCash` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `isCash`($id_journal_voucher INT(11)) RETURNS int
BEGIN
    DECLARE result  INT(11);
    set result = 0;
	
	SELECT COUNT(*) INTO result FROM journal_voucher_details as jvd
	LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account 
	WHERE id_chart_account_category <= 2 and jvd.id_journal_voucher = $id_journal_voucher;

  RETURN if(result > 0,1,0);
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `loanStatus` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `loanStatus`($status_in int) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
    
	IF $status_in = 0
    THEN
		SET result = "Submitted";
	ELSEIF $status_in = 1 
    THEN
		SET result = "Processing";
	ELSEIF $status_in = 2
    THEN
		SET result = "Approved";
	ELSEIF $status_in = 3
    THEN
		SET result = "Active";
	ELSEIF $status_in = 4 
    THEN
		SET result = "Cancelled";
	ELSEIF $status_in = 5 
    THEN
		SET result = "Disapproved";
	ELSEIF $status_in = 6 
    THEN
		SET result = "Closed";
	ELSE
		SET result = null;
	END IF;
    

  RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `menu_child` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `menu_child`(`id_parent` INT) RETURNS int
BEGIN
declare result int;
SET result = 0;
SET result = (SELECT COUNT(*) from cms_menus WHERE parent_id = id_parent);
RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `RepaymentDescription` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `RepaymentDescription`($payment_for INT,$id_repayment INT) RETURNS text CHARSET utf8mb4
BEGIN
    DECLARE result TEXT;
    IF $payment_for = 1
    THEN        
    SELECT if(count=4,'Payment by multiple members',description) INTO result FROM (
    SELECT concat('',group_concat(member_name SEPARATOR '  ')) as description,COUNT(*) as count FROM (
    SELECT DISTINCT FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as member_name FROM repayment_transaction as rt
    LEFT JOIN member as m on m.id_member = rt.id_member
    WHERE rt.id_repayment=$id_repayment
    GROUP BY rt.id_member) as k) as m;
    ELSE
    SELECT if(count =4,'Statement for multiple Barangays/LGU',description) INTO result FROM (
    SELECT concat('',GROUP_CONCAT(concat(brgy,' [',DATE_FORMAT(s.date,'%m-%Y'),' | <a href=/repayment-statement/view/',id_repayment_statement,' target=_blank>',id_repayment_statement,'</a>',']') SEPARATOR ', ')) as description,COUNT(*) as count FROM (
    SELECT DISTINCT concat(if(bl.type=1,'Brgy. ','LGU - '),bl.name) as brgy,rs.date,rs.id_repayment_statement FROM repayment_transaction as rt
    LEFT JOIN repayment_statement as rs on rs.id_repayment_statement = rt.id_repayment_statement
    LEFT JOIN baranggay_lgu as bl on bl.id_baranggay_lgu = rs.id_baranggay_lgu
    WHERE rt.id_repayment = $id_repayment) as s) as k;
    END IF;
    return result;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `view_child` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` FUNCTION `view_child`(`id_parent` INT,`id_priv` INT) RETURNS int
BEGIN
declare result int;
SET result = 0;
SET result = (SELECT COUNT(*) FROM credentials c 
			  LEFT JOIN cms_menus on cms_menus.id = c.id_menu
			  where c.id_cms_privileges = id_priv AND parent_id = id_parent
			  AND is_view = 1);
RETURN result;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ActiveLoanSummary` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActiveLoanSummary`($date DATE,$type INT)
BEGIN
/***
SELECT *,loan_amount-total_amount_paid as loan_balance,concat(id_member,' || ',bor_name) as borrower_name  FROM (
SELECT loan.id_loan,m.id_member,concat(m.first_name,' ',m.last_name,' ',m.suffix) as bor_name,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as 'loan_service_name',loan.principal_amount,loan.interest_rate,
loan.terms,period.description as 'term_desc',loan.not_deducted_charges as 'loan_fees',loan.loan_amount,
getLoanTotalPaymentType(loan.id_loan,1) as total_pr_paid,getLoanTotalPaymentType(loan.id_loan,2) as total_in_paid,getLoanTotalPaymentType(loan.id_loan,3) as total_fee_paid,
getLoanTotalPayment(loan.id_loan) as total_amount_paid
FROM loan
LEFT JOIN member as m on m.id_member = loan.id_member
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
LEFT JOIN period on period.id_period = ls.id_term_period
WHERE loan.loan_status = 1
GROUP BY loan.id_loan
ORDER by bor_name,id_loan) as loan_table;**/
 
SELECT *,principal_amount-total_pr_paid as principal_balance,concat(id_member,' || ',bor_name) as borrower_name,@surcharge:=getSurchargeBalanceAsOf(id_loan,$date) as penalties,(principal_amount-total_pr_paid+unpaid_interest+@surcharge) as total_receivables  FROM (
SELECT ls.name as ls,loan.terms,m.last_name,loan.id_loan,m.id_member,concat(m.last_name,if(m.suffix='','',concat(' ',m.suffix,' ')),', ',m.first_name,' ',if(m.middle_name <> '',UPPER(concat(LEFT(m.middle_name,1),'.')),'')) as bor_name,concat('ID# ',loan.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_service_name',loan.interest_rate,
(lt.total_due) as schedulued_am,loan.principal_amount,
getLoanTotalPaymentType(loan.id_loan,1) as total_pr_paid,if(loan.deduct_interest=1,getLoanTotalDeductedInterest(loan.id_loan),getLoanTotalPaymentType(loan.id_loan,2)) as total_in_paid,getInterestBalanceAsOf(loan.id_loan,$date) as unpaid_interest,
loan.not_deducted_charges as 'loan_fees',loan.loan_amount,getLoanTotalPaymentType(loan.id_loan,3) as total_fee_paid,lt.repayment_amount as principal_amt,lt.interest_amount as interest_amt,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as date_granted
FROM loan
LEFT JOIN member as m on m.id_member = loan.id_member
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
LEFT JOIN period on period.id_period = ls.id_term_period
WHERE loan.loan_status = 1
GROUP BY loan.id_loan
ORDER by if($type=1,bor_name,ls),id_loan) as loan_table;


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ActiveLoanSummary2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `ActiveLoanSummary2`($type INT)
BEGIN
/***
SELECT *,loan_amount-total_amount_paid as loan_balance,concat(id_member,' || ',bor_name) as borrower_name  FROM (
SELECT loan.id_loan,m.id_member,concat(m.first_name,' ',m.last_name,' ',m.suffix) as bor_name,getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms) as 'loan_service_name',loan.principal_amount,loan.interest_rate,
loan.terms,period.description as 'term_desc',loan.not_deducted_charges as 'loan_fees',loan.loan_amount,
getLoanTotalPaymentType(loan.id_loan,1) as total_pr_paid,getLoanTotalPaymentType(loan.id_loan,2) as total_in_paid,getLoanTotalPaymentType(loan.id_loan,3) as total_fee_paid,
getLoanTotalPayment(loan.id_loan) as total_amount_paid
FROM loan
LEFT JOIN member as m on m.id_member = loan.id_member
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
LEFT JOIN period on period.id_period = ls.id_term_period
WHERE loan.loan_status = 1
GROUP BY loan.id_loan
ORDER by bor_name,id_loan) as loan_table;**/
 
SELECT *,principal_amount-total_pr_paid as principal_balance,concat(id_member,' || ',bor_name) as borrower_name,0 as penalties,(principal_amount-total_pr_paid+unpaid_interest) as total_receivables  FROM (
SELECT ls.name as ls,loan.terms,m.last_name,loan.id_loan,m.id_member,concat(m.last_name,if(m.suffix='','',concat(' ',m.suffix,' ')),', ',m.first_name,' ',if(m.middle_name <> '',UPPER(concat(LEFT(m.middle_name,1),'.')),'')) as bor_name,concat('ID# ',loan.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms)) as 'loan_service_name',loan.interest_rate,
(lt.total_due) as schedulued_am,loan.principal_amount,
getLoanTotalPaymentType(loan.id_loan,1) as total_pr_paid,if(loan.deduct_interest=1,getLoanTotalDeductedInterest(loan.id_loan),getLoanTotalPaymentType(loan.id_loan,2)) as total_in_paid,getInterestBalanceAsOf(loan.id_loan,loan.maturity_date) as unpaid_interest,
loan.not_deducted_charges as 'loan_fees',loan.loan_amount,getLoanTotalPaymentType(loan.id_loan,3) as total_fee_paid,lt.repayment_amount as principal_amt,lt.interest_amount as interest_amt,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as date_granted
FROM loan
LEFT JOIN member as m on m.id_member = loan.id_member
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
LEFT JOIN period on period.id_period = ls.id_term_period
WHERE loan.loan_status = 1
GROUP BY loan.id_loan
ORDER by if($type=1,bor_name,ls),id_loan) as loan_table;


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `getMemberCBU` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMemberCBU`($id_member INT)
BEGIN
SELECT ifnull(SUM(amount),0) as amount FROM (
SELECT ifnull(amount,0) as amount
FROM cash_receipt_details as cd
LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
WHERE id_payment_type = 3 AND status <> 10 AND id_member = $id_member
UNION ALL
SELECT ifnull(lc.calculated_charge,0) as amount from loan_charges as lc
LEFT JOIN loan on loan.id_loan = lc.id_loan
WHERE id_member = $id_member and id_loan_fees in (2,7) and (loan.status in (6) or loan.loan_status in (1))) as k;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `getMemberCBULedger` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMemberCBULedger`($id_member INT)
BEGIN
SELECT * FROM (
SELECT c.date_received as act_date,DATE_FORMAT(c.date_received,'%m/%d/%Y') as transaction_date,pt.description as description,concat("Cash Receipt #",cd.id_Cash_Receipt) as reference,ifnull(amount,0) as amount
FROM cash_receipt_details as cd
LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10 AND id_member = $id_member
UNION ALL
SELECT loan.date_released as act_date,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as transaction_date,ifnull(pt.alt_description,pt.description) as description,concat("Loan ID #",loan.id_loan) as reference,ifnull(lc.calculated_charge,0) as amount from loan_charges as lc
LEFT JOIN loan on loan.id_loan = lc.id_loan
LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
WHERE id_member = $id_member and pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2
UNION ALL
SELECT rt.transaction_date as act_date,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,ifnull(pt.alt_description,pt.description) as description,concat("Repayment ID #",rt.id_repayment_transaction) as reference,ifnull(rf.amount,0) as amount FROM repayment_fees as rf
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
WHERE id_member = $id_member AND pt.is_cbu = 1 and pt.type =3 and rt.status < 10
UNION ALL
SELECT date,DATE_FORMAT(date,'%m/%d/%Y') as transaction_date,'Beginning CBU','-',amount
FROM cbu_beginning
WHERE id_member = $id_member
) as cbu_ledger
ORDER by cbu_ledger.act_date DESC;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `getMemberCBULedger2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMemberCBULedger2`($id_member INT,$start_date DATE,$end_date DATE)
BEGIN
SELECT * FROM (

SELECT 1 as ordering,'-' as act_date,'-' as transaction_date,'BEGINNING' as description,'-' as reference,ifnull(SUM(amount),0) as amount,ifnull(SUM(amount),0) as credit,0 as debit FROM (
SELECT c.date_received as act_date,DATE_FORMAT(c.date_received,'%m/%d/%Y') as transaction_date,'' as description,'' as reference,ifnull(amount,0) as amount
FROM cash_receipt_details as cd
LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10 AND id_member = $id_member and c.date_received < $start_date
UNION ALL
SELECT loan.date_released as act_date,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as transaction_date,ifnull(pt.alt_description,pt.description) as description,concat("Loan ID #",loan.id_loan) as reference,ifnull(lc.calculated_charge,0) as amount from loan_charges as lc
LEFT JOIN loan on loan.id_loan = lc.id_loan
LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
WHERE id_member = $id_member and pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2  AND loan.date_released < $start_date
UNION ALL
SELECT rt.transaction_date as act_date,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,ifnull(pt.alt_description,pt.description) as description,concat("Repayment ID #",rt.id_repayment_transaction) as reference,ifnull(rf.amount,0) as amount FROM repayment_fees as rf
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
WHERE id_member = $id_member AND pt.is_cbu = 1 and pt.type =3 and rt.status < 10 and rt.transaction_date < $start_date and rf.amount > 0
UNION ALL
SELECT date,DATE_FORMAT(date,'%m/%d/%Y') as transaction_date,'Beginning CBU','-',amount
FROM cbu_beginning
WHERE id_member = $id_member AND date < $start_date
UNION ALL
SELECT cd.date,DATE_FORMAT(cd.date,'%m/%d/%Y') as transaction_date,cd.description,concat('CDV# ',cd.id_cash_disbursement) as reference,(cdd.debit*-1) as amount FROM cash_disbursement as cd
LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
WHERE cd.id_member = $id_member and  ca.iscbu = 1 and cd.status <> 10 AND cd.date < $start_date
UNION ALL
SELECT jv.date,DATE_FORMAT(jv.date,'%m/%d/%Y') as transaction_date,jv.description,concat('JV# ',jv.id_journal_voucher) as reference,(jvd.credit-jvd.debit) as amount FROM journal_voucher as jv
LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
WHERE ca.iscbu = 1 and jv.id_member=$id_member AND jv.status <> 10 AND jv.date < $start_date AND jv.type=1
) as beg  HAVING sum(beg.amount) >0
UNION ALL



/**********************DATE RANGE**********************/
SELECT 2 as ordering,c.date_received as act_date,DATE_FORMAT(c.date_received,'%m/%d/%Y') as transaction_date,pt.description as description,concat("Cash Receipt #",cd.id_Cash_Receipt," (OR# ",c.or_no,")") as reference,ifnull(amount,0) as amount,ifnull(amount,0) as credit,0 as debit
FROM cash_receipt_details as cd
LEFT JOIN cash_receipt as c on c.id_cash_receipt = cd.id_cash_receipt
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = cd.id_payment_type
WHERE pt.is_cbu = 1 AND pt.type in (1) AND status <> 10 AND id_member = $id_member and c.date_received >= $start_date and c.date_received <= $end_date
UNION ALL
SELECT 2 as ordering,loan.date_released as act_date,DATE_FORMAT(loan.date_released,'%m/%d/%Y') as transaction_date,ifnull(pt.alt_description,pt.description) as description,concat("Loan ID #",loan.id_loan) as reference,ifnull(lc.calculated_charge,0) as amount,ifnull(lc.calculated_charge,0) as credit,0 as debit from loan_charges as lc
LEFT JOIN loan on loan.id_loan = lc.id_loan
LEFT JOIN tbl_payment_type as pt on pt.reference = lc.id_loan_fees and pt.type = 2
WHERE id_member = $id_member and pt.is_cbu = 1 AND (loan.status in (6) or loan.loan_status in (1)) and pt.type =2 and loan.date_released >=$start_date AND loan.date_released <= $end_date
UNION ALL
SELECT 2 as ordering,rt.transaction_date as act_date,DATE_FORMAT(rt.transaction_date,'%m/%d/%Y') as transaction_date,ifnull(pt.alt_description,pt.description) as description,concat("Repayment ID #",rt.id_repayment_transaction) as reference,ifnull(rf.amount,0) as amount,ifnull(rf.amount,0) as credit,0 as debit FROM repayment_fees as rf
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
WHERE id_member = $id_member AND pt.is_cbu = 1 and pt.type =3 and rt.status < 10 and rt.transaction_date >= $start_date AND rt.transaction_date <= $end_date and rf.amount > 0
UNION ALL
SELECT 2 as ordering,date,DATE_FORMAT(date,'%m/%d/%Y') as transaction_date,'Beginning CBU','-',amount,amount as credit,0 as debit
FROM cbu_beginning
WHERE id_member = $id_member AND date >= $start_date AND date <= $end_date
UNION ALL
SELECT 2 as ordering, cd.date,DATE_FORMAT(cd.date,'%m/%d/%Y') as transaction_date,cd.description,concat('CDV# ',cd.id_cash_disbursement) as reference,(cdd.debit*-1) as amount,cdd.credit,cdd.debit FROM cash_disbursement as cd
LEFT JOIN cash_disbursement_details as cdd on cdd.id_cash_disbursement = cd.id_cash_disbursement
LEFT JOIN chart_account as ca on ca.id_chart_account = cdd.id_chart_account
WHERE cd.id_member = $id_member and  ca.iscbu = 1 and cd.status <> 10 AND cd.date >= $start_date AND cd.date <= $end_date and cdd.debit > 0
UNION ALL
SELECT 2 as ordering, jv.date,DATE_FORMAT(jv.date,'%m/%d/%Y') as transaction_date,jv.description,concat('JV# ',jv.id_journal_voucher) as reference,(jvd.debit*-1) as amount,jvd.credit,jvd.debit FROM journal_voucher as jv
LEFT JOIN journal_voucher_details as jvd on jvd.id_journal_voucher = jv.id_journal_voucher
LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
WHERE jv.id_member = $id_member and  ca.iscbu = 1 and jv.status <> 10 AND jv.date >= $start_date AND jv.date <= $end_date  AND jv.type=1
) as cbu_ledger
/*****ORDER by ordering ASC,cbu_ledger.act_date DESC;****/
ORDER by ordering ASC,cbu_ledger.act_date ASC; 


END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `getMemberPrime` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMemberPrime`($id_member INT,$start_date DATE,$end_date DATE)
BEGIN
SELECT * FROM (
SELECT 1 as ordering,'-' as act_date,'-' as transaction_date,'BEGINNING' as description,'-' as reference,SUM(credit-debit) as amount,SUM(credit) as credit,SUM(debit) as debit FROM (
SELECT cd.id_member,
cd.date as transaction_date,concat('CDV# ',cd.id_cash_disbursement) as reference,if(cdv.details is null OR cdv.details='',cd.description,cdv.details) as description,debit,credit
FROM cash_disbursement_details as cdv
LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = cdv.id_cash_disbursement
LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
LEFT JOIN cdv_type as ct on ct.id_cdv_type = cd.type
WHERE cd.status <> 10 and ca.isprime=1  and cd.date < $start_date and cd.id_member = $id_member
UNION ALL
SELECT jv.id_member,
jv.date as transaction_date,concat('JV# ',jv.id_journal_voucher) as reference,if(jvd.details is null OR jvd.details='',jv.description,jvd.details) as description,debit,credit
FROM journal_voucher_details as jvd
LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = jvd.id_journal_voucher
LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
WHERE jv.status <> 10 and ca.isprime=1 and jv.date < $start_date and jv.id_member = $id_member
UNION ALL
SELECT crv.id_member,
crv.date as transaction_date,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,if(crvd.details is null OR crvd.details='',crv.description,crvd.details) as description,debit,credit
FROM cash_receipt_voucher_details as crvd
LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = crvd.id_cash_receipt_voucher
LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
WHERE crv.status <> 10 and ca.isprime=1 and crv.date < $start_date and crv.id_member = $id_member
UNION ALL
SELECT id_member,date,'-' as reference,'Beginning' as description,0 as debit,amount as credit 
FROM prime_beginning as cb
WHERE cb.id_member = $id_member and date < $start_date
) as beg
HAVING SUM(credit-debit) > 0
UNION ALL
SELECT 2 as ordering,cur.transaction_date as act_date,DATE_FORMAT(transaction_date,'%m/%d/%Y') as transaction_date,description,reference,credit-debit as amount,credit,debit FROM (
SELECT cd.id_member,
cd.date as transaction_date,concat('CDV# ',cd.id_cash_disbursement) as reference,if(cdv.details is null OR cdv.details='',cd.description,cdv.details) as description,debit,credit
FROM cash_disbursement_details as cdv
LEFT JOIN cash_disbursement as cd on cd.id_cash_disbursement = cdv.id_cash_disbursement
LEFT JOIN chart_account as ca on ca.id_chart_account = cdv.id_chart_account
LEFT JOIN cdv_type as ct on ct.id_cdv_type = cd.type
WHERE cd.status <> 10 and ca.isprime=1  and cd.date >= $start_date AND cd.date <= $end_date and cd.id_member = $id_member
UNION ALL
SELECT jv.id_member,
jv.date as transaction_date,concat('JV# ',jv.id_journal_voucher) as reference,if(jvd.details is null OR jvd.details='',jv.description,jvd.details) as description,debit,credit
FROM journal_voucher_details as jvd
LEFT JOIN journal_voucher as jv on jv.id_journal_voucher = jvd.id_journal_voucher
LEFT JOIN chart_account as ca on ca.id_chart_account = jvd.id_chart_account
LEFT JOIN jv_type as jt on jt.id_jv_type = jv.type
WHERE jv.status <> 10 and ca.isprime=1 and jv.date >= $start_date AND jv.date <= $end_date and jv.id_member = $id_member
UNION ALL
SELECT crv.id_member,
crv.date as transaction_date,concat('CRV# ',crv.id_cash_receipt_voucher) as reference,if(crvd.details is null OR crvd.details='',crv.description,crvd.details) as description,debit,credit
FROM cash_receipt_voucher_details as crvd
LEFT JOIN cash_receipt_voucher as crv on crv.id_cash_receipt_voucher = crvd.id_cash_receipt_voucher
LEFT JOIN chart_account as ca on ca.id_chart_account = crvd.id_chart_account
LEFT JOIN crv_type as ct on ct.id_crv_type = crv.type
WHERE crv.status <> 10 and ca.isprime=1 and crv.date >= $start_date AND crv.date <= $end_date and crv.id_member = $id_member
UNION ALL
SELECT id_member,date,'-' as reference,'Beginning' as description,0 as debit,amount as credit 
FROM prime_beginning as cb
WHERE cb.id_member = $id_member AND cb.date >= $start_date AND cb.date <= $end_date) as cur) as prime
ORDER BY ordering,transaction_date;



END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `getMonthlyDepreciation` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMonthlyDepreciation`($asset_code VARCHAR(45),$date DATE,$id_asset_disposal INT(11))
BEGIN
SELECT getAssetQuantity($asset_code,$id_asset_disposal) as remaining_quantity,
if((MONTH($date) < MONTH(start_life) AND YEAR($date) <= YEAR(start_life) OR YEAR($date) < YEAR(start_life)),0,unit_cost) as purchase_cost,
CASE
	WHEN (MONTH($date) < MONTH(start_life) AND YEAR($date) <= YEAR(start_life) OR YEAR($date) < YEAR(start_life)) THEN 0
    WHEN MONTH($date) > MONTH(end_life) AND YEAR($date) >= YEAR(end_life) THEN ROUND(unit_cost,2)-ROUND((salvage_value/quantity),2)
    ELSE ad.accumulated_depreciation END as accumulated_depreciation,
CASE
	WHEN (MONTH($date) < MONTH(start_life) AND YEAR($date) <= YEAR(start_life) OR YEAR($date) < YEAR(start_life)) THEN 0
    WHEN MONTH($date) > MONTH(end_life) AND YEAR($date) >= YEAR(end_life) THEN ROUND((salvage_value/quantity),2)
    ELSE ad.end_book_value END as current_value
FROM asset_item as ai
LEFT JOIN asset_depreciation_month as ad on ai.id_asset_item = ad.id_asset_item and ad.year = YEAR($date) and ad.month = MONTH($date)
where ai.asset_code = $asset_code;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `getMonthlyDepreciation2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `getMonthlyDepreciation2`($asset_code VARCHAR(45),$date DATE,$id_asset_disposal INT(11))
BEGIN
SELECT 
getAssetQuantity($asset_code,$id_asset_disposal) as remaining_quantity,unit_cost as purchase_cost,
@q:=
ROUND(
CASE 
    WHEN (depreciation_amount is null OR  YEAR($date) = year_end AND MONTH($date) >= month_end) THEN unit_cost - salvage_value
    WHEN YEAR($date)=year_start THEN (depreciation_amount/first_year_remaining)*(MONTH($date)-month_start+1)
    ELSE  unit_cost-start_book_value + (depreciation_amount/12)*month_year_used
    END,2) as accumulated_depreciation,ROUND(unit_cost-@q,2) as current_value
FROM (
SELECT ai.asset_code,unit_cost,ifnull(ad.year,YEAR($date)) as year,ROUND(salvage_value/quantity,2) as salvage_value,start_book_value,end_book_value,MONTH(start_life) as month_start,YEAR(start_life) as year_start,
MONTH(end_life) as month_end,YEAR(end_life) as year_end,
(ad.depreciation_amount) as depreciation_amount,(12-MONTH(start_life)+1) as first_year_remaining,ifnull((MONTH($date)),1) month_year_used
FROM asset_item as ai
LEFT JOIN asset_depreciation as ad on ai.id_asset_item = ad.id_asset_item and ad.year = YEAR($date)
where ai.asset_code = $asset_code) as t;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `RepaymentRunningBalance` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `RepaymentRunningBalance`($id_loan INT)
BEGIN
SELECT repayment_token,repayment_reference,loan_service_name,count,due_date,repayment_date,repayment_amount,interest_amount,fees,total_due,principal_running_balance,paid_principal,interest_running_balance,paid_interest,fees_running_balance,paid_fees,
((principal_running_balance+interest_running_balance+fees_running_balance) - (paid_principal+paid_interest+paid_fees)) as total_balance,
repayment_type,accrued FROM (
SELECT *,
ifnull(ROUND(if((@previous_term <> term_code OR @previous_term is null),@prin_bal:= repayment_amount,@prin_bal:=@prin),2),0) as principal_running_balance,@prin := @prin_bal - paid_principal,
ifnull(ROUND(if((@previous_term <> term_code OR @previous_term is null),@int_bal:= interest_amount,@int_bal:=@int),2),0) as interest_running_balance,@int := @int_bal - paid_interest,
ifnull(ROUND(if((@previous_term <> term_code OR @previous_term is null),@fee_bal:= fees,@int_bal:=@fees),2),0) as fees_running_balance,@fees := @fees_bal - paid_fees,
@previous_term,@previous_term := term_code FROM (
SELECT ifnull(rt.repayment_token,'-') as repayment_token,ifnull(rl.id_repayment_transaction,'-') as repayment_reference,ls.name as 'loan_service_name',lt.count,DATE_FORMAT(lt.due_date,'%m/%d/%Y') as due_date,
ifnull(DATE_FORMAT(rt.transaction_date,'%m/%d/%Y'),'-') as repayment_date,if(rl.type=1,'On Due Payment',if(rl.type=2,'Previous Payment','-')) as repayment_type,
lt.repayment_amount,ifnull(rl.paid_principal,0) as paid_principal,lt.interest_amount,ifnull(rl.paid_interest,0) as paid_interest,lt.fees,ifnull(rl.paid_fees,0) as paid_fees,lt.term_code,lt.total_due,lt.accrued
FROM loan 
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
LEFT JOIN loan_table as lt on lt.id_loan = loan.id_loan
LEFT JOIN repayment_loans as rl on rl.id_loan = lt.id_loan and rl.term_code = lt.term_code and rl.status < 10
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
where loan.id_loan = $id_loan
ORDER BY lt.count,repayment_date,rt.id_repayment_transaction ) as t) as final_repayment_table
WHERE (paid_principal+paid_interest+paid_fees) > 0;

END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `RepaymentSummary` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_ALL_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `RepaymentSummary`($transaction_date DATE,$due_date DATE,$transaction_type INT)
BEGIN
SELECT concat(m.id_member,' || ',m.first_name,' ',m.last_name,' ',m.suffix) as borrower,description,amount,due FROM (
SELECT parent_ordering,ordering,rls.id_loan as reference,rls.id_member,concat('ID#',rls.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms),' ',CONVERT(rls.description USING utf8)) as description,amount,due FROM (
SELECT 1 as parent_ordering,1 as ordering,id_loan,rt.id_member,'Principal' as description ,SUM(paid_principal) as amount,getDueRepaymentSummary(1,rl.id_loan,$transaction_date,$due_date) as due FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_principal > 0 and rt.status < 10
GROUP BY id_loan
UNION ALL
SELECT 1 as parent_ordering,2 as ordering,id_loan,rt.id_member,'Interest' as description ,SUM(paid_interest) as amount,getDueRepaymentSummary(2,rl.id_loan,$transaction_date,$due_date) as due FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_interest > 0 and rt.status < 10
GROUP BY id_loan
UNION ALL
SELECT 1 as parent_ordering,3 as ordering,id_loan,rt.id_member,'Fees' as description ,SUM(paid_fees) as amount,getDueRepaymentSummary(3,rl.id_loan,$transaction_date,$due_date) as due FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_fees > 0 and rt.status < 10
GROUP BY id_loan) as rls
LEFT JOIN loan on loan.id_loan = rls.id_loan
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
UNION ALL
SELECT 2 as parent_ordering,rf.id_repayment_fees as ordering,rf.id_repayment_fees as reference,rt.id_member,pt.description,SUM(rf.amount) as amount,SUM(rf.amount) as due FROM repayment_fees as rf
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
WHERE transaction_date = $transaction_date and rt.transaction_type = $transaction_type and rf.amount > 0 and rt.status < 10
GROUP BY id_member,rf.id_payment_type
UNION ALL
SELECT 3 as parent_ordering,rp.id_repayment_penalty as ordering,rp.id_repayment_penalty as reference,rt.id_member,pt.description,SUM(rp.amount) as amount,SUM(rp.amount) as due FROM repayment_penalty as rp
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rp.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on rp.id_payment_type = pt.id_payment_type
WHERE transaction_date = $transaction_date and rt.transaction_type = $transaction_type and rp.amount > 0 and rt.status < 10
GROUP BY id_member,rp.id_payment_type
UNION ALL
SELECT 4 as parent_ordering,0 as ordering,0 as reference,id_member,'TOTAL' as description ,SUM(total_payment) as amount,SUM(total_payment) as due
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and repayment_transaction.status < 10
GROUP BY id_member
UNION ALL
SELECT 5 as parent_ordering,0 as ordering,0 as reference,id_member,'Swiping Amount',SUM(swiping_amount) as amount,SUM(swiping_amount) as due
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and swiping_amount > 0 and repayment_transaction.status < 10
GROUP BY id_member
UNION ALL
SELECT 6 as parent_ordering,0 as ordering,0 as reference,id_member,'Change',SUM(`change`) as amount,SUM(`change`) as due
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and `change` > 0 AND repayment_transaction.status < 10
GROUP BY id_member) as repayment_summary
LEFT JOIN member as m on m.id_member = repayment_summary.id_member
ORDER BY borrower,parent_ordering,reference,ordering;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `RepaymentSummary2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `RepaymentSummary2`($transaction_date DATE,$due_date DATE,$transaction_type INT)
BEGIN 

SELECT concat(m.id_member,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as borrower,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as bor_name ,description,amount,due,data_type FROM (
SELECT parent_ordering,ordering,rls.id_loan as reference,rls.id_member,concat('ID#',rls.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms),' ',CONVERT(rls.description USING utf8)) as description,amount,due,data_type FROM (
SELECT 1 as parent_ordering,1 as ordering,id_loan,rt.id_member,'Principal' as description ,SUM(paid_principal) as amount,getDueRepaymentSummary(1,rl.id_loan,$transaction_date,$due_date) as due,'principal' as data_type FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_principal > 0 and rt.status < 10
GROUP BY id_loan
UNION ALL
SELECT 1 as parent_ordering,2 as ordering,id_loan,rt.id_member,'Interest' as description ,SUM(paid_interest) as amount,getDueRepaymentSummary(2,rl.id_loan,$transaction_date,$due_date) as due,'interest' as data_type  FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_interest > 0 and rt.status < 10
GROUP BY id_loan
UNION ALL
SELECT 1 as parent_ordering,3 as ordering,id_loan,rt.id_member,'Fees' as description ,SUM(paid_fees) as amount,getDueRepaymentSummary(3,rl.id_loan,$transaction_date,$due_date) as due,'fees' as data_type  FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_fees > 0 and rt.status < 10
GROUP BY id_loan

UNION ALL
SELECT 1 as parent_ordering,4 as ordering,rls.id_loan,rt.id_member,'Penalty' as description,rls.amount,0 as due,'penalty' as data_type
FROM repayment_loan_surcharges as rls
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rls.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type  and rt.status < 10
UNION ALL
SELECT 1 as parent_ordering,5 as ordering,rb.id_loan,rt.id_member,'Rebates' as description,rb.amount*-1 as amount,0 as due,'rebates' as data_type
FROM repayment_rebates as rb
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rb.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type  and rt.status < 10


) as rls
LEFT JOIN loan on loan.id_loan = rls.id_loan
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
UNION ALL
SELECT 2 as parent_ordering,rf.id_repayment_fees as ordering,rf.id_repayment_fees as reference,rt.id_member,pt.description,SUM(rf.amount) as amount,SUM(rf.amount) as due,if(pt.is_cbu=1,'capital_share','misc') as data_type FROM repayment_fees as rf
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
WHERE transaction_date = $transaction_date and rt.transaction_type = $transaction_type and rf.amount > 0 and rt.status < 10
GROUP BY id_member,rf.id_payment_type
UNION ALL
SELECT 3 as parent_ordering,rp.id_repayment_penalty as ordering,rp.id_repayment_penalty as reference,rt.id_member,pt.description,SUM(rp.amount) as amount,SUM(rp.amount) as due,'penalty' as data_type FROM repayment_penalty as rp
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rp.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on rp.id_payment_type = pt.id_payment_type
WHERE transaction_date = $transaction_date and rt.transaction_type = $transaction_type and rp.amount > 0 and rt.status < 10
GROUP BY id_member,rp.id_payment_type
UNION ALL
SELECT 4 as parent_ordering,0 as ordering,0 as reference,id_member,'Total Payment' as description ,SUM(total_payment) as amount,SUM(total_payment) as due,'total_payment' as data_type
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and repayment_transaction.status < 10
GROUP BY id_member

UNION ALL
SELECT 5 as parent_ordering,0 as ordering,0 as reference,id_member,'Check Amount',SUM(swiping_amount) as amount,SUM(swiping_amount) as due,'swiping_amount' as data_type
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and swiping_amount > 0 and repayment_transaction.status < 10
GROUP BY id_member
UNION ALL
SELECT 6 as parent_ordering,0 as ordering,0 as reference,id_member,'Change',SUM(`change`) as amount,SUM(`change`) as due,'change' as data_type
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and `change` > 0 AND repayment_transaction.status < 10
GROUP BY id_member
) as repayment_summary
LEFT JOIN member as m on m.id_member = repayment_summary.id_member
ORDER BY bor_name,parent_ordering,reference,ordering;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `RepaymentSummary3` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`localhost` PROCEDURE `RepaymentSummary3`($transaction_date DATE,$due_date DATE,$transaction_type INT)
BEGIN 

SELECT concat(m.id_member,' || ',FormatName(m.first_name,m.middle_name,m.last_name,m.suffix)) as borrower,FormatName(m.first_name,m.middle_name,m.last_name,m.suffix) as bor_name ,description,amount,due,data_type FROM (
SELECT parent_ordering,ordering,rls.id_loan as reference,rls.id_member,concat('ID#',rls.id_loan,' ',getLoanServiceName(loan.id_loan_payment_type,ls.name,loan.terms),' ',CONVERT(rls.description USING utf8)) as description,amount,due,data_type FROM (
SELECT 1 as parent_ordering,1 as ordering,id_loan,rt.id_member,'Principal' as description ,SUM(paid_principal) as amount,getDueRepaymentSummary(1,rl.id_loan,$transaction_date,$due_date) as due,'principal' as data_type FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_principal > 0 and rt.status < 10
GROUP BY id_loan
UNION ALL
SELECT 1 as parent_ordering,2 as ordering,id_loan,rt.id_member,'Interest' as description ,SUM(paid_interest) as amount,getDueRepaymentSummary(2,rl.id_loan,$transaction_date,$due_date) as due,'interest' as data_type  FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_interest > 0 and rt.status < 10
GROUP BY id_loan
UNION ALL
SELECT 1 as parent_ordering,3 as ordering,id_loan,rt.id_member,'Fees' as description ,SUM(paid_fees) as amount,getDueRepaymentSummary(3,rl.id_loan,$transaction_date,$due_date) as due,'fees' as data_type  FROM repayment_loans as rl
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rl.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type and paid_fees > 0 and rt.status < 10
GROUP BY id_loan

UNION ALL
SELECT 1 as parent_ordering,4 as ordering,rls.id_loan,rt.id_member,'Penalty' as description,rls.amount,0 as due,'penalty' as data_type
FROM repayment_loan_surcharges as rls
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rls.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type  and rt.status < 10
UNION ALL
SELECT 1 as parent_ordering,5 as ordering,rb.id_loan,rt.id_member,'Rebates' as description,rb.amount*-1 as amount,0 as due,'rebates' as data_type
FROM repayment_rebates as rb
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rb.id_repayment_transaction
WHERE rt.transaction_date = $transaction_date and rt.transaction_type = $transaction_type  and rt.status < 10


) as rls
LEFT JOIN loan on loan.id_loan = rls.id_loan
LEFT JOIN loan_service as ls on ls.id_loan_service = loan.id_loan_service
UNION ALL
SELECT 2 as parent_ordering,rf.id_repayment_fees as ordering,rf.id_repayment_fees as reference,rt.id_member,pt.description,SUM(rf.amount) as amount,SUM(rf.amount) as due,if(pt.is_cbu=1,'capital_share','misc') as data_type FROM repayment_fees as rf
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rf.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on pt.id_payment_type = rf.id_payment_type
WHERE transaction_date = $transaction_date and rt.transaction_type = $transaction_type and rf.amount > 0 and rt.status < 10
GROUP BY id_member,rf.id_payment_type
UNION ALL
SELECT 3 as parent_ordering,rp.id_repayment_penalty as ordering,rp.id_repayment_penalty as reference,rt.id_member,pt.description,SUM(rp.amount) as amount,SUM(rp.amount) as due,'penalty' as data_type FROM repayment_penalty as rp
LEFT JOIN repayment_transaction as rt on rt.id_repayment_transaction = rp.id_repayment_transaction
LEFT JOIN tbl_payment_type as pt on rp.id_payment_type = pt.id_payment_type
WHERE transaction_date = $transaction_date and rt.transaction_type = $transaction_type and rp.amount > 0 and rt.status < 10
GROUP BY id_member,rp.id_payment_type
UNION ALL
SELECT 4 as parent_ordering,0 as ordering,0 as reference,id_member,'Total Payment' as description ,SUM(total_payment) as amount,SUM(total_payment) as due,'total_payment' as data_type
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and repayment_transaction.status < 10
GROUP BY id_member

UNION ALL
SELECT 5 as parent_ordering,0 as ordering,0 as reference,id_member,'Check Amount',SUM(swiping_amount) as amount,SUM(swiping_amount) as due,'swiping_amount' as data_type
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and swiping_amount > 0 and repayment_transaction.status < 10
GROUP BY id_member
UNION ALL
SELECT 6 as parent_ordering,0 as ordering,0 as reference,id_member,'Change',SUM(`change`) as amount,SUM(`change`) as due,'change' as data_type
FROM repayment_transaction
where transaction_date = $transaction_date and repayment_transaction.transaction_type = $transaction_type and `change` > 0 AND repayment_transaction.status < 10
GROUP BY id_member
) as repayment_summary
LEFT JOIN member as m on m.id_member = repayment_summary.id_member
ORDER BY bor_name,parent_ordering,reference,ordering;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-17 20:21:48
