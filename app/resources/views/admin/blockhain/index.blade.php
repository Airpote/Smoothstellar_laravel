@extends('include.admindashboard')

@section('body')
   <div class="page-content">
        <div class="container">
            <div class="row">

                <div class="main-content col-lg-12">
                                        <div class="content-area user-account-dashboard">



        <div class="card content-area col-lg-12">
            <div class="card-innr">
                <div class="card-head d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{$coin->name}} Wallet</h4>

                </div>
                                <div class="gaps-1x"></div>
                <div class="row guttar-vr-30px">
@if(count($wallet) > 0)
@foreach($wallet as $data)


                                        <div class="col-xl-4 col-md-6">
                        <div class="payment-card">
    <div class="payment-head">
        <div class="payment-logo"><br>
            <img src="{{asset('dash-assets/images')}}/{{$coin->logo}}" style="width:192.5px;height:60px;" alt="{{$coin->logo}}">
        </div>
 <div class="payment-action">
            <a href="#" class="toggle-tigger rotate"><em class="ti ti-more-alt"></em></a>
            <div class="toggle-class dropdown-content dropdown-content-top-left">
                <ul class="dropdown-list">
                    <li><a href="{{route('admin.viewwallet',$data->address)}}">Open Wallet</a></li>
                    @if($data->status == 1)
                    <li><a class="quick-action" href="{{route('admin.blockwallet',$data->address)}}" data-name="bank">Block Wallet</a></li>
                    @elseif($data->status == 0)
                    <li><a class="quick-action" href="{{route('admin.unblockwallet',$data->address)}}" data-name="bank">Unblock Wallet</a></li>
                   @endif
                </ul>
            </div>
        </div>
    </div>
    <div class="payment-body">
        <h5 class="payment-title">Wallet Adress:</h5>
        <p class="payment-text">{{$data->address}}</p>

        @if($data->status == 1)
        <div class="payment-status payment-status-connect">
            <span class="payment-status-icon"><em class="ti ti-wallet"></em></span>
            <div class="payment-status-text">{{$data->balance}} {{$coin->currency}}</div>
        </div>

        @else
        <div class="payment-status payment-status-disabled">
            <span class="payment-status-icon"><em class="ti ti-na"></em></span>
            <div class="payment-status-text">Currently Blocked</div>
        </div>
        @endif

            </div>

</div>
                    </div>


                    </div>
@endforeach
@else
                                        <div class="col-xl-4 col-md-6">
                        <div class="payment-card">
    <div class="payment-head">
        <div class="payment-logo">
        <br>
            <img src="{{asset('dash-assets/images')}}/{{$coin->logo}}" style="width:192.5px;height:60px;" alt="{{$coin->logo}}">
        </div>

    </div>
    <div class="payment-body">
        <h5 class="payment-title">No Wallet</h5>
        <p class="payment-text">You Currently Don't Have Any {{$coin->name}} Wallet At The Moment</p>
                <div class="payment-status payment-status-disabled">
            <span class="payment-status-icon"><em class="ti ti-na"></em></span>
            <div class="payment-status-text">Currently Inactive</div>
        </div>
            </div>

</div>
                    </div>



                    </div>
@endif
                                    </div>
                <div class="gaps-0x"></div>
            </div>
        </div>
    </div>
</div>



                </div>




            </div>
        </div>
    </div>
@stop


