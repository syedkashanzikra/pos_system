@extends('layouts.master')
@section('main-content')
@section('page-css')
<link rel="stylesheet" href="{{asset('assets/styles/vendor/datatables.min.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/nprogress.css')}}">
<link rel="stylesheet" href="{{asset('assets/styles/vendor/daterangepicker.css')}}">
@endsection

<div class="breadcrumb">
    <h1>{{ __('translate.product_report') }}</h1>
</div>

<div class="separator-breadcrumb border-top"></div>

<div id="product_report">
    <!-- Summary Cards -->
    <div class="row mb-3" id="report_totals_row">
        <div class="col-md-4">
            <div class="card bg-light p-3">
                <h6 class="mb-1">{{ __('translate.Total_Products') }}</h6>
                <h5 class="mb-0 text-dark fw-bold">{{ $totalProducts }}</h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light p-3">
                <h6 class="mb-1">{{ __('Total_Quantity') }}</h6>
                <h5 class="mb-0 text-success fw-bold">{{ number_format($totalQuantity, 2) }}</h5>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-light p-3">
                <h6 class="mb-1">{{ __('Total_Value_At_Cost') }}</h6>
                <h5 class="mb-0 text-danger fw-bold">
                    @if($symbol_placement == 'before')
                        {{$currency}} {{ number_format($totalValueAtCost, 2) }}
                    @else
                        {{ number_format($totalValueAtCost, 2) }} {{$currency}}
                    @endif
                </h5>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="form-group col-md-6">
            <label for="warehouse_id">{{ __('translate.warehouse') }}
            </label>
            <select name="warehouse_id" id="warehouse_id" class="form-control">
                <option value="0">{{ __('translate.All') }}</option>
                @foreach ($warehouses as $warehouse)
                <option value="{{$warehouse->id}}">{{$warehouse->name}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                        <div class="text-end mb-3">
                                <a id="reportrange">
                                    <i class="fa fa-calendar"></i>&nbsp;
                                    <span></span> <i class="fa fa-caret-down"></i>
                                </a>
                            </div>
                    <div class="table-responsive">
                        <table id="products_table" class="display table table-hover">
                            <thead>
                                <tr>
                                    <th>{{ __('translate.Code') }}</th>
                                    <th>{{ __('translate.Name') }}</th>
                                    <th>{{ __('translate.Product_Type') }}</th>
                                    <th>{{ __('translate.Category') }}</th>
                                    <th>{{ __('translate.warehouse') }}</th>
                                    <th>{{ __('translate.Current_Stock') }}</th>
                                    <th>{{ __('translate.Qty_sold') }}</th>
                                    <th>{{ __('translate.Amount_Sold') }}</th>
                                    <th>{{ __('translate.Qty_purchased') }}</th>
                                    <th>{{ __('translate.Amount_purchased') }}</th>
                                </tr>
                            </thead>
                            <tbody class="height_140">
                            </tbody>
                            <tfoot>
                                    <tr>
                                        <th>{{ __('translate.Total') }} :</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('page-js')

<script src="{{asset('assets/js/vendor/datatables.min.js')}}"></script>
<script src="{{asset('assets/js/nprogress.js')}}"></script>
<script src="{{asset('assets/js/daterangepicker.min.js')}}"></script>


<script type="text/javascript">
    $(function() {
        "use strict";

        $(document).ready(function () {
          //init datatable
          products_datatable();
        });

        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
            var start_date = picker.startDate.format('YYYY-MM-DD');
            var end_date = picker.endDate.format('YYYY-MM-DD');
            let warehouse_id = $('#warehouse_id').val();

            $('#products_table').DataTable().destroy();
            products_datatable(start_date, end_date, warehouse_id);

        });
    
        var start = moment().subtract(10, 'year');
        var end = moment().add(10, 'year');
    
        function cb(start, end) {
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    
        $('#reportrange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                '{{ __('translate.Since_launch') }}' : [moment().subtract(10, 'year'), moment().add(10, 'year')],
                '{{ __('translate.Today') }}': [moment(), moment()],
                '{{ __('translate.Yesterday') }}' : [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '{{ __('translate.Last_7_Days') }}' : [moment().subtract(6, 'days'), moment()],
                '{{ __('translate.Last_30_Days') }}': [moment().subtract(29, 'days') , moment()],
                '{{ __('translate.This_Month') }}': [moment().startOf('month'), moment().endOf('month')],
                '{{ __('translate.Last_Month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, cb);
    
        cb(start, end);


            //Get Data
            function products_datatable(start_date ='', end_date ='', warehouse_id =''){
                var $symbol_placement = @json($symbol_placement);
                var table = $('#products_table').DataTable({
                    processing: true,
                    serverSide: true,
                    "order": [[ 0, "asc" ]],
                    'columnDefs': [
                  {
                      "orderable": false,
                      'targets': [2,3,4,5,6,7,8,9]
                  },
                ],

                    ajax: {
                        url: "{{ route('get_report_product_datatable') }}",
                        data: {
                            start_date: start_date === null?'':start_date,
                            end_date: end_date === null?'':end_date,
                            warehouse_id: warehouse_id == '0'?'':warehouse_id,

                            "_token": "{{ csrf_token()}}"
                        },
                        dataType: "json",
                        type:"post"
                    },
                    columns: [
                        {data: 'code'},
                        {data: 'name'},
                        {data: 'type'},
                        {data: 'category'},
                        {data: 'warehouse_name'},
                        {data: 'current_stock'},
                        {data: 'sold_qty'},
                        {data: 'sold_amount'},
                        {data: 'purchased_qty'},
                        {data: 'purchased_amount'},
                    ],

                    footerCallback: function (row, data, start, end, display) {
                        var api = this.api();
            
                        // Remove the formatting to get integer data for summation
                        var intVal = function (i) {
                            return typeof i === 'string' ? i.replace(/[\$, ]/g, '') * 1 : typeof i === 'number' ? i : 0;
                        };
            
                        var current_stock = api.column(5, { page: 'current' }).data().reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        // Update the other column indices
                        var sold_qty = api.column(6, { page: 'current' }).data().reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        var sold_amount = api.column(7, { page: 'current' }).data().reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                        var purchased_qty = api.column(8, { page: 'current' }).data().reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                     
                        var purchased_amount = api.column(9, { page: 'current' }).data().reduce(function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0);

                      
                        // Update footer
                        var numberRenderer = $.fn.dataTable.render.number(',', '.', 2).display;

                        $(api.column(5).footer()).html(numberRenderer(current_stock));
                        $(api.column(6).footer()).html(numberRenderer(sold_qty));

                        if ($symbol_placement == 'before') {
                            $(api.column(7).footer()).html('{{$currency}}' +' '+ numberRenderer(sold_amount));
                            $(api.column(9).footer()).html('{{$currency}}' +' '+ numberRenderer(purchased_amount));
                        }else{
                            $(api.column(7).footer()).html(numberRenderer(sold_amount) +' ' +'{{$currency}}');
                            $(api.column(9).footer()).html(numberRenderer(purchased_amount) +' ' +'{{$currency}}');
                        }

                        $(api.column(8).footer()).html(numberRenderer(purchased_qty));
                       
                    },
                
                    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
                    dom: "<'row'<'col-sm-12 col-md-7'lB><'col-sm-12 col-md-5 p-0'f>>rtip",
                    oLanguage: {
                        sEmptyTable: "{{ __('datatable.sEmptyTable') }}",
                        sInfo: "{{ __('datatable.sInfo') }}",
                        sInfoEmpty: "{{ __('datatable.sInfoEmpty') }}",
                        sInfoFiltered: "{{ __('datatable.sInfoFiltered') }}",
                        sInfoThousands: "{{ __('datatable.sInfoThousands') }}",
                        sLengthMenu: "_MENU_", 
                        sLoadingRecords: "{{ __('datatable.sLoadingRecords') }}",
                        sProcessing: "{{ __('datatable.sProcessing') }}",
                        sSearch: "",
                        sSearchPlaceholder: "{{ __('datatable.sSearchPlaceholder') }}",
                        oPaginate: {
                            sFirst: "{{ __('datatable.oPaginate.sFirst') }}",
                            sLast: "{{ __('datatable.oPaginate.sLast') }}",
                            sNext: "{{ __('datatable.oPaginate.sNext') }}",
                            sPrevious: "{{ __('datatable.oPaginate.sPrevious') }}",
                        },
                        oAria: {
                            sSortAscending: "{{ __('datatable.oAria.sSortAscending') }}",
                            sSortDescending: "{{ __('datatable.oAria.sSortDescending') }}",
                        }
                    },
                    buttons: [
                    {
                        extend: 'collection',
                        text: "{{ __('translate.EXPORT') }}",
                        buttons: [
                          {
                            extend: 'print',
                            text: 'Print',
                            exportOptions: {
                                columns: ':visible:Not(.not_show)',
                                rows: ':visible'
                            },
                            title: function(){
                                return 'Report Products';
                            },
                          },
                          {
                            extend: 'pdf',
                            text: 'Pdf',
                            exportOptions: {
                                columns: ':visible:Not(.not_show)',
                                rows: ':visible'
                            },
                            title: function(){
                              return 'Report Products';
                            },
                           
                        },
                          {
                            extend: 'excel',
                            text: 'Excel',
                            exportOptions: {
                                columns: ':visible:Not(.not_show)',
                                rows: ':visible'
                            },
                            title: function(){
                              return 'Report Products';
                            },
                          },
                          {
                            extend: 'csv',
                            text: 'Csv',
                            exportOptions: {
                                columns: ':visible:Not(.not_show)',
                                rows: ':visible'
                            },
                            title: function(){
                              return 'Report Products';
                            },
                          },
                        ]
                }],
                });
            }

            // Submit Filter
           $('#warehouse_id').on('change' , function (e) {

               var date_range = $('#reportrange > span').text();
               var dates = date_range.split(" - ");
               var start = dates[0];
               var end = dates[1];
               var start_date = moment(dates[0]).format("YYYY-MM-DD");
               var end_date = moment(dates[1]).format("YYYY-MM-DD");
               
               let warehouse_id = $('#warehouse_id').val();
       
               $('#products_table').DataTable().destroy();
               products_datatable(start_date, end_date, warehouse_id);

           });

      
    });
</script>



@endsection