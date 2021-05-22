@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
<h1 class="m-0 text-dark">Dashboard</h1>
@stop

@section('content')
<!-- Small boxes (Stat box) -->
<div class="row">
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-info">
      <div class="inner">
        <h3>{{$stats->today_orders->count()}}</h3>

        <p>Today Orders</p>
      </div>
      <div class="icon">
        <i class="fa fa-shopping-bag"></i>
      </div>
      <a href="/restaurant-owner/live-orders" class="small-box-footer">More info <i
          class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-success">
      <div class="inner">
        <h3><sup style="font-size: 20px">{{setting('currency_symbol')}}</sup>{{$stats->today_orders->sum('total')}}</h3>

        <p>Today Earnings</p>
      </div>
      <div class="icon">
        <i class="fa fa-money-bill"></i>
      </div>
      <a href="restaurant-owner/live-orders" class="small-box-footer">More info <i
          class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-warning">
      <div class="inner">
        <h3>{{$stats->all_orders->count()}}</h3>

        <p>Total Orders</p>
      </div>
      <div class="icon">
        <i class="fa fa-shopping-bag"></i>
      </div>
      <a href="restaurant-owner/orders" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
  <div class="col-lg-3 col-6">
    <!-- small box -->
    <div class="small-box bg-danger">
      <div class="inner">
        <h3><sup style="font-size: 20px">{{setting('currency_symbol')}}</sup>{{$stats->all_orders->sum('total')}}</h3>

        <p>Total Earnings</p>
      </div>
      <div class="icon">
        <i class="fa fa-money-bill"></i>
      </div>
      <a href="restaurant-owner/orders" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
    </div>
  </div>
  <!-- ./col -->
</div>
<!-- /.row -->
<!-- Main row -->
<div class="row">
  <div class="col-lg-12">
    <div class="card">
      <div class="card-header border-0">
        <div class="d-flex justify-content-between">
          <h3 class="card-title">Last 7 days orders</h3>
          <a href="javascript:void(0);">View Report</a>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex">
          {{-- <p class="d-flex flex-column">
            <span class="text-bold text-lg">{{$stats->last_seven_days_orders->count()}}</span>
            <span>Orders Over Time</span>
          </p> --}}
          <p class="d-flex flex-column">
            <span class="text-success">
              <i class="fas fa-shopping-bag"></i> {{$stats->last_seven_days_orders_count}} Orders
            </span>
            <span class="text-muted">Since last week</span>
          </p>
        </div>
        <!-- /.d-flex -->

        <div class="position-relative mb-4">
          <canvas id="visitors-chart" height="200"></canvas>
        </div>

        <div class="d-flex flex-row justify-content-end">
          <span class="mr-2">
            <i class="fas fa-square text-primary"></i> This Week
          </span>
        </div>
      </div>
    </div>
    <!-- /.card -->
  </div>
</div>
@stop

@section('js')
<script>
  $(function () {

'use strict'

var ticksStyle = {
    fontColor: '#495057',
    fontStyle: 'bold'
  }

  var mode      = 'index'
  var intersect = true

function ordinal_suffix_of(i) {
    var j = i % 10,
        k = i % 100;
    if (j == 1 && k != 11) {
        return i + "st";
    }
    if (j == 2 && k != 12) {
        return i + "nd";
    }
    if (j == 3 && k != 13) {
        return i + "rd";
    }
    return i + "th";
}


var dates = [];
  for (var i=0; i<7; i++) {
      var d = new Date();
      d.setDate(d.getDate() - i);
      dates.push( ordinal_suffix_of(d.getDate()) )
  }
    
var $visitorsChart = $('#visitors-chart')

  var visitorsChart  = new Chart($visitorsChart, {
    data   : {
      labels  : dates.reverse(),
      datasets: [{
        type                : 'line',
        data                : {{json_encode($stats->seven_days_record)}},
        backgroundColor     : 'transparent',
        borderColor         : '#007bff',
        pointBorderColor    : '#007bff',
        pointBackgroundColor: '#007bff',
        fill                : false
      }]
    },
    options: {
      maintainAspectRatio: false,
      tooltips           : {
        mode     : mode,
        intersect: intersect
      },
      hover              : {
        mode     : mode,
        intersect: intersect
      },
      legend             : {
        display: false
      },
      scales             : {
        yAxes: [{
          // display: false,
          gridLines: {
            display      : true,
            lineWidth    : '4px',
            color        : 'rgba(0, 0, 0, .2)',
            zeroLineColor: 'transparent'
          },
          ticks    : $.extend({
            beginAtZero : true,
            suggestedMax: {{max($stats->seven_days_record)+1}}
          }, ticksStyle)
        }],
        xAxes: [{
          display  : true,
          gridLines: {
            display: false
          },
          ticks    : ticksStyle
        }]
      }
    }
  })

})    
</script>
@stop