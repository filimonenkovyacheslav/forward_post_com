// Table
$(document).ready(function() {
    $('#bootstrap-data-table-export').DataTable();

    if(location.href.indexOf('china') == -1 || location.href.indexOf('phil-ind') == -1){
        $('#bootstrap-data-table_length > label > select > option:nth-child(4)').text('Все');
    
        if ($('.dataTables_empty').text() == 'No data available in table') {
            $('.dataTables_empty').text('Нет данных');
        } 
    }

    let btnMove = $('a.btn-move');
    btnMove.detach();
    if (location.href.indexOf('couriers-tasks') == -1 && location.href.indexOf('warehouse') == -1 && location.href.indexOf('packing-eng-new') == -1 && location.href.indexOf('receipts') == -1 && location.href.indexOf('new-worksheet') == -1 && location.href.indexOf('phil-ind-worksheet') == -1 && location.href.indexOf('draft-worksheet') == -1 && location.href.indexOf('new-packing') == -1 && location.href.indexOf('invoice') == -1 && location.href.indexOf('manifest') == -1) {
        $('#bootstrap-data-table_length').after(btnMove.get(0));
        $('#bootstrap-data-table_length').after(btnMove.get(2));
        $('#bootstrap-data-table_length').after(btnMove.get(1));
    }
    else{ 
        $('.btn-move-wrapper').prepend(btnMove.get(0));
        $('.btn-move-wrapper').prepend(btnMove.get(2));
        $('.btn-move-wrapper').prepend(btnMove.get(1));            
    }
    btnMove.css('display','block');

});


// Worksheet update
$('form.worksheet-update-form button').click((e)=>{
    e.preventDefault();

    const phone = document.querySelector('[name="standard_phone"]'); 
    if (phone.value.length < 10 || phone.value.length > 13) {
        alert('Кол-во знаков в телефоне должно быть от 10 до 13 !');
        return false;
    }
   
    const form = $(e.target).parent();
    $('form.worksheet-update-form input').each((k,el)=>{
        let inputVal = '';
        let inputName = '';
        if ($(el).attr('type') !== 'hidden') {
            inputVal = $(el).val();
            inputName = $(el).attr('name');
            form.find('[name="'+inputName+'"]').each((j,elem)=>{
                if ($(elem).attr('type') === 'hidden') {$(elem).val(inputVal)}
            })
        }                   
    });
    $('form.worksheet-update-form select').each((k,el)=>{
        let inputVal = $(el).val();
        let inputName = $(el).attr('name');
        form.find('[name="'+inputName+'"]').each((j,elem)=>{
            if ($(elem).attr('type') === 'hidden') {$(elem).val(inputVal)}
        })                  
    });
    form.submit();        
})


// Philippines India Worksheet update
$('form.phil-ind-update-form button').click((e)=>{
    e.preventDefault();

    const phone = document.querySelector('[name="standard_phone"]'); 
    if (phone.value.length < 10 || phone.value.length > 24) {
        alert('The number of characters in the phone must be from 10 to 24 !');
        return false;
    }
    
    const form = $(e.target).parent();
    $('form.phil-ind-update-form input').each((k,el)=>{
        let inputVal = '';
        let inputName = '';
        if ($(el).attr('type') !== 'hidden') {
            inputVal = $(el).val();
            inputName = $(el).attr('name');
            form.find('[name="'+inputName+'"]').each((j,elem)=>{
                if ($(elem).attr('type') === 'hidden') {$(elem).val(inputVal)}
            })
        }                   
    });
    $('form.phil-ind-update-form select').each((k,el)=>{
        let inputVal = $(el).val();
        let inputName = $(el).attr('name');
        form.find('[name="'+inputName+'"]').each((j,elem)=>{
            if ($(elem).attr('type') === 'hidden') {$(elem).val(inputVal)}
        })                  
    });
    form.submit();        
})


// Receipt update
$('form.receipt-update-form button').click((e)=>{
    e.preventDefault();
   
    const form = $(e.target).parent();
    $('form.receipt-update-form input').each((k,el)=>{
        let inputVal = '';
        let inputName = '';
        if ($(el).attr('type') !== 'hidden') {
            inputVal = $(el).val();
            inputName = $(el).attr('name');
            form.find('[name="'+inputName+'"]').each((j,elem)=>{
                if ($(elem).attr('type') === 'hidden') {$(elem).val(inputVal)}
            })
        }                   
    });

    form.submit();        
})


// Worksheet status
const enArr = {
    "Доставляется на склад в стране отправителя": "Forwarding to the warehouse in the sender country",
    "На складе в стране отправителя": "At the warehouse in the sender country",
    "На таможне в стране отправителя": "At the customs in the sender country",
    "Доставляется в страну получателя": "Forwarding to the receiver country",
    "На таможне в стране получателя": "At the customs in the receiver country",
    "Доставляется получателю": "Forwarding to the receiver",
    "Доставлено": "Delivered"
};
const heArr = {
    "Доставляется на склад в стране отправителя": "נשלח למחסן במדינת השולח",
    "На складе в стране отправителя": "במחסן במדינת השולח",
    "На таможне в стране отправителя": " במכס במדינת השולח",
    "Доставляется в страну получателя": " נשלח למדינת המקבל",
    "На таможне в стране получателя": " במכס במדינת המקבל",
    "Доставляется получателю": " נמסר למקבל",
    "Доставлено": " נמסר"
};
const uaArr = {
    "Доставляется на склад в стране отправителя": "Доставляється до складу в країні відправника",
    "На складе в стране отправителя": "На складі в країні відправника",
    "На таможне в стране отправителя": "На митниці в країні відправника",
    "Доставляется в страну получателя": "Доставляється в країну отримувача",
    "На таможне в стране получателя": "На митниці в країні отримувача",
    "Доставляется получателю": "Доставляється отримувачу",
    "Доставлено": "Доставлено"
};
const ruArrChina = {
    "Forwarding to the warehouse in the sender country": "Доставляется на склад в стране отправителя",
    "At the warehouse in the sender country": "На складе в стране отправителя",
    "At the customs in the sender country": "На таможне в стране отправителя",
    "Forwarding to the receiver country": "Доставляется в страну получателя",
    "At the customs in the receiver country": "На таможне в стране получателя",
    "Forwarding to the receiver": "Доставляется получателю",
    "Delivered": "Доставлено"
};
const heArrChina = {
    "Forwarding to the warehouse in the sender country": "נשלח למחסן במדינת השולח",
    "At the warehouse in the sender country": "במחסן במדינת השולח",
    "At the customs in the sender country": " במכס במדינת השולח",
    "Forwarding to the receiver country": " נשלח למדינת המקבל",
    "At the customs in the receiver country": " במכס במדינת המקבל",
    "Forwarding to the receiver": " נמסר למקבל",
    "Delivered": " נמסר"
};

$('form.worksheet-add-form select[name="status"]').change((e)=>{
    const key = $(e.target).val();

    $('form.worksheet-add-form [name="status_en"]').val(enArr[key]);
    $('form.worksheet-add-form [name="status_en_disabled"]').val(enArr[key]);
    $('form.worksheet-add-form [name="status_he"]').val(heArr[key]);
    $('form.worksheet-add-form [name="status_he_disabled"]').val(heArr[key]);
    $('form.worksheet-add-form [name="status_ua"]').val(uaArr[key]);
    $('form.worksheet-add-form [name="status_ua_disabled"]').val(uaArr[key]);
})

$('form.worksheet-update-form select[name="status"]').change((e)=>{
    const key = $(e.target).val();
    
    $('form.worksheet-update-form [name="status_en"]').val(enArr[key]);
    $('form.worksheet-update-form [name="status_en_disabled"]').val(enArr[key]);
    $('form.worksheet-update-form [name="status_he"]').val(heArr[key]);
    $('form.worksheet-update-form [name="status_he_disabled"]').val(heArr[key]);
    $('form.worksheet-update-form [name="status_ua"]').val(uaArr[key]);
    $('form.worksheet-update-form [name="status_ua_disabled"]').val(uaArr[key]);
})

$('form.old-worksheet-update-form select[name="status"]').change((e)=>{
    const key = $(e.target).val();
    
    $('form.old-worksheet-update-form [name="guarantee_text_en"]').val(enArr[key]);
    $('form.old-worksheet-update-form [name="status_en_disabled"]').val(enArr[key]);
    $('form.old-worksheet-update-form [name="guarantee_text_he"]').val(heArr[key]);
    $('form.old-worksheet-update-form [name="status_he_disabled"]').val(heArr[key]);
    $('form.old-worksheet-update-form [name="guarantee_text_ua"]').val(uaArr[key]);
    $('form.old-worksheet-update-form [name="status_ua_disabled"]').val(uaArr[key]);
})

$('form.china-worksheet-form select[name="status"]').change((e)=>{
    const key = $(e.target).val();
    
    $('form.china-worksheet-form [name="status_he"]').val(heArrChina[key]);
    $('form.china-worksheet-form [name="status_he_disabled"]').val(heArrChina[key]);
    $('form.china-worksheet-form [name="status_ru"]').val(ruArrChina[key]);
    $('form.china-worksheet-form [name="status_ru_disabled"]').val(ruArrChina[key]);
})


// Filter
$(document).ready(function() {   
    if(location.href.indexOf('china') !== -1){ 
        $('.card-body.new-worksheet #bootstrap-data-table_filter').prepend(`
        <label class="table-columns">Change column:
            <select class="form-control" id="table-columns" name="table-columns">
                <option value="" selected="selected"></option>
                    
                <option value="0">Date</option>
                    
                <option value="1">Tracking number main</option>
                    
                <option value="2">Local tracking number</option>
                    
                <option value="3">Status</option>
                    
                <option value="4">Customer name</option>
                    
                <option value="5">Customer address</option>
                    
                <option value="6">Customer phone number</option>
                    
                <option value="7">Customer email</option>
                    
                <option value="8">Supplier name</option>
                    
                <option value="9">Supplier address</option>
                    
                <option value="10">Supplier phone number</option>
                    
                <option value="11">Supplier email</option>
                    
                <option value="12">Shipment description</option>
                    
                <option value="13">Shipment weight</option>
                    
                <option value="14">Shipment length</option>
                    
                <option value="15">Shipment width</option>
                    
                <option value="16">Shipment height</option>
                    
                <option value="17">Lot number</option>
                    
                <option value="18">Status He</option>
                    
                <option value="19">Status Ru</option>
                                         
            </select>
        </label>
        `);              
    }
    else if (location.href.indexOf('packing-sea') !== -1) {
        $('.card-body.packing-sea #bootstrap-data-table_filter').prepend(`
        <label class="table-columns">Change column:
            <select class="form-control" id="table-columns" name="table-columns">
                <option value="" selected="selected"></option>
                    
                <option value="0">Плательщик</option>
                    
                <option value="1">Contract Nr.</option>
                    
                <option value="2">Type</option>
                    
                <option value="3">Trek-KOD</option>
                    
                <option value="4">ФИО Отправителя</option>
                    
                <option value="5">ФИО получателя</option>
                    
                <option value="6">Код Страны</option>
                    
                <option value="7">Индекс</option>
                    
                <option value="8">Регион</option>
                    
                <option value="9">Район</option>
                    
                <option value="10">Город доставки</option>
                    
                <option value="11">улица</option>
                    
                <option value="12">дом</option>
                    
                <option value="13">корпус</option>
                    
                <option value="14">квартира</option>
                    
                <option value="15">Телефон(+7ххххх)</option>
                    
                <option value="16">Tarif €</option>
                    
                <option value="17">Tarif €-cent</option>
                    
                <option value="18">weight kg</option>
                    
                <option value="19">weight g</option>
                    
                <option value="20">код услуги</option>
                    
                <option value="21">Amount of COD Rbl</option>
                    
                <option value="22">Amount of COD kop</option>
                    
                <option value="23">номер вложения</option>
                    
                <option value="24">Наименования вложения</option>
                    
                <option value="25">Количество вложений</option>
                    
                <option value="26">weight of enclosures kg</option>
                    
                <option value="27">weight of enclosures g</option>
                    
                <option value="28">стоимость евро</option>
                    
                <option value="29">стоимость евроценты</option>
                                                    
            </select>
        </label>
        `); 
    }
    else if (location.href.indexOf('packing-eng') !== -1) {
        $('.card-body.packing-eng #bootstrap-data-table_filter').prepend(`
        <label class="table-columns">Change column:
            <select class="form-control" id="table-columns" name="table-columns">
                <option value="" selected="selected"></option>
                    
                <option value="0">Tracking Number</option>
                    
                <option value="1">Destination Country</option>
                    
                <option value="2">Shipper name</option>
                    
                <option value="3">Shipper address</option>
                    
                <option value="4">Shipper phone no</option>
                    
                <option value="5">Shipper ID no</option>
                    
                <option value="6">Consignee name</option>
                    
                <option value="7">Consignee address</option>
                    
                <option value="8">Consignee phone no</option>
                    
                <option value="9">Consignee ID no</option>
                    
                <option value="10">Dimensions (length)</option>
                    
                <option value="11">Dimensions (width)</option>
                    
                <option value="12">Dimensions (height)</option>
                    
                <option value="13">Weight</option>
                    
                <option value="14">Items enclosed</option>
                     
                <option value="15">Declared Value</option>
                                                                   
            </select>
        </label>
        `); 
    }  
})


$(document).ready(function() {
    $('.card-body.worksheet #bootstrap-data-table_filter').prepend(`
    <label class="table-columns">Выберите колонку:
        <select class="form-control" id="table-columns" name="table-columns">
            <option value="" selected="selected"></option>
                    
            <option value="0">Номер</option>
                    
            <option value="1">Дата</option>
                    
            <option value="2">Направление</option>
                    
            <option value="3">Статус</option>
                    
            <option value="4">Локальный</option>
                    
            <option value="5">Трекинг</option>
                    
            <option value="6">Коммент менеджера</option>
                    
            <option value="7">Коммент</option>
                    
            <option value="8">Комментарии</option>
                    
            <option value="9">Отправитель</option>
                    
            <option value="10">Данные отправителя</option>
                    
            <option value="11">Получатель</option>
                    
            <option value="12">Данные получателя</option>
                    
            <option value="13">E-mail получателя</option>
                    
            <option value="14">Декларируемая стоимость посылки, $</option>
                    
            <option value="15">Упаковка</option>
                    
            <option value="16">Оплачивает посылку</option>
                    
            <option value="17">Трекинг номер и вес посылки</option>
                    
            <option value="18">Ширина</option>
                    
            <option value="19">Высота</option>
                    
            <option value="20">Длина</option>
                    
            <option value="21">Номер партии</option>
                    
            <option value="22">Тип отправления</option>
                    
            <option value="23">Описание содержимого посылки</option>
                    
            <option value="24">1. позиция</option>
                    
            <option value="25">2. позиция</option>
                    
            <option value="26">3. позиция</option>
                    
            <option value="27">4. позиция</option>
                    
            <option value="28">5. позиция</option>
                    
            <option value="29">6. позиция</option>
                    
            <option value="30">7. позиция</option>
                    
            <option value="31">ENG</option>
                    
            <option value="32">RU</option>
                    
            <option value="33">HE</option>
                    
            <option value="34">UA</option>
                    
            <option value="35">Оплата</option>
                    
            <option value="36">Физ. вес</option>
                    
            <option value="37">Объем. вес</option>
                    
            <option value="38">К-во ед</option>
                    
            <option value="39">Комментарии</option>
                           
        </select>
    </label>
    `);
})


$('#bootstrap-data-table_filter input').on('input',(e)=>{
    $('#bootstrap-data-table_info').hide();
    const column = $('#table-columns').val();
    const thisVal = $(e.target).val();
    if (column !== '' && thisVal.length > 2) {
        $('#bootstrap-data-table tbody tr').each((k,elem)=>{
            $(elem).children('td:not(.td-button, .td-checkbox)').each((i,el)=>{
                if (i == column && $(el).text().indexOf(thisVal) == -1) {
                    $(elem).remove();
                }
            })
        })
    }   
})


$('#bootstrap-data-table_paginate').on('click',(e)=>{
    $('#bootstrap-data-table_info').hide();
    const column = $('#table-columns').val();
    const thisVal = $('#bootstrap-data-table_filter input').val();
    if (column !== '' && thisVal.length > 2) {
        $('#bootstrap-data-table tbody tr').each((k,elem)=>{
            $(elem).children('td:not(.td-button, .td-checkbox)').each((i,el)=>{
                if (i == column && $(el).text().indexOf(thisVal) == -1) {
                    $(elem).remove();
                }
            })
        })
    }   
})

const idArr = ['id','worksheet_id','eng_worksheet_id','draft_id','eng_draft_id'];
$('#table_filter_button').on('click',(e)=>{
    const column = $('#table_columns').val();
    const thisVal = $('[name="table_filter_value"]').val();
    if (thisVal.length > 2) {
        $('#form-worksheet-table-filter').submit()
    } 
    else if (idArr.indexOf(column) !== -1 && thisVal) {
        $('#form-worksheet-table-filter').submit()
    }  
})


/* Tracking filter checkbox*/
var checkboxArr = [];


function handleCheckbox(element) {
    checkboxArr.push(element.value)
}


function handleCencel() {
    const checkbox = document.querySelectorAll('#checkbox-group [type="checkbox"]');
    checkbox.forEach(function(item) {
        item.checked = false;           
    })
    checkboxArr = [];
}


document.onselectionchange = function() {
    let selection = document.getSelection();

    const checkbox = document.querySelectorAll('#checkbox-group [type="checkbox"]');
    checkbox.forEach(function(item) {
        if (selection.toString().indexOf(item.value) != -1) {
            item.checked = true;
        }
        else if (checkboxArr.indexOf(item.value) != -1){
            item.checked = true;
        }
        else {
            item.checked = false;
        }
    })
};

const checkboxGroup = document.getElementById('checkbox-group');
if (checkboxGroup) {
    checkboxGroup.addEventListener('mouseup', function (event) {
        const checkbox = document.querySelectorAll('#checkbox-group [type="checkbox"]');
        checkbox.forEach(function(item) {
            if (item.checked === true) {
                checkboxArr.push(item.value)
            }           
        })
    });
}


// new worksheet
$('#tracking-columns').change((e)=>{
    const thisVal = $(e.target).val();
    if (thisVal === 'status') {
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('[name="site_name"]').remove()
        $('[name="tariff"]').remove()
        $('[name="partner"]').remove()
        $('.city-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.value-by-tracking').append(`
            <div class="status-value">
                <select class="form-control" id="status" name="status">
                   <option value="" selected="selected"></option>
                    
                   <option value="Доставляется на склад в стране отправителя">Доставляется на склад в стране отправителя</option>
                    
                   <option value="На складе в стране отправителя">На складе в стране отправителя</option>
                    
                   <option value="На таможне в стране отправителя">На таможне в стране отправителя</option>
                    
                   <option value="Доставляется в страну получателя">Доставляется в страну получателя</option>
                    
                   <option value="На таможне в стране получателя">На таможне в стране получателя</option>
                    
                   <option value="Доставляется получателю">Доставляется получателю</option>
                    
                   <option value="Доставлено">Доставлено</option>
                    
                   <option value="Подготовка">Подготовка</option>

                   <option value="Пакинг лист">Пакинг лист</option>
                    
                   <option value="Возврат">Возврат</option>
                    
                   <option value="Коробка">Коробка</option>
                    
                   <option value="Забрать">Забрать</option>
                    
                   <option value="Уточнить">Уточнить</option>
                    
                   <option value="Думают">Думают</option>
                    
                   <option value="Отмена">Отмена</option>
                    
                </select>                
            </div>
            `)
    }
    else if(thisVal === 'site_name'){
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.status-value').remove()
        $('[name="tariff"]').remove()
        $('[name="partner"]').remove()
        $('.city-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.value-by-tracking').append(`
            <select class="form-control" id="site_name" name="site_name">
               <option value="DD-C">DD-C</option>
                    
               <option value="For">For</option>
                    
            </select>
            `)
    }
    else if(thisVal === 'tariff'){
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.status-value').remove()
        $('[name="site_name"]').remove()
        $('[name="partner"]').remove()
        $('.city-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.value-by-tracking').append(`
            <select class="form-control" id="tariff" name="tariff">
               <option value="" selected="selected"></option>
                    
               <option value="Море">Море</option>
                    
               <option value="Авиа">Авиа</option>
                    
            </select>
            `)
    }
    else if(thisVal === 'partner'){
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.status-value').remove()
        $('[name="site_name"]').remove()
        $('[name="tariff"]').remove()
        $('.city-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.value-by-tracking').append(`
            <select class="form-control" id="partner" name="partner">
               <option value="" selected="selected"></option>
                    
               <option value="viewer_1">viewer_1</option>
                    
               <option value="viewer_2">viewer_2</option>
                    
               <option value="viewer_3">viewer_3</option>
                    
               <option value="viewer_4">viewer_4</option>
                    
               <option value="viewer_5">viewer_5</option>
                    
            </select>
            `)
    }
    else if(thisVal === 'sender_city') {
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.status-value').remove()
        $('[name="site_name"]').remove()
        $('[name="tariff"]').remove()
        $('[name="partner"]').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.value-by-tracking').append(`
            <div class="city-value">
                <div class="col-md-4">
                    <select class="form-control" name="choose_city_ru"><option value="0" selected="selected">Метод изменения города</option>
                    <option value="1">Выбрать из списка (автоматически определится Регион)</option>
                    <option value="2">Ввести вручную (Регион возможно не определится)</option>
                    </select>
                </div>

                <div class="col-md-4 choose-city-ru">                    
                    <select class="form-control" style="display:none" disabled="disabled" id="sender_city" name="sender_city">
                    <option value="Acre">Acre</option>                   
                    <option value="Afula">Afula</option>                    
                    <option value="Arad">Arad</option>                    
                    <option value="Ariel">Ariel</option>                    
                    <option value="Ashdod">Ashdod</option>                   
                    <option value="Ashkelon">Ashkelon</option>                    
                    <option value="Baqa-Jatt">Baqa-Jatt</option>                    
                    <option value="Bat Yam">Bat Yam</option>                    
                    <option value="Beersheba">Beersheba</option>                    
                    <option value="Beit She'an">Beit She'an</option>                   
                    <option value="Beit Shemesh">Beit Shemesh</option>                    
                    <option value="Beitar Illit">Beitar Illit</option>
                    <option value="Binyamina">Binyamina</option>                     
                    <option value="Bnei Brak">Bnei Brak</option>  
                    <option value="Caesaria">Caesaria</option>                  
                    <option value="Dimona">Dimona</option>                    
                    <option value="Eilat">Eilat</option>                    
                    <option value="El'ad">El'ad</option>                    
                    <option value="Giv'atayim">Giv'atayim</option>                    
                    <option value="Giv'at Shmuel">Giv'at Shmuel</option>                    
                    <option value="Hadera">Hadera</option>                    
                    <option value="Haifa">Haifa</option>                    
                    <option value="Herzliya">Herzliya</option>                    
                    <option value="Hod HaSharon">Hod HaSharon</option>                    
                    <option value="Holon">Holon</option>                    
                    <option value="Jerusalem">Jerusalem</option>                    
                    <option value="Karmiel">Karmiel</option>                    
                    <option value="Kafr Qasim">Kafr Qasim</option>                    
                    <option value="Kfar Saba">Kfar Saba</option>                    
                    <option value="Kiryat Ata">Kiryat Ata</option>                    
                    <option value="Kiryat Bialik">Kiryat Bialik</option>                    
                    <option value="Kiryat Gat">Kiryat Gat</option>                    
                    <option value="Kiryat Malakhi">Kiryat Malakhi</option>                    
                    <option value="Kiryat Motzkin">Kiryat Motzkin</option>                    
                    <option value="Kiryat Ono">Kiryat Ono</option>                   
                    <option value="Kiryat Shmona">Kiryat Shmona</option>                    
                    <option value="Kiryat Yam">Kiryat Yam</option>                    
                    <option value="Lod">Lod</option>                   
                    <option value="Ma'ale Adumim">Ma'ale Adumim</option>                   
                    <option value="Ma'alot-Tarshiha">Ma'alot-Tarshiha</option>
                    <option value="Migdal HaEmek">Migdal HaEmek</option>
                    <option value="Modi'in Illit">Modi'in Illit</option>
                    <option value="Modi'in-Maccabim-Re'ut">Modi'in-Maccabim-Re'ut</option>
                    <option value="Nahariya">Nahariya</option>
                    <option value="Nazareth">Nazareth</option>
                    <option value="Nazareth Illit">Nazareth Illit</option>
                    <option value="Nesher">Nesher</option>
                    <option value="Ness Ziona">Ness Ziona</option>
                    <option value="Netanya">Netanya</option>
                    <option value="Netivot">Netivot</option>
                    <option value="Ofakim">Ofakim</option>
                    <option value="Or Akiva">Or Akiva</option>
                    <option value="Or Yehuda">Or Yehuda</option>
                    <option value="Pardes Hana">Pardes Hana</option>
                    <option value="Petah Tikva">Petah Tikva</option>
                    <option value="Qalansawe">Qalansawe</option>
                    <option value="Ra'anana">Ra'anana</option>
                    <option value="Rahat">Rahat</option>
                    <option value="Ramat Gan">Ramat Gan</option>
                    <option value="Ramat HaSharon">Ramat HaSharon</option>
                    <option value="Ramla">Ramla</option>
                    <option value="Rehovot">Rehovot</option>
                    <option value="Rishon LeZion">Rishon LeZion</option>
                    <option value="Rosh HaAyin">Rosh HaAyin</option>
                    <option value="Safed">Safed</option>
                    <option value="Sakhnin">Sakhnin</option>
                    <option value="Sderot">Sderot</option>
                    <option value="Shefa-'Amr (Shfar'am)">Shefa-'Amr (Shfar'am)</option>
                    <option value="Tamra">Tamra</option>
                    <option value="Tayibe">Tayibe</option>
                    <option value="Tel Aviv">Tel Aviv</option>
                    <option value="Tiberias">Tiberias</option>
                    <option value="Tira">Tira</option>
                    <option value="Tirat Carmel">Tirat Carmel</option>
                    <option value="Umm al-Fahm">Umm al-Fahm</option>
                    <option value="Yavne">Yavne</option>
                    <option value="Yehud-Monosson">Yehud-Monosson</option>
                    <option value="Yokneam">Yokneam</option>
                    <option value="Zikhron Yakov">Zikhron Yakov</option>
                    </select>
                    <input class="form-control" name="sender_city" type="text" id="sender_city">                    
                </div>
            </div>
            `)
    }
    else if(thisVal === 'status_date'){
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('[name="site_name"]').remove()
        $('.status-value').remove()
        $('[name="tariff"]').remove()
        $('[name="partner"]').remove()
        $('.city-value').remove()
        $('.value-by-tracking').append(`
            <input class="form-control" type="date" name="status_date">
            `)
    }
    else if(thisVal === 'order_date'){
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('[name="site_name"]').remove()
        $('.status-value').remove()
        $('[name="tariff"]').remove()
        $('[name="partner"]').remove()
        $('[name="status_date"]').remove()
        $('.city-value').remove()
        $('.value-by-tracking').append(`
            <input class="form-control" type="date" name="order_date">
            `)
    }
    else if(thisVal === 'date'){
        $('[name="value-by-tracking"]').remove()
        $('[name="site_name"]').remove()
        $('.status-value').remove()
        $('[name="tariff"]').remove()
        $('[name="partner"]').remove()
        $('.city-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.value-by-tracking').append(`
            <input class="form-control" type="date" name="date">
            `)
    }
    else {
        $('.status-value').remove()
        $('[name="date"]').remove()
        $('[name="site_name"]').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('[name="tariff"]').remove()
        $('[name="partner"]').remove()
        $('.city-value').remove()
        $('[name="value-by-tracking"]').remove()
        $('.value-by-tracking').append(`
            <textarea class="form-control" name="value-by-tracking"></textarea>
            `)
    }
});

$(document).delegate('.status-value select[name="status"]', 'change',(e)=>{
    const key = $(e.target).val();
    $('.value-by-tracking [name="status_en"]').val(enArr[key]);
    $('.value-by-tracking [name="status_he"]').val(heArr[key]);
    $('.value-by-tracking [name="status_ua"]').val(uaArr[key]);
})


// phil-ind worksheet
$('#phil-ind-tracking-columns').change((e)=>{
    const thisVal = $(e.target).val();   
    if (thisVal === 'status') {
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.consignee-country-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.shipper-country-value').remove()
        $('.city-value').remove()
        $('.phil-ind-value-by-tracking').append(`
            <div class="phil-ind-status-value">
                <select class="form-control" id="status" name="status">
                   <option value="" selected="selected"></option>
                    
                   <option value="Pending">Pending</option>

                   <option value="Packing list">Packing list</option>
                    
                   <option value="Forwarding to the warehouse in the sender country">Forwarding to the warehouse in the sender country</option>
                    
                   <option value="At the warehouse in the sender country">At the warehouse in the sender country</option>
                    
                   <option value="At the customs in the sender country">At the customs in the sender country</option>
                    
                   <option value="Forwarding to the receiver country">Forwarding to the receiver country</option>
                    
                   <option value="At the customs in the receiver country">At the customs in the receiver country</option>
                    
                   <option value="Forwarding to the receiver">Forwarding to the receiver</option>
                    
                   <option value="Delivered">Delivered</option>
                    
                   <option value="Return">Return</option>
                    
                   <option value="Box">Box</option>
                    
                   <option value="Pick up">Pick up</option>
                    
                   <option value="Specify">Specify</option>
                    
                   <option value="Think">Think</option>
                    
                   <option value="Canceled">Canceled</option>
                    
                </select>                
            </div>
            `)
    }
    else if (thisVal === 'shipper_country') {       
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.phil-ind-status-value').remove()
        $('.consignee-country-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.city-value').remove()
        $('.phil-ind-value-by-tracking').append(`
            <div class="shipper-country-value">
                <select class="form-control" id="shipper_country" name="shipper_country">
                    <option value="" selected="selected"></option>
                    
                    <option value="Israel">Israel</option>
                    
                    <option value="Germany">Germany</option>
                    
                </select>                
            </div>
            `)
    }
    else if (thisVal === 'consignee_country') {
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.phil-ind-status-value').remove()
        $('.shipper-country-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.city-value').remove()
        $('.phil-ind-value-by-tracking').append(`
            <div class="consignee-country-value">
                <select class="form-control" id="consignee_country" name="consignee_country">
                    
                    <option value="India">India</option>
                    
                    <option value="Ivory Coast">Ivory Coast</option>
                    
                    <option value="Nigeria">Nigeria</option>
                    
                    <option value="Ghana">Ghana</option>
                    
                    <option value="Philippines">Philippines</option>
                    
                    <option value="Thailand">Thailand</option>
                    
                </select>                
            </div>
            `)
    }
    else if (thisVal === 'shipper_city') {
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.phil-ind-status-value').remove()
        $('.shipper-country-value').remove()
        $('.consignee-country-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.phil-ind-value-by-tracking').append(`
            <div class="city-value">
                <div class="col-md-4 choose-city-eng">
                    <select class="form-control" name="choose_city_eng"><option value="0" selected="selected">City change method</option>
                    <option value="1">Select from the list (Region will be automatically determined)</option>
                    <option value="2">Enter manually (Region may not be determined)</option>
                    </select>
                </div>

                <div class="col-md-4 choose-city-eng">                    
                    <select class="form-control" style="display:none" disabled="disabled" id="shipper_city" name="shipper_city"><option value="Acre">Acre</option>
                    <option value="Afula">Afula</option>
                    <option value="Arad">Arad</option>
                    <option value="Ariel">Ariel</option>
                    <option value="Ashdod">Ashdod</option>
                    <option value="Ashkelon">Ashkelon</option>
                    <option value="Baqa-Jatt">Baqa-Jatt</option>
                    <option value="Bat Yam">Bat Yam</option>
                    <option value="Beersheba">Beersheba</option>
                    <option value="Beit She'an">Beit She'an</option>
                    <option value="Beit Shemesh">Beit Shemesh</option>
                    <option value="Beitar Illit">Beitar Illit</option>
                    <option value="Binyamina">Binyamina</option> 
                    <option value="Bnei Brak">Bnei Brak</option>
                    <option value="Caesaria">Caesaria</option>
                    <option value="Dimona">Dimona</option>
                    <option value="Eilat">Eilat</option>
                    <option value="El'ad">El'ad</option>
                    <option value="Giv'atayim">Giv'atayim</option>
                    <option value="Giv'at Shmuel">Giv'at Shmuel</option>
                    <option value="Hadera">Hadera</option>
                    <option value="Haifa">Haifa</option>
                    <option value="Herzliya">Herzliya</option>
                    <option value="Hod HaSharon">Hod HaSharon</option>
                    <option value="Holon">Holon</option>
                    <option value="Jerusalem">Jerusalem</option>
                    <option value="Karmiel">Karmiel</option>
                    <option value="Kafr Qasim">Kafr Qasim</option>
                    <option value="Kfar Saba">Kfar Saba</option>
                    <option value="Kiryat Ata">Kiryat Ata</option>
                    <option value="Kiryat Bialik">Kiryat Bialik</option>
                    <option value="Kiryat Gat">Kiryat Gat</option>
                    <option value="Kiryat Malakhi">Kiryat Malakhi</option>
                    <option value="Kiryat Motzkin">Kiryat Motzkin</option>
                    <option value="Kiryat Ono">Kiryat Ono</option>
                    <option value="Kiryat Shmona">Kiryat Shmona</option>
                    <option value="Kiryat Yam">Kiryat Yam</option>
                    <option value="Lod">Lod</option>
                    <option value="Ma'ale Adumim">Ma'ale Adumim</option>
                    <option value="Ma'alot-Tarshiha">Ma'alot-Tarshiha</option>
                    <option value="Migdal HaEmek">Migdal HaEmek</option>
                    <option value="Modi'in Illit">Modi'in Illit</option>
                    <option value="Modi'in-Maccabim-Re'ut">Modi'in-Maccabim-Re'ut</option>
                    <option value="Nahariya">Nahariya</option>
                    <option value="Nazareth">Nazareth</option>
                    <option value="Nazareth Illit">Nazareth Illit</option>
                    <option value="Nesher">Nesher</option>
                    <option value="Ness Ziona">Ness Ziona</option>
                    <option value="Netanya">Netanya</option>
                    <option value="Netivot">Netivot</option>
                    <option value="Ofakim">Ofakim</option>
                    <option value="Or Akiva">Or Akiva</option>
                    <option value="Or Yehuda">Or Yehuda</option>
                    <option value="Pardes Hana">Pardes Hana</option>
                    <option value="Petah Tikva">Petah Tikva</option>
                    <option value="Qalansawe">Qalansawe</option>
                    <option value="Ra'anana">Ra'anana</option>
                    <option value="Rahat">Rahat</option>
                    <option value="Ramat Gan">Ramat Gan</option>
                    <option value="Ramat HaSharon">Ramat HaSharon</option>
                    <option value="Ramla">Ramla</option>
                    <option value="Rehovot">Rehovot</option>
                    <option value="Rishon LeZion">Rishon LeZion</option>
                    <option value="Rosh HaAyin">Rosh HaAyin</option>
                    <option value="Safed">Safed</option>
                    <option value="Sakhnin">Sakhnin</option>
                    <option value="Sderot">Sderot</option>
                    <option value="Shefa-'Amr (Shfar'am)">Shefa-'Amr (Shfar'am)</option>
                    <option value="Tamra">Tamra</option>
                    <option value="Tayibe">Tayibe</option>
                    <option value="Tel Aviv">Tel Aviv</option>
                    <option value="Tiberias">Tiberias</option>
                    <option value="Tira">Tira</option>
                    <option value="Tirat Carmel">Tirat Carmel</option>
                    <option value="Umm al-Fahm">Umm al-Fahm</option>
                    <option value="Yavne">Yavne</option>
                    <option value="Yehud-Monosson">Yehud-Monosson</option>
                    <option value="Yokneam">Yokneam</option>
                    <option value="Zikhron Yakov">Zikhron Yakov</option>
                    </select>
                    <input class="form-control" name="shipper_city" type="text" id="shipper_city">                   
                </div>           
            </div>
            `)
    }
    else if (thisVal === 'status_date') {       
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.phil-ind-status-value').remove()
        $('.shipper-country-value').remove()
        $('.consignee-country-value').remove()
        $('.city-value').remove()
        $('.phil-ind-value-by-tracking').append(`
            <input class="form-control" type="date" name="status_date">
            `)
    }
    else if (thisVal === 'order_date') {       
        $('[name="value-by-tracking"]').remove()
        $('[name="date"]').remove()
        $('.phil-ind-status-value').remove()
        $('.shipper-country-value').remove()
        $('.consignee-country-value').remove()
        $('[name="status_date"]').remove()
        $('.city-value').remove()
        $('.phil-ind-value-by-tracking').append(`
            <input class="form-control" type="date" name="order_date">
            `)
    }
    else if (thisVal === 'date') {       
        $('[name="value-by-tracking"]').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('.phil-ind-status-value').remove()
        $('.shipper-country-value').remove()
        $('.consignee-country-value').remove()
        $('.city-value').remove()
        $('.phil-ind-value-by-tracking').append(`
            <input class="form-control" type="date" name="date">
            `)
    }
    else {
        $('.phil-ind-status-value').remove()
        $('[name="date"]').remove()
        $('.consignee-country-value').remove()
        $('.shipper-country-value').remove()
        $('[name="status_date"]').remove()
        $('[name="order_date"]').remove()
        $('[name="value-by-tracking"]').remove()
        $('.city-value').remove()
        $('.phil-ind-value-by-tracking').append(`
            <textarea class="form-control" name="value-by-tracking"></textarea>
            `)
    }
})

$(document).delegate('.phil-ind-status-value select[name="status"]', 'change',(e)=>{
    const key = $(e.target).val();
    $('.phil-ind-value-by-tracking [name="status_ru"]').val(ruArrChina[key]);
    $('.phil-ind-value-by-tracking [name="status_he"]').val(heArrChina[key]);
})

$(document).delegate('.shipper-country-value select[name="shipper_country"]', 'change',(e)=>{
    $('.phil-ind-value-by-tracking [name="shipper_country_val"]').val($(e.target).val());
})
$(document).delegate('.consignee-country-value select[name="consignee_country"]', 'change',(e)=>{
    $('.phil-ind-value-by-tracking [name="consignee_country_val"]').val($(e.target).val());
})
/* End Tracking filter checkbox*/


/* ID filter checkbox*/
$('[name="checkbox_operations_select"]').change((e)=>{
    const thisVal = $(e.target).val();
    if (thisVal === 'delete') {
        $('.checkbox-operations-change').hide()
        $('.checkbox-operations-color').hide()
        $('.checkbox-operations-delete').show()
    }
    else if (thisVal === 'change'){
        if ($('[name="row_id[]"]:checked').length > 1) {
            $('.checkbox-operations-change').show()
            $('.checkbox-operations-delete').hide()
            $('.checkbox-operations-color').hide()
        }
        else if ($('[type="checkbox"][name="row_id[]"]:checked').length == 1){
            let action = $('.checkbox-operations-change-one').attr('action')
            action += '/'+$('.checkbox-operations-change-one [name="row_id[]"]').val()
            $('.checkbox-operations-change-one').attr('action',action)
            $('.checkbox-operations-change-one').submit()
        }
    }
    else if (thisVal === 'double'){
        if ($('[name="row_id[]"]:checked').length == 1) {
            const x = confirm("You can duplicate only if you have a PDF. Are you sure you want to duplicate?");           
            if (x){
                $('#double-qty').click()
                $('#doubleQty').addClass('show')
                $('#doubleQty').removeAttr('aria-hidden')
                $('body').addClass('modal-open')
                $('[name="duplicate_qty"]').val()
                
                let action = $('.checkbox-operations-double').attr('action')
                action += '/'+$('.checkbox-operations-double [name="row_id[]"]').val()
                $('.checkbox-operations-double').attr('action',action)               
            }
            else
                return false;
            
        }
        else{
            alert('This option is only available with one line!')
        }
    }
    else if (thisVal === 'activate'){
        $('.alert.alert-danger').remove();
        if ($('[name="row_id[]"]:checked').length == 1) {
            const x = confirm("Are you sure you want to activate?");
            if (x){
                let action = $('.checkbox-operations-activate').attr('action');
                const rowId = $('.checkbox-operations-activate [name="row_id[]"]').val();
                const href = action + '-check-activate/'+rowId;               
                $.ajax({
                    url: href,
                    type: "GET",
                    headers: {
                        'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        console.log(data);
                        if (data.error) {
                            $('.card-header').after(`
                                <div class="alert alert-danger">
                                `+data.error+`                                      
                                </div>`)
                            return 0;
                        }
                        else if (data.phone_exist) {
                            let phone = confirm("A record with the same phone number was added to the database recently. Are you sure you want to add the record/records?");
                            if (phone) {
                                action += '-activate/'+rowId
                                $('.checkbox-operations-activate').attr('action',action)
                                $('.checkbox-operations-activate').submit()
                            }                       
                        }
                        else{
                            action += '-activate/'+rowId
                            $('.checkbox-operations-activate').attr('action',action)
                            $('.checkbox-operations-activate').submit()
                        }
                    },
                    error: function (msg) {
                        alert('Request error!');
                    }
                });                
            }       
            else
                return false;
            
        }
        else{
            alert('This option is only available with one line!')
        }
    }
    else if (thisVal === 'cancel-pdf'){
        if ($('[name="row_id[]"]:checked').length == 1) {
            $('.checkbox-operations-cancel-pdf').submit()            
        }
        else{
            alert('This option is only available with one line!')
        }
    }
    else if (thisVal === 'return-draft'){
        if ($('[name="row_id[]"]:checked').length == 1) {
            const x = confirm("Are you sure you want to return it to draft?");
            if (x)
            {
                let action = $('.checkbox-operations-return-draft').attr('action');
                const rowId = $('.checkbox-operations-return-draft [name="row_id[]"]').val();
                action += '/'+rowId; 
                $('.checkbox-operations-return-draft').attr('action',action);
                $('.checkbox-operations-return-draft').submit()
            } 
            else 
                return false
        }
        else{
            alert('This option is only available with one line!')
        }
    }    
    else if (thisVal === 'return-eng-draft'){
        if ($('[name="row_id[]"]:checked').length == 1) {
            const x = confirm("Are you sure you want to return it to draft?");
            if (x)
            {
                let action = $('.checkbox-operations-return-eng-draft').attr('action');
                const rowId = $('.checkbox-operations-return-eng-draft [name="row_id[]"]').val();
                action += '/'+rowId; 
                $('.checkbox-operations-return-eng-draft').attr('action',action);
                $('.checkbox-operations-return-eng-draft').submit()
            } 
            else 
                return false
        }
        else{
            alert('This option is only available with one line!')
        }
    } 
    else if (thisVal === 'add-pdf'){
        if ($('[name="row_id[]"]:checked').length == 1 || $('[name="row_id[]"]:checked').length == 0) {           
            let action = $('.checkbox-operations-add-pdf').attr('action');
            const uId = Date.now().toString(36) + Math.random().toString(36).substr(2);
            let rowId = 0;
            if ($('[name="row_id[]"]:checked').length == 1)
                rowId = $('.checkbox-operations-add-pdf [name="row_id[]"]').val(); 
                   
            $.ajax({
                type:'POST',
                url:createTableUrl,
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {"session_token":uId},
                success:function(data){
                    if (data) {
                        action += '/'+rowId+'/'+data+'/'+userName
                        $('.checkbox-operations-add-pdf').attr('action',action)
                        $('.checkbox-operations-add-pdf').submit() 
                    }               
                },
                error: function (msg){
                    alert('Error')
                }
            });                                 
        }
        else{
            alert('This option is only available with one or null line!')
        }
    }
    else if (thisVal === 'download-pdf'){
        if ($('[name="row_id[]"]:checked').length == 1) {
            $('.checkbox-operations-download-pdf').submit()            
        }
        else{
            alert('This option is only available with one line!')
        }
    }
    else if (thisVal === 'color'){
        $('.checkbox-operations-change').hide()
        $('.checkbox-operations-delete').hide()
        $('.checkbox-operations-color').show()
        $('button.checkbox-operations-change').show()
    }
    else{
        $('.checkbox-operations-change').hide()
        $('.checkbox-operations-delete').hide()
        $('.checkbox-operations-color').hide()
    }
})


$('#add_double_qty').click((e)=>{
    $('[name="duplicate_qty"]').val($('[name="double_qty"]').val())
    $('.checkbox-operations-double').submit()
})


$('[name="row_id[]"]').change((e)=>{
    const thisVal = $(e.target).val();
    if (e.target.checked === true) {        
        $('.checkbox-operations form').append(`
            <input type="hidden" name="row_id[]" value="`+thisVal+`" data-id="`+thisVal+`">
            `);
        $('.checkbox-operations form').append(`
            <input type="hidden" name="old_color[]" 
            value="`+$(e.target).siblings('[name="old_color[]"]').val()+`" 
            data-color="`+thisVal+`">
            `);
        $('.checkbox-operations form .cancel-pdf').val(thisVal)
        $('.checkbox-operations form .download-pdf').val(thisVal)
    }
    else{
        $('.checkbox-operations form input[data-id="'+thisVal+'"]').remove();
        $('.checkbox-operations form input[data-color="'+thisVal+'"]').remove();
    }
})

$('.checkbox-operations form').submit((e)=>{
    if (!$('.checkbox-operations form [name="row_id[]"]').length) {
        if ($('[name="checkbox_operations_select"]').val() !== 'add-pdf') {
            if (location.href.indexOf('new-worksheet') == -1) {
                alert('Select rows!')
            }
            else{
                alert('Выберите строчки!')
            }
            return false
        }       
    }
})


// Phone mask
let countryCode = "+972";
if ($('[name="shipper_country"]').val() === 'Germany') countryCode = "+49";
$('[name="shipper_country"]').on('change', function(){
    if (location.href.indexOf('phil-ind') !== -1 || location.href.indexOf('courier-eng-draft') !== -1){
        if ($(this).val() === 'Germany') {
            countryCode = "+49";
            console.log('"+49"')
            $('.choose-city-eng').hide();
            $('.choose-city-eng [name="shipper_city"]').prop('disabled', true);
            $('.choose-city-germany').show();
            $('.choose-city-germany [name="shipper_city"]').prop('disabled', false);
        }
        if ($(this).val() === 'Israel') {
            countryCode = "+972";
            console.log('"+972"')
            $('.choose-city-germany').hide();
            $('.choose-city-germany [name="shipper_city"]').prop('disabled', true);  
            $('.choose-city-eng').show();
            $('.choose-city-eng [name="shipper_city"]').prop('disabled', false);        
        } 
    }   
});

let count_error = 0;
$(document).delegate('.standard-phone','input', function() {
    $('div.error-phone').remove();
    if (location.href.indexOf('phil-ind') == -1 && location.href.indexOf('courier-eng-draft') == -1) {

        if ($(this).val()[0] !== '+' && $(this).val().length == 1) {
            $(this).val(countryCode);
        }
        else if($(this).val().length > 16){
            if ($(this).val().length == 17) {
                $(this).val($(this).val().slice(0, -1));
            }
            else{
                $(this).val(countryCode);
            }
        }
        else if($(this).val().length < 5){
            $(this).val(countryCode);
        }
        else{
            var regexp = /^\+972[0-9]+$/i;
            if (!regexp.test($(this).val()) && count_error == 0) {
                for (var i = $(this).val().length - 1; i >= 0; i--) {
                    if (!regexp.test($(this).val())) {
                        $(this).val($(this).val().slice(0, -1));
                    }
                    else break;
                }           
                count_error = 1; 

                $(this).before(`
                    <div class="error-phone">
                    Пожалуйста, заполните поле "Номер телефона отправителя (основной)" в 
                    международном формате, например: "+972531111111".
                    </div>`);

            } else if (!regexp.test($(this).val()) && count_error == 1 && $(this).val().length > 1) {
                for (var i = $(this).val().length - 1; i >= 0; i--) {
                    if (!regexp.test($(this).val())) {
                        $(this).val($(this).val().slice(0, -1));
                    }
                    else break;
                }

                $(this).before(`
                    <div class="error-phone">
                    Пожалуйста, заполните поле "Номер телефона отправителя (основной)" в 
                    международном формате, например: "+972531111111".
                    </div>`);

            } else if ($(this).val().length < 5 || regexp.test($(this).val())) {
                count_error = 0;
            }
        }    
    }
    else if (location.href.indexOf('phil-ind') !== -1 || location.href.indexOf('courier-eng-draft') !== -1){
        let phoneVal = "+972531111111";
        let regexp = /^\+972[0-9]+$/i;
        let minLength = 5;
        if ($('[name="shipper_country"]').val() === 'Germany') {
            regexp = /^\+49[0-9]+$/i;
            phoneVal = "+4953111111111";
            countryCode = "+49";
            minLength = 4;
        }
        
        if ($(this).val()[0] !== '+' && $(this).val().length == 1) {
            $(this).val(countryCode);
        }
        else if($(this).val().length > 16){
            if ($(this).val().length == 17) {
                $(this).val($(this).val().slice(0, -1));
            }
            else{
                $(this).val(countryCode);
            }
        }
        else if($(this).val().length < minLength){
            $(this).val(countryCode);
        }
        else{           
            if (!regexp.test($(this).val()) && count_error == 0) {
                for (var i = $(this).val().length - 1; i >= 0; i--) {
                    if (!regexp.test($(this).val())) {
                        $(this).val($(this).val().slice(0, -1));
                    }
                    else break;
                }           
                count_error = 1; 

                $(this).before(`
                    <div class="error-phone">
                    Please fill the box "Shipper\'s phone number (standard)" in the 
                    international format, i.e. `+phoneVal+`.
                    </div>`);

            } else if (!regexp.test($(this).val()) && count_error == 1 && $(this).val().length > 1) {
                for (var i = $(this).val().length - 1; i >= 0; i--) {
                    if (!regexp.test($(this).val())) {
                        $(this).val($(this).val().slice(0, -1));
                    }
                    else break;
                }

                $(this).before(`
                    <div class="error-phone">
                    Please fill the box "Shipper\'s phone number (standard)" in the 
                    international format, i.e. `+phoneVal+`.
                    </div>`);

            } else if ($(this).val().length < minLength || regexp.test($(this).val())) {
                count_error = 0;
            }
        }    
    }        
});


/* Table scroll */
$(".table-container").scroll(function() {
    if ($(".table-container").scrollLeft()) {
        $(".table-container table tbody td:first-child input").css({
            'position':'absolute',
            'margin-top':'-15px'
        })
    }
    else{
        $(".table-container table tbody td:first-child input").css({
            'position':'inherit'
        })
    }
    if ($(".table-container").scrollTop()) {
        $(".table-container table tbody td:first-child input").css({
            'position':'inherit'
        })
    }
});


/*ФУНКЦИИ ДЛЯ УДАЛЕНИЯ И ДОБАВЛЕНИЯ ОПРЕДЕЛЁННЫХ GET ПАРАМЕТРОВ */
function removeURLParameter(url, parameter) {
    //prefer to use l.search if you have a location/link object
    var urlparts= url.split('?');   
    if (urlparts.length>=2) {

        var prefix= encodeURIComponent(parameter)+'=';
        var pars= urlparts[1].split(/[&;]/g);

        //reverse iteration as may be destructive
        for (var i= pars.length; i-- > 0;) {    
            //idiom for string.startsWith
            if (pars[i].lastIndexOf(prefix, 0) !== -1) {  
                pars.splice(i, 1);
            }
        }
        
        if(pars.length > 0) {
            url= urlparts[0]+'?'+pars.join('&');
        } else {
            url= urlparts[0];
        }

        return url;
    } else {
        return url;
    }
}


function serializeGet(obj) {
    var str = [];
    for(var p in obj){
        if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
        }
    }

    return str.join("&");
}


function addGet(url, get) {

    if (typeof(get) === 'object') {
        get = serializeGet(get);
    }

    if (url.match(/\?/)) {
        return url + '&' + get;
    }

    if (!url.match(/\.\w{3,4}$/) && url.substr(-1, 1) !== '/') {
        url += '/';
    }
    
    return url + '?' + get;
}


// Show posts for activation
function forActivation(event)
{
    let href = location.href;

    if (event.target.checked){                      
        document.querySelector('[name="for_active"]').value = 'for_active';
        location.href = addGet(href, 'for_active=for_active');
    }
    else{
        document.querySelector('[name="for_active"]').value = '';
        location.href = removeURLParameter(href, 'for_active');         
    }
}


// Shipper City list
$(document).delegate('select[name="choose_city_ru"]', 'change', function(){
    if ($(this).val() === '1') {
        $('.choose-city-ru select[name="sender_city"]').show();
        $('.choose-city-ru select[name="sender_city"]').prop('disabled', false);
        $('.choose-city-ru input[name="sender_city"]').hide();
        $('.choose-city-ru input[name="sender_city"]').prop('disabled', true);
    }  
    else if ($(this).val() === '2') {
        $('.choose-city-ru select[name="sender_city"]').hide();
        $('.choose-city-ru select[name="sender_city"]').prop('disabled', true);
        $('.choose-city-ru input[name="sender_city"]').show();
        $('.choose-city-ru input[name="sender_city"]').prop('disabled', false);
    }                         
});

$(document).delegate('select[name="choose_city_eng"]', 'change', function(){
    if ($(this).val() === '1') {
        $('.choose-city-eng select[name="shipper_city"]').show();
        $('.choose-city-eng select[name="shipper_city"]').prop('disabled', false);
        $('.choose-city-eng input[name="shipper_city"]').hide();
        $('.choose-city-eng input[name="shipper_city"]').prop('disabled', true);
    }  
    else if ($(this).val() === '2') {
        $('.choose-city-eng select[name="shipper_city"]').hide();
        $('.choose-city-eng select[name="shipper_city"]').prop('disabled', true);
        $('.choose-city-eng input[name="shipper_city"]').show();
        $('.choose-city-eng input[name="shipper_city"]').prop('disabled', false);
    }                         
});


// Modals for table cells
$('table td.allowed-update').not('.td-checkbox, .td-button, .pdf-file').click((e)=>{
    $('#updateCellModal [name="row_id[]"]').remove()
    $('#updateCellModal [name="tracking-columns"]').remove()
    $('#updateCellModal [name="phil-ind-tracking-columns"]').remove()
    $('#updateCellModal select').remove()
    $('#updateCellModal [type="date"]').remove()
    let id = ''
    let name = ''
    let table = ''
    let value = ''
    if ($(e.target).prop("tagName") === 'DIV') {
        id = $(e.target).attr('data-id')
        name = $(e.target).attr('data-name')
        value = $(e.target).text()
    }
    else if ($(e.target).prop("tagName") === 'TD') {
        id = $(e.target).children('div').attr('data-id')
        name = $(e.target).children('div').attr('data-name')
    }
    if (id) {       
        $('#update-cell').click()
        $('#updateCellModal').addClass('show')
        $('#updateCellModal').removeAttr('aria-hidden')
        $('body').addClass('modal-open')
        $('#tracking-columns').val(name).change()
        $('#phil-ind-tracking-columns').val(name).change()
        $('#updateCellModal [name="value-by-tracking"]').val(value)
        $('#updateCellModal select').val(value)
        if (name === 'standard_phone') {
            $('#updateCellModal [name="value-by-tracking"]').remove()
            $('#updateCellModal .value-by-tracking').append(`
            <input type="text" class="form-control standard-phone" 
                name="value-by-tracking" value="`+value+`" >
            `)
            $('#updateCellModal .phil-ind-value-by-tracking').append(`
            <input type="text" class="form-control standard-phone" 
                name="value-by-tracking" value="`+value+`" >
            `)
        }
        $('#updateCellModal .value-by-tracking').append(`
            <input type="hidden" name="row_id[]" value="`+id+`">
            <input type="hidden" name="tracking-columns" value="`+name+`">
            `)
        $('#updateCellModal .phil-ind-value-by-tracking').append(`
            <input type="hidden" name="row_id[]" value="`+id+`">
            <input type="hidden" name="phil-ind-tracking-columns" value="`+name+`">
            `)
        
    }
    
})