@extends('adminLTE.admin_template_frame')
@section('content')
<style type="text/css">
  
  html {
  box-sizing: border-box;
}
*,
*:before,
*:after {
  box-sizing: inherit;
}
.table-scroll {
  position: relative;
  width:100%;
  z-index: 1;
  margin: auto;
  overflow: auto;
/*  min-height: 300px;
  max-height: 600px;*/
  /*height: 600px;*/
}
.table-scroll table {
  width: 100%;
  min-width: 1280px;
  margin: auto;
  border-spacing: 0;
}
.table-wrap {
  position: relative;
}
.table-scroll th,
.table-scroll td {
  /*border:1px solid #000 !important;*/
  /*background: #fff;*/
  vertical-align: top;
}
.table-scroll th{
  font-size: 13px !important;
  font-family: Arial !important;
  padding: 2px;
}
.table-scroll td{
  padding: 2px;
  font-size: 13px !important;
  font-family: Arial !important;
}
.tbl_tran_summary td{
  padding: 2px;
  font-size: 13px !important;
  font-family: Arial !important;
}
.table-scroll thead th {
  background: #00264d;
  color: #fff;
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}
.dark-mode .table-scroll thead th {
  background: #0047b3;
  color: #fff;
  position: -webkit-sticky;
  position: sticky;
  top: 0;
}

tbody th{
  padding: 2px;
}

/* safari and ios need the tfoot itself to be position:sticky also */
.table-scroll tfoot,
.table-scroll tfoot th,
.table-scroll tfoot td {
  position: -webkit-sticky;
  position: sticky;
  bottom: 0;
  background: #666;
  color: #fff;
  z-index:4;
}
.group_padding{
  padding-left: 12px !important;
}

thead th:first-child,
tfoot th:first-child {
  z-index: 5;
}
.tbl_transactions {
   border: 1px solid #bfbfbf;
}

.tbl_transactions thead th {
  border-top: 1px solid #bfbfbf!important;
  border-bottom: 1px solid #bfbfbf!important;
  border-left: 1px solid #bfbfbf;
  border-right: 1px solid #bfbfbf;
}

.tbl_transactions td {
  border-left: 1px solid #bfbfbf;
  border-right: 1px solid #bfbfbf;
  border-top: none!important;
}
.col_amount{
  text-align: right;
}
.pad_2{
  padding-left: 15px !important;
}
.pad_3{
  padding-left: 30px !important;
}
.subtotal_border{
  border-bottom: 2px solid;
}
</style>
<div class="container-fluid">
    
  <!-- <button class="btn btn-primary" type="button" id="btn_save_soa" disabled="">Generate SOA</button> -->
  <div class="col-md-12">

  <?php
    $g_total = array();
    if(count($sum_fields) > 0){
      foreach($sum_fields as $f){
        $g_total[$f] = 0;
      }
    }
  ?>
  <div id="table-scroll" class="table-scroll" style="max-height: calc(100vh - 150px);overflow-y: auto;margin-top: 10px">
    <table class="table table-striped main-table tbl_transactions" style="white-space: nowrap;">
      <thead>
        <tr class="table_header" style="border-top: 10px;">
    			@foreach($headers as $header)
    				<th>{{ $header }}</th>
    			@endforeach
        </tr>
      </thead>
        <tbody>
        	@if($group_count == 0)
        		@foreach($data_list as $item)
        			<tr>
        				@foreach($fields as $field)
                  <?php
                    $dt = $data_types[$field];
                    if($dt == "amount"){
                      $val = number_format($item->{$field},2);
                    }else{
                      $val = $item->{$field};
                    }
                  ?>
        					<td class="col_{{$dt}}">{{ $val }}</td>
        				@endforeach
        			</tr>
        		@endforeach
        	  @elseif($group_count == 1)
              @foreach($data_list as $first_key => $first_item)
                <tr>
                  <th colspan="{{count($fields)}}">{{$first_key}}</th>
                </tr>
                @foreach($first_item as $item)
                  <tr>
                    @foreach($fields as $field)
                    <?php
                      $dt = $data_types[$field];
                      if($dt == "amount"){
                        $val = number_format($item->{$field},2);
                      }else{
                        $val = $item->{$field};
                      }
                    ?>
                    <td class="col_{{$dt}} pad_2">{{ $val }}</td>
                    @endforeach
                  </tr>
                @endforeach
                @if(count($sum_fields) > 0)
                  <tr>
                    @foreach($fields as $c=>$field)
                      @if($c == 0)
                        <th class="subtotal_border">Sub total</th>
                      @else
                        <th class="col_amount subtotal_border">{{ isset($group_total[$first_key][$field])?number_format($group_total[$first_key][$field],2):'' }}</th>
                      @endif

                    @endforeach
                  </tr>
                @endif
              @endforeach
            @else
              @foreach($data_list as $first_key => $first_item)
                <tr>
                  <th colspan="{{count($fields)}}">{{$first_key}}</th>
                </tr>
                @foreach($first_item as $second_key => $second_item)
                  <tr>
                    <th colspan="{{count($fields)}}" class="pad_2">{{$second_key}}</th>
                  </tr>
                   @foreach($second_item as $item)
                    <tr>
                      @foreach($fields as $field)
                      <?php
                        $dt = $data_types[$field];
                        if($dt == "amount"){
                          $val = number_format($item->{$field},2);
                        }else{
                          $val = $item->{$field};
                        }
                      ?>
                      <td class="col_{{$dt}} pad_3">{{ $val }}</td>
                      @endforeach
                    </tr>
                   @endforeach
                  @if(count($sum_fields) > 0)
                  <tr>
                    @foreach($fields as $c=>$field)
                      @if($c == 0)
                        <th>Sub total</th>
                      @else
                        <th class="col_amount">{{ isset($group_total[$first_key][$second_key][$field])?number_format($group_total[$first_key][$second_key][$field],2):'' }}</th>
                      @endif

                    @endforeach
                  </tr>
                @endif
                @endforeach
                @if(count($sum_fields) > 0)
                  <tr>
                    @foreach($fields as $c=>$field)
                      @if($c == 0)
                        <th>Sub total</th>
                      @else
                        <th class="col_amount">{{ isset($group_total[$first_key][$field])?number_format($group_total[$first_key][$field],2):'' }}</th>
                      @endif

                    @endforeach
                  </tr>
                @endif
              @endforeach
        	@endif
      </tbody>
      @if(count($sum_fields) > 0)
      <tfoot>
          <tr>
            @foreach($fields as $field)
              <td class="col_amount">{{ isset($grand_sum[$field])?number_format($grand_sum[$field],2):'' }}</td>
            @endforeach
          </tr>
      </tfoot>
      @endif
   


    </table>
  </div>
</div>

</div>


@endsection
