@extends('include.admindashboard')

@section('body')


    <script>
        function goBack() {
            window.history.back()
        }
    </script>

    <div class="page-content">
        <div class="container">
            <div class="card content-area">
                <div class="card-innr">
                    <div class="card-head d-flex justify-content-between align-items-center"><h4
                            class="card-title mb-0">Transaction Details</h4><a href="#" onclick="goBack()"
                                                                               class="btn btn-sm btn-auto btn-primary d-sm-block d-none"><em
                                class="fas fa-arrow-left mr-3"></em>Back</a><a href="#" onclick="goBack()"
                                                                               class="btn btn-icon btn-sm btn-primary d-sm-none"><em
                                class="fas fa-arrow-left"></em></a></div>
                    <div class="gaps-1-5x"></div>
                    <div class="data-details d-md-flex">
                        <div class="fake-class"><span class="data-details-title">Tranx Date</span><span
                                class="data-details-info">{{date('d M Y',strtotime($exchange->created_at))}}</span>
                        </div>
                        <div class="fake-class"><span
                                class="data-details-title">Tranx Status</span> @if( $exchange->status ==2 )
                                <span class="badge badge-success">Success</span>
                            @elseif( $exchange->status == -2 )
                                <span class="badge badge-danger">Rejected</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif</div>
                        <div class="fake-class"><span class="data-details-title">Tranx Time</span><span
                                class="data-details-info"> {{$exchange->created_at}}</span></div>
                    </div>
                    <div class="gaps-3x"></div>
                    <h6 class="card-sub-title">Transaction Info</h6>
                    <ul class="data-details-list">
                        <li>
                            <div class="data-details-head">Transaction Type</div>
                            <div class="data-details-des"><strong>Withdraw</strong></div>
                        </li><!-- li -->
                        <li>
                            <div class="data-details-head">Amount</div>
                            <div class="data-details-des"><strong>{{number_format($exchange->amount, 5)}}
                                    USD</strong></div>
                        </li><!-- li -->
                        <li>
                            <div class="data-details-head">Cryptocurrency</div>
                            <div class="data-details-des"><strong>{{$exchange->currency->name}}</strong></div>
                        </li><!-- li -->
                        <li>
                            <div class="data-details-head">Amount In {{$basic->currency}}</div>
                            <div class="data-details-des">
                                <span>{{number_format($exchange->amount, 5)}}{{$basic->currency}}</span>
                                <span></span></div>
                        </li><!-- li -->
                        <li>
                            <div class="data-details-head">Transaction ID</div>
                            <div class="data-details-des"><span>{{$exchange->trx}} </span> <span></span></div>
                        </li>
                   </ul>
                    <div class="gaps-3x"></div>
                    <h6 class="card-sub-title">Transaction Details</h6>
                    <ul class="data-details-list">
                        <li>
                            <div class="data-details-head">Payment method</div>
                            <div class="data-details-des"><span><strong>
                            @if($exchange->remark=='USD')
                                Paypal
                            @else
                                {{$exchange->remark}}
                            @endif
                            </strong>  </span>
                            </div>
                        </li>
                        <li>
                            <div class="data-details-head">Amount Paid</div>
                            <div class="data-details-des"><span><strong>{{number_format($exchange->amountpaid , 5)}} {{$basic->currency}}</strong>  </span>
                            </div>
                        </li>
                        <li>
                            <div class="data-details-head">Trx charge</div>
                            <div class="data-details-des"><span><strong>{{number_format($exchange->charge , 2)}} USD</strong>  </span>
                            </div>
                        </li>
                        <li>
                            <div class="data-details-head">Amount To Receive</div>
                            <div class="data-details-des">
                                <span>{{number_format($exchange->getamo , 5)}}{{$exchange->remark}}</span>
                            </div>
                        </li>
                        <li>
                            <div class="data-details-head">Amount To Credit</div>
                            <div class="data-details-des">
                                <span>${{number_format($exchange->main_amo , 5)}} worth of {{$exchange->remark}}</span>
                            </div>
                        </li>
                        <li>
                            <div class="data-details-head">Payment To</div>
                            <div class="data-details-des">
                                <span>{{$exchange->wallet}}</span>
                            </div>
                        </li>
                        <!-- li --></ul><!-- .data-details --></div>
            </div><!-- .card --></div><!-- .container --></div>
@endsection
@section('script')
@endsection
