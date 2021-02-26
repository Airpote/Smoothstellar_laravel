
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
                                <h4 class="card-title mb-0">Create New Coin</h4>
                            </div>
                            <div class="gaps-1x"></div>
                            <div class="row guttar-vr-30px">
                                <div class="col-xl-12 col-md-12">
                                <form method="post" class="coin-form" action="{{ route('admin.save_coin') }}">
                                    @csrf    
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="coin_name">Coin Name</label>
                                        </div>
                                        <div class="form-control-group">
                                            <input type="text" id="coin_name" class="form-control form-control-lg" name="coin_name" value="{{$coin}}" placeholder="Please Enter the Coin Name" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="coin_name">Coin Amount</label>
                                        </div>
                                        <div class="form-control-group">
                                            <input type="number" id="coin_maount" class="form-control form-control-lg" name="coin_amount" value="{{$amount}}" placeholder="Please Enter the Coin Amount" />
                                        </div>
                                    </div>

                                    <div class="form-action">
                                        <button type="submit" class="btn btn-lg  btn-outline btn-danger">
                                            Create
                                        </button>
                                    </div>
                                </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@stop



