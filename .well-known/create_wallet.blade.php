@extends('include.dashboard')

@section('content')
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-lg">
            <div class="nk-content-body">
                <div class="buysell  ">
                    <div class="buysell-nav text-center">

                    </div>
                    <div class="buysell-title text-center"><h4 class="title">Create My Stellar Wallet</h4></div>
                    <div class="buysell-block">

                        <form method="post" class="wallet-form" action="{{ route('create_wallet') }}">
                            @csrf
                            
                            <div class="buysell-field form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="private_key">Private Key</label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text" id="private_key" readonly class="form-control form-control-lg" name="private_key" placeholder="Please Generate the Private Key" />
                                </div>
                            </div>

                            <div class="buysell-field form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="public_key">Private Key</label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text" id="public_key" readonly class="form-control form-control-lg" name="public_key" placeholder="Please Generater the Public Key" />
                                </div>
                            </div>

                            <div class="buysell-field form-action" style="display: flex; align-items: center;">
                                <button type="button" id="keygenerate_btn" class="btn btn-lg  btn-outline btn-primary">
                                    Generate
                                </button>
                                <button type="submit" class="btn btn-lg  btn-outline btn-danger">
                                    Save
                                </button>
                            </div>
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
