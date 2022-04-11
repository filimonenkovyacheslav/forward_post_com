@extends('layouts.front')

@section('content')

    <section class="app-content page-bg">
        <div class="container">                       
            <div class="parcel-form">

                @if (session('status-error'))
                <div class="alert alert-danger">
                    {{ session('status-error') }}
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

                <div class="form-group">
                    <label class="control-label">My parcel has a tracking number</label>
                    <input onclick="clickRadio(this)" type="radio" name="has_tracking" value="yes">
                </div>

                <div class="form-group">
                    <label class="control-label">My parcel does not have a tracking number</label>
                    <input onclick="clickRadio(this)" type="radio" name="has_tracking" value="no">
                </div>              

                <div class="container">
                    <!-- Modal -->
                    <div class="modal fade" id="addEngParcel" role="dialog">
                        <div class="modal-dialog">

                            <!-- Modal content-->
                            <div class="modal-content">
                                
                                {!! Form::open(['url'=>route('engCheckTrackingPhone'), 'class'=>'form-horizontal check-tracking-phone','method' => 'POST']) !!}
                                
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                </div>
                                
                                <div class="modal-body">
                                    <div class="form-group tracking-main">
                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! Form::label('tracking_main','Enter your tracking number',['class' => 'control-label']) !!}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! Form::text('tracking_main',old('tracking_main'),['class' => 'form-control'])!!}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! Form::label('standard_phone','Enter the shipper’s phone number the same as you used for submitting your order',['class' => 'control-label']) !!}
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12">
                                                {!! Form::text('standard_phone',old('standard_phone'),['class' => 'form-control', 'required'])!!}
                                            </div>
                                        </div>
                                    </div>                                
                                </div>
                                
                                <div class="modal-footer">
                                    <div class="form-group">
                                        <div class="row">
                                            <div class="col-md-6">
                                            </div>
                                            <div class="col-md-6">
                                                {!! Form::button('Submit',['class'=>'btn btn-success','type'=>'submit']) !!}
                                            </div>
                                        </div>
                                    </div>                                                                           
                                </div>
                                
                                {!! Form::close() !!}
                            </div>
                        </div>
                    </div>
                </div> 

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

                <!-- Link to open the modal -->
                <a href="#addEngParcel" class="add-eng-parcel" style="display:none" data-toggle="modal"></a>

                {!! Form::open(['url'=>route('addFormEng'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal add-form-eng','method' => 'POST']) !!}

                @if (session('sheet') && in_array(session('sheet'),['courier','worksheet','draft']))
                
                <script type="text/javascript">$('.add-form-eng').show()</script>
                {!! Form::hidden('sheet', session('sheet'))!!}
                {!! Form::hidden('id', session('id'))!!}
                
                @endif

                <h3>Shipper’s Data</h3>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::select('shipper_country', array('Israel' => 'Israel', 'Germany' => 'Germany'), isset($data_parcel->shipper_country) ? $data_parcel->shipper_country : '',['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('first_name',isset($data_parcel->first_name) ? $data_parcel->first_name : old('first_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s first name'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('last_name',isset($data_parcel->last_name) ? $data_parcel->last_name : old('last_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s last name'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('standard_phone',isset($data_parcel->standard_phone) ? $data_parcel->standard_phone : old('standard_phone'),['class' => 'form-control standard-phone', 'placeholder' => 'Shipper\'s phone number (standard)'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('shipper_phone',isset($data_parcel->shipper_phone) ? $data_parcel->shipper_phone : old('shipper_phone'),['class' => 'form-control', 'placeholder' => 'Shipper\'s phone number (additionally)'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('shipper_city',isset($data_parcel->shipper_city) ? $data_parcel->shipper_city : old('shipper_city'),['class' => 'form-control', 'placeholder' => 'Shipper\'s city/village'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('shipper_address',isset($data_parcel->shipper_address) ? $data_parcel->shipper_address : old('shipper_address'),['class' => 'form-control', 'placeholder' => 'Shipper\'s address'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('passport_number',isset($data_parcel->passport_number) ? $data_parcel->passport_number : old('passport_number'),['class' => 'form-control', 'placeholder' => 'GSTN/Passport number'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('return_date',isset($data_parcel->return_date) ? $data_parcel->return_date : old('return_date'),['class' => 'form-control', 'placeholder' => 'Estimated return to India date'])!!}
                        </div>
                    </div>
                </div>

                <h3>Consignee’s Data</h3>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('consignee_first_name',isset($data_parcel->consignee_first_name) ? $data_parcel->consignee_first_name : old('consignee_first_name'),['class' => 'form-control', 'placeholder' => 'Consignee\'s first name'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('consignee_last_name',isset($data_parcel->consignee_last_name) ? $data_parcel->consignee_last_name : old('consignee_last_name'),['class' => 'form-control', 'placeholder' => 'Consignee\'s last name'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('house_name',isset($data_parcel->house_name) ? $data_parcel->house_name : old('house_name'),['class' => 'form-control', 'placeholder' => 'House name'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('post_office',isset($data_parcel->post_office) ? $data_parcel->post_office : old('post_office'),['class' => 'form-control', 'placeholder' => 'Local post office'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('district',isset($data_parcel->district) ? $data_parcel->district : old('district'),['class' => 'form-control', 'placeholder' => 'District/City'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('state_pincode',isset($data_parcel->state_pincode) ? $data_parcel->state_pincode : old('state_pincode'),['class' => 'form-control', 'placeholder' => 'State pincode'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::select('consignee_country', array('India' => 'India', 'Nepal' => 'Nepal', 'Nigeria' => 'Nigeria', 'Ghana' => 'Ghana', 'Cote D\'Ivoire' => 'Cote D\'Ivoire', 'South Africa' => 'South Africa', 'Thailand' => 'Thailand'), isset($data_parcel->consignee_country) ? $data_parcel->consignee_country: '',['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('consignee_address',isset($data_parcel->consignee_address) ? $data_parcel->consignee_address : old('consignee_address'),['class' => 'form-control', 'placeholder' => 'Consignee\'s address'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('consignee_phone',isset($data_parcel->consignee_phone) ? $data_parcel->consignee_phone : old('consignee_phone'),['class' => 'form-control', 'placeholder' => 'Consignee\'s phone number'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-9">
                            <h3>Shipped items (description)</h3>
                        </div>
                        <div class="col-md-3">
                            <h3>Quantity</h3>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_1', isset($data_parcel->item_1) ? $data_parcel->item_1 : old('item_1'),['class' => 'form-control', 'placeholder' => 'item 1', 'data-item' => '1'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_1', isset($data_parcel->q_item_1) ? $data_parcel->q_item_1 : old('q_item_1'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_2', isset($data_parcel->item_2) ? $data_parcel->item_2 : old('item_2'),['class' => 'form-control', 'placeholder' => 'item 2', 'data-item' => '2'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_2', isset($data_parcel->q_item_2) ? $data_parcel->q_item_2 : old('q_item_2'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_3', isset($data_parcel->item_3) ? $data_parcel->item_3 : old('item_3'),['class' => 'form-control', 'placeholder' => 'item 3', 'data-item' => '3'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_3', isset($data_parcel->q_item_3) ? $data_parcel->q_item_3 : old('q_item_3'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_4', isset($data_parcel->item_4) ? $data_parcel->item_4 : old('item_4'),['class' => 'form-control', 'placeholder' => 'item 4', 'data-item' => '4'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_4', isset($data_parcel->q_item_4) ? $data_parcel->q_item_4 : old('q_item_4'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_5', isset($data_parcel->item_5) ? $data_parcel->item_5 : old('item_5'),['class' => 'form-control', 'placeholder' => 'item 5', 'data-item' => '5'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_5', isset($data_parcel->q_item_5) ? $data_parcel->q_item_5 : old('q_item_5'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_6', isset($data_parcel->item_6) ? $data_parcel->item_6 : old('item_6'),['class' => 'form-control', 'placeholder' => 'item 6', 'data-item' => '6'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_6', isset($data_parcel->q_item_6) ? $data_parcel->q_item_6 : old('q_item_6'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_7', isset($data_parcel->item_7) ? $data_parcel->item_7 : old('item_7'),['class' => 'form-control', 'placeholder' => 'item 7', 'data-item' => '7'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_7', isset($data_parcel->q_item_7) ? $data_parcel->q_item_7 : old('q_item_7'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_8', isset($data_parcel->item_8) ? $data_parcel->item_8 : old('item_8'),['class' => 'form-control', 'placeholder' => 'item 8', 'data-item' => '8'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_8', isset($data_parcel->q_item_8) ? $data_parcel->q_item_8 : old('q_item_8'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_9', isset($data_parcel->item_9) ? $data_parcel->item_9 : old('item_9'),['class' => 'form-control', 'placeholder' => 'item 9', 'data-item' => '9'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_9', isset($data_parcel->q_item_9) ? $data_parcel->q_item_9 : old('q_item_9'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-9">
                            {!! Form::text('item_10', isset($data_parcel->item_10) ? $data_parcel->item_10 : old('item_10'),['class' => 'form-control', 'placeholder' => 'item 10', 'data-item' => '10'])!!}
                        </div>
                        <div class="col-3">
                            {!! Form::text('q_item_10', isset($data_parcel->q_item_10) ? $data_parcel->q_item_10 : old('q_item_10'),['class' => 'form-control'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('shipment_val', isset($data_parcel->shipment_val) ? $data_parcel->shipment_val : old('shipment_val'),['class' => 'form-control', 'placeholder' => 'Claimed value'])!!}
                        </div>
                    </div>
                </div>
                                
                {!! Form::button('Submit',['class'=>'btn','type'=>'submit']) !!}
                {!! Form::close() !!}
               
                <!-- временное -->
                <br>
                <div class="tracking">
                    <a href="{{ route('showFormEng') }}">
                        <div class="style-tracking">
                            <span>Reopen the form</span> 
                        </div>           
                    </a>
                </div>
                <br>
                <div class="ask">
                    <a href="{{__('front.home_link')}}">
                        <div class="style-ask">
                            <span>Return to the homepage</span>
                        </div>    
                    </a>    
                </div>
                <!-- /временное -->           
            
            </div>
        </div>           
    </section><!-- /.app-content -->

    <script>
        
        function clickRadio(elem)
        {          
            if (elem.value === 'yes') {  
                $('.tracking-main input').prop('required',true);             
                $('.add-eng-parcel').click();               
            }
            else{
                $('.tracking-main input').prop('required',false);
                $('.tracking-main').hide();
                $('.add-eng-parcel').click();
            }
        }


        function сonfirmSigned(event)
        {
            event.preventDefault();
            const form = event.target;

            const phone = document.querySelector('.add-form-eng [name="standard_phone"]'); 
            if (phone.value.length < 10 || phone.value.length > 24) {
                alert('The number of characters in the standard phone must be from 10 to 24 !');
                return false;
            }

            const recipientPhone = document.querySelector('[name="consignee_phone"]');
            const regexp = /[0-9]/g;
            const phoneDigits = recipientPhone.value.slice(1); 
            if (recipientPhone.value.length < 6 || recipientPhone.value.length > 24) {
                alert('The number of characters in the consignee phone must be from 6 to 24 !');
                return false;
            }
            else if (recipientPhone.value[0] !== '+') {
                alert('The consignee phone must start with "+" !');
                return false;
            }
            else if (!regexp.test(phoneDigits)) {
                alert('The consignee phone must contain only numbers !');
                return false;
            }

            /*Parcel content items*/
            const input = document.querySelectorAll('.add-form-eng input');

            let contentFull = false;
            for (let item of input) {
                if (item.hasAttribute('data-item')) {
                    const num = item.getAttribute('data-item');
                    const content = document.querySelector('[name="item_'+num+'"]');
                    const quantity = document.querySelector('[name="q_item_'+num+'"]');
                    if (content.value && !(quantity.value)) {
                        alert('Fill in the quantity !');
                        return false;
                    }
                    else if(!(content.value) && quantity.value){
                        alert('Fill in the description !');
                        return false;
                    }
                    else if(content.value && quantity.value){
                        contentFull = true;
                    }
                }
            }  

            if(!contentFull){
                document.querySelector('[name="item_1"]').value = "Empty";
                document.querySelector('[name="q_item_1"]').value = "0";
            }
            
            form.submit();
        }

    </script>

@endsection