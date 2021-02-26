@extends('include.admindashboard')

@section('body')
@php
    $tr =  App\Trx::whereType(0)->sum('amountpaid');
    $pr =  App\Trx::whereType(0)->whereStatus(1)->sum('amountpaid');
    $dr =  App\Trx::whereType(0)->whereStatus(-2)->sum('amountpaid');


    $ts =  App\Trx::whereType(0)->sum('amount');
    $ps =  App\Trx::whereType(0)->whereStatus(1)->sum('amount');
    $ds =  App\Trx::whereType(0)->whereStatus(-2)->sum('amount');

    $ta =  App\Trx::whereType(0)->count();
    $pa =  App\Trx::whereType(0)->whereStatus(1)->count();
    $da =  App\Trx::whereType(0)->whereStatus(-2)->count();

    $currency =  App\Currency::find(1)->symbol;

@endphp

    <div class="page-content">
        <div class="container">
        <div class="row">
                <div class="col-lg-4">
                    <div class="bg-primary token-statistics card card-token height-auto">
                        <a class="card-innr card-token" href="{{route('sell-currency')}}">
                            <div class="token-balance token-balance-with-icon">
                                <div class="token-balance-icon"><em class="h2 color-white ti ti-export"></em></div>
                                <div class="token-balance-text"><h6 class="card-sub-title">Total Withdraw</h6><span
                                        class="lead">{{$ta}} <span>withdraw trades</span></span></div>
                            </div>
                            <div class="token-balance token-balance-s2"><h6 class="card-sub-title">Summary</h6>
                                <ul class="token-balance-list">
                                    <li class="token-balance-sub"><span
                                            class="lead">{{number_format($ts, 2)}} $</span><span
                                            class="sub">Send</span></li>&nbsp;&nbsp;<li class="token-balance-sub">
                                        <span
                                            class="lead">{{number_format($tr, 5)}} {{$currency}}</span><span
                                            class="sub">Receive</span>
                                    </li>
                                </ul>
                            </div>
                        </a>
                    </div>
                </div>


                <div class="col-lg-4">
                    <div class=" token-statistics card  height-auto">
                        <a class=" bg-secondary card-innr card-token" href="{{route('pendingsell-currency')}}">
                            <div class="token-balance token-balance-with-icon">
                                <div class="token-balance-icon"><em class="h2 color-white ti ti-export"></em></div>
                                <div class="token-balance-text"><h6 class="card-sub-title">Pending Withdraw</h6>
                                    <span
                                        class="lead">{{$pa}}  <span>withdraw trades</span></span>
                                </div>
                            </div>
                            <div class="token-balance token-balance-s2"><h6 class="card-sub-title">Summary</h6>
                                <ul class="token-balance-list">
                                    <li class="token-balance-sub"><span
                                            class="lead">{{number_format($ps, 2)}} $</span><span
                                            class="sub">Send</span></li>&nbsp;&nbsp;<li class="token-balance-sub">
                                        <span
                                            class="lead">{{number_format($pr, 5)}} {{$currency}}</span><span
                                            class="sub">Receive</span>
                                    </li>
                                </ul>
                            </div>
                        </a>
                    </div>         
                </div>


                <div class="col-lg-4">
                    <div class="bg-primary token-statistics card  height-auto">
                        <a class="card-innr card-token" href="{{route('declinedsell-currency')}}">
                            <div class="token-balance token-balance-with-icon">
                                <div class="token-balance-icon"><em class="h2 color-white ti ti-export"></em></div>
                                <div class="token-balance-text"><h6 class="card-sub-title">Decline Withdraw</h6><span
                                        class="lead">{{$da}}<span> withdraw trades </span></span>
                                </div>
                            </div>
                            <div class="token-balance token-balance-s2"><h6 class="card-sub-title">Summary</h6>
                                <ul class="token-balance-list">
                                    <li class="token-balance-sub"><span
                                            class="lead">{{number_format($ds, 2)}} $</span><span
                                            class="sub">Send</span></li>&nbsp;&nbsp;<li class="token-balance-sub">
                                        <span
                                            class="lead">{{number_format($dr, 5)}} {{$currency}}</span><span
                                            class="sub">Receive</span></li>
                                </ul>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <br>
            <div class="content-area card">
                <div class="card-innr">
                    <div class="card-head"><h4 class="card-title">Sell Log</h4></div>


                    <table class="data-table dt-filter-init admin-tnx">
                        <thead>
                        <tr class="data-item data-head">
                            <th class="data-col dt-tnxno" style="text-align:center;">User</th>
                            <th class="data-col dt-token" style="text-align:center;">Receive</th>
                            <th class="data-col dt-token" style="text-align:center;">Send</th>
                            <th class="data-col dt-account" style="text-align:center;">Address</th>
                            <th class="data-col dt-account" style="text-align:center;">Payment</th>
                            <th class="data-col dt-type" style="text-align:center;">
                                <div class="dt-type-text" >Status</div>
                            </th>
                            <th class="data-col"></th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach($exchange as $k=>$data)
                            <tr class="data-item">
                                <td class="data-col dt-tnxno" style="text-align:center;">
                                    <div class="d-flex align-items-center">


                                        @if( $data->status ==2 )
                                            <div class="data-state data-state-approved"><span
                                                    class="d-none">Approved</span></div>
                                        @elseif( $data->status == -2 )
                                            <div class="data-state data-state-canceled"><span
                                                    class="d-none">Rejected</span></div>
                                        @else
                                            <div class="data-state data-state-pending"><span
                                                    class="d-none">Pending</span></div>
                                        @endif


                                        <div class="fake-class"><span class="lead tnx-id"><a
                                                    href="{{route('user.single',$data->user->id)}}">
                                            {{$data->user->email}}
                                        </a></span><span class="sub sub-date">{{$data->created_at}}</span></div>
                                    </div>
                                </td>
                                <td class="data-col dt-token" style="text-align:center;"><span
                                        class="lead amount-pay">{{number_format($data->getamo, $basic->decimal)}}</span><span
                                        class="sub sub-symbol">SMC <em class="fas fa-info-circle" data-toggle="tooltip"
                                                                       data-placement="bottom"
                                                                       title="{{$data->currency->name}}"></em></span>
                                </td>
                                <td class="data-col dt-token" style="text-align:center;"><span
                                        class="lead token-amount">{{number_format($data->amountpaid, 5)}}</span><span
                                        class="sub sub-symbol">{{$data->remark}}</span></td>
                                
                                <td class="data-col dt-account" style="text-align:center;"><span
                                        class="lead user-info">{{$data->wallet}}</span></td>

                                <td class="data-col dt-account" style="text-align:center;"><span
                                        class="lead user-info">
                                    @if($data->remark=='USD')
                                        Paypal
                                    @else
                                       {{$data->remark}}
                                    @endif       
                                </span></td>    
                                <td class="data-col dt-type" style="text-align:center;">
                                    @if( $data->status ==2 )
                                        <span
                                            class="dt-type-md badge badge-outline badge-success badge-md">Approved</span>
                                        <span
                                            class="dt-type-sm badge badge-sq badge-outline badge-success badge-md">A</span>
                                    @elseif( $data->status == -2 )
                                        <span
                                            class="dt-type-md badge badge-outline badge-danger badge-md">Declined</span>
                                        <span
                                            class="dt-type-sm badge badge-sq badge-outline badge-danger badge-md">P</span>
                                    @else
                                        <span
                                            class="dt-type-md badge badge-outline badge-warning badge-md">Pending</span>
                                        <span
                                            class="dt-type-sm badge badge-sq badge-outline badge-warning badge-md">P</span>
                                    @endif
                                </td>

                                <td class="data-col text-right">
                                    <div class="relative d-inline-block"><a href="#"
                                                                            class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em
                                                class="ti ti-more-alt"></em></a>
                                        <div class="toggle-class dropdown-content dropdown-content-top-left">
                                            <ul class="dropdown-list">
                                                <li><a href="{{route('sell-info',$data->id)}}"><em
                                                            class="ti ti-eye"></em> View Details</a></li>

                                                @if($data->status == 1)
                                                    <li><a href="{{route('sell.approve',$data->id)}}"><em
                                                                class="ti ti-check"></em> Approve</a></li>
                                                    @if($data->remark=='USD')
                                                    <li><a href="https://www.paypal.com"><em
                                                    class="ti ti-check-box"></em> Paypal Payment</a></li>
                                                    @endif
                                                    <li><a href="{{route('sell.reject',$data->id)}}"><em
                                                                class="ti ti-na"></em> Decline</a></li>
                                                @endif


                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr><!-- .data-item -->
                        @endforeach

                        <!-- .data-item --></tbody>
                    </table>

                </div>
            </div>
        </div>
    </div></div>
@endsection
