@extends('layouts.front')

@section('content')

    <section class="app-content">
        <div class="container new-parcel">  
        
        <div class="row row-black-button">
            <a class="black-button" href="{{ route('trackingForm') }}">Tracking Shipments</a>
            <a class="black-button" href="https://www.orientalexp.com/">To Homepage</a>
        </div>                     

                @if (session('status') === 'The phone number is exist in Draft!')
                    <div class="alert alert-danger">
                        {{ session('status') }}
                    </div>
                @elseif (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif

                @php
                if (session('data_parcel')){
                    $data_parcel = json_decode(session('data_parcel'));
                }
                @endphp
                
                @if (session('no_phone'))
                    <div class="alert alert-danger">
                        {{ session('no_phone') }}
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

                                        {!! Form::open(['url'=>route('philIndCheckPhone'), 'class'=>'form-horizontal check-phone','method' => 'POST']) !!}

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {!! Form::text('shipper_phone',old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                                    {!! Form::hidden('quantity_sender')!!}
                                                    {!! Form::hidden('quantity_recipient')!!}
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

                                        {!! Form::open(['url'=>route('philIndCheckPhone'), 'class'=>'form-horizontal check-phone','method' => 'POST']) !!}

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    {!! Form::text('shipper_phone',old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                                    {!! Form::hidden('quantity_sender')!!}
                                                    {!! Form::hidden('quantity_recipient')!!}
                                                    {!! Form::hidden('draft','draft')!!}
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
                
                @if (session('add_parcel'))
                    <script type="text/javascript">
                        var addParcel = '<?=session("add_parcel")?>'
                    </script>
                @else
                    <script type="text/javascript">
                        var addParcel = ''
                    </script>
                @endif

                @if (session('phone_exist'))
                    <script type="text/javascript">
                        var phoneExist = '<?=session("phone_exist")?>';
                        var phoneNumber = '<?=session("phone_number")?>';
                    </script>
                @else
                    <script type="text/javascript">
                        var phoneExist = ''
                    </script>
                @endif 

                {!! Form::open(['url'=>route('philIndParcelAdd'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'POST']) !!}

                {!! Form::hidden('phone_exist_checked',isset($data_parcel->phone_exist_checked) ? $data_parcel->phone_exist_checked : '')!!}
                {!! Form::hidden('status_box','')!!}
                {!! Form::hidden('comments_2','')!!}

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
                            {!! Form::text('shipper_name',isset($data_parcel->shipper_name) ? $data_parcel->shipper_name : old('shipper_name'),['class' => 'form-control', 'required'])!!}
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
                                
                {!! Form::button('SEND',['class'=>'btn btn-default','type'=>'submit','style'=>'width:200px']) !!}
                {!! Form::close() !!}
               
                <!-- временное -->
                <br>
                <div>
                    <button class="btn btn-default" style="width:200px"><a style="color:#000" href="{{ route('philIndParcelForm') }}">ADD SHIPMENT</a></button>
                </div>
                <br>
                <div>
                    <button class="btn btn-default" style="width:200px"><a style="color:#000" href="https://www.orientalexp.com/">TO HOMEPAGE</a></button>
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
            if (phone.value.length < 10 || phone.value.length > 24) {
                alert('The number of characters in the standard phone must be from 10 to 24 !');
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