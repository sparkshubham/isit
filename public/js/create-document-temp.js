$(document).ready(function(){
    /*setTimeout(function(){
      processTotalProgress();
    },1);*/

    //** for last section, line orginating from that should not be visible. (below code will useful if last section is hidden)
    if(documentSection == 1){
      $(".doc-order-list li:visible:last").addClass('section-form-hide-prev');
    }
    
    //** show hide field based on state selection and condition
    if(!documentEditMode){
      //** if first section is hidden, show next section
      if($(".doc-order-list li:first").hasClass('section-form-hide')){
        triggerNext(0);
      }
      processFormConditions(statepromptuin);
    }
    else{
      $("#document-form").find('input,select,textarea').each(function(){
        
        if($(this).attr('type') == 'checkbox'){
          processFormConditions($(this).attr('id'));
        }else{
          processFormConditions($(this).attr('name'));
        }
        
      });
      processFormConditions(statepromptuin);
      processTotalProgress();

      //** save dataset group html to handle the skip operation
      holdOldDatasetHtml();

      //** in edit mode, we will open last completed section also
      if(documentSection && lastActiveSection){
        setTimeout(function(){
          $("#tab-"+lastActiveSection+" a").trigger('click');
        },100);
      }
    }

    $('.datepicker').datetimepicker({
      format: 'MM/DD/YYYY',
    });
    $('.timepicker').datetimepicker({
      format: 'HH:mm',
    });

    $('#document-form').validate({
        ignore: ".ignore",
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
        },
        errorPlacement: function(error,element){
          if($(element).prop('nodeName') == 'SELECT'){
            error.insertAfter($(element).parent('div'));
          }
          else{
            if($(element).attr('type') == 'checkbox'){
              error.insertAfter($(element).parent('label').parent('div'));
            }
            else if($(element).attr('type') == 'radio'){
              error.insertAfter($(element).parent('label').parent('div'));
            }
            else{
              error.insertAfter($(element));
            } 
          }
        },
        /*showErrors: function (errorMap, errorList) {

              if (typeof errorList[0] != "undefined") {
                  var position = $(errorList[0].element).position().top;
                  $('html, body').animate({
                      scrollTop: position
                  }, 300);
              }
              this.defaultShowErrors(); // keep error messages next to each input element   
        }*/
    });

    //** handle save and next click
    $(document).on('click',"#save",function(){
      
        var currentActiveSectionTab = $(".tab-content .tab-pane.active").attr('id');
        if($("#"+currentActiveSectionTab).find('input,textarea,select').length > 0){
          if($("#"+currentActiveSectionTab).find('input,textarea,select').valid()){
            saveUserDocumentData(1,0,0);
            $("#tab-"+currentActiveSectionTab).removeClass('section-error');
            return true;
          }
          else{
            $('html, body').animate({
                 scrollTop: ($("#"+currentActiveSectionTab).find('.help-block:visible').offset().top - 300)
            }, 500);
            return false;
          }
        }
    }); 

    //** handle save work button
    $(document).on('click',"#savework",function(){
      /*saveUserDocumentData(0,0,1);
      if(authCheck){
        return true;
      }
      else{
        //alert('Signup Flow/Login popup');
        $('.signin').html("Sign In to Save your Work");
        $('.signup').html("Register to Save your Work");
        $('.sign-in-popup-model').trigger('click');
      }*/
      return false;
    }); 

    //** handle form submit at last step
    $("#document-form").submit(function(){
      if($(this).valid()){
        return false;
      }
      else{
        //** mark those section red for which child fields have some error
        $("div.section-form-show").each(function(){
            var sectionId = $(this).attr('id');
            if($(this).find('input,textarea,select').length > 0){
              if(!$(this).find('input,textarea,select').valid()){
                $("#tab-"+sectionId).addClass('section-error');
              }
            }
          });
        $(".section-error:first a").trigger('click');
        return false;
      }
    });

    //** handle back button
    $(document).on('click','#back',function(){
      triggerBack();
    });

    //** handle skip button
    $(document).on('click','#skip',function(){
      resetCurrentSectionForm();
      triggerNext(0);
    });

    //alert(JSON.stringify(formConditions));
    
    $(document).on('change',"input[type='radio'],input[type='checkbox'],select",function(){
      if($(this).attr('type') == 'checkbox'){
        var elementName = $(this).attr('id');
        //alert(elementName);
      }
      else{
        var elementName = $(this).attr('name');
      }
      
      //** create a dynamic variable with the name of field uin. We will use this variable for the math formulas created on any other field
      createUpdateDynamicalVariableValues(this,$(this).val());
      processFormConditions(elementName); 
      processFormConditions(statepromptuin);
      processTotalProgress();     
    });

    $(document).on('blur',"input[type!='radio'],input[type!='checkbox'],textarea",function(){
      var elementName = $(this).attr('name');
      //** create a dynamic variable with the name of field uin. We will use this variable for the math formulas created on any other field
      createUpdateDynamicalVariableValues(this,$(this).val());
      processFormConditions(elementName); 
      processFormConditions(statepromptuin);
      processTotalProgress();     
    });

    $(document).on('click',".section-tab",function(){
      submitShowHide($(this).parent('li').attr('data-uin'));
      //** show hide faqs
      var currentSectionUin = $(".doc-order-list li.active").attr('data-uin');
      $(".faqs").hide();
      $("#docFaq-"+currentSectionUin).show();

    });

    $(document).on('click',"#docRefreshBtn",function(){
      refreshDocument();
    });

    $(document).on('click','.add-more',function(){
      var clonedHtml = $(this).closest('.data-set-row').clone();
      var fieldUin = $(this).data('uin');

      var currentCount = eval(fieldUin)['count'];
      var newCount = parseInt(currentCount+1);

      //** check number of rows option
      var numberOfRowsOption = eval(fieldUin)['number_of_rows_option'];
      
      if(numberOfRowsOption == 2){
        var numberOfRowsReference = eval(fieldUin)['number_of_rows'];
        var rowsLimit = parseInt($("[id='"+numberOfRowsReference+"']").val());
      }
      else if(numberOfRowsOption == 3){
        var rowsLimit = parseInt(eval(fieldUin)['number_of_rows']);
      }

      //** validate limit
      if(typeof rowsLimit !== 'undefined' && !isNaN(rowsLimit)){
        var currentRows = $('[id="field-'+fieldUin+'"]').find('.normal-dataset-fields .data-set-row').length;
        if(currentRows >= rowsLimit){
          bootbox.alert(rowsError);
          return false;
        }
      }


      $(clonedHtml).find('.hide').removeClass('hide').addClass('show');
      $(clonedHtml).find('.select-box-btn').addClass('add-remove-button-parent');
      //** reset selectpicker if any
      clonedHtml.find('.bootstrap-select').replaceWith(function() { return $('select', this); })    
      clonedHtml.find('.selectpicker').selectpicker('render'); 
      //** reset input/textarea fields

      $(clonedHtml).find('input,textarea,select').each(function(){
        if($(this).prop('nodeName') == 'SELECT'){
          //** reseting select picker with jquery as selectpicker render/refresh not reseting the select picker in edit mode
          $(this).find('option').each(function(){
            if($(this).val() == ''){
              $(this).attr('selected','selected');
            }
            else{
              $(this).removeAttr('selected');
            }
          });
          $(this).next('button').find('.filter-option-inner-inner').text('Select');
        }
        else{
          if($(this).attr('type') == 'radio'){
            $(this).prop('checked',false);
          }
          else{
            $(this).val('');
          }
        }
        //** change element name attr according to total row count 
        
        //** update new count in data set new row's elements
        $(this).attr('name',$(this).data('uin')+'['+newCount+']');
        $(this).data('count',newCount);
      });      

      $('[id="field-'+fieldUin+'"]').find('.normal-dataset-fields').append(clonedHtml);
      //** update global count value of dataset field uin dynamic variable
      eval(fieldUin)['count'] = newCount;
      processTotalProgress();
    });

    $(document).on('click','.remove-more',function(){
      $(this).closest('.data-set-row').remove();
      processTotalProgress();
    });

    //** prevent form-submission on enter key
    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });


    /*$("#document-form").find('input').keypress(function(event) {
      allowTwoDecimalPoints(this,event);
    });*/
      
});

/**
 * @summary This function will be used to store user document data
*/
function saveUserDocumentData(triggerNextFlag,documentCompleted,saveWork){
  if(documentSection == 1){
    var currentActiveSection = $(".tab-content .tab-pane.active").attr('id');
    var completedSections = {};
    if(saveWork){
      var postData = {};
      var datasetData = {};
      $(".tab-content .tab-pane").each(function(i){
        var sectionId = $(this).attr('id');
        postData[sectionId] = $(this).find('input[type!="checkbox"],textarea,select').not('.dataset').serializeArray();

        //** process checkbox data
        var checkboxPushed = [];
        $(this).find('input[type="checkbox"]').each(function(){
          //var elementName = getCheckboxName(this);
          var elementName = $(this).attr('name');

          /** Creating unchecked checkbox object with null value */
          if(eval(elementName)){
            if (!eval(elementName).checkbox.hasOwnProperty($(this).attr('uin'))) {
              window[elementName]['checkbox'][$(this).attr('uin')] = null;
            }
          }else{
            window[elementName] = {};
            window[elementName]['checkbox'] = {};
            window[elementName]['checkbox'][$(this).attr('uin')] = null;
          }
          /** Creating unchecked checkbox object with null value */

          if(typeof checkboxPushed[elementName] === 'undefined'){
            if(eval(elementName)){
              postData[sectionId].push({name: elementName, value: eval(elementName)});
              checkboxPushed[elementName] = 1;
            }
          }
        });

        datasetData[sectionId] = {};
        $(this).find('.data-set-parent').each(function(){
          var uin = $(this).attr('id').split('-')[1];
          datasetData[sectionId][uin] = {};
          datasetData[sectionId][uin]['normal'] = $(this).find('.normal-dataset-fields').find('input,textarea,select').serialize();
          datasetData[sectionId][uin]['other'] = $(this).find('.other-dataset-fields').find('input').serialize();

          setValueProperty(this);
          eval(uin)['oldHtml'] = $(this).html();
        });

        if($(this).find('input,textarea,select').length > 0){
          if($(this).find('input,textarea,select').valid()){
            completedSections[i] = sectionId;
          }
        }
        else{
            completedSections[i] = sectionId;
        }
      });
    }
    else{
      var postData = $("#"+currentActiveSection).find('input[type!="checkbox"],textarea,select').not('.dataset').serializeArray();
      //** process checkbox data
      var checkboxPushed = [];
      $("#"+currentActiveSection).find('input[type="checkbox"]').each(function(){
        
        //var elementName = getCheckboxName(this);
        var elementName = $(this).attr('name');
        /** Creating unchecked checkbox object with null value */
        if(eval(elementName)){
          if (!eval(elementName).checkbox.hasOwnProperty($(this).attr('uin'))) {
            window[elementName]['checkbox'][$(this).attr('uin')] = null;
          }
        }else{
          window[elementName] = {};
          window[elementName]['checkbox'] = {};
          window[elementName]['checkbox'][$(this).attr('uin')] = null;
        }
        /** Creating unchecked checkbox object with null value */
        if(typeof checkboxPushed[elementName] === 'undefined'){
          if(eval(elementName)){
            
            postData.push({name: elementName, value: eval(elementName)});
            checkboxPushed[elementName] = 1;
          }
        }
        
      });
      //** send dataset field seperately

      var datasetData = {};
      $("#"+currentActiveSection).find('.data-set-parent').each(function(){
        var uin = $(this).attr('id').split('-')[1];
        datasetData[uin] = {};
        datasetData[uin]['normal'] = $(this).find('.normal-dataset-fields').find('input,textarea,select').serialize();
        datasetData[uin]['other'] = $(this).find('.other-dataset-fields').find('input').serialize();

        setValueProperty(this);
        eval(uin)['oldHtml'] = $(this).html();
      });

      //** validate if section is completed or not
      if($("#"+currentActiveSection).find('input,textarea,select').length > 0){
        if($("#"+currentActiveSection).find('input,textarea,select').valid()){
          var completed = 1;
        }
        else{
          var completed = 0;
        }
      }
      else{
        var completed = 1;
      }
    }
    var progress = $("#progress").val();
    var data = {formData:postData,currentActiveSection:currentActiveSection,documentSection:documentSection,completed:completed,datasetData:datasetData,documentCompleted:documentCompleted,progress:progress,saveWork:saveWork,completedSections:completedSections};
  }
  else{
    var postData = $("#document-form").find('input[type!="checkbox"],textarea,select').not('.dataset').serializeArray();

    //** process checkbox data
    var checkboxPushed = [];
    $("#document-form").find('input[type="checkbox"]').each(function(){
      //var elementName = getCheckboxName(this);
      var elementName = $(this).attr('name');
      /** Creating unchecked checkbox object with null value */
        if(eval(elementName)){
          if (!eval(elementName).checkbox.hasOwnProperty($(this).attr('uin'))) {
            window[elementName]['checkbox'][$(this).attr('uin')] = null;
          }
        }else{
          window[elementName] = {};
          window[elementName]['checkbox'] = {};
          window[elementName]['checkbox'][$(this).attr('uin')] = null;
        }
      if(typeof checkboxPushed[elementName] === 'undefined'){
        if(eval(elementName)){
          postData.push({name: elementName, value: eval(elementName)});
          checkboxPushed[elementName] = 1;
        }
      }
    });

    //** send dataset field seperately

    var datasetData = {};
    $("#document-form").find('.data-set-parent').each(function(){
      var uin = $(this).attr('id').split('-')[1];
      datasetData[uin] = {};
      datasetData[uin]['normal'] = $(this).find('.normal-dataset-fields').find('input,textarea,select').serialize();
      datasetData[uin]['other'] = $(this).find('.other-dataset-fields').find('input').serialize();

      setValueProperty(this);
      eval(uin)['oldHtml'] = $(this).html();
    });
    var progress = $("#progress").val();
    var data = {formData:postData,documentSection:documentSection,datasetData:datasetData,documentCompleted:documentCompleted,progress:progress};
  }
  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    url: saveUserDocumentDataUrl,
    dataType: "json",
    data: data,
    success: function (response) {
      if(response.status){
        userFormData = response.userFormData;
        if(triggerNextFlag){
          triggerNext(1);
        }
        else{
          if(authCheck){
            //location.href = redirectUrl;
            location.href = response.myAccountURL;
          }
          else{
            return false;
          }
        }
      }
      else{
        showAlert(0,response.message);
      }
    },
    beforeSend: function () {           
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
      showAlert(0,jqXHR.responseText);
    }
  });
}

/**
 * @summary This function will be used to move to next section
*/
function triggerNext(completed){
  var currentActiveSection = $(".doc-order-list li.active").attr('id');
  if($("#"+currentActiveSection).is(":last-child")){
    location.href = redirectUrl;
    return false;
  }
  var currentSectionUin = $(".doc-order-list li.active").attr('data-uin');
  var nextSectionUin = $("#"+currentActiveSection).nextAll('li.section-form-show:first').attr('data-uin');
  $("html, body").animate({ scrollTop: 0 });
  setTimeout(function(){
    if(completed){
      $("#"+currentActiveSection).removeClass('active').addClass('completed');
    }
    else{
      $("#"+currentActiveSection).removeClass('active');
    }
    $("#"+currentActiveSection).nextAll('li.section-form-show:first').find('a').trigger('click');

    $("#docFaq-"+currentSectionUin).hide();
    $("#docFaq-"+nextSectionUin).show();

    submitShowHide(nextSectionUin);

  },200);
}

/**
 * @summary This function will be used to move to previous section
*/
function triggerBack(){
  if(documentSection == 1){
    var currentActiveSection = $(".doc-order-list li.active").attr('id');
    if($("#"+currentActiveSection).is(":first-child")){
      location.href = myAccountUrl;
    }
    var currentSectionUin = $(".doc-order-list li.active").attr('data-uin');
    var prevSectionUin = $("#"+currentActiveSection).prevAll('li.section-form-show:first').attr('data-uin');
    $("html, body").animate({ scrollTop: 0 });
    setTimeout(function(){
      $("#"+currentActiveSection).removeClass('active');
      //$("#"+currentActiveSection).prev('li').find('a').trigger('click');
      $("#"+currentActiveSection).prevAll('li.section-form-show:first').find('a').trigger('click');

      $("#docFaq-"+currentSectionUin).hide();
      $("#docFaq-"+prevSectionUin).show();

      submitShowHide(prevSectionUin);

    },200);
  }
  else{
    location.href = myAccountUrl;
  }
}

/**
 * @summary This function will be used to show submit and savenext button
*/
function submitShowHide(uin){
  if(documentSection == 1){
    //if($("#tab-"+uin).is(":last-child")){
    if($("#tab-"+uin).hasClass("section-form-hide-prev")){
      $("#save").hide();
      $("#submit").show();
      $("#skip").hide();
    }
    else{
      $("#save").show();
      $("#submit").hide();
      $("#skip").show();
    }
  }
}

/**
 * @summary This function will be used to process form conditions created in admin panel
*/
function processFormConditions(elementName){
  
  $.each(formConditions,function(i,data){
      var condition_source_fields = JSON.parse(data.condition_source_fields);
      var condition_destination_fields = JSON.parse(data.condition_destination_fields);
      if(jQuery.inArray(elementName, condition_source_fields) !== -1 || jQuery.inArray(elementName, condition_destination_fields) !== -1){
          var condition = data.condition_formatted;
          var checkCondition = new Function('', 'return ' + condition);
          if(checkCondition(condition)){
              if(data.action == 1){
                if(data.conditional_field_option == 1){
                  $('[id="field-'+data.uin+'"]').removeClass('form-hide').addClass('form-show').attr('data-cond-state',"1");
                  //** remove ignore class
                  $('[id="'+data.uin+'"]').removeClass('ignore').attr('data-cond-state',"1");

                  //** dataset fields
                  $('[id="field-'+data.uin+'"]').find('.form-hide').removeClass('form-hide').addClass('form-show').attr('data-cond-state',"1");
                  $('[id="field-'+data.uin+'"]').find('input,select,textarea').removeClass('ignore').attr('data-cond-state',"1");
                }
                else{
                  $("#tab-"+data.uin+",#"+data.uin).removeClass('section-form-hide').addClass('section-form-show');
                  //** show hide section fields
                  $("#"+data.uin).find('.form-hide').filter('[data-state = "1"][data-cond-state = "1"],[data-state = "0"][data-cond-state = "1"]').removeClass('form-hide').addClass('form-show');
                  //** remove ignore class
                  $("#"+data.uin).find('input,select,textarea').filter('[data-state = "1"][data-cond-state = "1"],[data-state = "0"][data-cond-state = "1"]').removeClass('ignore');
                }
              }
              else if(data.action == 0){
                if(data.conditional_field_option == 1){
                  $('[id="field-'+data.uin+'"]').removeClass('form-show').addClass('form-hide').attr('data-cond-state',"0");
                  $('[id="'+data.uin+'"]').addClass('ignore').attr('data-cond-state',"0");

                  //** dataset fields
                  $('[id="field-'+data.uin+'"]').find('.form-show').removeClass('form-show').addClass('form-hide').attr('data-cond-state',"0");;
                  $('[id="field-'+data.uin+'"]').find('input,select,textarea').addClass('ignore').attr('data-cond-state',"0");
                }
                else{
                  $("#tab-"+data.uin+",#"+data.uin).removeClass('section-form-show').addClass('section-form-hide');
                  //** show hide section fields
                  $("#"+data.uin).find('.form-show').removeClass('form-show').addClass('form-hide');
                  $("#"+data.uin).find('input,select,textarea').addClass('ignore');
                }
              }
          }
          else{
              if(data.action == 0){
                if(data.conditional_field_option == 1){
                  $('[id="field-'+data.uin+'"]').removeClass('form-hide').addClass('form-show').attr('data-cond-state',"1");
                  $('[id="'+data.uin+'"]').removeClass('ignore').attr('data-cond-state',"1");

                  //** dataset fields
                  $('[id="field-'+data.uin+'"]').find('.form-hide').removeClass('form-hide').addClass('form-show').attr('data-cond-state',"1");
                  $('[id="field-'+data.uin+'"]').find('input,select,textarea').removeClass('ignore').attr('data-cond-state',"1");
                }
                else{
                  $("#tab-"+data.uin+",#"+data.uin).removeClass('section-form-hide').addClass('section-form-show');
                  //** show hide section fields
                  $("#"+data.uin).find('.form-hide').filter('[data-state = "1"][data-cond-state = "1"],[data-state = "0"][data-cond-state = "1"]').removeClass('form-hide').addClass('form-show');
                  $("#"+data.uin).find('input,select,textarea').filter('[data-state = "1"][data-cond-state = "1"],[data-state = "0"][data-cond-state = "1"]').removeClass('ignore');
                }
              }
              else if(data.action == 1){
                if(data.conditional_field_option == 1){
                  $('[id="field-'+data.uin+'"]').removeClass('form-show').addClass('form-hide').attr('data-cond-state',"0");
                  $('[id="'+data.uin+'"]').addClass('ignore').attr('data-cond-state',"0");

                  //** dataset fields
                  $('[id="field-'+data.uin+'"]').find('.form-show').removeClass('form-show').addClass('form-hide').attr('data-cond-state',"0");;
                  $('[id="field-'+data.uin+'"]').find('input,select,textarea').addClass('ignore').attr('data-cond-state',"0");
                }
                else{
                  $("#tab-"+data.uin+",#"+data.uin).removeClass('section-form-show').addClass('section-form-hide');
                  //** show hide section fields
                  $("#"+data.uin).find('.form-show').removeClass('form-show').addClass('form-hide');
                  $("#"+data.uin).find('input,select,textarea').addClass('ignore');
                }
              }
          }
     }
  });
  
  //** for last visible section, add section-form-hide-prev class 
  $(".doc-order-list li").removeClass('section-form-hide-prev');
  $(".doc-order-list li:visible:last").addClass('section-form-hide-prev');

  setBgHeight();
  //** show hide save/submit button
  submitShowHide($(".doc-order-list li.active").attr('data-uin'));
}

/**
 * @summary This function will be used calculate progress of form according to total filled fields
*/
function processTotalProgress(){
  var totalFields = $(".form-show").not('.data-set-parent').length;
  var eachFieldWeight = 100/totalFields;
  var currentProgress = 0;
  /* below variable will be used for progress of checkboxes. A single checkbox check(from a group of checkbox)
  will be considered as progress of that checkbox*/
  var checkboxProgress = [];
  $("#document-form").find('input,textarea,select').not('.ignore').each(function(){
    if($(this).attr('type') == 'radio'){
      var name = $(this).attr('name');
      if($(this).is(':checked')){
        currentProgress += eachFieldWeight;
      }
    }
    else if($(this).attr('type') == 'checkbox'){
      //var elementName = getCheckboxName(this);
      var elementName = $(this).attr('name');
      if(typeof checkboxProgress[elementName] === 'undefined'){
        checkboxProgress[elementName] = 0;
      }
      if(checkboxProgress[elementName] == 0){
        if($(this).is(':checked')){
          currentProgress += eachFieldWeight;
          checkboxProgress[elementName] = 1;
        }
      }
    }
    else{
      if($(this).attr('type') != 'hidden'){
        if($(this).val().length>0){
          currentProgress += eachFieldWeight;
        }

        //** if there is any formula added for textbox/textarea field by admin, apply that here
        processMathFormulaForFields(this);

      }
    }
  });

  if(currentProgress > 100){
    currentProgress = 100;
  }

  $(".obox-progress-process").css('width',currentProgress+'%');
  
  //** display round percentage to user
  if(currentProgress != 0){
    currentProgress = Math.round(currentProgress);
  }
  
  $(".progress-bar-value").text(currentProgress+'%');
  $("#progress").val(currentProgress);
}

/**
 * @summary This function will be called when user will click on skip button
*/
function resetCurrentSectionForm(){
  var currentActiveSectionTab = $(".tab-content .tab-pane.active").attr('id');
  $("#"+currentActiveSectionTab).find("input,textarea,select").not('.dataset').each(function(){
    if(!jQuery.isEmptyObject(userFormData) && userFormData.hasOwnProperty($(this).attr('name'))){
      //** set old value in edit mode
      var value = userFormData[$(this).attr('name')];
      if($(this).prop('nodeName') == 'SELECT'){
        $(this).val(value);
        $(this).selectpicker("refresh");
      }
      else{
        if($(this).attr('type') == 'radio'){
          if(value){
            $('[id="'+$(this).attr('name')+'-'+value+'"]').prop('checked', true); 
          }
          else{
            $(this).prop('checked',false);
          }
        }
        else if($(this).attr('type') == 'checkbox'){
          if(value){

            /**@modify code by Pratyush on 23 Jan 2020 */
            var tempIdOfCheckbox = $(this).attr('id');
            $.each(value.checkbox, function(key,val){
              if(key == tempIdOfCheckbox){
                if(val !== null){
                  $('[id="'+key+'"]').prop('checked', true);
                }else{
                  $('[id="'+key+'"]').prop('checked', false);
                }
              }
            });

            /**@modify code by Pratyush on 23 Jan 2020 */

            /**@old code by Ankit bhootra */
            // var valueArray = value.split(',');
            // if(value.indexOf($(this)) !== null){
              // $('[id="'+$(this).attr('name')+'-'+$(this).val()+'"]').prop('checked', true); 
            // }
            // else{
            //   $('[id="'+$(this).attr('name')+'-'+$(this).val()+'"]').prop('checked', false); 
            // }
            /**@old code by Ankit bhootra */
          }
          else{
            $(this).prop('checked',false);
          }
        }
        else{
          $(this).val(value);
        }
      }
    }
    else{
      if($(this).prop('nodeName') == 'SELECT'){
        $(this).val('');
        $(this).selectpicker("refresh");
      }
      else{
        if($(this).attr('type') == 'radio'){
          $(this).prop('checked',false);
        }
        else if($(this).attr('type') == 'checkbox'){
          $(this).prop('checked',false);
        }
        else{
          $(this).val('');
        }
      }
    }
    //** We are reseting the value, as manage conditions again


    /**@modify code by pratyush bharti on 23 Jan 2020 */
    if($(this).attr('type') == 'checkbox'){
      processFormConditions($(this).attr('id'));
    }else{
      processFormConditions($(this).attr('name'));
    }
    /**@modify code by pratyush bharti on 23 Jan 2020 */

    /**@old code by ankit bhootra */
    // processFormConditions($(this).attr('name'));
    /**@old code by ankit bhootra */
    
    processFormConditions(statepromptuin);
    createUpdateDynamicalVariableValues(this,$(this).val());
  });

  //** reset dataset group
  $("#"+currentActiveSectionTab).find('.data-set-parent').each(function(){
    var uin = $(this).attr('id').split('-')[1];
    $(this).html(eval(uin)['oldHtml']);
    $(this).find("input,textarea,select").each(function(){
      createUpdateDynamicalVariableValues(this,$(this).val());
    });
  });

  processTotalProgress();
}

/**
 * @summary This function will be used to refresh the PDF document
*/
function refreshDocument(){
  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'GET',
    url: previewDocumentUrl,
    success: function (response) {
      if(response.status){
        $(".doc-preview-file .mCSB_container").html(response.documentPdfContent);
      }
      else{
        showAlert(0,response.message);
      }
      /*$(".mCustomScrollbar").mCustomScrollbar({
        scrollButtons:{enable:true},
        theme: 'dark-thick'
      });*/
      //$('#docPreviewRefresh').hide('slow');
    },
    beforeSend: function () {  
      //$('.mCustomScrollbar').mCustomScrollbar("destroy");
      //$('#docPreviewRefresh').show('slow');         
    },
    error: function(jqXHR, textStatus, errorThrown)
    {
      showAlert(0,jqXHR.responseText);
    }
  });
}

/**
 * @summary This function will be used used to proces formula is applied on any of the fields
*/
function processMathFormulaForFields(element){
  if($(element).data('content')){
    var content = $(element).data('content');
    
    
    if($(element).hasClass('other-dataset') && $(element).data('sum') == 1){
      var oldContent = content;
      content = '';
      $(element).closest('.data-set-parent').find('.data-set-row').each(function(i){
        //content += $(this).find("[name*='"+oldContent+"']").attr('name')+'+';
        content += $(this).find("[data-uin='"+oldContent+"']").attr('name')+'+';
      });
      content = content.slice(0, -1);
    }

    var separators = [' ', '\\\+', '-', '\\\(', '\\\)', '\\*', '/', ':', '\\\?'];
    var contentArray = content.split(new RegExp(separators.join('|'), 'g'));
    //** apply the formula only if the field user in the formula has some value in it or not
    var applyFormula = 1;

    $.each(contentArray,function(i,elem){
      if(elem && !$.isNumeric(elem)){
        
        //** if element is a normal dataset field, we need to apply formula on the same row's element which are of array type, so convert element of formula to array with indexing
        if($(element).hasClass('normal-dataset')){
          var oldElem = elem;
          elem = oldElem+'['+$(element).data('count')+']';
          content = content.replace(oldElem,elem);
        }

        if($('[name="'+elem+'"]').attr('type') == 'radio'){
          if(!$('[name="'+elem+'"]:checked').val()){
            applyFormula = 0;
          }
        }
        /**@modify code by pratyush on 24 Jan 2020 */
        else if($('[id="'+elem+'"]').attr('type') == 'checkbox'){
          
          if(!$('[id="'+elem+'"]:checked').val()){
            applyFormula = 0;
            // window[elem] = 0;
          }
        }
        /**@modify code by pratyush on 24 Jan 2020 */
        else{
          if(!$('[name="'+elem+'"]').val()){
            applyFormula = 0;
          }
        }
      }
    });

    // if($(element).attr('name') == 'C1$8'){
    //   // console.log(eval('C1$7$CB$2'));
    //   // alert(applyFormula);
    // }
    
    if(applyFormula == 1){
      var formula = new Function('','return ' + content);
      
      var value = formula(content);
      
      if(value || !isNaN(value)){
        //** if value is decimal, round the value upto 2 decimal
        value = (value % 1 === 0)?value:value.toFixed(2);
        $(element).val(value);

        //** update dynamically created variable value also
        createUpdateDynamicalVariableValues(element,value);
      }
    }
    else{
      $(element).val('');
      createUpdateDynamicalVariableValues(element,'');
    }
  }
}

/**
 * @summary This function will be used to create/update dynamic variables and arrays based on fields uin
*/
function createUpdateDynamicalVariableValues(element,value){
  var isNumber = /^[0-9.]*$/.test(value);
  if(isNumber){
    value = (value % 1 === 0)?parseInt(value):parseFloat(value);
  }
  if($(element).hasClass('normal-dataset')){
    //** for normal dataset, as we are using array, we can update using below method
    eval($(element).data('uin'))[$(element).data('count')] = value;
  }
  else{
    if($(element).attr('type') == 'checkbox'){
      
      //var elementName = getCheckboxName(element);
      var elementName = $(element).attr('name');
      if($(element).is(':checked')){
        
        // console.log(eval(elementName));
        // if(typeof eval(elementName) !== 'undefined' && typeof eval(elementName) === 'string'){
        //   window[elementName] = JSON.parse(data);
        // }
        
        if(!jQuery.isEmptyObject(eval(elementName))){
          
          /**@old code by Ankit Bhootra */
            // window[elementName] = eval(elementName)+','+value;
          /**@old code by Ankit Bhootra */
          
          /**@modify by Pratyush on 21 Jan 2020 */
            // window[elementName][$(element).attr('uin')] = $(element).val();
            window[elementName]['checkbox'][$(element).attr('uin')] = $(element).val();
            if($.isNumeric($(element).val())){
              window[$(element).attr('uin')] = ($(element).val() % 1 === 0)?parseInt($(element).val()):parseFloat($(element).val());
            }else{
              window[$(element).attr('uin')] = $(element).val();
            }
          /**@modify by Pratyush on 21 Jan 2020 */
        }
        else{
          /**@old code by Ankit Bhootra */
            // window[elementName] = value; 
          /**@old code by Ankit Bhootra */

          /**@modify by Pratyush on 21 Jan 2020 */
            window[elementName] = {};
            window[elementName]['checkbox'] = {};
            window[elementName]['checkbox'][$(element).attr('uin')] = $(element).val();
            // window[$(element).attr('uin')] = $(element).val();
            if($.isNumeric($(element).val())){
              window[$(element).attr('uin')] = ($(element).val() % 1 === 0)?parseInt($(element).val()):parseFloat($(element).val());
            }else{
              window[$(element).attr('uin')] = $(element).val();
            }
            
          /**@modify by Pratyush on 21 Jan 2020 */
        }
      }
      else{
        /**@old code by Ankit Bhootra */
          // window[elementName] = eval(elementName).replace(value,'');
          // window[elementName] = eval(elementName).replace(/^,|,$/g,'');
        /**@old code by Ankit Bhootra */

        /**@modify by Pratyush on 21 Jan 2020 */
          
          var tempUin = $(element).attr('uin'); // Getting element uin
          var data = eval(elementName).checkbox;
          // if(typeof data === 'string'){
          //   data = JSON.parse(data);
          // }
          var tempData = {}; // creating new object
          $.each(data, function(i, el){
          
            if (i != tempUin){
              tempData[i] = el;
            }
          });
          window[elementName]['checkbox'] = tempData;
          
        /**@modify by Pratyush on 21 Jan 2020 */
      }
    }
    else{
      window[$(element).attr('name')] = value;
    }
  }
}

/**
 * @summary This function will be used to hold the HTML of dataset fields. We will replace this old HTML when user 
 will click on skip button
*/
function holdOldDatasetHtml(){
  $(".data-set-parent").each(function(){
    var uin = $(this).attr('id').split('-')[1];
    eval(uin)['oldHtml'] = $(this).html();
  });
}

/**
 * @summary This function will be used to set HTML input elements value in DOM
*/
function setValueProperty(element){
  $(element).find("input,textarea,select").each(function(){
     if($(this).prop('nodeName') == 'SELECT'){
        var value= $(this).val();
        $(this).find('option').each(function(){
          if($(this).val() == value){
            $(this).attr('selected','selected');
          }
          else{
            $(this).removeAttr('selected');
          }
        });
      }
      else{
        $(this).prop("defaultValue",$(this).val()); 
      }
  });
}

/**
 * @summary This function will allow only two decimal points in the input box
*/
function allowTwoDecimalPoints(element,event){
  var $this = $(element);
  
  var text = $(element).val();
  if ((event.which == 46) && (text.indexOf('.') == -1)) {
      setTimeout(function() {
          if ($this.val().substring($this.val().indexOf('.')).length > 3) {
              $this.val($this.val().substring(0, $this.val().indexOf('.') + 3));
          }
      }, 1);
  }

  if ((text.indexOf('.') != -1) &&
      (text.substring(text.indexOf('.')).length > 2) &&
      (event.which != 0 && event.which != 8) &&
      ($(element)[0].selectionStart >= text.length - 2)) {
          event.preventDefault();
  }
}

/**
 * @summary This function will be used to get checkbox name from the field
*/
function getCheckboxName(element){
  return $(element).attr('name').replace("[]", "");
}

/**
 * @summary This function will remove key from object by key name
 * @author Pratyush 
 * @created 21 Jan 2020
*/
function getKeyByValue(object, value) { 
  for (var prop in object) { 
    if (object.hasOwnProperty(prop)) { 
      if (object[prop]['name'] === value)
      {
        delete object[prop];
        return true; 
      } 
    } 
  } 
}