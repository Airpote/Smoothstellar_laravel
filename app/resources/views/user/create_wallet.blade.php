@extends('include.dashboard')

@section('content')
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-lg">
            <div class="nk-content-body" style="min-height:400px;">
                <div class="buysell  ">
                    <div class="buysell-title text-center"><h4 class="title">Add Payment Account</h4></div>
                    <br>
                    <div class="buysell-block">

                        <form method="post" class="wallet-form" action="{{ route('create_wallet') }}">
                            @csrf
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-label-group"><label class="form-label">Select Payment
                                            Account</label></div>
                                    <select required id="cboOptions" onchange="showDiv(this)"
                                            class="form-control form-control-lg" sstyle="height:45px;" name="payment">
                                        <div class="coin-icon" id="icon1"><em class="icon ni ni-sign-usdc-alt"></em>
                                        </div>
                                        <option value="3" selected>Select Option</option>
                                        <option value="1" >Add Stellar Wallet</option>
                                        <option value="2">Add Paypal Account</option>
                                        
                                    </select>
                                </div>
                            </div>
                                <br><br>
                            <div id="paypals" style="display:none;">
                                <div class="buysell-field form-group">
                                    <div class="form-label-group">
                                        <label class="form-label">Paypal Address</label>
                                    </div>
                                    <div class="form-control-group">
                                        <input type="text" id="paypal" class="form-control form-control-lg" name="paypal" placeholder="Enter the Paypal Address" />
                                    </div>
                                </div>
                                <div class="buysell-field form-action" style="display: flex; align-items: center;">
                                    <button type="submit" class="btn btn-lg  btn-outline btn-danger">
                                        Add
                                    </button>
                                </div>
                            </div> 
                            <div id="stellar" style="display:none;">            
                                <div class="buysell-field form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="private_key">Private Key</label>
                                    </div>
                                    <div class="form-control-group">
                                        <input type="text" id="private_key" class="form-control form-control-lg" name="private_key" placeholder="Enter the Private Key" />
                                    </div>
                                </div>

                                <div class="buysell-field form-group">
                                    <div class="form-label-group">
                                        <label class="form-label" for="public_key">Public Key</label>
                                    </div>
                                    <div class="form-control-group">
                                        <input type="text" id="public_key" class="form-control form-control-lg" name="public_key" placeholder="Enter the Public Key" />
                                    </div>
                                </div>
                                <div class="buysell-field form-action" style="display: flex; align-items: center;">
                                    <button type="button" id="keygenerate_btn" class="btn btn-lg  btn-outline btn-primary">
                                        Generate
                                    </button>
                                    <button type="submit" class="btn btn-lg  btn-outline btn-danger">
                                        Add
                                    </button>
                                </div>
                            <div>
                            
                            <script>
                                function showDiv(chooser) {
                                    var selectedOption = (chooser.options[chooser.selectedIndex].value);
                                    if (selectedOption == "1") {
                                        var div1=document.getElementById('stellar');
                                        var div2=document.getElementById('paypals');
                                        div2.style.display='none';
                                        div1.style.display='block';  
                                    }
                                    if (selectedOption == "2") {
                                        var div1=document.getElementById('stellar');
                                        var div2=document.getElementById('paypals');
                                        div1.style.display='none';
                                        div2.style.display='block';
                                    }
                                    if (selectedOption == "3") {
                                        var div1=document.getElementById('stellar');
                                        var div2=document.getElementById('paypals');
                                        div1.style.display='none';
                                        div2.style.display='none';
                                    }
                                }
                            </script>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('scripts')
<script>
    var base_url = "{{ url('/') }}";

    $("#keygenerate_btn").on('click', function() {
        $.ajax({
            url: base_url + '/user/api_wallet',
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
