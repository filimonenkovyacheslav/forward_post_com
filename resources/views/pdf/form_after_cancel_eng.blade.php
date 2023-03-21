@extends('layouts.front_signature_form')

@section('content')

    <section class="app-content page-bg">
        <div class="container">                       
            <div class="parcel-form new-form">

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
                if ($data_parcel){
                    $data_parcel = json_decode($data_parcel);
                }
                @endphp
                
                @if (session('no_phone'))
                    <div class="alert alert-danger">
                        {{ session('no_phone') }}
                    </div>
                @endif
                
                <h1>ORDER FORM</h1>
                <h5>Required fields are marked with (*)</h5>

                <div class="form-group">
                    <label class="control-label">I need empty box/boxes</label>
                    <input onclick="clickRadio(this)" type="radio" name="need_box" value="need">
                    <h6>please specify the boxes types and quantity</h6>
                    <h6>TYPE - QUANTITY</h6>
                    <ul class="box-group">
                        <li style="width: 130px;">
                            <label class="control-label">Large</label>
                            <input type="number" name="large" style="width: 40px;float: right;" min="0">
                        </li>
                        <li style="width: 130px;">
                            <label class="control-label">Medium</label>
                            <input type="number" name="medium" style="width: 40px;float: right;" min="0">
                        </li>
                        <li style="width: 130px;">
                            <label class="control-label">Small</label>
                            <input type="number" name="small" style="width: 40px;float: right;" min="0">
                        </li>
                    </ul>
                    
                    <label class="control-label">I send in my own box</label>
                    <input onclick="clickRadio(this)" type="radio" name="need_box" value="not_need" checked>
                </div>                
                
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

                {!! Form::open(['url'=>route('formUpdateAfterCancel'),'onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'POST']) !!}

                {!! Form::hidden('phone_exist_checked',isset($data_parcel->phone_exist_checked) ? $data_parcel->phone_exist_checked : '')!!}
                {!! Form::hidden('status_box','')!!}
                {!! Form::hidden('comments_2','')!!}

                {!! Form::hidden('signature','signature') !!}

                @if (isset($token))
                <input type="hidden" name="session_token" value="{{ $token }}">
                @endif

                @if (isset($document_id))
                <input type="hidden" name="document_id" value="{{ $document_id }}">
                <input type="hidden" name="type" value="{{ $type }}">
                <input type="hidden" name="id" value="{{ $id }}">
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
                        @php
                        $temp = array('' => 'Please choose the nearest city');
                        $israel_cities = array_merge($temp, $israel_cities);
                        @endphp
                        <div class="col-md-12">
                            {!! Form::select('shipper_city', $israel_cities, isset($data_parcel->shipper_city) ? $data_parcel->shipper_city : '',['class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('first_name',isset($data_parcel->first_name) ? $data_parcel->first_name : old('first_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s first name*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('last_name',isset($data_parcel->last_name) ? $data_parcel->last_name : old('last_name'),['class' => 'form-control', 'placeholder' => 'Shipper\'s last name*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('standard_phone',isset($data_parcel->standard_phone) ? $data_parcel->standard_phone : old('standard_phone'),['class' => 'form-control standard-phone', 'placeholder' => 'Shipper\'s phone number (standard)*', 'required'])!!}
                        </div>

                        <div class="col-md-6">
                            {!! Form::text('shipper_address',isset($data_parcel->shipper_address) ? $data_parcel->shipper_address : old('shipper_address'),['class' => 'form-control', 'placeholder' => 'Shipper\'s address*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <h3>Shipper’s Personal Data</h3>
                <h6>*For citizens of India only</h6>

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
                            {!! Form::text('consignee_first_name',isset($data_parcel->consignee_first_name) ? $data_parcel->consignee_first_name : old('consignee_first_name'),['class' => 'form-control', 'placeholder' => 'Consignee\'s first name*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('consignee_last_name',isset($data_parcel->consignee_last_name) ? $data_parcel->consignee_last_name : old('consignee_last_name'),['class' => 'form-control', 'placeholder' => 'Consignee\'s last name*', 'required'])!!}
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
                            {!! Form::select('consignee_country', $to_country, isset($data_parcel->consignee_country) ? $data_parcel->consignee_country: '',['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('consignee_address',isset($data_parcel->consignee_address) ? $data_parcel->consignee_address : old('consignee_address'),['class' => 'form-control', 'placeholder' => 'Consignee\'s address*', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('consignee_phone',isset($data_parcel->consignee_phone) ? $data_parcel->consignee_phone : old('consignee_phone'),['class' => 'form-control', 'placeholder' => 'Consignee\'s phone number*', 'required'])!!}
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

                {!! Form::hidden('parcels_qty',isset($data_parcel->parcels_qty) ? $data_parcel->parcels_qty :'1') !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('weight',old('weight'),['class' => 'form-control', 'placeholder' => 'Shipment weight, kg'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('length',old('length'),['class' => 'form-control', 'placeholder' => 'Shipment length, cm'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('height',old('height'),['class' => 'form-control', 'placeholder' => 'Shipment height, cm'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('width',old('width'),['class' => 'form-control', 'placeholder' => 'Shipment width, cm'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('shipment_val',old('shipment_val'),['class' => 'form-control', 'placeholder' => 'Shipment\'s declared value*', 'required'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('operator',old('operator'),['class' => 'form-control', 'placeholder' => 'Operator'])!!}
                        </div>
                    </div>
                </div>

                @if($domain === 'forward')
                <strong>Hereby by signing this packing list I declare:</strong>
                <p>
                    1) The goods in this parcel are handed over by me to the transporters and are my personal goods. Courier / Logistics Companies are just facilitators for shipping my cargo / goods - and hold no responsibility for breakage / shortage / damage or content of cargo is my responsibility - and we abide my all laws of local country as there , and well versed for same. 
                    <br>
                    2) Further these used/old if household goods then bear no commercial value and are not for sale. 
                    <br>
                    3) I guarantee that I provided true and complete information about the items shipped in this parcel. In case of any false or incomplete data I recognize my obligation to cover all legal penalties in origin, destination, and transit countries as well as to pay the costs incurred through my fault caused with delays in customs clearance and/or return of the parcel from a warehouse in Israel or from the destination country to me.
                    <br>
                    I am aware of that the forwarding company is not responsible for any delay in delivery has occurred due to circumstances beyond its control, in particular, due to delays in customs clearance, and agree with this, as the terms of service for the parcel delivery.
                </p>

                @else
                <strong>Hereby by signing this packing list I declare:</strong>
                <p>The goods in this parcel are handed over by me to the transporters and are my personal goods. I accept that the transporters hold no responsibility for shortage or damage or content of cargo.
                    <br>
                    This parcel includes goods then bear no commercial value and are not for sale.
                    <br>
                    I guarantee that I received the detailed information on items restricted for shipment to the destianation country. None of the items has been included in the the parcel.
                    <br>
                    I provided true and complete information about the items shipped in this parcsel. In case of any false or incomplete data I recognize my obligation to cover all legal penalties in origin, destination, and transit countries as well as to pay the costs incurred through my fault caused with delays in customs clearance and/or return of the parcel from a warehouse in Israel or from the destination country to me.
                    <br>
                    I am aware of that the forwarding company is not responsible for any delay in delivery has occurred due to circumstances beyond its control, in particular, due to delays in customs clearance, and agree with this, as the terms of service for the parcel delivery.
                </p>
                @endif

                <input type="hidden" id="form_cancel_disabled" name="form_cancel_disabled" value="false">
                                
                {!! Form::button('To sign',['class'=>'btn','type'=>'submit']) !!}
                {!! Form::close() !!}   

                @if(Auth::user())
                @if(Auth::user()->role === 'office_1' || Auth::user()->role === 'admin' || Auth::user()->role === 'office_eng')
                <!-- <br>
                <button class="btn btn-danger" id="cancel-disabled" onclick="cancelDisabled()">To cancel Disabled</button> -->
                <hr> 
                @if($type === 'eng_draft_id')
                <a class="btn btn-success" href="{{ url('/admin/courier-eng-draft-worksheet') }}">To Admin Panel</a>
                @else
                <a class="btn btn-success" href="{{ url('/admin/phil-ind-worksheet') }}">To Admin Panel</a>
                @endif
                @endif
                @endif    
                
            </div>
        </div>           
    </section><!-- /.app-content -->

    <script>
        const boxGroup = document.querySelectorAll('.box-group input');
        boxGroup.forEach(function(item) {
            item.disabled = true;
        })
        
        function clickRadio(elem){    
            const boxGroup = document.querySelectorAll('.box-group input');       
            if (elem.value === 'need') {                
                boxGroup.forEach(function(item) {
                    item.disabled = false;
                })               
            }
            else{
                boxGroup.forEach(function(item) {
                    item.disabled = true;
                })
            }
        }


        function сonfirmSigned(event)
        {
            event.preventDefault();
            const form = event.target;

            const phone = document.querySelector('[name="standard_phone"]'); 
            if (phone.value.length !== 13 && countryCode === "+972") {
                alert('The number of characters in the standard phone must be 13 !');
                return false;
            }
            if (phone.value.length !== 14 && countryCode === "+49") {
                alert('The number of characters in the standard phone must be 14 !');
                return false;
            }

            if (!document.querySelector('[name="shipper_country"]').value){
                alert('The shipper country field is required !');
                return false;
            }
            if (!document.querySelector('[name="consignee_country"]').value){
                alert('The consignee country field is required !');
                return false;
            }
            if (document.querySelector('[name="shipper_country"]').value !== 'Germany') {
                if (!document.querySelector('[name="shipper_city"]').value){
                    alert('The city field is required !');
                    return false;
                }
            }
            else{
                if (!document.querySelector('input[name="shipper_city"]').value){
                    alert('The city field is required !');
                    return false;
                }
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
            const input = document.querySelectorAll('.form-send-parcel input');
            const parcelsQty = document.querySelector('[name="parcels_qty"]');
            if (!parcelsQty.value) parcelsQty.value = 1;
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

            /*Boxes info*/
            const needBox = $('[name="need_box"]:checked').val();        
            if (needBox === 'need') {
                $('[name="status_box"]').val('true');
                let boxString = '';
                let boxVal = 0;
                $('.box-group input').each((k,el)=>{
                    boxVal += parseInt($(el).val());
                })
                if (boxVal < 1) {
                    alert('PLEASE SPECIFY THE TYPE OF AT LEAST ONE BOX !');
                    return false;
                }
                else{
                    $('.box-group input').each((k,el)=>{
                        if(parseInt($(el).val())){
                            boxString += $(el).attr('name') +': '+ $(el).val() + '; ';
                        }                   
                    })
                    $('[name="comments_2"]').val(boxString);
                }            
            }
            
            form.submit();
        }

    </script>

@endsection