@extends('layouts.front')

@section('content')

    <section class="app-content page-bg">
        <div class="container">                       
            <div class="parcel-form">

                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                
                <h1>ORDER FORM / טופס הזמנה     </h1>

                {!! Form::open(['url'=>route('chinaParcelAdd'), 'class'=>'form-horizontal form-send-parcel','method' => 'POST']) !!}

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('customer_name',old('customer_name'),['class' => 'form-control', 'placeholder' => 'Customer name /שם הלקוח  ', 'required'])!!}
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('customer_address',old('customer_address'),['class' => 'form-control', 'placeholder' => 'Customer address /כתובת הלקוח  ', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('customer_phone',old('customer_phone'),['class' => 'form-control', 'placeholder' => 'Customer phone number /מספר טלפון של הלקוח  ', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('customer_email',old('customer_email'),['class' => 'form-control', 'placeholder' => 'Customer email /כתובת המייל של הלקוח  ', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('supplier_name',old('supplier_name'),['class' => 'form-control', 'placeholder' => 'Supplier name /שם הספק  ', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('supplier_address',old('supplier_address'),['class' => 'form-control', 'placeholder' => 'Supplier address /כתובת הספק  ', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('supplier_phone',old('supplier_phone'),['class' => 'form-control', 'placeholder' => 'Supplier phone number /מספר טלפון של הספק  ', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('supplier_email',old('supplier_email'),['class' => 'form-control', 'placeholder' => 'Supplier email /כתובת המייל של הספק  ', 'required'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::textarea('shipment_description',old('shipment_description'),['class' => 'form-control', 'rows' => '5', 'cols' =>  120, 'placeholder' => 'Shipment description /תאור המשלוח  '])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('weight',old('weight'),['class' => 'form-control', 'placeholder' => 'Shipment weight, kg /משקל המשלוח, ק״ג'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('length',old('length'),['class' => 'form-control', 'placeholder' => 'Shipment length, cm / המשלוח ארוך, ס״מ'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-6">
                            {!! Form::text('height',old('height'),['class' => 'form-control', 'placeholder' => 'Shipment height, cm / משלוח גובה, ס״מ'])!!}
                        </div>
                        <div class="col-md-6">
                            {!! Form::text('width',old('width'),['class' => 'form-control', 'placeholder' => 'Shipment width, cm / משלוח רוחב ,ס"מ'])!!}
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="row">
                        <div class="col-md-12">
                            {!! Form::text('tracking_local',old('tracking_local'),['class' => 'form-control', 'placeholder' => 'Tracking number local /מספר מקומי למעקב', 'required'])!!}
                        </div>
                    </div>
                </div>

                {!! Form::button('Send',['class'=>'btn','type'=>'submit']) !!}
                {!! Form::close() !!}
               
                <!-- временное -->
                <br>
                <div class="tracking">
                    <a href="{{ route('chinaParcelForm') }}">
                        <div class="style-tracking">
                            <span>{{__('front.create_another')}}</span> 
                        </div>           
                    </a>
                </div>
                <br>
                <div class="ask">
                    <a href="{{__('front.home_link')}}">
                        <div class="style-ask">
                            <span>{{__('front.back')}}</span>
                        </div>    
                    </a>    
                </div>
                <!-- /временное -->           
            
            </div>
        </div>           
    </section><!-- /.app-content -->

    <script>

    function сonfirmSigned()
    {
        var x = document.querySelector('[name="signed_form"]').checked;
        if (x)
            return true;
        else{
            alert('Sign form !');
            return false;
        }
    }

</script>

@endsection