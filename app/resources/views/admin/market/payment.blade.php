@extends('include.admindashboard')

@section('body')
 <div class="page-content">
    <div class="container">
 	<div class="gaps-1x mgb-0-5x d-lg-none d-none d-sm-block"></div>


        <div class="card content-area content-area-mh table-responsive"">
            <div class="card-innr">
                <div class="card-head has-aside">
                    <h4 class="card-title">{{$page_title}}</h4>
                    <div class="card-opt">
                        <ul class="btn-grp btn-grp-block guttar-20px">
                            <li>

                            </li>
                        </ul>
                    </div>
                </div>


                <table class="data-table admin-tnxd >
                    <thead>
                        <tr class="data-item data-head">
                            <th class="data-col tnx-status dt-tnxno">Tranx ID</th>
                            <th class="data-col dt-token">Currency</th>
                            <th class="data-col dt-amount">Buyer</th>
                            <th class="data-col dt-usd-token">Seller</th>
                            <th class="data-col pm-gateway dt-account">Amount</th>
                            <th class="data-col pm-gateway dt-account">Payment Gateway</th>
                            <th class="data-col dt-type tnx-type">Action</th>
                            <th class="data-col"></th>
                        </tr>
                    </thead>
                    <tbody>
                                                       @if(count($market) > 0)
                                         @foreach($market as $data)
                                                                        <tr class="data-item" id="tnx-item-1">
                            <td class="data-col dt-tnxno">
                                <div class="d-flex align-items-center">
                                @if($data->paid < 1)
                                    <div id="ds-1" data-toggle="tooltip" data-placement="top" title="Approved" class="data-state data-state-pending">
                                @elseif($data->paid == 1)
                                    <div id="ds-1" data-toggle="tooltip" data-placement="top" title="Approved" class="data-state data-state-approved">
                                @elseif($data->paid == 2)
                                    <div id="ds-1" data-toggle="tooltip" data-placement="top" title="Approved" class="data-state data-state-canceled">
                                @endif
                                        <span class="d-none">Approved</span>
                                    </div>
                                    <div class="fake-class">
                                        <span class="lead tnx-id">{{$data->trx}}</span>
                                        <span class="sub sub-date">{!! date('D. M d, Y', strtotime($data->created_at)) !!}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="data-col dt-token">
                                <span class="lead token-amount">
                                                                {{App\Currency::whereId($data->coin)->first()->name}}
                                </span>
                                <span class="sub sub-symbol">{{App\Currency::whereId($data->coin)->first()->symbol}}</span>
                            </td>
                            <td class="data-col dt-amount">

                                <span class="lead amount-pay">
                                                                 {{isset(App\User::whereId($data->buyer)->first()->username) ? App\User::whereId($data->buyer)->first()->username  : 'N/A'}}
                                                                </span>
                                <span class="sub sub-symbol">{{isset(App\User::whereId($data->buyer)->first()->country) ? App\User::whereId($data->buyer)->first()->country  : 'N/A'}}   </span>
                                                            </td>
                            <td class="data-col dt-usd-token">

                                <span class="lead amount-pay">
                                                                 {{isset(App\User::whereId($data->seller)->first()->username) ? App\User::whereId($data->seller)->first()->username  : 'N/A'}}
                                                                </span>
                                <span class="sub sub-symbol">{{isset(App\User::whereId($data->seller)->first()->country) ? App\User::whereId($data->seller)->first()->country  : 'N/A'}}   </span>
                                                            </td>
                            <td class="data-col dt-account">

                                <span class="lead amount-pay">
                                                                 ${{number_format($data->amount, $basic->decimal)}}
                                                                </span>
                                <span class="sub sub-symbol">{{$basic->currency_sym}}{{number_format($data->amount*$basic->rate, $basic->decimal)}}  </span>
                                                            </td>
                            <td class="data-col data-type">

                                <span class="lead amount-pay">
                                                                 {{isset(App\Gateway::whereId($data->gateway)->first()->name) ? App\Gateway::whereId($data->gateway)->first()->name : 'N/A'}}
                                                                </span>
                                <span class="sub sub-symbol">Online </span>
                                                            </td>
                            </td>

                            <td class="data-col text-right">

                                <div class="relative d-inline-block">
                                    <a href="{{route('viewdeal',$data->id)}}" class="btn btn-light-alt btn-xs btn-icon  r"><em class="ti ti-more-alt"></em></a>

                                </div>
                                                            </td>
                        </tr>
                        @endforeach
                        @else
                                               <a href="#" class="btn btn-warning-alt btn-between w-100 mgb-1-5x user-wallet">There is no {{$page_title}} at the moment<em class="ti ti-arrow-right"></em></a>

                        @endif





                                            </tbody>
                </table><br>
{{$market->links()}}
                            </div>
        </div>
    </div>
</div>
@endsection
