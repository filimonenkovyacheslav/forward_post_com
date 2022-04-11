// Очистка URL
history.pushState("", document.title, window.location.pathname);
/**
 * Switch of languages
**/ 
$(document).ready(()=>{
	$('.language-list > div').each((k,el)=>{
		if ($(el).hasClass('active-language')) {
			const target = $(el);
			target.detach();
			target.prependTo('.language-list');
		}
	});
})

function languageParent(elem) {
	const target = $(elem);
	if (target.hasClass('active-div-too')) {
		location.href = target.attr('data-locale');
	}
	else if(target.hasClass('active-language')){
		if (!target.hasClass('active-div') && !target.hasClass('child-event')) {
			target.addClass('active-div');
			$('.language-list > div').each((k,el)=>{
				if (!$(el).hasClass('active-div')) {
					$(el).addClass('active-div-too');
				}
			});
			$('.language-list div').css('display','flex');
			$('.language-list > div').css('width','153px');
			$('.language-list').css('background-color','rgba(243, 243, 243, 1)');
		}
		else{
			$('.language-list div').removeClass('child-event');		
			$('.language-list div').removeClass('active-div');
			$('.language-list div').removeClass('active-div-too');
			$('.language-list div').css('display','none');
			$('.language-list > div').css('width','99px');
			$('.language-list .active-language').css('display','flex');
			$('.language-list').css('background-color','#E2E2E2');
		}
	}
}


function languageChild(elem) {	
	const target = $(elem).parent();
	if (target.hasClass('active-div-too')) {
		location.href = target.attr('data-locale');
	}
	else if(target.hasClass('active-language')){
		if (!target.hasClass('active-div')) {
			target.addClass('active-div');
			$('.language-list > div').each((k,el)=>{
				if (!$(el).hasClass('active-div')) {
					$(el).addClass('active-div-too');
				}
			});
			$('.language-list div').css('display','flex');
			$('.language-list > div').css('width','153px');
			$('.language-list').css('background-color','rgba(243, 243, 243, 1)');
		}
		else{		
			$('.language-list div').removeClass('active-div');
			$('.language-list div').removeClass('active-div-too');
			$('.language-list div').css('display','none');
			$('.language-list > div').css('width','99px');
			$('.language-list .active-language').css('display','flex');
			$('.language-list').css('background-color','#E2E2E2');
		}
	}
}  


/**
 * Adaptive
**/ 
$(document).ready(()=>{
	const widthW = $(window).width();
	if (widthW < 992) {
		const target = $('.language-menu');
		target.removeClass('col-md-2');
		$('.guarantee').removeClass('col-md-2');
		target.detach();
		target.prependTo('.row.first-row');
	}
	
	$('ul.navbar-nav>li.nav-item').click((e)=>{		
		const target = $(e.target);
		if (target.hasClass('nav-link')) {
			e.preventDefault();
		}
		if (target.siblings('ul').css('display') == 'none') {
			target.siblings('ul').css('display','flex');
		}
		else{
			target.siblings('ul').css('display','none');
		}		
	})

	$('.temporary-account > a').click((e)=>{		
		const target = $(e.target);
		e.preventDefault();
		if (target.siblings('.dd-dropdown-menu').css('display') == 'none') {
			target.siblings('.dd-dropdown-menu').css('display','block');
		}
		else{
			target.siblings('.dd-dropdown-menu').css('display','none');
		}		
	})
})

$(window).resize(()=>{
	const widthW = $(window).width();
	if (widthW < 992) {
		const target = $('.language-menu');
		target.removeClass('col-md-2');
		$('.guarantee').removeClass('col-md-2');
		target.detach();
		target.prependTo('.row.first-row');
	}
	else{
		const target = $('.language-menu');
		target.addClass('col-md-2');
		$('.guarantee').addClass('col-md-2');
		target.detach();
		target.appendTo('.row.first-row');
	}
})


/**
 * Parcel form modal
**/ 

$('[name="not_first_order"]').change((e)=>{
	if (e.target.checked === true) {
		$('.ru-modal').click();
		$('.eng-modal').click();
	}		
})

var quantityClick = 0;
var quantityYes = 0;
var quantityNo = 0;
var quantitySender = 0;
var quantityRecipient = 0;

function clickAnswer(elem) {	
	quantityClick++;
	if ($(elem).hasClass('yes')) quantityYes++;
	if ($(elem).hasClass('no')) quantityNo++;
	if ($(elem).hasClass('sender')) {
		quantitySender++;
		$('[name="quantity_sender"]').val(quantitySender);
	}
	if ($(elem).hasClass('recipient')) {
		quantityRecipient++;
		$('[name="quantity_recipient"]').val(quantityRecipient);
	}

	if(quantityClick == 1) {		
		setTimeout(
			()=>{ 
				$('#addRuParcel .question').text('Введите ваш номер телефона');
				$('#addRuParcel .yes').hide();
				$('#addRuParcel .no').hide();
				$('#addRuParcel .check-phone').show();
				$('#addRuParcel').modal(); 
			}, 500);					
	}
}


function philIndAnswer(elem) {
	quantityClick++;
	if ($(elem).hasClass('yes')) quantityYes++;
	if ($(elem).hasClass('no')) quantityNo++;
	if ($(elem).hasClass('sender')) {
		quantitySender++;
		$('[name="quantity_sender"]').val(quantitySender);
	}
	if ($(elem).hasClass('recipient')) {
		quantityRecipient++;
		$('[name="quantity_recipient"]').val(quantityRecipient);
	}
	
	if(quantityClick == 1) {		
		setTimeout(
			()=>{ 
				$('#philIndParcel .question').text('Enter your phone number');
				$('#philIndParcel .yes').hide();
				$('#philIndParcel .no').hide();
				$('#philIndParcel .check-phone').show();
				$('#philIndParcel').modal(); 
			}, 500);					
	}
}


if (phoneExist) {
	$('.ru-modal-2').click();
	$('.eng-modal-2').click();
}


function clickAnswer2(elem) {	
	quantityClick++;
	if ($(elem).hasClass('yes')) quantityYes++;
	if ($(elem).hasClass('no')) quantityNo++;
	if ($(elem).hasClass('sender')) {
		quantitySender++;
		$('[name="quantity_sender"]').val(quantitySender);
	}
	if ($(elem).hasClass('recipient')) {
		quantityRecipient++;
		$('[name="quantity_recipient"]').val(quantityRecipient);
	}
	
	if(quantityClick == 1 && quantityYes == 0) {		
		setTimeout(
			()=>{ 
				$('#phoneExist .question').text('Благодарим за уточнение. Ваш существующий заказ обрабатывается');
				$('#phoneExist .yes').hide();
				$('#phoneExist .no').hide();
				$('#phoneExist').modal(); 
				quantityClick = 0;
				quantityYes = 0;
				quantityNo = 0;
				quantitySender = 0;
				quantityRecipient = 0;
			}, 500);					
	}
	else if(quantityClick == 1 && quantityYes == 1) {		
		setTimeout(
			()=>{ 
				$('#phoneExist [name="sender_phone"]').val(phoneNumber);
				$('[name="quantity_recipient"]').val('');
				$('#phoneExist .check-phone').submit();				
			}, 500);					
	}
}


function philIndAnswer2(elem) {
	quantityClick++;
	if ($(elem).hasClass('yes')) quantityYes++;
	if ($(elem).hasClass('no')) quantityNo++;
	if ($(elem).hasClass('sender')) {
		quantitySender++;
		$('[name="quantity_sender"]').val(quantitySender);
	}
	if ($(elem).hasClass('recipient')) {
		quantityRecipient++;
		$('[name="quantity_recipient"]').val(quantityRecipient);
	}
	
	if(quantityClick == 1 && quantityYes == 0) {		
		setTimeout(
			()=>{ 
				$('#phoneExist .question').text('Thank you for your clarification. Your existing order is being processed');
				$('#phoneExist .yes').hide();
				$('#phoneExist .no').hide();
				$('#phoneExist').modal(); 
				quantityClick = 0;
				quantityYes = 0;
				quantityNo = 0;
				quantitySender = 0;
				quantityRecipient = 0;
			}, 500);					
	}
	else if(quantityClick == 1 && quantityYes == 1) {		
		setTimeout(
			()=>{ 
				$('#phoneExist [name="shipper_phone"]').val(phoneNumber);
				$('[name="quantity_recipient"]').val('');
				$('#phoneExist .check-phone').submit();				
			}, 500);					
	}
}


// Phone mask
let countryCode = "+972";
$('[name="shipper_country"]').on('change', function(){
	if (location.href.indexOf('phil-ind') !== -1 || location.href.indexOf('add-form-en') !== -1){
		if ($(this).val() === 'Germany') {
			countryCode = "+49";
			$('select[name="shipper_city"]').hide();
			$('label[for="shipper_city"]').hide();
            $('select[name="shipper_city"]').prop('disabled', true);
            $('select[name="shipper_city"]').after(`
                <input placeholder="Shipper's city*" required="required" name="shipper_city" type="text" class="form-control">
                `);
		}
		if ($(this).val() === 'Israel') {
			countryCode = "+972";
			$('select[name="shipper_city"]').show();
			$('label[for="shipper_city"]').show();
            $('select[name="shipper_city"]').prop('disabled', false);
            $('input[name="shipper_city"]').remove();
		}
		$('.standard-phone').val(countryCode);	
	}	
});
if (location.href.indexOf('phil-ind') !== -1 || location.href.indexOf('add-form-en') !== -1){
	if ($('[name="shipper_country"]').val() === 'Germany') countryCode = "+49";
	if (!$('.standard-phone').val()) $('.standard-phone').val(countryCode);	
}

let count_error = 0;
$('.standard-phone').on('input', function() {
	$('div.error-phone').remove();
	if (location.href.indexOf('phil-ind') == -1 && location.href.indexOf('add-form-en') == -1) {

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
	else if (location.href.indexOf('phil-ind') !== -1 || location.href.indexOf('add-form-en') !== -1){
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


// Shipper City list
$('select[name="sender_city"]').on('change', function(){
	if ($(this).val() === 'other') {
		$('select[name="sender_city"]').hide();
		$('label[for="sender_city"]').hide();
		$('select[name="sender_city"]').prop('disabled', true);
		$('select[name="sender_city"]').after(`
			<input placeholder="Shipper's city*" required="required" name="sender_city" type="text" class="form-control">
			`);
	}							
});
$('select[name="shipper_city"]').on('change', function(){
	if ($(this).val() === 'other') {
		$('select[name="shipper_city"]').hide();
		$('label[for="shipper_city"]').hide();
		$('select[name="shipper_city"]').prop('disabled', true);
		$('select[name="shipper_city"]').after(`
			<input placeholder="Shipper's city*" required="required" name="shipper_city" type="text" class="form-control">
			`);
	}							
});

