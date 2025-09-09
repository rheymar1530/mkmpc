<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
    @page {
        margin: 0;
        padding: 0;
        margin-top: 1cm;
        /* margin-right: 0.3cm; */
        margin-left: 0.7cm;
        width: auto;
        size: landscape;
    }
    @media print{
        @page {
            size: landscape;
            margin-top: 0.65cm;
            margin-left: 0.3cm;
            padding: 0;
        }
    }
    #tn_box{
        width: 20.8cm;
        height: 7.45cm;
        /* border: 1px solid black; */
        position: relative;
        /*background: blue;*/
        overflow: hidden;
        padding: 0;
        margin: 0;
    }
    .column {
        float: left;
    }
    #col-grp-1{
        width: 10cm;
        min-width: 10cm;
        max-width: 10cm;
        padding: 0;
        margin: 0;
        /*background-color: gray;*/
    /*    width: 20.2m;
        height: 2.875in;
        border-right: 1px solid black;*/
    }
    #col-grp-2 {
        width: 5.8cm;
        min-width: 5.8cm;
        max-width: 5.8cm;
        padding: 0;
        margin: 0;
    }
    #col-grp-3{
        width: 5cm;
        min-width: 5cm;
        max-width: 5cm;
        padding: 0;
        margin: 0;
    }
    .middle {
        width: 50%;
    }
    /* Clear floats after the columns */
    .row:after {
      content: "";
      display: table;
      clear: both;
    }
    .border-right:{
        /*border-right: 1px solid black;*/
    }
    .border-bottom:{
        /*border-bottom: 1px solid black;*/
    }
    .align-top{
       vertical-align: top !important;
    }
    div.sec_address {
        overflow: hidden;
        vertical-align: top;
        -webkit-box-sizing: border-box; 
        -moz-box-sizing: border-box;    
        box-sizing: border-box; 
        font-size: 14px;
    }
    .text_line{
        padding-top: 3.2mm;
        padding-left: 2mm;   
        text-overflow: ellipsis;
        display: -webkit-box;                                                                      
        -webkit-box-orient: vertical;
    }
    .text_line2{
        padding-top: 0;
        padding-left: 0;   
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-box-orient: vertical;
    }
    td::after {
        content: ''; 
        display: block; 
    }
    .div_cols{
        overflow: hidden;
        position: relative;
        -webkit-box-sizing: border-box; 
        -moz-box-sizing: border-box;    
        box-sizing: border-box; 

        /* border-bottom: 1px solid black;  
        border-right: 1px solid black; */
    }
    .div_r1_h{
        height:0.7cm;
        font-size: 13px;
    }
    .div_r2_h,.div_r4_h,.div_g2_r1_h,.div_g3_r1_h,.div_g3_r2_h{
        height:0.7cm;
        font-size: 14px;
    }
    .div_r3_h{
        height:1.5cm;
    }
    .div_g2_r2_h{
        height: 5.45cm;
    }
    .div_r5_h{
        height: 0.95cm;
    }
    /*attached document boolean*/
    .div_g2_r3_h{
        height: 0.3cm;
    }
    /*attached document*/
    .div_g2_r4_h{
        height: 1cm;
        /*background: red;*/
    }
    .div_content{
        position: absolute;
        bottom: 0;
        left: 2mm;
    }
    .div_bool_attachment{
        position: absolute;
        bottom: 0;
       /* left: 2mm;*/
    }
    .no_wrap{
        white-space: nowrap;
    }
    .cus-border{
/*        border-bottom: none;  
        border-right: 1px solid black;
        border-left: 1px solid black;*/
    }
    .line_3{
         -webkit-line-clamp: 3; /* number of lines to show */
    }
    .line_1{
         -webkit-line-clamp: 1; /* number of lines to show */
    }
    .line_2{
         -webkit-line-clamp: 2; /* number of lines to show */
    }
    .name{
        font-size: 14px;
    }
    .font-12{
        font-size :11px;
    }
</style>
</head>
<body onload="window.print();">
<!-- <body> -->
<div class="row" id="tn_box">
    <!-- COLUMN 1 -->
    <div class="column border-right" id="col-grp-1" style="padding:0px !important">
        <div class="row">
             <!-- Date of pickup-->
            <div class="column border-right div_r1_h div_cols" style="width:2.6cm">
                <span class="div_content">{{$details->booking_date}}</span>
            </div>
            <!-- Origin-->
            <div class="column border-right div_r1_h div_cols" style="width: 2.3cm;">
               <span class="div_content">{{$details->branch_nick}}</span>
            </div>
            <!-- Destination-->
            <div class="column border-right div_r1_h div_cols" style="width: 2.1cm;">
                <span class="div_content">{{$details->dest_tat}}</span>
            </div>
            <!-- Booking Reference-->
            <div class="column border-right div_r1_h div_cols" style="width: 3cm;">
                <span class="div_content">{{$details->book_ref}} ({{$details->hawb_no}})</span>
            </div>
        </div>
        <!-- Consignee -->
        <div class="row">
            <!-- Consignee Name-->
            <div class="column border-right div_r2_h div_cols" style="width: 7.6cm ">
                <span class="div_content text_line line_2 name" style="left: 0 !important;line-height:3.2mm">{{$details->c_name}}</span>
            </div>
            <!-- Consignee Account no-->
            <div class="column border-right div_r2_h div_cols" style="width: 2.4cm;">
               <span class="div_content">{{$details->c_account}}</span>
            </div>
        </div>
        <div class="row">
            <!-- Consignee Address -->
            <div class="column border-right div_r3_h div_cols" style="width: 10cm">
                <div class="sec_address text_line line_3" style="line-height:4mm;">
                    {{$details->c_address}}
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Consignee Email -->
            <div class="column border-right div_r4_h div_cols" style="width: 6.25cm">
                <span class="div_content">{{$details->c_email}}</span>
            </div>
            <!-- Consignee Contact -->
            <div class="column border-right div_r4_h div_cols" style="width: 3.75cm;">
               <span class="div_content">{{$details->c_phone}}</span>
            </div>
        </div>
        <!-- Shipper -->
        <div class="row">
            <!-- Shipper Name -->
            <div class="column border-right div_r2_h div_cols" style="width: 7.6cm">
                <span class="div_content text_line line_2 name" style="left: 0 !important">{{$details->s_name}}</span>
            </div>
            <!-- Shipper Account No-->
            <div class="column border-right div_r2_h div_cols" style="width: 2.4cm;">
               <span class="div_content text_line line_2 name" style="left: 0 !important;font-size: 10px;line-height:2.5mm">{{$details->cost_center}}</span>
            </div>
        </div>
        <div class="row">
            <!-- Shipper Address -->
            <div class="column border-right div_r3_h div_cols" style="width: 10cm">
                <div class="sec_address  text_line line_3" style="line-height:4mm;">
                    {{$details->s_address}}
                </div>
            </div>
        </div>
        <div class="row">
            <!-- Shipper Email -->
            <div class="column border-right div_r4_h div_cols" style="width: 6.25cm">
                <span class="div_content">{{$details->s_email}}</span>
            </div>
            <!-- Shipper Contact -->
            <div class="column border-right div_r4_h div_cols" style="width: 3.75cm;">
               <span class="div_content  text_line line_1" style="font-size:11px;left: 0" >{{$details->s_phone}}</span>
            </div>
        </div>
        <div class="row">
            <div class="column border-right div_r5_h div_cols" style="width: 10cm">
                <span class="div_content"></span>
            </div>
        </div>
    </div>
    <div class="column border-right" id="col-grp-2">
        <div class="row">
            <!-- Nature of Goods -->
            <div class="column border-right div_g2_r1_h div_cols" style="width: 3.5cm">
                <span class="div_content" style="white-space: nowrap;font-size:11px">{{$details->content}}</span>
            </div>
            <!-- Special Instruction -->
            <div class="column border-right div_g2_r1_h div_cols" style="width: 2.3cm;">
               <span class="div_content" style="white-space: nowrap;font-size:11px">{{$details->delivery}}</span>
            </div>
        </div>

        <!-- TOTAL HEIGHT OF THIS SECTION MUST BE 5.45cm -->
        <div class="row">
<!--              <div class="column border-right div_g2_r2_h div_cols" style="width: 5.8cm">
                <span class="div_content" style="white-space: nowrap;"></span>
            </div> -->
            <!-- Dimensions HEADER -->
            <div class="column border-right div_cols" style="width: 1.8cm;height: 0.35cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px"></span>
            </div>
            <div class="column border-right div_cols" style="width: 1.7cm;height: 0.35cm;">
                <span class="div_content" style="white-space: nowrap;"></span>
            </div>
            <div class="column border-right div_cols" style="width: 2.3cm;height: 0.35cm;">
                <span class="div_content" style="white-space: nowrap;"></span>
            </div>
        </div>
        <!-- END DIMENSION HEADER -->

        <!-- DIMENSION BODY -->
        <!-- 1.45 -->
        <?php
            $dimensions_count = count($item_dimensions);
        ?>
        @if($dimensions_count > 4)
            <div class="row">
                <div class="column border-right div_cols" style="width: 5.8cm;height: 1.45cm;">
                    <span class="div_content" style="white-space: nowrap;text-align: center; !important;font-size:10px;width:5.8cm;vertical-align: center;line-height: 1.45cm;">PLEASE SEE THE ATTACHMENT</span>
                </div>
            </div>
        @else
        <?php
            $row = 4;
            $dimension_item_height = 1.45/$row;
        ?>
        @for($i=0;$i<4;$i++)
        <div class="row">
            <div class="column border-right div_cols" style="width: 1.8cm;height: <?php echo $dimension_item_height;?>cm;">
                <span class="div_content" style="white-space: nowrap;left: 8mm;text-align: right !important;font-size:10px;width:0.8cm">{{$item_dimensions[$i]->quantity ?? ''}}</span>
            </div>
            <div class="column border-right div_cols" style="width: 1.7cm;height: <?php echo $dimension_item_height;?>cm;">
                <span class="div_content" style="white-space: nowrap;font-size:10px;text-align: right !important;width:1.2cm">{{$item_dimensions[$i]->actual_weight ?? ''}}</span>
            </div>
            <div class="column border-right div_cols" style="width: 2.3cm;height: <?php echo $dimension_item_height;?>cm;">
                <span class="div_content" style="white-space: nowrap;font-size:10px">{{$item_dimensions[$i]->volume_weight ?? ''}}{{$item_dimensions[$i]->dimension ?? ''}}</span>
            </div>
        </div>
        @endfor
        @endif
        <!-- END DIMENSION BODY -->

        <!-- DIMENSION TOTAL -->
        <div class="row">
            <div class="column border-right div_cols" style="width: 1.8cm;height: 0.4cm;">
               <span class="div_content" style="white-space: nowrap;left: 8mm;text-align: right !important;font-size:13px;width:0.8cm">{{$total_quantity}}</span>
            </div>
            <div class="column border-right div_cols" style="width: 1.7cm;height: 0.4cm;">
                <span class="div_content" style="white-space: nowrap;font-size:13px;text-align: right !important;width:1.2cm">{{$total_weight}}</span>
            </div>
            <div class="column border-right div_cols" style="width: 2.3cm;height: 0.4cm;">
                <span class="div_content" style="white-space: nowrap;font-size:13px">{{$total_vol_weight}}</span>
            </div>
        </div>
        <!-- END DIMENSION TOTAL -->

        <!-- SERVICES LABEL -->
        <div class="row">
            <div class="column border-right div_cols" style="width: 5.8cm;height: 0.25cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px"></span>
            </div>
        </div>
        <!-- END SERVICES LABEL -->

        <!-- MODE OF SHIPMENT -->
        <div class="row">
<!--             <div class="column border-right div_cols" style="width: 5.8cm;height: 0.3cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px"></span>
            </div> -->
            <?php
                $mode_of_shipment = [
                    ['tag'=>'AIRFREIGHT','val'=>1,'width' => '2cm','left' => '2mm !important'],
                    ['tag'=>'SEAFREIGHT','val'=>2,'width' => '2cm','left' => '0 !important'],
                    ['tag'=>'LOcAL','val'=>3,'width' => '1.8cm','left' => '0 !important'],
                ];
            ?>
            @foreach($mode_of_shipment as $ms)
            <div class="column border-right div_cols" style="width: <?php echo $ms['width'] ?>;height: 0.3cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px;left: <?php echo $ms['left'] ?> ;">
                    &nbsp;{{($ms['val']==$details->shipment) ? 'x':''}}
                </span>
            </div>
            @endforeach
<!--             <div class="column border-right div_cols" style="width: 2cm;height: 0.3cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px;left: 0 !important">x</span>
            </div>
            <div class="column border-right div_cols" style="width: 1.8cm;height: 0.3cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px;left: 0 !important">x</span>
            </div> -->
        </div>
        <!-- END MODE OF SHIPMENT -->

        <!-- SERVICES -->

        <?php
            $serv_1 = [
                ['tag'=>'S','val'=>3,'width' => '0.9cm','left' => '2mm !important'],
                ['tag'=>'M','val'=>4,'width' => '0.75cm','left' => '0 !important'],
                ['tag'=>'L','val'=>5,'width' => '0.7cm','left' => '0 !important'],
                ['tag'=>'P','val'=>6,'width' => '0.6cm','left' => '0 !important'],
                ['tag'=>'LCL','val'=>'99999999','width' => '0.85cm','left' => '1.5mm !important'],
                ['tag'=>'CON','val'=>'99999999','width' => '1.05cm','left' => '2mm !important'],
            ];
            $serv_2 = [
                ['tag'=>'SB','val'=>'99999999','width' => '0.9cm','left' => '2mm !important'],
                ['tag'=>'MB','val'=>'99999999','width' => '0.75cm','left' => '0 !important'],
                ['tag'=>'LB','val'=>'99999999','width' => '0.7cm','left' => '0 !important'],
                ['tag'=>'BB','val'=>'99999999','width' => '0.6cm','left' => '0 !important'],
                ['tag'=>'FCL','val'=>'99999999','width' => '0.85cm','left' => '1.5mm !important'],
                ['tag'=>'FTL','val'=>21,'width' => '1.05cm','left' => '2mm !important'],
            ];
            $serv_3 = [
                ['tag'=>'EC','val'=>'99999999','width' => '0.9cm','left' => '2mm !important'],
                ['tag'=>'RS','val'=>'99999999','width' => '0.75cm','left' => '0 !important'],
                ['tag'=>'CRG','val'=>'99999999','width' => '0.7cm','left' => '0 !important'],
                ['tag'=>'','val'=>'99999999','width' => '0.6cm','left' => '0 !important'],
                ['tag'=>'','val'=>'99999999','width' => '0.85cm','left' => '1.5mm !important'],
                ['tag'=>'','val'=>'99999999','width' => '1.05cm','left' => '2mm !important'],
            ];

            $with_service_id = [3,4,5,6,21];
        ?>
        <div class="row">
            @foreach($serv_1 as $s1)
            <div class="column border-right div_cols" style="width: <?php echo $s1['width'] ?>;height: 0.3cm;">
                <span class="div_content" style="white-space: nowrap;font-size:11px;left:<?php echo $s1['left'] ?>">&nbsp;{{ ($s1['val'] == $details->service_id)?'x':'' }}</span>
            </div>
            @endforeach
            <div class="column border-right div_cols" style="width: 0.95cm;height: 0.3cm;">
                <span class="div_content" style="white-space: nowrap;font-size:11px"></span>
            </div>
        </div>
        <div class="row">
            @foreach($serv_2 as $s2)
            <div class="column border-right div_cols" style="width: <?php echo $s2['width'] ?>;height: 0.35cm;">
                <span class="div_content" style="white-space: nowrap;font-size:11px;left:<?php echo $s2['left'] ?>">&nbsp;{{ ($s2['val'] == $details->service_id)?'x':'' }}</span>
            </div>
            @endforeach
            <div class="column border-right div_cols" style="width: 0.95cm;height: 0.35cm;">
                <span class="div_content" style="white-space: nowrap;font-size:7px;left: 0.2mm !important;font-weight: bold;">
                    @if(!in_array($details->service_id,$with_service_id))
                        {{$details->service_name}}
                    @endif
                </span>
            </div>
        </div>
        <div class="row">
            @foreach($serv_3 as $s3)
            <div class="column border-right div_cols" style="width: <?php echo $s3['width'] ?>;height: 0.35cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px;left:<?php echo $s3['left'] ?>">&nbsp;{{ ($s3['val'] == $details->service_id)?'x':'' }}</span>
            </div>
            @endforeach
            <div class="column border-right div_cols" style="width: 0.95cm;height: 0.35cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px"></span>
            </div>
        </div>
        <!-- END SERVICES -->

        <!-- SERVICES MODE -->
        <div class="row">
<!--             <div class="column border-right div_cols" style="width: 5.8cm;height: 0.55cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px"></span>
            </div> -->
            <?php
                $service_mode =[
                    ['tag'=>'DD','val'=>1,'width' => '1cm','left' => '3mm !important'],
                    ['tag'=>'AA','val'=>2,'width' => '0.8cm','left' => '0 !important'],
                    ['tag'=>'DA','val'=>3,'width' => '0.8cm','left' => '0 !important'],
                    ['tag'=>'AD','val'=>4,'width' => '0.8cm','left' => '0 !important'],
                    ['tag'=>'PP','val'=>5,'width' => '0.8cm','left' => '0 !important'],
                    ['tag'=>'DP','val'=>6,'width' => '0.8cm','left' => '0 !important'],
                    ['tag'=>'PD','val'=>7,'width' => '0.8cm','left' => '0 !important'],
                ];
            ?>
            @foreach($service_mode as $sm)
            <div class="column border-right div_cols" style="width: <?php echo $sm['width'] ?>;height: 0.55cm ; ?>">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px;left:<?php echo $sm['left'] ?>">&nbsp;{{ ($sm['val'] == $details->service_mode)?'x':'' }}</span>
            </div>
            @endforeach
        </div>
        <!-- END SERVICES MODE -->

        <!-- SHI -->
        <div class="row">
            <?php
                $shi =[
                    ['val'=>1,'width' => '1.45cm','left' => '3mm !important'],
                    ['val'=>2,'width' => '0.95cm','left' => '0 !important'],
                    ['val'=>3,'width' => '1cm','left' => '0 !important'],
                    ['val'=>4,'width' => '1.1cm','left' => '0 !important'],
                    ['val'=>5,'width' => '1.3cm','left' => '0 !important'],
                ];
            ?>
            @foreach($shi as $s)
            <div class="column border-right div_cols" style="width: <?php echo $s['width'] ?>;height: 0.55cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px;left:<?php echo $s['left'] ?>"></span>
            </div>
            @endforeach
        </div>
        <!-- END SHI -->
        <!-- PAYMODE -->
        <div class="row">
            <?php
                $paymode =[
                    ['val'=>1,'width' => '0.7cm','left' => '1mm !important'],
                    ['val'=>2,'width' => '0.65cm','left' => '0 !important'],
                    ['val'=>3,'width' => '0.75cm','left' => '0 !important'],
                    ['val'=>4,'width' => '0.8cm','left' => '0 !important'],
                ];
                $paymode_value =2;
            ?>
            @foreach($paymode as $p)
            <div class="column border-right div_cols" style="width: <?php echo $p['width'] ?>;height: 0.6cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px;left:<?php echo $p['left']?>">
                    @if($p['val'] == $paymode_value)
                    &nbsp;x
                    @endif
                </span>
            </div>
            @endforeach
            <!-- OR NO -->
            <div class="column border-right div_cols" style="width: 2.9cm;height: 0.6cm;">
                <span class="div_content" style="white-space: nowrap;text-align: center !important;font-size:11px"></span>
            </div>       
        </div>
        <!-- END PAYMODE -->
        <!-- END ADDITIONAL INFO -->
        <div class="row">
            <!-- Attached document boolean -->
<!--              <div class="column border-right div_g2_r3_h div_cols" style="width: 5.65cm">
                <span class="div_bool_attachment" style="font-size: 12px;line-height: 3mm;">
                    <span style="padding-left: 2.7cm;background: red">x</span>
                </span>
            </div> -->
            <div class="column border-right div_g2_r3_h div_cols" style="width: 2.9cm;border-right:none !important">
                <span class="div_bool_attachment" style="font-size: 12px;line-height: 2mm;right:0 !important;">&nbsp;
                    <?php echo ($details->doc_type != null)?"x":"";?>
                   
                </span>
            </div>
            <div class="column border-right div_g2_r3_h div_cols" style="width: 2.9cm">
                <span class="div_bool_attachment" style="font-size: 12px;line-height: 3mm;left: 0.6cm;">&nbsp;
                    <?php echo ($details->doc_type == null)?"x":"";?>
                </span>
            </div>
        </div>
        <div class="row">
            <!-- Attached document type -->
            <div class="column border-right div_g2_r4_h div_cols" style="width: 2.75cm;border-bottom: none;">
                <span class="div_content text_line2 line_2 name">{{$details->doc_type}}</span>
            </div>
            <!-- Attached document ref -->
             <div class="column border-right div_g2_r4_h div_cols" style="width: 3.05cm;border-bottom: none;">
                <span class="div_content text_line2 line_2 name">{{$details->doc_ref}}</span>
            </div>
           <!--  <div class="column border-right div_g2_r4_h div_cols" style="width: 2.75cm;border-bottom: none;">
                <span class="div_content" style="white-space: nowrap;">123xx</span>
            </div> -->
        </div>
    </div>

    <div class="column"  id="col-grp-3">
        <div class="row">
            <!-- Shippers Account -->
             <div class="column border-right div_g3_r1_h div_cols" style="width: 3cm;">
                <span class="div_content" style="white-space: nowrap;">{{$details->s_account}}</span>
            </div>
        </div>
        <!-- Declared Value -->
        <div class="row">
             <div class="column border-right div_g3_r2_h div_cols" style="width: 3cm;">
                <span class="div_content" style="white-space: nowrap;">{{$details->declared}}</span>
            </div>
        </div>
    </div>
</div>

</body>
</html>