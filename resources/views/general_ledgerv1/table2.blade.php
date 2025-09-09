<?php
function check_amt($amt){
    return ($amt == 0)?'':number_format($amt,2);
}

$acc_totals = array();
$g_total_debit = 0;
$g_total_credit = 0;
?>
@foreach($general_ledger as $account=>$gl)
<tr class="acc_head" data-widget="expandable-table" aria-expanded="false" data-id="{{str_replace(' ','_',$account)}}" ondblclick="trigger_header()">
    <td colspan="3">
        <i class="expandable-table-caret fas fa-caret-right fa-fw"></i>
        <b>{{$account}}</b>
    </td>
    <td class="class_amount"><span class="col_tot col_tot_debit">1,234.12</span></td>
    <td class="class_amount"><span class="col_tot col_tot_credit">1,235.12</span></td>
    <td></td>
</tr>
<?php
    $total_debit = 0;
    $total_credit = 0;
?>
@foreach($gl as $row)
<tr class="row_gl" aria-id="{{str_replace(' ','_',$account)}}" style="display:none">
    <td style="padding-left:20px"><div class="div_gl" style="display:none" >{{$row->date}}</div></td>
    <td><div class="div_gl" style="display:none">{{$row->description}}</div></td>
    <td><div class="div_gl" style="display:none">{{$row->post_reference}}</div></td>
    <td class="class_amount"><div class="div_gl" style="display:none"><?php echo check_amt($row->debit); ?></div></td>
    <td class="class_amount"><div class="div_gl" style="display:none"><?php echo check_amt($row->credit); ?></div></td>
    <td><div class="div_gl" style="display:none">{{$row->remarks}}</div></td>
</tr>
<?php
$total_debit += $row->debit;
$total_credit += $row->credit;
?>
@endforeach
<tr class="row_gl row_total" aria-id="{{str_replace(' ','_',$account)}}" style="display:none;font-weight: bold;">
    <td style="padding-left:20px" colspan="3"><div class="div_gl" style="display:none" >TOTAL</div></td>
    <td class="class_amount"><div class="div_gl" style="display:none">{{check_amt($total_debit)}}</div></td>
    <td class="class_amount"><div class="div_gl" style="display:none">{{check_amt($total_credit)}}</div></td>
    <td><div class="div_gl" style="display:none"></div></td>
</tr>
<?php
    $acc_totals[str_replace(' ','_',$account)]['credit'] = check_amt($total_credit);
    $acc_totals[str_replace(' ','_',$account)]['debit'] = check_amt($total_debit);


$g_total_debit += $total_debit;
$g_total_credit += $total_credit;
?>


   
@endforeach
<tr class="row_total">
    <th colspan="3" style="padding-left:20px">GRAND TOTAL</th>
    <th class="class_amount">{{check_amt($g_total_debit)}}</th>
    <th class="class_amount">{{check_amt($g_total_credit)}}</th>
    <th></th>
</tr>
@push('scripts')
<script type="text/javascript">
 const acct_totals = jQuery.parseJSON('<?php echo json_encode($acc_totals); ?>');


 $(document).ready(function(){
    $.each(acct_totals,function(account,arr){
        var parent_row = $('.acc_head[data-id="'+account+'"]');
        parent_row.find('.col_tot_debit').text(arr['debit']);
        parent_row.find('.col_tot_credit').text(arr['credit']);
    })
 })
 $(document).on('click','.acc_head',function(){
    var exp = $(this).attr('aria-expanded');
    var parent_row = $(this);

    var id = $(this).attr('data-id');

    var rows = $(".row_gl[aria-id='"+id+"']");
    console.log(rows.length)
    var divs = $(rows).find('.div_gl');

    if(exp == "true"){
        parent_row.find('.col_tot_debit,.col_tot_credit').hide();
        $(rows).show();

        $(divs).slideDown("1000",function(){
            
        });
       
    }else{
         
        $(divs).slideUp("1000",function(){

            $(rows).hide();  
            parent_row.find('.col_tot_debit,.col_tot_credit').show();
        });    
        
   }

})
</script>
@endpush