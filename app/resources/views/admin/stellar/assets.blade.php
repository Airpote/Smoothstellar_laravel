@extends('include.admindashboard')

@section('body')
    <div class="page-content">
        <div class="container">
            <div class="card content-area">
                <div class="card-innr">
                    <div class="card-head"><h4 class="card-title">Cryptocurrency List</h4></div>
                    <table class="data-table dt-init user-list">
                        <thead>
                        <tr class="data-item data-head">
                            <th class="data-col dt-user">Name</th>
                            <th class="data-col dt-email" style="text-align:center;">Address</th>
                            <th class="data-col dt-token" style="text-align:center;">Rate</th>
                            <th class="data-col dt-token" style="text-align:center;">Balance</th>
                            <th class="data-col dt-status">
                                <div class="dt-status-text">Status</div>
                            </th>
                            <th class="data-col"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <a href="#" data-toggle="modal" data-target="#createcoin"
                           class="btn btn-sm btn-primary btn-outline"><em class="ti ti-trash"></em> Add New</a>
                        @foreach($coins as $k=>$data)
                            <tr class="data-item">
                                <td class="data-col dt-user" ><span class="lead user-name">{{$data->AssetCode }}</span><span
                                        class="sub user-id"></span></td>
                                <td class="data-col dt-email"><span
                                        class="lead sub-email" style="text-align:center;">{{$data->AssetID}}</span></td>
                                <td class="data-col dt-token"><span class="lead lead-btoken" style="text-align:center;">{{$data->price}} USD</span></td>
                                <td class="data-col dt-token"><span class="lead lead-btoken" style="text-align:center;">{{$data->balance}}{{$data->AssetCode}}</span></td>
                                <td class="data-col dt-status">

                                    @if($data->status == 1)
                                        <span
                                            class="dt-status-md badge badge-outline badge-success badge-md">Active</span>
                                        <span
                                            class="dt-status-sm badge badge-sq badge-outline badge-success badge-md">A</span>
                                    @else
                                        <span
                                            class="dt-status-md badge badge-outline badge-danger badge-md">Inactive</span>
                                        <span
                                            class="dt-status-sm badge badge-sq badge-outline badge-danger badge-md">I</span>
                                    @endif

                                </td>
                                <td class="data-col text-right">
                                    <div class="relative d-inline-block"><a href="#"
                                                                            class="btn btn-light-alt btn-xs btn-icon toggle-tigger"><em
                                                class="ti ti-more-alt"></em></a>
                                        <div class="toggle-class dropdown-content dropdown-content-top-left">
                                            <ul class="dropdown-list">
                                                @if($data->AssetCode=='SMC')
                                                <li><a href="{{route('charge_asset')}}"><em
                                                            class="ti ti-check-box"></em> Charge</a></li>
                                                @endif
                                                <li><a href="{{route('activateasset',$data->id)}}"><em
                                                            class="ti ti-check"></em> Activate</a></li>
                                                <li><a href="{{route('deactivateasset',$data->id)}}"><em
                                                            class="ti ti-na"></em> Deactivate</a></li>

                                                <li><a href="{{route('deleteasset',$data->id)}}"><em
                                                            class="ti ti-trash"></em> Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach

                        <!-- .data-item --></tbody>
                    </table>
                </div><!-- .card-innr --></div><!-- .card --></div><!-- .container --></div><!-- .page-content -->





    <!-- .modal-dialog --></div><!-- Modal End -->
    <div class="modal fade" id="createcoin" tabindex="-1">
        <div class="modal-dialog modal-dialog-md modal-dialog-centered">
            <div class="modal-content">
                <div class="popup-body"><h4 class="popup-title">Add New Asset</h4>
                    <p>Fill the form below to add a new stellar asset for the system.  <a
                            href="https://stellar.expert/explorer/public?sort=rating">Here</a> to see list of supported asset.
                    </p>

                    <div class="input-item input-with-label">

                        <form role="form" method="POST" action="{{route('admin.add_asset')}}" name="editForm"
                              enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="form-group col-md-6">
                                    <label class="input-item-label text-exlight">AssetCode:</label>
                                    <div class="input-group">
                                        <input type="text" class="input-bordered" placeholder="Asset Name"
                                               value="{{old('name')}}"
                                               name="assetcode">

                                    </div>

                                </div>
                                <div class="form-group col-md-6">
                                    <label class="input-item-label text-exlight"> Price:</label>
                                    <input type="text" class="input-bordered"  onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                           value="{{old('price')}}"
                                           name="price">


                                </div>

                            </div>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="input-item-label text-exlight">AssetID:</label>
                                    <div class="input-group">

                                        <input type="text" name="assetid" value="{{old('assetid')}}" class="input-bordered" placeholder="Anchor Address">
                                    </div>
                                </div>
                            </div>

                            <!-- TrustLine -->
                            <h4 class="popup-title">Trustline</h4>
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="input-item-label text-exlight">PrivateKey:</label>
                                    <div class="input-group">
                                        <input type="text" name="private_key" value="{{old('privatekey')}}" class="input-bordered" placeholder="Anchor Address">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label class="input-item-label text-exlight">PublicKey:</label>
                                    <div class="input-group">
                                    <? $data = DB::table('basic_settings')->whereId('1')->get(); ?>
                                    <? $public_key=$data[0]->stellar_wallet; ?>
                                        <input type="text" readonly name="public_key" value="{{$public_key}}" class="input-bordered" placeholder="Anchor Address">
                                    </div>
                                </div>
                            </div>


                    </div><!-- .input-item -->
                    <ul class="d-flex flex-wrap align-items-center guttar-30px">
                        <li>
                            <button type="submit" class="btn btn-primary">Add Asset&Trustline</button>
                            </form></li>
                        <li class="pdt-1x pdb-1x"><a href="#" data-dismiss="modal" data-toggle="modal"
                                                     data-target="#pay-online" class="link link-primary">Cancel</a></li>
                    </ul>
                    <div class="gaps-2x"></div>
                    <div class="gaps-1x d-none d-sm-block"></div>
                </div>
            </div><!-- .modal-content --></div><!-- .modal-dialog --></div><!-- Modal End -->
@endsection
