// select address
// $(document).on('click', '.address-location-box', function(){
//     // remove active class from old select box if any
//     $('.address-location-box.active').not(this).removeClass('active');

//     // add active class
//     $(this).addClass('active');
// });

$(document).on('change','select.state-dropdown', function(){
    updateAddress($(this).val(),'',true);
    $('.location-state-name').html($('.state-dropdown :selected').text());
});

$(document).on('change','select.city-dropdown', function(){
    updateAddress($('select.state-dropdown').val(),$(this).val(),false);
});

function updateAddress(stateCode=null, cityId=null,from=false){
    
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: getLocationsDataPath,
        dataType: "html",
        data: {stateCode,cityId},
        success: function (response) {
            response = JSON.parse(response);

            // update cities
            if(!cityId){
                // $('.city-dropdown').selectpicker('destroy');
                
                let optionHtml = '<option value="">All Cities</option>';
                $.each(response.city, function(i, v){
                    // $('select.city-dropdown').append('<option value="'+i+'">'+v+'</option>');
                    optionHtml += `<option value="${i}">${v}</option>`;
                });
                $('select.city-dropdown').html(optionHtml).selectpicker('refresh');
                // $('.city-drpdown').addClass('selectpicker');
                // $('.city-drpdown').selectpicker('refresh');
                $('.city-drpdown').selectpicker('val',cityId);
            }
            
            // // refresh select picker
            // if(cityId){
            //     $('.city-drpdown').selectpicker('val',cityId);
            // }

            // update html
            $('.location-address-section').html(response.data);

            if(response.cityName != '' && response.cityContent){
                $('.city-name-cls').html(response.cityName);
                $('.city-content-cls').html(response.cityContent);
                $('.location-city-section').removeClass('hide-section-cls');
            }else{
                $('.location-city-section').addClass('hide-section-cls');
            }

            // reload map
            // The location 
		var customLocation = {lat: parseFloat(response.lat), lng: parseFloat(response.lng)};
		
		var mapOptions  = {
			panControl: true,
			zoomControl: true,
			mapTypeControl: false,
			scaleControl: true,
			streetViewControl: false,
			overviewMapControl: true,
			rotateControl: true
		};

        var zoom = 6;
        // dynamic zoom value
        if($('select.city-dropdown').val() != ''){
            zoom = 8;
        }

		// The map, centered at Uluru
		var map = new google.maps.Map(
		document.getElementById('map'), {zoom: zoom, center: customLocation, ...mapOptions});
        
		// new info windows
        var infoWind = new google.maps.InfoWindow;
        
		// The markers
		$.each(JSON.parse(response.locationDetails), function(i, v){

			// create element for title (map marker)
			var content = document.createElement('div');
			var strong = document.createElement('strong');
			strong.textContent = v.title;
			content.appendChild(strong);

			var marker = new google.maps.Marker({
				position: new google.maps.LatLng(v.lat, v.lng),
				map: map
			});

			marker.addListener('click', function(){
				infoWind.setContent(content)
				infoWind.open(map, marker);
			});
        });
        
        },
        beforeSend: function () {
            //disable the submit button

        },
        error: function (jqXHR, textStatus, errorThrown) {

        }
    });
}