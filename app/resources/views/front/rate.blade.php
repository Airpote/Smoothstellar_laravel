@extends('include.frontend')
@section('content')

 <!-- =========== Start of Search ============ -->
        <section class="space d-flex align-items-center">
            <div class="background-holder background--cover">
                <img src="{{asset('frontend/img/image-1.jpg')}}" alt="image" class="background-image-holder">
            </div>
            <!-- end of backgound image -->
            <div class="background-holder bg-color--primary"></div>
            <!-- end of overlay backgound color-->
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-8 mx-auto text-center pt-7 pt-lg-9">
                        <h1 class="h2-font color--white mb-4 mb-lg-6">Our Competitive Rate</h1>

                        <!-- end of form wrapper-->
                        <ul class="m-0 list-unstyled d-flex flex-wrap justify-content-center remove-space--x">
                        @foreach($currency as $key => $data)
                            <li class="mr-1 mb-1"><a href="#" class="btn-sm px-1 font-size--15 color--white bg-color--white-opacity--20">{{$data->name}}</a></li>
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </section>
        <!-- =========== End of Search ============ -->

        <br>
        <br>
<div class="nk-ovm shape-a-sm"></div></div><!-- .header-banner @e --></header><main class="nk-pages"><section class="section bg-white"><div class="container"><div class="row justify-content-around"><div class="col-lg-5 mgb-r"><h3 class="title title title-md">Supported Cryptocurrencies</h3><p>Our list of supported cryptocurrencies</p><table class="table"><tbody>

@foreach($currency as $key => $data)
<tr><td class="table-head">{{$data->name}}</td><td class="table-des">{{number_format($data->price, $basic->decimal)}} USD</td></tr>
@endforeach

</tbody></table></div><div class="col-lg-5"><h3 class="title title title-md">{{$basic->sitename}} Trade Rate</h3><p>Coin Exchange Price + {{$basic->sitename}} Charge</p><table class="table table-bordered"><tbody>
<tr><b><td class="table-head">Coins</td><td class="table-head">We Buy At</td><td class="table-head">We Sell At</td></b></tr>
@foreach($currency as $key => $data)
<tr><td class="table-head">{{$data->name}}</td><td class="table-des">{{number_format($data->sell, $basic->decimal)}} {{$basic->currency}}</td><td class="table-des">{{number_format($data->buy, $basic->decimal)}} USD</td></tr>
@endforeach
</tbody></table></div></div></div></section>



<script>

function myFunction() {
//  var amount = $('#mySelect2').val() ;

//  var price = $("#mySelect option:selected").attr('data-price');
//  var name = $("#mySelect option:selected").attr('data-name');
//  var sell = $("#mySelect option:selected").attr('data-sell');
//  var buy = $("#mySelect option:selected").attr('data-buy');
//  var cur = $("#mySelect option:selected").attr('data-cur');
//  var rate = Math.round(price).toFixed(2);

//  var sell = amount * sell;
//  var buy = amount * buy;
//  var rate = parseFloat(1*amount/price).toFixed(8);

//  document.getElementById("unit").innerHTML = "What you get: " + rate + cur;

//  document.getElementById("buy").innerHTML = "We buy at: USD " + sell;
//  document.getElementById("sell").innerHTML = "We sell at: USD " + buy;
//  var unit = parseFloat(amount / price).toFixed(8);
//  document.getElementById("price").innerHTML = "USD " +      Math.round(rate).toFixed(2);
    var currency = <?php echo json_encode($currency)?>;
    var sel_coin = $('#mySelect').val();
    
    var amount_smc = $('#mySelect2').val();

    if(sel_coin[sel_coin.length-1] == 's')
    {
        sel_coin = sel_coin.substring(0, sel_coin.length - 1);
        sel_coin = parseFloat(sel_coin);
        var buy_smc =  currency[0].buy * amount_smc;
        var sell_smc = currency[0].sell * amount_smc;
        var buy_smc = parseFloat(buy_smc / sel_coin).toFixed(8);
        var sell_smc = parseFloat(sell_smc / sel_coin).toFixed(8);
        var cur = $("#mySelect option:selected").attr('data-cur');
        if(buy_smc != 0)
            document.getElementById("buy").innerHTML = "We buy at: " + cur + " (Stellar) " + buy_smc;
        if(sell_smc != 0)
            document.getElementById("sell").innerHTML = "We sell at: " + cur + " (Stellar) " + sell_smc;
    }

    else if(sel_coin[sel_coin.length-1] == 'n')
    {
        sel_coin = sel_coin.substring(0, sel_coin.length - 1);
        sel_coin = parseFloat(sel_coin);
        var buy_smc =  currency[0].buy * amount_smc;
        var sell_smc = currency[0].sell * amount_smc;
        var buy_smc = parseFloat(buy_smc / sel_coin).toFixed(8);
        var sell_smc = parseFloat(sell_smc / sel_coin).toFixed(8);
        var cur = $("#mySelect option:selected").attr('data-cur');
        if(buy_smc != 0)
            document.getElementById("buy").innerHTML = "We buy at: " + cur + " " + buy_smc;
        if(sell_smc != 0)
            document.getElementById("sell").innerHTML = "We sell at: " + cur + " " + sell_smc;
    }

    else if(sel_coin == 'u')
    {
        sel_coin = sel_coin.substring(0, sel_coin.length - 1);
        sel_coin = parseFloat(sel_coin);
        var buy_smc =  currency[0].buy * amount_smc;
        var sell_smc = currency[0].sell * amount_smc;
        buy_smc = buy_smc.toFixed(8);
        sell_smc = sell_smc.toFixed(8);

        if(buy_smc != 0)
            document.getElementById("buy").innerHTML = "We buy at: USD " + buy_smc;
        if(sell_smc != 0)
            document.getElementById("sell").innerHTML = "We sell at: USD " + sell_smc;
    }
 };
</script>


<section class="section bg-light"><div class="container "><br><h3 class="title title-md">Cryptocurrency Calculator</h3>

<div class="field-item"><label class="field-label">Select Currency</label><div class="field-wrap">
<select onchange="myFunction()" class="form-control" style="padding: 0px;" id="mySelect"><option value="">Please select</option>
@foreach($stellar_coins as $key => $data)
<!-- <option data-cur="{{$data->symbol}}" data-sell="{{$data->sell}}"  data-name="{{$data->name}}"  data-price="{{$data->price}}" data-buy="{{$data->buy}}">{{$data->AssetCode}}(Stellar) </option> -->
<option value="{{$data->price}}s" data-cur="{{$data->AssetCode}}">{{$data->AssetCode}}(Stellar) </option>
@endforeach
@foreach($coins as $key => $data)
<!-- <option data-cur="{{$data->symbol}}" data-sell="{{$data->sell}}"  data-name="{{$data->name}}"  data-price="{{$data->price}}" data-buy="{{$data->buy}}">{{$data->AssetCode}}(Stellar) </option> -->
<option value="{{$data->price}}n" data-cur="{{$data->currency}}">{{$data->currency}} </option>
@endforeach
<option value="u" data-cur="{{$data->currency}}">USD</option>
</select></div> </div>

<div class="field-item"><label class="field-label">Input Amount</label><small><p>Enter amount in <code> SMC </code> </p></small><div class="field-wrap">


<input  id="mySelect2" onkeyup="myFunction()"  type="number" class="form-control" required></div></div>


<p id="buy"></p>
<p id="sell"></p>


</div></section>


</main>
@endsection



