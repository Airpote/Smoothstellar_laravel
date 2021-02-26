@extends('include.dashboard')

@section('content')
    <div class="nk-content nk-content-fluid">
        <div class="container-xl wide-lg">
            <div class="nk-content-body">
                <div class="buysell  ">
                    <div class="buysell-nav text-center">

                    </div>
                    <div class="buysell-title text-center"><h4 class="title">TrustLine</h4></div>
                    <div class="buysell-block">
                        <form method="post" class="trustline-form" action="{{ route('trustline_save') }}">
                            @csrf    
                            <div class="buysell-field form-group">
                                <div class="form-label-group">
                                    <label class="form-label" for="private_key">Private Key</label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text" id="private_key" class="form-control form-control-lg" name="private_key" placeholder="Please Enter the Private Key" />
                                </div>
                            </div>

                            <div class="buysell-field form-action">
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
