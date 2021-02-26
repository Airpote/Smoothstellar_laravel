@extends('include.admindashboard')

@section('body')
   <div class="page-content">
    <div class="container">
         	<div class="gaps-1x mgb-0-5x d-lg-none d-none d-sm-block"></div>


        <div class="card content-area">
            <div class="card-innr">
                <div class="card-head d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">{{$page_title}}</h4>
                </div>
                <div class="gaps-1-5x"></div>
                <div class="row guttar-vr-30px">

                                        @if(count($market) > 0)
                                                                                     @foreach($market as $data)
                                        <div class="col-xl-4 col-md-6">
                        <div class="stage-item stage-card ">
                            <div class="stage-head">
                                <div class="stage-title">
                                    <h6>Currency
                                                                        <span class="badge btn-primary">{{App\Currency::whereId($data->coin)->first()->name}}</span>

                                                                        <h4>Code: {{$data->code}}</h4>
                                </div>

                                <div class="stage-action">
                                    <a href="#" class="toggle-tigger rotate"><em class="ti ti-more-alt"></em></a>
                                    <div class="toggle-class dropdown-content dropdown-content-top-left">
                                        <ul class="dropdown-list">
                                             @if($data->status == 0)
                                            <li><a href="{{route('actdeal',$data->id)}}">Activate Deal</a></li>
                                            @else
                                            <li><a href="{{route('deactdeal',$data->id)}}">Deactivate Deal</a></li>
                                            @endif

                                            <li><a href="{{route('deldeal',$data->id)}}">Delete Deal</a></li>

                                                                                                                                </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="stage-info stage-info-status">
                                <div class="stage-info-graph">
                                                                        <div class="progress-pie progress-circle">
                                        <input class="knob d-none" data-thickness=".125" data-width="100%" data-fgColor="#2b56f5" data-bgColor="#c8d2e5" value="{{number_format($data->sold/$data->value*100)}}">
                                        <div class="progress-txt"><span class="progress-amount">{{number_format($data->sold/$data->value*100)}} </span>% <span class="progress-status">Sold</span></div>
                                    </div>
                                                                    </div>
                                <div class="stage-info-txt">
                                    <h6>Amount</h6>
                                    <span class="stage-info-total h2">${{number_format($data->value, $basic->decimal)}} </span>
                                    <div class="stage-info-count">Sold <span>{{number_format($data->sold, $basic->decimal)}} </span> USD</div>
                                </div>
                            </div>
                            <div class="stage-info">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="stage-info-txt">
                                            <h6>Seller</h6>
                                            <div class="h2 stage-info-number">{{isset(App\User::whereId($data->user_id)->first()->username) ? App\User::whereId($data->user_id)->first()->username  : 'N/A'}}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="stage-info-txt">
                                            <h6>Country</h6>
                                            <div class="h2 stage-info-number">{{isset(App\User::whereId($data->user_id)->first()->country) ? App\User::whereId($data->user_id)->first()->country  : 'N/A'}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="stage-date">
                                <div class="row">
                                    <div class="col-6">
                                        <h6>Creation Date</h6>
                                        <h5>{!! date('d M, Y', strtotime($data->created_at)) !!} <small>{!! date('h:m A', strtotime($data->created_at)) !!}</small></h5>
                                    </div>
                                    <div class="col-6">
                                        <h6>View Seller</h6>
                                        <a  class="btn btn-primary btn-dim btn-xs btn-icon"  href="{{route('user.single', $data->user_id)}}"><em class="ti ti-user"></em> View Seller</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @else
                     <a href="#" class="btn btn-warning-alt btn-between w-100 mgb-1-5x user-wallet">Please No {{$page_title}} At The Moment,Check Back Later<em class="ti ti-arrow-right"></em></a>

                    @endif



                </div>
                <div class="gaps-0-5x"></div>
            </div>
        </div>
    </div>
</div>
@stop


