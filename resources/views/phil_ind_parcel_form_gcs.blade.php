@extends('layouts.front_gcs')

@section('content')

    <section class="app-content">
        <div class="container new-parcel">  
        
        <div class="row row-black-button">
            <a class="yellow-button" href="{{ route('trackingFormGcs') }}">Tracking Shipments</a>
            <a class="yellow-button" href="https://www.gcs-deliveries.com/">To Homepage</a>
        </div>                     

            @php
            $get_data = request()->all();
            @endphp
            
            @if (isset($get_data['message']))
            <div class="alert alert-success">
                {{ $get_data['message'] }}
            </div>
            @endif

            @php
            if (request()->all()){
                $data_parcel = request();
            }
            @endphp

            @if (isset($get_data['err_message']))
            <div class="alert alert-danger">
                {{ $get_data['err_message'] }}
            </div>
            @endif
                
                <h1>SHIPMENT ORDER</h1>

                <div class="form-group">
                    <label class="control-label">I have already sent shipments with your company</label>
                    <input type="checkbox" name="not_first_order" style="width:20px;height:20px">
                </div>

                <div class="form-group">
                    <label class="control-label">I need empty boxes</label>
                    <input type="checkbox" name="need_box" value="need">
                </div>                

                <div class="container">
                    <!-- Modal -->

                    <div class="modal fade" id="philIndParcel" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p class="question">Enter the same sender data that was on the previous order?</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="philIndAnswer(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Yes</button>
                                    <button type="button" onclick="philIndAnswer(this)" class="btn btn-danger pull-left no" data-dismiss="modal">No</button>

                                        {!! Form::open(['url'=>'https://forward-post.com/api/forward-check-phone-eng', 'class'=>'form-horizontal check-phone','method' => 'GET']) !!}

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {!! Form::text('shipper_phone',old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                                    {!! Form::hidden('quantity_sender')!!}
                                                    {!! Form::hidden('quantity_recipient')!!}
                                                    {!! Form::hidden('url_name','https://www.forward-post.com/phil-ind-parcel-form-gcs')!!}
                                                </div>
                                                <div class="col-md-6">
                                                    {!! Form::button('Send',['class'=>'btn btn-success','type'=>'submit']) !!}
                                                </div>
                                            </div>
                                        </div>                                        
                                                                                
                                        {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade" id="phoneExist" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p class="question">{{ session('phone_exist') }}</p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" onclick="philIndAnswer2(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Yes</button>
                                    <button type="button" onclick="philIndAnswer2(this)" class="btn btn-danger pull-left no" data-dismiss="modal">No</button>

                                        {!! Form::open(['url'=>'https://forward-post.com/api/forward-check-phone-eng', 'class'=>'form-horizontal check-phone','method' => 'GET']) !!}

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {!! Form::text('shipper_phone',old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                                    {!! Form::hidden('quantity_sender')!!}
                                                    {!! Form::hidden('quantity_recipient')!!}
                                                    {!! Form::hidden('draft','draft')!!}
                                                    {!! Form::hidden('url_name','https://www.forward-post.com/phil-ind-parcel-form-gcs')!!}
                                                </div>
                                                <div class="col-md-6">
                                                    {!! Form::button('Send',['class'=>'btn btn-success','type'=>'submit']) !!}
                                                </div>
                                            </div>
                                        </div>                                        
                                                                                
                                        {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                    </div>
                
                </div> 

                <!-- Link to open the modal -->
                <p><a href="#philIndParcel" class="btn btn-success eng-modal" data-toggle="modal">Add shipment</a></p>
                <a href="#phoneExist" class="btn btn-success eng-modal-2" data-toggle="modal"></a>
                
                @if (isset($get_data['add_parcel']))
                <script type="text/javascript">
                    var addParcel = '<?=$get_data["add_parcel"]?>'
                </script>
                @else
                <script type="text/javascript">
                    var addParcel = ''
                </script>
                @endif

                @if (isset($get_data['phone_exist']))
                <script type="text/javascript">
                    var phoneExist = '<?=$get_data["phone_exist"]?>';
                    var phoneNumber = '<?=$get_data["phone_number"]?>';
                </script>
                @else
                <script type="text/javascript">
                    var phoneExist = ''
                </script>
                @endif 

                {!! Form::open(['url'=>'https://forward-post.com/api/forward-parcel-form-eng','onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'GET']) !!}

                {!! Form::hidden('phone_exist_checked',isset($data_parcel->phone_exist_checked) ? $data_parcel->phone_exist_checked : '')!!}
                {!! Form::hidden('status_box','')!!}
                {!! Form::hidden('comments_2','')!!}
                {!! Form::hidden('short_order','short_order') !!}
                {!! Form::hidden('url_name','https://www.forward-post.com/phil-ind-parcel-form-gcs')!!}

                <h3>CUSTOMER INFORMATION</h3>                

                <div class="form-group">
                    <div class="row">
                        {!! Form::label('shipper_city','City*',['class' => 'col-md-12 control-label']) !!}
                        <div class="col-md-12">
                            {!! Form::select('shipper_city', $israel_cities, isset($data_parcel->shipper_city) ? $data_parcel->shipper_city : '',['class' => 'form-control']) !!}
                            <h6>In case your settlement is missing in the list choose the nearest city</h6>
                        </div>                        
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        {!! Form::label('shipper_name','Full name*',['class' => 'col-md-12 control-label']) !!}
                        <div class="col-md-12">
                            {!! Form::text('shipper_name',isset($data_parcel->first_name) ? $data_parcel->first_name.' '.$data_parcel->last_name : old('shipper_name'),['class' => 'form-control', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        {!! Form::label('standard_phone','Phone number*',['class' => 'col-md-12 control-label']) !!}
                        <div class="col-md-12">
                            {!! Form::text('standard_phone',isset($data_parcel->standard_phone) ? $data_parcel->standard_phone : old('standard_phone'),['class' => 'form-control standard-phone', 'required'])!!}
                        </div>                       
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        {!! Form::label('shipper_address','Address',['class' => 'col-md-12 control-label']) !!}
                        <div class="col-md-12">
                            {!! Form::text('shipper_address',isset($data_parcel->shipper_address) ? $data_parcel->shipper_address : old('shipper_address'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        {!! Form::label('consignee_country','Destination County',['class' => 'col-md-6 control-label'])   !!}
                        <div class="col-md-6">
                            {!! Form::select('consignee_country', $to_country, isset($data_parcel->consignee_country) ? $data_parcel->consignee_country: '',['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
                
                {!! Form::hidden('item_1','')!!}
                {!! Form::hidden('q_item_1','')!!}
                {!! Form::hidden('consignee_address','')!!}
                {!! Form::hidden('parcels_qty','')!!}
                                
                {!! Form::button('SEND',['class'=>'yellow-button','type'=>'submit','style'=>'width:200px']) !!}
                {!! Form::close() !!}
               
                <!-- временное -->
                <br>
                <div>
                    <button class="yellow-button" style="width:200px"><a style="color:#000" href="{{ route('parcelFormEngGcs') }}">ADD SHIPMENT</a></button>
                </div>
                <br>
                <div>
                    <button class="yellow-button" style="width:200px"><a style="color:#000" href="https://www.gcs-deliveries.com/">TO HOMEPAGE</a></button>
                </div>         
            
        </div>           
    </section><!-- /.app-content -->

    <script>

        function сonfirmSigned(event)
        {
            event.preventDefault();
            const form = event.target;

            /*if (!document.querySelector('[name="shipper_country"]').value){
                alert('The country field is required !');
                return false;
            }*/
            if (!document.querySelector('[name="consignee_country"]').value){
                alert('The country field is required !');
                return false;
            }

            const phone = document.querySelector('[name="standard_phone"]'); 
            if (phone.value.length !== 13 && countryCode === "+972") {
                alert('The number of characters in the standard phone must be 13 !');
                return false;
            }
            if (phone.value.length !== 14 && countryCode === "+49") {
                alert('The number of characters in the standard phone must be 14 !');
                return false;
            }

            /*Parcel content items*/
            const parcelsQty = document.querySelector('[name="parcels_qty"]');
            if (!parcelsQty.value) parcelsQty.value = 1;
            let contentFull = false;

            if(!contentFull){
                document.querySelector('[name="item_1"]').value = "Empty";
                document.querySelector('[name="q_item_1"]').value = "0";
            } 

            /*Boxes info*/
            let boxString = ''; 

            if ($('[name="need_box"]').prop('checked')) {
                boxString = 'I need boxes';
            } 
            else{
                boxString = 'I do not need boxes';
            }     
            
            $('[name="comments_2"]').val(boxString);
            
            form.submit();
        }

    </script>

@endsection