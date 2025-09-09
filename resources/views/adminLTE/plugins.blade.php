
<!-- jQuery -->
<script src="{{URL::asset('plugins/jquery/jquery.min.js')}}" type='text/javascript'></script>
<!-- Bootstrap -->

<script src="{{URL::asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}" type='text/javascript'></script>
<!-- AdminLTE -->
<script src="{{URL::asset('dist/js/adminlte.js')}}" type='text/javascript'></script>
<script src="{{URL::asset('plugins/sweetalert2/sweetalert2.min.js')}}" type='text/javascript'></script>
<!-- Toastr -->
<script src="{{URL::asset('plugins/toastr/toastr.min.js')}}" type='text/javascript'></script>


<!--DATA TABLE-->
<script src="{{URL::asset('plugins/datatables/jquery.dataTables.min.js')}}" type='text/javascript'></script>

<script src="{{URL::asset('plugins/datatables/jquery.dataTables.min.js')}}"></script>
<script src="{{URL::asset('plugins/datatables-bs4/js/dataTables.bootstrap4.min.js')}}"></script>

<script src="{{URL::asset('plugins/datatables-buttons/js/dataTables.buttons.min.js')}}"></script>
<script src="{{URL::asset('plugins/datatables-buttons/js/buttons.bootstrap4.min.js')}}"></script>
<script src="{{URL::asset('plugins/jszip/jszip.min.js')}}"></script>
<script src="{{URL::asset('plugins/pdfmake/pdfmake.min.js')}}"></script>
<script src="{{URL::asset('plugins/pdfmake/vfs_fonts.js')}}"></script>
<script src="{{URL::asset('plugins/datatables-buttons/js/buttons.html5.min.js')}}"></script>
<script src="{{URL::asset('plugins/datatables-buttons/js/buttons.print.min.js')}}"></script>
<script src="{{URL::asset('plugins/datatables-buttons/js/buttons.colVis.min.js')}}"></script>

<!-- OPTIONAL SCRIPTS -->
<script src="{{URL::asset('plugins/chart.js/Chart.min.js')}}" type='text/javascript'></script>

<script src="{{URL::asset('plugins/chart.js/Chart-label.js')}}" type='text/javascript'></script>
<script src="{{URL::asset('plugins/chart.js/Chart-data-labels.js')}}" type='text/javascript'></script>

<!-- <script src="{{URL::asset('dist/js/demo.js')}}" type='text/javascript'></script> -->

<!-- Select2 -->
<script src="{{ URL::asset('plugins/select2/js/select2.full.min.js') }}"></script>


<script src="{{ URL::asset('plugins/moment/moment.min.js') }}"></script>
<script src="{{ URL::asset('plugins/inputmask/jquery.inputmask.min.js') }}"></script>

<script src="{{ URL::asset('plugins/daterangepicker/daterangepicker.js') }}"></script>

<!-- Tempusdominus Bootstrap 4 -->
<script src="{{ URL::asset('plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js') }}"></script>
<!-- Bootstrap Switch -->
<script src="{{ URL::asset('plugins/bootstrap-switch/js/bootstrap-switch.min.js') }}"></script>
<!-- BS-Stepper -->
<script src="{{ URL::asset('plugins/bs-stepper/js/bs-stepper.min.js') }}"></script>
<!-- dropzonejs -->
<script src="{{ URL::asset('plugins/dropzone/min/dropzone.min.js') }}"></script>

<!-- Context Menu -->

<script src="{{ URL::asset('plugins/context-menu/js/context-menu.js') }}"></script>
<script src="{{ URL::asset('plugins/context-menu/js/context-menu-ui-position.js') }}"></script>





<!-- lightbox -->
<script src="{{ URL::asset('plugins/ekko-lightbox/ekko-lightbox.js') }}"></script>

<!-- Table -->
<!-- <script src="https://cdn.jsdelivr.net/gh/rainabba/jquery-table2excel@1.1.0/dist/jquery.table2excel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tableexport-xls-bold-headers@1.0.3/tableexport-xls-bold-headers.js"></script> -->
    <script>
    function animate_element(obj,is_show){
        
        if(is_show == 1){
            obj.hide();
            obj.show(300);
        }else{
            obj.hide(300, function(){ obj.remove(); });
        }
    }
        $(function () {
            if($(".datepicker").length > 0) {               
                $('.datepicker').daterangepicker({               
                    singleDatePicker: true,
                    showDropdowns: true,
                    minDate: '1900-01-01',
                    format:'YYYY-MM-DD'
                })
            }
            $.ajaxSetup({
              headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             }
         });
        // var url = window.location;
        var url = window.location.origin + window.location.pathname;
        // for single sidebar menu
        $('ul.nav-sidebar a').filter(function () {
            return this.href == url;
        }).addClass('active');

        // for sidebar menu and treeview
        $('ul.nav-treeview a').filter(function () {

            return this.href == url;
        }).parentsUntil(".nav-sidebar > .nav-treeview")
        .css({'display': 'block'})
        .addClass('menu-open').prev('a')
        .addClass('active');
    });
        function show_loader(){
            $('.main-header').css('z-index','-1')
            $( '.loader' ).slideDown( 300, function() {
             // Animation complete.
         });
        }
        function hide_loader(){
            $( '.loader' ).slideUp( 300, function() {
                $('.main-header').css('z-index','')
             // Animation complete.
         });
        }
        $(window).resize(function() { 
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            $('.dataTables_scrollBody .head_rem').remove();
        // console.log('zoom')
    });

        function number_format(number){
            number = parseFloat(number);
            var result =  number.toFixed(2).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
            return result;
        }
        function decode_number_format(number){
        var result=number.replace(/\,/g,''); // 1125, but a string, so convert it to number
        result=parseFloat(result,10);
        return result;
    }
</script>

<script type="text/javascript">
(function($) {

  var Defaults = $.fn.select2.amd.require('select2/defaults');

  $.extend(Defaults.defaults, {
    dropdownPosition: 'auto'
  });
  var AttachBody = $.fn.select2.amd.require('select2/dropdown/attachBody');

  var _positionDropdown = AttachBody.prototype._positionDropdown;
  AttachBody.prototype._positionDropdown = function() {

    var $window = $(window);

    var isCurrentlyAbove = this.$dropdown.hasClass('select2-dropdown--above');
    var isCurrentlyBelow = this.$dropdown.hasClass('select2-dropdown--below');

    var newDirection = null;

    var offset = this.$container.offset();
    var zoom = 0.9;


    offset.top= offset.top/(1/zoom);
    offset.left= offset.left/(1/zoom);
    // offset.top= offset.left/1.11;
    // console.log({offset})
    // var offset = { top: this.$container[0].offsetTop, left: this.$container[0].offsetLeft };
    offset.bottom = offset.top + this.$container.outerHeight(false);

    var container = {
        height: this.$container.outerHeight(false)
    };

    container.top = offset.top;
    container.bottom = offset.top + container.height;

    var dropdown = {
      height: this.$dropdown.outerHeight(false)
    };

    var viewport = {
      top: $window.scrollTop(),
      bottom: $window.scrollTop() + $window.height()
    };

    var enoughRoomAbove = viewport.top < (offset.top - dropdown.height);
    var enoughRoomBelow = viewport.bottom > (offset.bottom + dropdown.height);

    var css = {
      left: offset.left,
      top: container.bottom
    };

    // Determine what the parent element is to use for calciulating the offset
    var $offsetParent = this.$dropdownParent;

    // For statically positoned elements, we need to get the element
    // that is determining the offset
    if ($offsetParent.css('position') === 'static') {
      $offsetParent = $offsetParent.offsetParent();
    }

    var parentOffset = $offsetParent.offset();

    css.top -= parentOffset.top
    css.left -= parentOffset.left;
    
    var dropdownPositionOption = this.options.get('dropdownPosition');

    if (dropdownPositionOption === 'above' || dropdownPositionOption === 'below') {
      newDirection = dropdownPositionOption;
    } else {

      if (!isCurrentlyAbove && !isCurrentlyBelow) {
        newDirection = 'below';
      }

      if (!enoughRoomBelow && enoughRoomAbove && !isCurrentlyAbove) {
        newDirection = 'above';
      }else if (!enoughRoomAbove && enoughRoomBelow && isCurrentlyAbove) {
        newDirection = 'below';
      }
    }

    if (newDirection == 'above' ||
    (isCurrentlyAbove && newDirection !== 'below')) {
        css.top = container.top - parentOffset.top - dropdown.height;
    }

    var cont_width = this.$container.width() / (1/zoom);

    if (newDirection != null) {

      this.$dropdown
        .removeClass('select2-dropdown--below select2-dropdown--above')
        .addClass('select2-dropdown--' + newDirection)

      this.$container
        .removeClass('select2-container--below select2-container--above')
        .addClass('select2-container--' + newDirection)
    }
    // css.color = 'red';
    css.width = cont_width;

    this.$dropdownContainer.css(css);
    $('.select2-dropdown').css('width',cont_width);


  };
})(window.jQuery);
    function roundoff(num,decimals) {
        decimals = decimals ?? 2;

        return Math.round(num*Math.pow(10, decimals)) / Math.pow(10, decimals);
    }

</script>

<script type="text/javascript">
    
    function change_header_color(background_color){
        $('.nav-custom-color').css({
            background: background_color
        });
        $('.nav-custom-color').find('a').css({'color': 'white'})
    }

    function change_sidebar_color(background_color){
        $('.cust-sidebar-color').css({
            background: background_color
        });
        $('.nav-custom-color').find('a').css({'color': 'white'})
    }

</script>
@stack('scripts')
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<!-- <script src="dist/js/pages/dashboard3.js"></script> -->
