
@extends('include.admindashboard')

@section('body')
<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="main-content col-lg-12">
                <div class="content-area user-account-dashboard">
                    <div class="card content-area col-lg-12">
                        <div class="card-innr" style="min-height:300px;">
                            <div class="card-head d-flex justify-content-between align-items-center">
                                <h4 class="card-title mb-0">Add Payment Account</h4>
                            </div>
                            <div class="gaps-1x"></div>
                            <div class="row guttar-vr-30px">
                                <div class="col-xl-12 col-md-12">
                                    <div class="form-label-group">
                                        <label class="form-label">Select Payment Account</label>
                                    </div>
                                    <form method="post" class="buysell-form" action="{{ route('admin.savestellarwallet') }}">
                                        @csrf
                                        <select required id="cboOptions" onchange="showDiv(this)" class="form-control form-control-lg" style="height:45px;" name="payment">
                                            <div class="coin-icon" id="icon1"><em class="icon ni ni-sign-usdc-alt"></em>
                                            </div>
                                            <option value="3" selected>Select Option</option>
                                            <option value="1" >Add Stellar Wallet</option>
                                            <option value="2">Add Paypal Account</option>
                                            <option value="4">Add Block Wallet</option>
                                        </select>
                                        <br>
                                        <div id="stellar" style="display:none;">
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="private_key">Private Key (You must remember this key and store.)</label>
                                                </div>
                                                <div class="form-control-group">
                                                    <input type="text" id="private_key" class="form-control form-control-lg" name="private_key" placeholder="Please Generate the Private Key" value="{{$private_key}}"/>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="public_key">Public Key</label>
                                                </div>
                                                <div class="form-control-group">
                                                    <input type="text" id="public_key" class="form-control form-control-lg" name="public_key" value="{{$public_key}}" placeholder="Please Enter the Public Key" />
                                                </div>
                                            </div>

                                            <div class="form-action" style="display: flex; align-items: center;">
                                                <button type="button" id="keygenerate_btn" class="btn btn-lg  btn-outline btn-primary" style="margin-right: 20px;">
                                                    Create
                                                </button>
                                                    <button type="submit" class="btn btn-lg  btn-outline btn-danger" id="add" style="margin-right: 20px;">
                                                        Add
                                                    </button>
                                            </div>
                                        </div>
                                        <div id="paypals" style="display:none;">
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="private_key">Paypal ID</label>
                                                </div>
                                                <div class="form-control-group">
                                                    <input type="text" id="paypals" class="form-control form-control-lg" value="{{$paypal_id}}" name="paypal_address" placeholder="Please Paypal ID" />
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="private_key">Paypal Password</label>
                                                </div>
                                                <div class="form-control-group">
                                                    <input type="text" id="paypals" class="form-control form-control-lg" name="paypal_secret" placeholder="Please Paypal Password" value="{{$paypal_secret}}"/>
                                                </div>
                                            </div>
                                            <div class="form-action" style="display: flex; align-items: center;">
                                                <button type="submit" class="btn btn-lg  btn-outline btn-danger" id="add" style="margin-right: 20px;">
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                        <div id="block" style="display:none;">
                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="private_key">Bitcoin Address</label>
                                                </div>
                                                <div class="form-control-group">
                                                    <input type="text" id="bit_key" class="form-control form-control-lg" name="bit_key" value="{{$bit}}" placeholder="Please Enter bitcoin address" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="public_key">Litecoin Address</label>
                                                </div>
                                                <div class="form-control-group">
                                                    <input type="text" id="lite_key" class="form-control form-control-lg" name="lite_key" value="{{$lite}}" placeholder="Please Enter litecoin address" />
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="form-label-group">
                                                    <label class="form-label" for="public_key">Dogecoin Address</label>
                                                </div>
                                                <div class="form-control-group">
                                                    <input type="text" id="doge_key" class="form-control form-control-lg" name="doge_key" value="{{$doge}}" placeholder="Please Enter dogecoin address" />
                                                </div>
                                            </div>

                                            <div class="form-action" style="display: flex; align-items: center;">
                                                <button type="submit" class="btn btn-lg  btn-outline btn-danger" id="add" style="margin-right: 20px;">
                                                    Add
                                                </button>
                                            </div>
                                        </div>
                                        <script>
                                            function showDiv(chooser) {
                                                var selectedOption = (chooser.options[chooser.selectedIndex].value);
                                                if (selectedOption == "1") {
                                                    var div1=document.getElementById('stellar');
                                                    var div2=document.getElementById('paypals');
                                                    var div3=document.getElementById('block');
                                                    div2.style.display='none';
                                                    div1.style.display='block';
                                                    div3.style.display='none'; 
                                                }
                                                if (selectedOption == "2") {
                                                    var div1=document.getElementById('stellar');
                                                    var div2=document.getElementById('paypals');
                                                    var div3=document.getElementById('block');
                                                    div1.style.display='none';
                                                    div2.style.display='block';
                                                    div3.style.display='none';
                                                }
                                                if (selectedOption == "3") {
                                                    var div1=document.getElementById('stellar');
                                                    var div2=document.getElementById('paypals');
                                                    var div3=document.getElementById('block');
                                                    div1.style.display='none';
                                                    div2.style.display='none';
                                                    div3.style.display='none';
                                                }
                                                if (selectedOption == "4") {
                                                    var div1=document.getElementById('stellar');
                                                    var div2=document.getElementById('paypals');
                                                    var div3=document.getElementById('block');
                                                    div1.style.display='none';
                                                    div2.style.display='none';
                                                    div3.style.display='block';
                                                }
                                            }
                                        </script>
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

@section('js')
<script>
    var base_url = "{{ url('/') }}";

    $("#keygenerate_btn").on('click', function() {
        $.ajax({
            url: base_url + '/admin/api_wallet',
            type: 'POST',
            data: {
                _token: $("[name='_token']").val(),
            },
            dataType: 'json',
            success: function(response) {
                $("#private_key").val(response.private_key);
                $("#public_key").val(response.public_key);
            }
        })
    })
</script>
@endsection


