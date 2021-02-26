@extends('include.dashboard')
@section('custom-css')
<link rel="stylesheet" type="text/css" href="{{asset('frontend/css/datatable/datatables.min.css')}}">
@endsection
@section('content')


    @php
        $ip = \App\UserLogin::whereUser_id(Auth::user()->id)->latest()->take(1)->first();
         $ncount = \App\Message::whereUser_id(Auth::user()->id)->whereAdmin(1)->whereStatus(0)->count();

         $ipcount = \App\UserLogin::whereUser_id(Auth::user()->id)->count();
          $depocount = \App\Deposit::whereUser_id(Auth::user()->id)->whereStatus(1)->count();
          $ref = \App\User::whereReference(Auth::user()->id)->count();
          $lastref = \App\User::whereReference(Auth::user()->id)->first();
          $depodate = \App\Deposit::whereUser_id(Auth::user()->id)->whereStatus(1)->first();
    @endphp
    @if($ncount > 0)

    @endif
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-lg">
            @if($ncount!=0)
            <div class="alert alert-warning">
                <div class="alert-cta flex-wrap flex-md-nowrap">
                    <div class="alert-text"><p>Hello {{Auth::User()->username}}!, You have <a href="{{route('inbox')}}"
                                                                                              class="link link-primary"> {{$ncount}}
                                unread message(s).</a></p></div>
                    <ul class="alert-actions gx-3 mt-3 mb-1 my-md-0">
                        <li class="order-md-last"><a href="#" class="btn btn-sm btn-warning" type="button" class="close"
                                                     data-dismiss="alert" aria-label="Close">Close</a></li>
                    </ul>
                </div>
            </div>
            @endif
            
            <div class="nk-block-head nk-block-head-sm">
                <div class="nk-block-between">
                    <div class="nk-block-head-content">
                        <h3 class="nk-block-title page-title">Dashboard</h3>
                        <div class="nk-block-des text-soft">
                            <p><?php
                                /* This sets the $time variable to the current hour in the 24 hour clock format */
                                $time = date("H");
                                /* Set the $timezone variable to become the current timezone */
                                $timezone = date("e");
                                /* If the time is less than 1200 hours, show good morning */
                                if ($time < "12") {
                                    echo "Good morning";
                                } else
                                    /* If the time is grater than or equal to 1200 hours, but less than 1700 hours, so good afternoon */
                                    if ($time >= "12" && $time < "17") {
                                        echo "Good afternoon";
                                    } else
                                        /* Should the time be between or equal to 1700 and 1900 hours, show good evening */
                                        if ($time >= "17" && $time < "19") {
                                            echo "Good evening";
                                        } else
                                            /* Finally, show good night if the time is greater than or equal to 1900 hours */
                                            if ($time >= "19") {
                                                echo "Good night";
                                            }
                                ?>, {{Auth::User()->fname}} {{Auth::User()->lname}}
                            </p>
                        </div>
                    </div>
                    <!-- <div class="nk-block-head-content">
                        <div class="toggle-wrap nk-block-tools-toggle">
                            <a href="#" class="btn btn-icon btn-trigger toggle-expand mr-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                            <div class="toggle-expand-content" data-content="pageMenu">
                                <ul class="nk-block-tools g-3">
                                    <li>
                                        <a href="#"  data-toggle="modal" data-target="#modalbonus"  class="btn btn-white btn-dim btn-outline-primary"><em class="icon ni ni-gift"></em><span>Earn Bonus</span></a>
                                    </li>
                                    <li class="nk-block-tools-opt">
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>

            <div class="nk-content-body">
                <br>
                <div class="nk-block">
                    <div class="row gy-gs">
                        <!-- <div class="col-lg-5 col-xl-4">
                            <div class="nk-block">
                                <div class="nk-block-head-xs">
                                    <div class="nk-block-head-content"><h5 class="nk-block-title title">Fiat
                                            Summary</h5></div>
                                </div>
                                <div class="nk-block">
                                    <div class="card card-bordered text-light is-dark h-100">
                                        <div class="card-inner">
                                            <div class="nk-wg7">
                                                <div class="nk-wg7-stats">
                                                    <div class="nk-wg7-title">Available balance
                                                        in {{$basic->currency}}</div>
                                                    <div
                                                        class="number-lg amount">{{$basic->currency_sym}} {{number_format(Auth::user()->balance, $basic->decimal)}} </div>
                                                </div>
                                                <div class="nk-wg7-stats-group">
                                                    <div class="nk-wg7-stats w-50">
                                                        <div class="nk-wg7-title">Pending</div>
                                                        <div
                                                            class="number">{{$basic->currency_sym}} {{number_format($pending, $basic->decimal)}} </div>
                                                    </div>
                                                    <div class="nk-wg7-stats w-50">
                                                        <div class="nk-wg7-title">Earnings</div>
                                                        <div
                                                            class="number">{{$basic->currency_sym}} {{number_format(Auth::user()->bonus, $basic->decimal)}} </div>
                                                    </div>
                                                </div>
                                                <div class="nk-wg7-foot">
                                                    <span
                                                        class="nk-wg7-note">Last deposit at: <span>@if($depodate){{ Carbon\Carbon::parse($depodate->updated_at)->diffForHumans() }} @else
                                                                None Deposit Yet @endif</span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <div class="col-lg-7 col-xl-8">
                            <div class="nk-block">
                                <div class="nk-block-head-xs">
                                    <div class="nk-block-between-md g-2">
                                        <div class="nk-block-head-content"><h6 class="nk-block-title title">Withdraw
                                                Summary</h6></div>

                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-4 col-6" id="withdraw-total">
                                        <div class="card bg-primary-dim">
                                            <div class="nk-wgw sm">
                                                <a class="nk-wgw-inner" href="#">
                                                    <div class="nk-wgw-name">
                                                        <div class="nk-wgw-icon"><em class="icon ni ni-coins"></em>
                                                        </div>
                                                        <h5 class="nk-wgw-title title">Total Sales</h5>
                                                    </div>
                                                    <div class="nk-wgw-balance">
                                                        <div class="amount"><span
                                                                class="currency currency-nio">{{$basic->currency_sym}}</span> {{number_format($sell, $basic->decimal)}}
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6" id="withdraw-pending">
                                        <div class="card bg-warning-dim">
                                            <div class="nk-wgw sm">
                                                <a class="nk-wgw-inner" href="#">
                                                    <div class="nk-wgw-name">
                                                        <div class="nk-wgw-icon"><em
                                                                class="icon ni ni-alert-circle"></em></div>
                                                        <h5 class="nk-wgw-title title">Pending Sales</h5>
                                                    </div>
                                                    <div class="nk-wgw-balance">
                                                        <div class="amount"><span
                                                                class="currency currency-nio">{{$basic->currency_sym}}</span> {{number_format($spend, $basic->decimal)}}
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-12" id="withdraw-decline">
                                        <div class="card bg-danger-dim">
                                            <div class="nk-wgw sm">
                                                <a class="nk-wgw-inner" href="#/crypto/wallet-bitcoin.html">
                                                    <div class="nk-wgw-name">
                                                        <div class="nk-wgw-icon"><em class="icon ni ni-trash"></em>
                                                        </div>
                                                        <h5 class="nk-wgw-title title">Declined Sales</h5>
                                                    </div>
                                                    <div class="nk-wgw-balance">
                                                        <div class="amount"><span
                                                                class="currency currency-nio">{{$basic->currency_sym}}</span> {{number_format($sdecline, $basic->decimal)}}
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="nk-block nk-block-md">
                                <div class="nk-block-head-xs">
                                    <div class="nk-block-between-md g-2">
                                        <div class="nk-block-head-content"><h6 class="nk-block-title title">Deposite
                                                Summary </h6></div>

                                    </div>
                                </div>
                                <div class="row g-2">
                                    <div class="col-sm-4 col-6" id="deposite-total">
                                        <div class="card bg-primary-dim">
                                            <div class="nk-wgw sm">
                                                <a class="nk-wgw-inner" href="#crypto/wallet-bitcoin.html">
                                                    <div class="nk-wgw-name">
                                                        <div class="nk-wgw-icon"><em class="icon ni ni-cart"></em></div>
                                                        <h5 class="nk-wgw-title title">Total Purchase</h5>
                                                    </div>
                                                    <div class="nk-wgw-balance">
                                                        <div class="amount"><span
                                                                class="currency currency-nio">{{$basic->currency_sym}}</span> {{number_format($buy - $bacharge, $basic->decimal)}}
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-6" id="deposite-pending">
                                        <div class="card bg-warning-dim">
                                            <div class="nk-wgw sm">
                                                <a class="nk-wgw-inner" href="#">
                                                    <div class="nk-wgw-name">
                                                        <div class="nk-wgw-icon"><em
                                                                class="icon ni ni-alert-circle"></em></div>
                                                        <h5 class="nk-wgw-title title">Pending Purchase</h5>
                                                    </div>
                                                    <div class="nk-wgw-balance">
                                                        <div class="amount"><span
                                                                class="currency currency-nio">{{$basic->currency_sym}}</span> {{number_format($bpend - $bcharge, $basic->decimal)}}
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4 col-12" id="deposite-declined">
                                        <div class="card bg-danger-dim">
                                            <div class="nk-wgw sm">
                                                <a class="nk-wgw-inner" href="#/crypto/wallet-bitcoin.html">
                                                    <div class="nk-wgw-name">
                                                        <div class="nk-wgw-icon"><em class="icon ni ni-trash"></em>
                                                        </div>
                                                        <h5 class="nk-wgw-title title">Declined Purchase</h5>
                                                    </div>
                                                    <div class="nk-wgw-balance">
                                                        <div class="amount"><span
                                                                class="currency currency-nio">{{$basic->currency_sym}}</span> {{number_format($bdecline - $bdeccharge, $basic->decimal)}}
                                                        </div>
                                                    </div>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <div class="nk-block-head-content"><h6 class="nk-block-title title">Unprocessed Trade </h6></div>
                <br>				
                <table class="table table-striped" id="table-id">
									<thead>
										<tr>
											<th><div class="nk-tb-col"><span>Order No.</span></div></th>
											<th><div class="nk-tb-col tb-col-md"><span>Date</span></div></th>
											<th><div class="nk-tb-col tb-col-lg"><span>SmoothCoin</span></div></th>
											<th><div class="nk-tb-col"><span>Currency</span></div></th>
											<th><div class="nk-tb-col"> Type</div></th>
											<!-- <th><div class="nk-tb-col"><span>Action</span></div></th> -->
										</tr>
									</thead>
									<tbody id="table-id-body">
									</tbody>
								</table>
            </div>
            <br><br>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script>
	var trx_all = <?php echo json_encode($trx_all)?>;
	var basic = <?php echo json_encode($basic)?>;
	var user_id = <?php echo json_encode(Auth::user()->id) ?>;
</script>
<script src="{{asset('frontend/js/datatable/datatables.min.js')}}"></script>
<script src="{{asset('frontend/js/datatable/dataTables.bootstrap4.min.js')}}"></script>
<script src="{{asset('frontend/js/datatable/dataTables.buttons.min.js')}}"></script>
<script src="{{asset('frontend/js/user-dashboard.js')}}"></script>
@endsection
