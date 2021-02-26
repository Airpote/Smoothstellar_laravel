
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
                                <h4 class="card-title mb-0">Create Wallet</h4>
                            </div>
                            <div class="gaps-1x"></div>
                            <div class="row guttar-vr-30px">
                                <div class="col-xl-12 col-md-12">
                                <form method="post" class="buysell-form" action="{{ route('admin.save_wallet') }}">
                                    @csrf
                                    
                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="private_key">Private Key</label>
                                        </div>
                                        <div class="form-control-group">
                                            <input type="text" id="private_key" readonly class="form-control form-control-lg" name="private_key" value="{{$private_key}}" placeholder="Please Generate the Private Key" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="form-label-group">
                                            <label class="form-label" for="public_key">Public Key</label>
                                        </div>
                                        <div class="form-control-group">
                                            <input type="text" id="public_key" readonly class="form-control form-control-lg" name="public_key"  value="{{$public_key}}" placeholder="Please Generater the Public Key" />
                                        </div>
                                    </div>

                                    <div class="form-action" style="display: flex; align-items: center;">
                                        <button type="button" id="keygenerate_btn" class="btn btn-lg  btn-outline btn-primary" style="margin-right: 20px;">
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


