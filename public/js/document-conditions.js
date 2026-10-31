$(document).ready(function () {

	if(pageSource == 'form'){
		var formId = 'form-conditions';
	}
	else if(pageSource == 'pdf'){
		var formId = 'pdf-conditions';
	}

	//** check if there are some existing form conditions already added
	console.log(documentConditions);
	if(!$.isEmptyObject(documentConditions)){
		$.each(documentConditions,function(i,data){
			addHeader(i);
			populateHeader(i,data);
		});
	}

	if(!$(".conditions-repeater").length){
		$(".add-first-condition").show();
	}

	$(document).on('click',".add-first-condition",function(){
		addHeader(0,1);	
		$(this).hide();
	});


	$(document).on('click','.add-header',function(){
		var headerRow = $(".conditions-repeater:last").data('header-row');
		headerRow++;
		addHeader(headerRow,1);
	});

	$(document).on('click','.remove-header',function(){
		var headerRow = $(this).data('header-row');
		removeHeader(headerRow);
		if(!$(".conditions-repeater").length){
			$(".add-first-condition").show();
		}
	});

	$(document).on('click','.add-body',function(){
		var headerRow = $(this).data('header-row');
		var bodyRow = $(".conditions_repeater_header_row_"+headerRow+" .form-condition-body .row:last").data('body-row');
		bodyRow++;
		addBody(headerRow,bodyRow);
	});

	$(document).on('click','.remove-body',function(){
		var headerRow = $(this).data('header-row');
		var bodyRow = $(this).data('body-row');
		removeBody(headerRow,bodyRow);
	});


	$(document).on('change','.conditional_field_option',function(){
		if($(this).val() == '1'){
			var language = conditionalFieldLang;
			$(this).closest('.conditional_field_option_container').next('.conditional_field_container').find('.document_field_id').show();
			$(this).closest('.conditional_field_option_container').next('.conditional_field_container').find('.document_section_id').hide();
			$(this).closest('.conditional_field_option_container').next('.conditional_field_container').find('label').text(language);
		}
		else if($(this).val() == '2'){
			var language = conditionalSecionLang;
			$(this).closest('.conditional_field_option_container').next('.conditional_field_container').find('.document_field_id').hide();
			$(this).closest('.conditional_field_option_container').next('.conditional_field_container').find('.document_section_id').show();
			$(this).closest('.conditional_field_option_container').next('.conditional_field_container').find('label').text(language);
		}
	});

	$(document).on('change','.condition_criteria',function(){
		if($(this).val() == 'reference'){
			$(this).closest('.condition_criteria_container').next('.reference_container').find('.reference_field').show();
			$(this).closest('.condition_criteria_container').next('.reference_container').find('.reference_value').hide();

			$(this).closest('.condition_criteria_container').next('.reference_container').find('.reference_value').removeAttr('required');
		}
		else if($(this).val() == 'value'){
			$(this).closest('.condition_criteria_container').next('.reference_container').find('.reference_field').hide();
			$(this).closest('.condition_criteria_container').next('.reference_container').find('.reference_value').show();

			$(this).closest('.condition_criteria_container').next('.reference_container').find('.reference_value').attr('required',true);

		}
	});

	jQuery.validator.addMethod("logic", function(value, element, params) {
		//alert($(element).closest('.row').html());
	if(value == '&&' || value == '||'){
		if(!$(element).closest('.row').next('.row').length){
			return false;
		}
		else{
			return true;
		}
	}
	else if(!value){
		if($(element).closest('.row').next('.row').length){
			return false;
		}
		else{
			return true;
		}
	}
	}, jQuery.validator.format("Please select proper value here."));

	//** form validation and submission
	$('#'+formId).validate({
      debug: false,
      errorClass: 'help-block',
      errorElement: "span",
      rules: {

      },
      highlight: function (element, errorClass, validClass)
			{
				$(element).parents('.col-field-wrapper').addClass('has-error');
			},
			unhighlight: function (element, errorClass, validClass) {
				$(element).parents('.col-field-wrapper').removeClass('has-error');
			},
			invalidHandler: function(form, validator)
			{
				if (!validator.numberOfInvalids())
					return;

				$('html, body').animate({
					scrollTop: ($(validator.errorList[0].element).offset().top - 100)
				}, 500);
			}
    });

    $("#"+formId).submit(function(){
    	if($('.form-validate').valid()){
    		return true;
    	}
    	else{
    		return false;
    	}
    });

});

function populateHeader(headerRow,data){
	$(".conditions_repeater_header_row_"+headerRow).find('.id').val(data.id);
	if(pageSource == 'form'){
		$(".conditions_repeater_header_row_"+headerRow).find('.conditional_field_option').val(data.conditional_field_option);
		if(data.conditional_field_option == 1){
			$(".conditions_repeater_header_row_"+headerRow).find('.document_field_id').show().val(data.document_field_id+'||'+data.uin);
			$(".conditions_repeater_header_row_"+headerRow).find('.document_section_id').hide();
		}
		else{
			$(".conditions_repeater_header_row_"+headerRow).find('.document_section_id').show().val(data.document_section_id+'||'+data.uin);
			$(".conditions_repeater_header_row_"+headerRow).find('.document_field_id').hide();
		}
	}
	else if(pageSource == 'pdf'){
		$(".conditions_repeater_header_row_"+headerRow).find('.document_pdf_text_id').show().val(data.document_pdf_text_id+'||'+data.uin);
	}
	$(".conditions_repeater_header_row_"+headerRow).find('.action').val(data.action);

	//** populate body
	var condition = JSON.parse(data.condition);
	$.each(condition.source_field,function(i,conditionData){
		addBody(headerRow,i);
		populateBody(headerRow,i,condition);
	});

}

function populateBody(headerRow,bodyRow,condition){
	$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.source_field').val(condition.source_field[bodyRow]);
	$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.operator').val(condition.operator[bodyRow]);
	$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.condition_criteria').val(condition.condition_criteria[bodyRow]);
	if(condition.condition_criteria[bodyRow] == 'reference'){
		$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.reference_field').show().val(condition.reference_field[bodyRow]);
		$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.reference_value').hide();

		$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.reference_value').removeAttr('required');

	}
	else if(condition.condition_criteria[bodyRow] == 'value'){
		$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.reference_value').show().val(condition.reference_value[bodyRow]);
		$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.reference_field').hide();

		$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.reference_value').attr('required',true);

	}
	$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).find('.logic').val(condition.logic[bodyRow]);
}

function addHeader(headerRow,toAddBody){
	var html = '<div class = "form-condition-header">'+header+'</div>';
	html = '<div class = "conditions-repeater conditions_repeater_header_row_hrg" data-header-row = "hrg">'+html+'</div>';
	html = html.replace(/hrg/g,headerRow);
	html = html.replace(/header-hide/g,'header-show');
	if(headerRow == 0){
		$(".manage-conditions-main").html(html);
	}
	else{
		$(html).insertAfter(".conditions-repeater:last");
	}
	if(toAddBody){
		addBody(headerRow,0);
	}
}

function removeHeader(headerRow){
	$(".conditions_repeater_header_row_"+headerRow).remove();
}

function addBody(headerRow,bodyRow){
	var html = body.replace(/hrg/g,headerRow);
	html = html.replace(/brg/g,bodyRow);

	if(bodyRow == 0){
		html = '<div class = "form-condition-body">'+html+'</div>';
		$(html).insertAfter(".conditions_repeater_header_row_"+headerRow+" .form-condition-header");
	}
	else{
		html = html.replace(/body-hide/g,'body-show');
		$(".conditions_repeater_header_row_"+headerRow+" .form-condition-body").append(html);
	}

	//** remove error from previous row's logic element
	$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).prev('.row').find('.logic').next('.help-block').remove();
	
}

function removeBody(headerRow,bodyRow){
	//** remove error from previous row's logic element
	$(".conditions_repeater_body_row_"+headerRow+"_"+bodyRow).prev('.row').find('.logic').next('.help-block').remove();
	
	$(".conditions_repeater_body_row_"+headerRow+'_'+bodyRow).remove();
}