@extends('layouts.front_gcs')

@section('content')

<section class="app-content">
    <div class="container new-parcel">   

        <div class="row row-black-button">
            <a class="yellow-button" href="{{ route('trackingRuFormGcs') }}">Отследить посылку</a>
            <a class="yellow-button" href="https://www.gcs-deliveries.com/">На главную</a>
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

            <h1>Заказ посылки</h1>
            <h5>Обязательные поля отмечены значком  (*)</h5>
            <h5>Данные отправителя (заполняется на английском)</h5>

            <div class="form-group">
                <label class="control-label">Это не первый мой заказ</label>
                <input type="checkbox" name="not_first_order">
            </div>

            <div class="form-group">
                <label class="control-label">Мне нужна пустая коробка/коробки</label>
                <input onclick="clickRadio(this)" type="radio" name="need_box" value="need">
                <h6>укажите типы и количество коробок</h6>
                <h6>ТИП - КОЛЛИЧЕСТВО</h6>
                <ul class="box-group">
                    <li style="width: 200px;">
                        <label class="control-label">Очень большая</label>
                        <input type="number" data-name="Очень большая" name="extra_large" style="width: 40px;float: right;" min="0">
                    </li>
                    <li style="width: 200px;">
                        <label class="control-label">Большая</label>
                        <input type="number" data-name="Большая" name="large" style="width: 40px;float: right;" min="0">
                    </li>
                    <li style="width: 200px;">
                        <label class="control-label">Средняя</label>
                        <input type="number" data-name="Средняя" name="medium" style="width: 40px;float: right;" min="0">
                    </li>
                    <li style="width: 200px;">
                        <label class="control-label">Маленькая</label>
                        <input type="number" data-name="Маленькая" name="small" style="width: 40px;float: right;" min="0">
                    </li>
                </ul>

                <label class="control-label">Мне не нужна коробка</label>
                <input onclick="clickRadio(this)" type="radio" name="need_box" value="not_need" checked>
            </div>                

            <div class="container">
                <!-- Modal -->

                <div class="modal fade" id="addRuParcel" role="dialog">
                    <div class="modal-dialog">

                        <!-- Modal content-->
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <div class="modal-body">
                                <p class="question">Ввести те же данные отправителя, которые были при предыдущем заказе?</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clickAnswer(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Да</button>
                                <button type="button" onclick="clickAnswer(this)" class="btn btn-danger pull-left no" data-dismiss="modal">Нет</button>

                                {!! Form::open(['url'=>'https://ddcargos.com/api/forward-check-phone', 'class'=>'form-horizontal check-phone','method' => 'GET']) !!}

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            {!! Form::text('sender_phone',old('sender_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                            {!! Form::hidden('quantity_sender')!!}
                                            {!! Form::hidden('quantity_recipient')!!}
                                            {!! Form::hidden('new_site','gcs-deliveries.com')!!}
                                            {!! Form::hidden('url_name','https://www.forward-post.com/parcel-form-gcs')!!}
                                        </div>
                                        <div class="col-md-6">
                                            {!! Form::button('Отправить',['class'=>'btn btn-success','type'=>'submit']) !!}
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
                                <p class="question">{{ isset($get_data['phone_exist']) ? $get_data['phone_exist'] : ''}}</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" onclick="clickAnswer2(this)" class="btn btn-primary pull-left yes sender" data-dismiss="modal">Yes</button>
                                <button type="button" onclick="clickAnswer2(this)" class="btn btn-danger pull-left no" data-dismiss="modal">Нет</button>

                                {!! Form::open(['url'=>'https://ddcargos.com/api/forward-check-phone', 'class'=>'form-horizontal check-phone','method' => 'GET']) !!}

                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-6">
                                            {!! Form::text('sender_phone',old('sender_phone'),['class' => 'form-control', 'placeholder' => 'Phone*', 'required'])!!}
                                            {!! Form::hidden('quantity_sender')!!}
                                            {!! Form::hidden('quantity_recipient')!!}
                                            {!! Form::hidden('draft','draft')!!}
                                            {!! Form::hidden('new_site','gcs-deliveries.com')!!}
                                            {!! Form::hidden('url_name','https://www.forward-post.com/parcel-form-gcs')!!}
                                        </div>
                                        <div class="col-md-6">
                                            {!! Form::button('Отправить',['class'=>'btn btn-success','type'=>'submit']) !!}
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
            <p><a href="#addRuParcel" class="btn btn-success ru-modal" data-toggle="modal">Добавить посылку</a>
            </p>
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

            {!! Form::open(['url'=>'https://ddcargos.com/api/forward-parcel-form','onsubmit' => 'сonfirmSigned(event)', 'class'=>'form-horizontal form-send-parcel','method' => 'GET']) !!}

            {!! Form::hidden('phone_exist_checked',isset($data_parcel->phone_exist_checked) ? $data_parcel->phone_exist_checked : '')!!}
            {!! Form::hidden('status_box','')!!}
            {!! Form::hidden('comment_2','')!!}
            {!! Form::hidden('short_order','short_order') !!}
            {!! Form::hidden('new_site','gcs-deliveries.com')!!}
            {!! Form::hidden('url_name','https://www.forward-post.com/parcel-form-gcs')!!}

            <h3>Данные отправителя</h3>

            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        {!! Form::select('sender_country', array('Israel' => 'Israel', 'Germany' => 'Germany'), isset($data_parcel->sender_country) ? $data_parcel->sender_country : '',['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">                   
                    <div class="col-md-12">
                        @php
                        $temp = array('' => 'Выберите название ближайшего к вам города');
                        $israel_cities = array_merge($temp, $israel_cities);
                        @endphp
                        {!! Form::select('sender_city', $israel_cities, isset($data_parcel->sender_city) ? $data_parcel->sender_city : '',['class' => 'form-control']) !!}
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
                        {!! Form::text('standard_phone',isset($data_parcel->standard_phone) ? $data_parcel->standard_phone : old('standard_phone'),['class' => 'form-control standard-phone', 'placeholder' => 'Shipper\'s phone number*', 'required'])!!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::text('sender_address',isset($data_parcel->sender_address) ? $data_parcel->sender_address : old('sender_address'),['class' => 'form-control', 'placeholder' => 'Shipper\'s address*', 'required'])!!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    {!! Form::label('recipient_country','Страна назначения',['class' => 'col-md-6 control-label'])   !!}
                    <div class="col-md-6">
                        {!! Form::select('recipient_country', array('RU' => 'Россия (RU)', 'UA' => 'Украина (UA)', 'BY' => 'Беларусь (BY)', 'KZ' => 'Казахстан (KZ)'), isset($data_parcel->recipient_country) ? $data_parcel->recipient_country: '',['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="row">
                    <div class="col-9">
                        <h3>Количество посылок</h3>
                    </div>
                    <div class="col-3">
                        {!! Form::number('parcels_qty',isset($data_parcel->parcels_qty) ? $data_parcel->parcels_qty :'1',['class' => 'form-control', 'min' => '1'])!!}
                    </div>
                </div>
            </div>

            {!! Form::hidden('item_1','')!!}
            {!! Form::hidden('q_item_1','')!!}
            {!! Form::hidden('recipient_address','')!!}

            {!! Form::button('Отправить',['class'=>'yellow-button','type'=>'submit']) !!}
            {!! Form::close() !!}

            <!-- временное -->
            <br>
            <div>
                <button class="yellow-button" style="width:200px"><a style="color:#000" href="{{ route('parcelFormGcs') }}">Оформить еще</a></button>
            </div>
            <br>
            <div>
                <button class="yellow-button" style="width:200px"><a style="color:#000" href="https://www.gcs-deliveries.com/">На главную</a></button>
            </div> 
            <br>
            <!-- /временное -->           
            
    </div>           
</section><!-- /.app-content -->

<script>
    const boxGroup = document.querySelectorAll('.box-group input');
    boxGroup.forEach(function(item) {
        if (localStorage.getItem('boxString')) 
            item.disabled = false;
        else
            item.disabled = true;                
    })

    setTimeout(()=>{
        if (localStorage.getItem('boxString')) {
            const boxString = localStorage.getItem('boxString');
            const tempArr = boxString.split('; ');

            $('[name="need_box"]').each((k,el)=>{
                if ($(el).val() === 'need') 
                    $(el).prop( "checked", true );
                else
                    $(el).prop( "checked", false );
            });

            $('.box-group input').each((k,el)=>{
                for (let i = 0; i < tempArr.length; i++) {
                    if ($(el).attr('data-name') === tempArr[i].split(': ')[0]) 
                        $(el).val(tempArr[i].split(': ')[1])                    
                }
            });
        }

        const button = document.querySelector('form.form-send-parcel button[type="submit"]')
        const checkPhone = document.querySelector('form.form-send-parcel [name="phone_exist_checked"]').value
        if (checkPhone) {
            result = confirm("Вы хотите отправить форму ?")
            if (result) button.click()
        }
    },500)



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

        if (!document.querySelector('[name="sender_country"]').value){
            alert('Поле страна обязательное к заполнению !');
            return false;
        }

        if (!document.querySelector('[name="sender_city"]').value){
            alert('Поле город обязательное к заполнению !');
            return false;
        }

        const phone = document.querySelector('[name="standard_phone"]'); 
        if (phone.value.length !== 13 && countryCode === "+972") {
            alert('Кол-во знаков в телефоне отправителя должно быть 13 !');
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
        const needBox = $('[name="need_box"]:checked').val();        
        if (needBox === 'need') {
            $('[name="status_box"]').val('true');
            let boxString = '';
            let boxVal = 0;
            $('.box-group input').each((k,el)=>{
                boxVal += parseInt($(el).val());
            })
            if (boxVal < 1) {
                alert('ПОЖАЛУЙСТА, УКАЗЫВАЙТЕ ТИП КАК МИНИМУМ ОДНОЙ КОРОБКИ !');
                return false;
            }
            else{
                $('.box-group input').each((k,el)=>{
                    if(parseInt($(el).val())){
                        boxString += $(el).attr('data-name') +': '+ $(el).val() + '; ';
                    }                   
                })
                $('[name="comment_2"]').val(boxString);

                if (!$('[name="phone_exist_checked"]').val()) {
                    localStorage.setItem('boxString',boxString);
                }
                else{
                    localStorage.removeItem('boxString');
                }

            }            
        }
        else $('[name="status_box"]').val('false');

        form.submit();
    }

</script>

@endsection