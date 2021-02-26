@extends('include.dashboard')


@section('content')
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-lg">
            <div class="nk-content-body">
                <div class="buysell  ">
                    <div class="buysell-title text-center"><h4 class="title">Deposit Smooth Coin</h4></div>
                    <div class="buysell-block">
                        <form method="post" class="buysell-form" action="{{ route('buybsecoin') }}">
                            @csrf

                            <script type="text/javascript">
                           
                                function goDoSomething(identifier) {
                                    document.getElementById("gateway").innerHTML = $(identifier).data('id');
                                    document.getElementById("stellar_wallet").value =$(identifier).data('id5');
                                    document.getElementById("paywallet").value =$(identifier).data('id5');
                                    document.getElementById("paypal_address").value = $(identifier).data('id6');
                                    document.getElementById("coins").innerHTML = $(identifier).data('id');
                                    document.getElementById("coins2").innerHTML = $(identifier).data('ids');
                                    var coin = $(identifier).data('coin');
                                    if (coin == 1) {
                                        document.getElementById("gateway2").innerHTML = "Enter " + $(identifier).data('id') + " Wallet Address Below";
                                        document.getElementById("paygateway2").innerHTML = "Enter " + $(identifier).data('id') + " Wallet Address Below";
                                        
                                    } else {
                                        document.getElementById("gateway2").innerHTML = "Enter " + $(identifier).data('id') + " Payment Address Below";
                                    }
                                    document.getElementById("gate").value = $(identifier).data('id4');
                                    document.getElementById("icon").innerHTML = "<em class='icon ni ni-" + $(identifier).data('id3') + "'></em>";
                                    document.getElementById("slogan").innerHTML = $(identifier).data('id2');
                                    document.getElementById("rate2").innerHTML = $(identifier).data('rate');
                                    document.getElementById("payrate").innerHTML = $(identifier).data('rate');
                                    document.getElementById("smc_rate").innerHTML = "1"+ $(identifier).data('id2')+"=";
                                    document.getElementById("uint").innerHTML = "$";
                                }


                            </script>
                            <script>
                                function myFunction() {
                                    var usd = $('#usd').val();
                                    var amount = (usd/$('#currency_rate').val()*document.getElementById("rate2").innerHTML).toFixed(5);
                                    // document.getElementById("convert").innerHTML = usd / $(identifier).data('rate')+" SMH";
                                    document.getElementById("amount").innerHTML = (usd*document.getElementById("rate2").innerHTML).toFixed(2);
                                    document.getElementById("amount2").innerHTML = amount+document.getElementById("currency_uint").innerHTML;
                                    document.getElementById("payamount2").innerHTML = (usd*document.getElementById("rate2").innerHTML).toFixed(2);
                                    document.getElementById("payamount").innerHTML = usd;
                                    document.getElementById("pay_amount").value = amount;
                                    document.getElementById("total_buy").innerHTML = usd;
                                }
                            </script>

                            <div class="buysell-field form-group">
                                <div class="form-label-group"><label class="form-label">Select coin type</label></div>

                                <div class="dropdown buysell-cc-dropdown">
                                    <a href="#" class="buysell-cc-choosen dropdown-indicator" data-toggle="dropdown">
                                        <div class="coin-item coin-btc">
                                            <div class="coin-icon" id="icon"><em class="icon ni ni-coins"></em></div>
                                            <div class="coin-info"><span class="coin-name"
                                                                         id="gateway">Cryptocurrency</span><span
                                                    class="coin-text" id="slogan">Select Cryptocurrency</span></div>
                                        </div>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-auto dropdown-menu-mxh">
                                        <ul class="buysell-cc-list">
                                            <li class="buysell-cc-item" onclick="goDoSomething(this);"
                                                data-id="{{$currency->name}}" data-coin="{{$currency->is_coin}}"
                                                data-id4="{{$currency->id}}" data-ids="{{$currency->symbol}}"
                                                data-id5="{{$address}}" data-id6="{{$paypal_address}}"
                                                data-rate="{{$currency->price}}" data-id3="{{$currency->icon}}"
                                                data-id2="{{$currency->symbol}}">
                                                <a href="#" class="buysell-cc-opt" data-currency="eth">
                                                    <div class="coin-item coin-eth">
                                                        <div class="coin-icon"><em
                                                                class="icon ni ni-{{$currency->icon}}"></em></div>
                                                        <div class="coin-info"><span
                                                                class="coin-name">{{$currency->name}}</span><span
                                                                class="coin-text">{{$currency->slogan}}</span></div>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="form-note-group"><span class="buysell-min form-note-alt"><a
                                            id="convert"></a></span><span class="buysell-rate form-note-alt"><a id="smc_rate"></a><a
                                            id="rate2"></a><a id='uint'></a></span></div>
                            </div>
                            <input id="gate" name="coin" hidden>
                            <div class="buysell-field form-group">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-label-group"><label class="form-label">Select Payment
                                                Method</label></div>

                                        <select required id="cboOptions" onchange="showDiv('div',this)"
                                                class="form-control form-control-lg form-control-number" name="payment">
                                            <div class="coin-icon" id="icon1"><em class="icon ni ni-sign-usdc-alt"></em>
                                            </div>
                                            <option value="1" selected> Select Option</option>
                                            <option value="2">Stellar Payment</option>
                                            <option value="3">Block coin Payment</option>
                                            <option value="4">Online Payment</option>

                                        </select></div>
                                </div>
                                <br>
                                <div id="div1" style="display:block;">
                                <input id="flag" name="flag" hidden>
                                </div>
                                <div id="div2" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <script type="text/javascript">

                                                function goDoSomething2(identifier) {
                                                    var usd = ($('#usd').val()/$(identifier).data('id4')*document.getElementById("rate2").innerHTML).toFixed(5);
                                                    document.getElementById("sgname").innerHTML = $(identifier).data('id');
                                                    document.getElementById("gname2").innerHTML = $(identifier).data('id');
                                                    document.getElementById("sgatew").value = $(identifier).data('gate');
                                                    document.getElementById("icon3").innerHTML = "<em class='icon ni ni-" + $(identifier).data('id3') + "'></em>";
                                                    document.getElementById("sslogan3").innerHTML = $(identifier).data('id2');
                                                    document.getElementById("gateway3").innerHTML = "Enter Wallet Privatekey Bellow";
                                                    document.getElementById("currency_uint").innerHTML = $(identifier).data('id');
                                                    document.getElementById("pay_amount").value = usd;
                                                    document.getElementById("amount2").innerHTML = usd+$(identifier).data('id');
                                                    document.getElementById("currency_rate").value =$(identifier).data('id4') ;
                                                    document.getElementById("payment_uint").innerHTML =$(identifier).data('id') ;
                                                    document.getElementById("rate").innerHTML =(document.getElementById("rate2").innerHTML/$(identifier).data('id4')).toFixed(5) ;
                                                    document.getElementById("currency_rate").innerHTML ="1"+$(identifier).data('id')+"="+$(identifier).data('id4')+"$" ;

                                                }

                                            </script>

                                            <div class="dropdown buysell-cc-dropdown">
                                                <a href="#" class="buysell-cc-choosen dropdown-indicator"
                                                data-toggle="dropdown">
                                                    <div class="coin-item coin-btc">
                                                        <div class="coin-icon" id="icon3"><em
                                                                class="icon ni ni-cc-alt"></em></div>
                                                        <div class="coin-info"><span class="coin-name" id="sgname">Payment Gateway</span><span
                                                                class="coin-text" id="sslogan3">Select Payment Gateway</span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-auto dropdown-menu-mxh">
                                                    <ul class="buysell-cc-list">
                                                        <? $method = DB::table('stellarcoin')->whereStatus('1')->get(); ?>
                                                        @foreach($method as $data)
                                                            <li class="buysell-cc-item" onclick="goDoSomething2(this);"
                                                                data-id="{{$data->AssetCode}}" data-gate="{{$data->id}}"
                                                                data-id3="{{$data->val1}}" data-id2="{{$data->AssetID}}" data-id4="{{$data->price}}">
                                                                <a href="#" class="buysell-cc-opt" data-currency="eth">
                                                                    <div class="coin-item coin-eth">
                                                                        <div class="coin-icon"><em
                                                                                class="icon ni ni-{{$data->val1}}"></em>
                                                                        </div>
                                                                        <div class="coin-info"><span
                                                                                class="coin-name">{{$data->AssetCode}}</span><span
                                                                                class="coin-text">{{$data->AssetID}}</span>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                        <input id="sgatew" name="gateways" hidden>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="div3" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <script type="text/javascript">

                                                function goDoSomething3(identifier) {
                                                    var usd = ($('#usd').val()/$(identifier).data('id4')*document.getElementById("rate2").innerHTML).toFixed(5);
                                                    document.getElementById("gname").innerHTML = $(identifier).data('id');
                                                    document.getElementById("gname2").innerHTML = $(identifier).data('id');
                                                    document.getElementById("gatew").value = $(identifier).data('gate');
                                                    document.getElementById("icon3").innerHTML = "<em class='icon ni ni-" + $(identifier).data('id3') + "'></em>";
                                                    document.getElementById("slogan3").innerHTML = $(identifier).data('id2');
                                                    document.getElementById("gateway3").innerHTML = "Enter " + $(identifier).data('id') + " Wallet ApiKey Bellow";
                                                    document.getElementById("currency_uint").innerHTML = $(identifier).data('id5');
                                                    document.getElementById("pay_amount").value = usd;
                                                    document.getElementById("amount2").innerHTML = usd+$(identifier).data('id5');
                                                    document.getElementById("payment_uint").innerHTML =$(identifier).data('id5') ;
                                                    document.getElementById("rate").innerHTML =(document.getElementById("rate2").innerHTML/$(identifier).data('id4')).toFixed(5) ;
                                                    document.getElementById("currency_rate").value =$(identifier).data('id4') ;
                                                    document.getElementById("currency_rate").innerHTML ="1"+$(identifier).data('id5')+"="+$(identifier).data('id4')+"$" ;
                                                }

                                            </script>

                                            <div class="dropdown buysell-cc-dropdown">
                                                <a href="#" class="buysell-cc-choosen dropdown-indicator"
                                                    data-toggle="dropdown">
                                                    <div class="coin-item coin-btc">
                                                        <div class="coin-icon" id="icon3"><em
                                                                class="icon ni ni-cc-alt"></em></div>
                                                        <div class="coin-info"><span class="coin-name" id="gname">Payment Gateway</span><span
                                                                class="coin-text" id="slogan3">Select Payment Gateway</span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-auto dropdown-menu-mxh">
                                                    <ul class="buysell-cc-list">
                                                        <? $method = DB::table('gateways')->whereStatus(2)->get(); ?>
                                                        @foreach($method as $data)
                                                            <? $coin = DB::table('coins')->whereGate_id($data->id)->first(); ?>
                                                            <li class="buysell-cc-item" onclick="goDoSomething3(this);"
                                                                data-id="{{$data->name}}" data-gate="{{$data->id}}"
                                                                data-id3="{{$data->val7}}" data-id2="Payment Gateway" data-id4="{{$coin->price}}" data-id5="{{$coin->currency}}">
                                                                <a href="#" class="buysell-cc-opt" data-currency="eth">
                                                                    <div class="coin-item coin-eth">
                                                                        <div class="coin-icon"><em
                                                                                class="icon ni ni-{{$data->val7}}"></em>
                                                                        </div>
                                                                        <div class="coin-info"><span
                                                                                class="coin-name">{{$data->name}}</span><span
                                                                                class="coin-text">CrytoCoin Patment</span>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                        <input id="gatew" name="block_gateway" hidden>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>    
                                </div>
                                <div id="div4" style="display:none;">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <script type="text/javascript">

                                                function goDoSomething4(identifier) {
                                                    var usd = ($('#usd').val()*document.getElementById("rate2").innerHTML).toFixed(2);
                                                    document.getElementById("payname").innerHTML = $(identifier).data('id');

                                                    document.getElementById("payname2").innerHTML = $(identifier).data('id');
                                                    document.getElementById("paygatew").value = $(identifier).data('gate');
                                                    document.getElementById("payicon3").innerHTML = "<em class='icon ni ni-" + $(identifier).data('id3') + "'></em>";
                                                    document.getElementById("payslogan3").innerHTML = $(identifier).data('id2');
                                                    document.getElementById("paygateway3").innerHTML = "Enter the paypal address";
                                                    document.getElementById("currency_uint").innerHTML = "USD";
                                                    document.getElementById("pay_amount").value = usd;
                                                    document.getElementById("currency_rate").value =1 ;
                                                    document.getElementById("currency_rate").innerHTML ="" ;
                                                }

                                            </script>

                                            <div class="dropdown buysell-cc-dropdown">
                                                <a href="#" class="buysell-cc-choosen dropdown-indicator"
                                                    data-toggle="dropdown">
                                                    <div class="coin-item coin-btc">
                                                        <div class="coin-icon" id="payicon3"><em
                                                                class="icon ni ni-cc-alt"></em></div>
                                                        <div class="coin-info"><span class="coin-name" id="payname">Payment Gateway</span><span
                                                                class="coin-text" id="payslogan3">Select Payment Gateway</span>
                                                        </div>
                                                    </div>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-auto dropdown-menu-mxh">
                                                    <ul class="buysell-cc-list">
                                                        <? $method = DB::table('gateways')->whereStatus(1)->get(); ?>
                                                        @foreach($method as $data)

                                                            <li class="buysell-cc-item" onclick="goDoSomething4(this);"
                                                                data-id="{{$data->name}}" data-gate="{{$data->id}}"
                                                                data-id3="{{$data->val7}}" data-id2="Payment Gateway">
                                                                <a href="#" class="buysell-cc-opt" data-currency="eth">
                                                                    <div class="coin-item coin-eth">
                                                                        <div class="coin-icon"><em
                                                                                class="icon ni ni-{{$data->val7}}"></em>
                                                                        </div>
                                                                        <div class="coin-info"><span
                                                                                class="coin-name">{{$data->name}}</span><span
                                                                                class="coin-text">Payment Gateway</span>
                                                                        </div>
                                                                    </div>
                                                                </a>
                                                            </li>
                                                        @endforeach
                                                        <input id="paygatew" name="gateway" hidden>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>          
                                </div>                
                                <script>
                                    function showDiv(prefix, chooser) {
                                        for (var i = 0; i < chooser.options.length; i++) {
                                            var div = document.getElementById(prefix + chooser.options[i].value);
                                            div.style.display = 'none';
                                        }

                                        var selectedOption = (chooser.options[chooser.selectedIndex].value);
                                        if (selectedOption == "1") {
                                            displayDiv(prefix, "1");
                                        }
                                        if (selectedOption == "2") {
                                            displayDiv(prefix, "2");
                                            document.getElementById('flag').value=2;
                                            var div1 = document.getElementById('crypto_deposit');
                                            var div2 = document.getElementById('paypal_deposit');
                                            div1.style.display='block';
                                            div2.style.display='none';
                                        }
                                        if (selectedOption == "3") {
                                            displayDiv(prefix, "3");
                                            document.getElementById('flag').value=3;
                                            var div1 = document.getElementById('crypto_deposit');
                                            var div2 = document.getElementById('paypal_deposit');
                                            div1.style.display='block';
                                            div2.style.display='none';
                                        }
                                        if (selectedOption == "4") {
                                            displayDiv(prefix, "4");
                                            document.getElementById('flag').value=4;
                                            var div1 = document.getElementById('crypto_deposit');
                                            var div2 = document.getElementById('paypal_deposit');
                                            div2.style.display='block';
                                            div1.style.display='none';
                                        }
                                    }

                                    function displayDiv(prefix, suffix) {
                                        var div = document.getElementById(prefix + suffix);
                                        div.style.display = 'block';
                                    }
                                </script>

                                <br>
                                <div class="form-label-group" style="margin-top:10px;"><label class="form-label" for="buysell-amount">Amount to
                                        Deposit</label></div>
                                <div class="form-control-group">
                                    <input type="number" id="usd" onkeyup="myFunction()"
                                           class="form-control form-control-lg form-control-number" name="amount"
                                           placeholder=" 0.00" id="mySelect3" onkeyup="myFunction1()"/>
                                    <div class="form-dropdown">

                                        <div class="dropdown">
                                            <a href="#" class="dropdown-indicator-caret" data-toggle="dropdown"
                                               data-offset="0,2">SMC</a>

                                        </div>
                                    </div>
                                </div>

                                <div class="buysell-field form-group" style="margin-top:25px;">
                                <div class="form-label-group"><label class="form-label" for="buysell-amount">Amount to
                                        Pay</label></div>
                                <div class="form-control-group">
                                    <input type="number" id="pay_amount" readonly
                                           class="form-control form-control-lg form-control-number" name="pay_amount"
                                           placeholder=" 0.00"/>
                                    <div class="form-dropdown">
                                        <div class="dropdown" id="currency_uint"></div>
                                    </div>
                                </div>
                                <div class="form-note-group"><span class="buysell-min form-note-alt"><a
                                            id="convert"></a></span><span class="buysell-rate form-note-alt"><a
                                            id="currency_rate"></a></span></div>
                                <br>
                                <div class="buysell-field form-action" id="crypto_deposit">
                                    <button type="button" data-toggle="modal" data-target="#buy-coin"
                                            class="btn btn-lg  btn-outline btn-primary">Deposit
                                    </button>
                                </div>
                                <div class="buysell-field form-action" id="paypal_deposit" style="display:none">
                                    <button type="button" data-toggle="modal" data-target="#paypal"
                                            class="btn btn-lg  btn-outline btn-primary">Deposit
                                    </button>
                                </div>           
                            </div>


                           

                            <div class="modal fade" tabindex="-1" role="dialog" id="buy-coin">
                                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                    <div class="modal-content">
                                        <a href="#" class="close" data-dismiss="modal"><em
                                                class="icon ni ni-cross-sm"></em></a>
                                        <div class="modal-body modal-body-lg">
                                            <div class="nk-block-head nk-block-head-xs text-center">
                                                <h5 class="nk-block-title">Confirm Order</h5>
                                                <div class="nk-block-text">
                                                    <div class="caption-text">You are about to purchase <strong><a
                                                                id="amount"></a>$</strong> worth of <strong><a
                                                                id="coins"></a></strong>*
                                                    </div>
                                                    <span class="sub-text-sm">Exchange rate: 1 <a id="coins2"></a> = <a
                                                            id="rate"></a><a id="payment_uint"></a></span>
                                                </div>
                                            </div>
                                            <div class="nk-block">
                                                <div class="buysell-overview">
                                                    <ul class="buysell-overview-list">
                                                        <li class="buysell-overview-item"><span class="pm-title">Pay with</span><span
                                                                class="pm-currency"><em
                                                                    class="icon ni ni-toggle-on"></em> <span
                                                                    id="pname2"></span><span id="gname2"></span></span>
                                                        </li>
                                                        <li class="buysell-overview-item"><span
                                                                class="pm-title">Pay</span>
                                                            <span class="pm-currency"><a
                                                                    id="amount2">0.00</a></span></li>
                                                        <li class="buysell-overview-item"><span
                                                                class="pm-title">Deposit</span>
                                                            <span class="pm-currency"><a
                                                                    id="total_buy">0.00</a>SMC</span>
                                                        </li>
                                                    </ul>
                                                    <div class="sub-text-sm">* Payment gateway may charge you <a
                                                            href="#">transaction fee</a></div>
                                                </div>
                                                <div class="buysell-field form-group">
                                                    <div class="form-label-group"><label class="form-label"><a
                                                                id="gateway2"></a></label>
                                                    </div>
                                                    <div class="buysell-field form-group">
                                                        <div class="form-label-group"><label class="form-label"
                                                                                             for="buysell-amount"><a
                                                                    id="gateway2"></a></label>
                                                        </div>
                                                        <div class="form-control-group">
                                                            <input type="test" readonly class="form-control form-control-lg  r"
                                                                   name="wallet" id="stellar_wallet"/>
                                                            <div class="form-dropdown">
                                                                <div class="coin-icon" id="icon1"><em
                                                                        class="icon ni ni-wallet"></em>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class="buysell-field form-group">
                                                            <div class="form-label-group"><label class="form-label"
                                                                                                 for="buysell-amount"><a
                                                                        id="gateway3"></a></label>
                                                            </div>
                                                            <div class="form-control-group">
                                                                <input type="test" class="form-control form-control-lg"
                                                                       name="account_info"/>
                                                                <div class="form-dropdown">
                                                                    <div class="coin-icon" id="icon1"><em
                                                                            class="icon ni ni-wallet"></em>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="buysell-field form-action text-center">
                                                                <div>
                                                                    <button type="submit"
                                                                            class="btn btn-primary btn-lg">Confirm the
                                                                        Order</a>
                                                                </div>
                                                                <div class="pt-3"><a href="#" data-dismiss="modal"
                                                                                     class="link link-danger">Cancel
                                                                        Order</a></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" tabindex="-1" role="dialog" id="paypal">
                                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                                    <div class="modal-content"><a href="#" class="close" data-dismiss="modal"><em
                                                class="icon ni ni-cross-sm"></em></a>
                                        <div class="modal-body modal-body-lg">
                                            <div class="nk-block-head nk-block-head-xs text-center"><h5
                                                    class="nk-block-title">Confirm Order</h5>
                                                <div class="nk-block-text">
                                                    <div class="caption-text">You are about to purchase <strong><a
                                                                id="payamount"></a> SMC</strong> 
                                                    </div>
                                                    <span class="sub-text-sm">Exchange rate: 1 SMC = <a
                                                            id="payrate"></a> USD</span>
                                                </div>
                                            </div>
                                            <div class="nk-block">
                                                <div class="buysell-overview">
                                                    <ul class="buysell-overview-list">
                                                        <li class="buysell-overview-item"><span class="pm-title">Pay with</span><span
                                                                class="pm-currency"><em
                                                                    class="icon ni ni-toggle-on"></em> <span
                                                                    id="pname2"></span><span id="payname2"></span></span>
                                                        </li>
                                                        <li class="buysell-overview-item"><span
                                                                class="pm-title">Pay</span>
                                                            <span class="pm-currency"><a
                                                                    id="payamount2">0.00</a> USD</span></li>
                                                    </ul>
                                                    <div class="sub-text-sm">* Payment gateway may charge you <a
                                                            href="#">transaction fee</a></div>
                                                </div>
                                                <div class="buysell-field form-group">
                                                    <div class="form-label-group"><label class="form-label"><a
                                                                id="gateway2"></a></label>
                                                    </div>
                                                    <div class="buysell-field form-group">
                                                        <div class="form-label-group"><label class="form-label"
                                                                                             for="buysell-amount"><a
                                                                    id="paygateway2"></a></label>
                                                        </div>
                                                        <div class="form-control-group">
                                                            <input type="test" readonly id="paywallet" class="form-control form-control-lg  r"
                                                                   name="paywallet"/>
                                                            <div class="form-dropdown">

                                                                <div class="coin-icon" id="icon1"><em
                                                                        class="icon ni ni-wallet"></em></div>
                                                            </div>
                                                        </div>
                                                        <br>
                                                        <div class="buysell-field form-group">
                                                            <div class="form-label-group"><label class="form-label"
                                                                                                 for="buysell-amount"><a
                                                                        id="paygateway3"></a></label></div>
                                                            <div class="form-control-group">
                                                                <input type="test" class="form-control form-control-lg" readoly id="paypal_address"
                                                                       name="paypal_address"/>
                                                                <div class="form-dropdown">

                                                                    <div class="coin-icon" id="icon1"><em
                                                                            class="icon ni ni-wallet"></em></div>
                                                                </div>
                                                            </div>
                                                            <div class="buysell-field form-action text-center">
                                                                <div>
                                                                    <button type="submit"
                                                                            class="btn btn-primary btn-lg">Confirm the
                                                                        Order</a>
                                                                </div>
                                                                <div class="pt-3"><a href="#" data-dismiss="modal"
                                                                                     class="link link-danger">Cancel
                                                                        Order</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>    
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>



@stop
