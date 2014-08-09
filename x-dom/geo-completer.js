$css('/x-dom/geo-completer');

$css('jquery-ui/core');
$css('jquery-ui/menu');
$css('jquery-ui/autocomplete');

$js([
	'jquery',

	'jquery-ui/core',
	'jquery-ui/widget',
	'jquery-ui/menu',
	'jquery-ui/position',
	'jquery-ui/autocomplete',
	
	'string',
	
	'simulate'
],function(){
	geocallback = function(){
		$('geo-completer').each(function(){
			var autocomplete;
			var geocoder = new google.maps.Geocoder();
			var autocompleteService = new google.maps.places.AutocompleteService();
			var floatFromStr = function(v){
				if(typeof(v)!='undefined')
					return parseFloat(v.replace(',','.'));
			};
			var distance = function(lat1, lon1, lat2, lon2){
				var R = 6371; // Radius of the earth in km
				var dLat = (lat2 - lat1) * Math.PI / 180;  // deg2rad below
				var dLon = (lon2 - lon1) * Math.PI / 180;
				var a = 0.5 - Math.cos(dLat)/2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * (1 - Math.cos(dLon))/2;
				return R * 2 * Math.asin(Math.sqrt(a));
			};
			
			var params = <!--#include virtual="/service/autocomplete/geoinit" -->; //Pyrénées-Orientales Square
			//$.getJSON('service/autocomplete/geoinit',function(params){
			var bounds = new google.maps.LatLngBounds(
				new google.maps.LatLng(params.southWestLatMainBound,params.southWestLngMainBound),
				new google.maps.LatLng(params.northEastLatMainBound,params.northEastLngMainBound)
			);

			var updatingGeocode = function(val,callback){
				autocompleteService.getQueryPredictions({input:val,types:['geocode']},function(predictions, status){
					if(status==google.maps.places.PlacesServiceStatus.OK&&predictions.length){
						geocoder.geocode({address:predictions[0].description,bounds:bounds},function(results,status){
							if(status===google.maps.places.PlacesServiceStatus.OK){
								input.val(results[0].formatted_address);
								theMAP.updatePlace(results[0],true,callback);
							}
						});
					}
				});
			};
			var geolocal = $(this).find('geo-local');
			var defaultMapZoom = 17;
			var input_lat = geolocal.find('input[type=number][step=any]:eq(0)');
			var input_lng = geolocal.find('input[type=number][step=any]:eq(1)');
			var input_rayon = geolocal.find('input[type=number][step][step!=any]:eq(0)');
			var input = geolocal.find('input[type=text][name*=label]');
			var theMAP = $('<div class="map-canvas"></div>');
			var inputUseAddress = $(this).find('input[type=checkbox][name*=use_address]');
			var inputGeoname = $(this).find('input[type=text][name*=geoname]');
			inputGeoname.wrap('<div>');
			inputGeoname.autocomplete({
				selectFirst:true,
				autoFill:true,
				minLength: 0,
				source:function(request,response){
					if(request.term.length>=1){
						$.ajax({
							type:'GET',
							dataType:'json',
							url:inputGeoname.attr('data-url'),
							data:{'term':request.term},
							success:function(j){
								var suggesting = [];
								for(var k in j)
									suggesting.push(j[k].name);
								//var suggesting = [];
								//for(var k in j)
									//suggesting.push({
										//label:j[k].name,
										//value:j[k].id
									//});
								response(suggesting);
							},
							error:function(){
								response([]);
							}
						});
					}
					else{
						response([]);
					}
				},
				appendTo: inputGeoname.parent(),
				position: {
					my: 'left top-3',
					at: 'left bottom',
					collision: 'none'
				}
			});
			
			var launch = function(){
				//https://developers.google.com/maps/documentation/javascript/reference
				theMAP.insertAfter(input);
				
				var updateAdresse = function(latLng,updateMark){
					geocoder.geocode({'latLng':latLng},function(results,status){
						if(status==google.maps.places.PlacesServiceStatus.OK){
							input.val(results[0].formatted_address);
							theMAP.updatePlace(results[0],updateMark);
						}				
					});
				};
				
				var updatePosition = function(){
					updateAdresse(new google.maps.LatLng(floatFromStr(input_lat.val()), floatFromStr(input_lng.val())),true);
				};
				var updateMarker = function(place){
					marker.setVisible(false);
					marker.setIcon({
					  url: typeof(place.icon)!='undefined'?place.icon:'img/geocode.png',
					  size: new google.maps.Size(71, 71),
					  origin: new google.maps.Point(0, 0),
					  anchor: new google.maps.Point(17, 34),
					  scaledSize: new google.maps.Size(35, 35)
					});
					marker.setPosition(place.geometry.location);
					marker.setVisible(true);
				};
				input.keypress(function(e){
					if(e.which==13){
						e.preventDefault();
						input.trigger('focus');
						input.simulate('keydown',{keyCode:$.ui.keyCode.DOWN}).simulate('keydown',{keyCode:$.ui.keyCode.ENTER});
						return false;
					}
				});
				input.on('input',function(){
					input_lat.val('');
					input_lng.val('');
					input_rayon.val('');
				});
				//input_lat.on('input',updatePosition);
				input_lng.on('input',updatePosition);
				input_rayon.on('input',function(){
					var val = $(this).val();
					if(val){
						circle.setRadius(floatFromStr(val)*1000.0);
						circle.bindTo('center', marker, 'position');
						circle.setVisible(true);
					}
					else{
						circle.setVisible(false);
					}
				});
				var map = new google.maps.Map(theMAP.get(0),{ //https://developers.google.com/maps/documentation/javascript/reference?csw=1#MapOptions
					zoom: 8,
					mapTypeId: google.maps.MapTypeId.HYBRID
					/*			
						MapTypeId.ROADMAP displays the default road map view. This is the default map type.
						MapTypeId.SATELLITE displays Google Earth satellite images
						MapTypeId.HYBRID displays a mixture of normal and satellite views
						MapTypeId.TERRAIN displays a physical map based on terrain information. 
					*/,
					center:new google.maps.LatLng(params.centerLatMainBound,params.centerLngMainBound)
				});
				//map.fitBounds(bounds);
				map.controls[google.maps.ControlPosition.TOP_LEFT].push(input.get(0));
				autocomplete = new google.maps.places.Autocomplete(input.get(0),{
					bounds:bounds,
					region:params.region,
					componentRestrictions:{
						country:params.country
					}
					,types: ['geocode'] //see https://developers.google.com/places/documentation/supported_types
				});
				autocomplete.bindTo('bounds', map);
				var infowindow = new google.maps.InfoWindow();
				var marker = new google.maps.Marker({map: map,draggable:true});
				var circle = new google.maps.Circle({ //https://developers.google.com/maps/documentation/javascript/reference?csw=1#CircleOptions
				  //visible: false,
				  map: map,
				  fillColor: '#AA0000',
				  fillOpacity: 0.5,
				  strokeOpacity:1,
				  strokeWeight:1,
				  strokeColor:'#000',
				  editable:true
				});
				var setRayon = function(place){
					if(place&&place.geometry&&place.geometry.viewport&&(place.types[0]=="locality"||place.types[0]=="administrative_area_level_2")){
						var center = place.geometry.viewport.getCenter();
						var northEast = place.geometry.viewport.getNorthEast();
						var southWest = place.geometry.viewport.getSouthWest();
						var lat = center.lat();
						var lng = center.lng();
						var r = (distance(lat,lng,northEast.lat(),northEast.lng())+distance(lat,lng,southWest.lat(),southWest.lng()))/2.0;
						input_rayon.val(r);
						circle.setRadius(r*1000.0);
						circle.bindTo('center', marker, 'position');
						circle.setVisible(true);
					}
					else{
						circle.setVisible(false);
						input_rayon.val('');
					}
				};
				theMAP.updatePlace = function(place,updateMark,callback){
					setRayon(place);
					if(typeof(place)!='object'||!place.geometry)
						return;
					if(place.geometry.viewport){
						console.log(place.geometry);
						map.fitBounds(place.geometry.viewport);
						//map.fitBounds(place.geometry.bounds);
					}
					else{
						map.setCenter(place.geometry.location);
						map.setZoom(defaultMapZoom);
					}
					if(updateMark)
						updateMarker(place);
					
					if(input_lat.val()!=place.geometry.location.lat())
						input_lat.val(place.geometry.location.lat());
					if(input_lng.val()!=place.geometry.location.lng())
						input_lng.val(place.geometry.location.lng());


					var address_components = place.address_components;
					inputGeoname.val('');
					for(var i in address_components){
						var compo = address_components[i];
						switch(compo.types[0]){
							case 'locality':
								inputGeoname.val(compo.long_name);
							break;
							//case 'country':
							//case 'administrative_area_level_1':
							//case 'administrative_area_level_2':
						}
					}
					
					if(callback)
						callback();
					input.trigger('change');
				};
				
				google.maps.event.addListener(circle, 'radius_changed', function(){
					var val = circle.getRadius()/1000.0;
					if(val!=floatFromStr(input_rayon.val()))
						input_rayon.val(val);
				});
				google.maps.event.addListener(marker, 'dragstart', function(e){
					circle.setVisible(false);
					infowindow.close();
				});
				google.maps.event.addListener(marker, 'dragend', function(e){
					var latLng = e.latLng;
					input_lat.val(latLng.lat());
					input_lng.val(latLng.lng());
					updateAdresse(latLng);
				});
				google.maps.event.addListener(map, 'dragend', function(){
					var center = map.getCenter();
					marker.setPosition(center);
					input_rayon.val('');
					circle.setVisible(false);
					input_lat.val(center.lat());
					input_lng.val(center.lng());
					//map.setZoom(defaultMapZoom);
					geocoder.geocode({'latLng':center},function(results,status){
						if(status==google.maps.places.PlacesServiceStatus.OK){
							input.val(results[0].formatted_address);
						}
					});
				});
				google.maps.event.addListener(autocomplete, 'place_changed', function(){
					var place = autocomplete.getPlace();
					if(typeof(place)=='object'&&place.geometry){
						if(place.geometry.viewport){
							var center = place.geometry.viewport.getCenter();
							input_lat.val(center.lat());
							input_lng.val(center.lng());
						}
						else{
							input_lat.val(place.geometry.location.lat());
							input_lng.val(place.geometry.location.lng());
						}
					}
					theMAP.updatePlace(place,true);
					
				});
				
				var updateByGeoname = function(event,ui){
					var val = ui&&ui.item?ui.item.value:inputGeoname.val();
					if(val){
						updatingGeocode(val);
					}
				};
				
				inputGeoname.on('autocompleteselect',updateByGeoname);
				inputGeoname.on('autocompletechange',updateByGeoname);
			};

			var launched = false;
			var showGeolocal = function(){
				geolocal.show();
				if(!launched){
					launched = true;
					launch();

				}
				var val = inputGeoname.val();
				if(val){
					updatingGeocode(val);
				}
				
			};
			var hideGeolocal = function(){
				geolocal.hide();
				inputGeoname.show();
			};
			
			if(inputUseAddress.is(':checked')){
				showGeolocal();
			}
			inputUseAddress.change(function(e){
				e.preventDefault();
				if($(this).attr('checked')){
					$(this).removeAttr('checked');
					hideGeolocal();
				}
				else{
					$(this).attr('checked','checked');
					showGeolocal();
				}
				return false;
			});
			
		});
	};
	$js('http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&callback=geocallback');

	
	
	/*
	$(window).on('unload',function(){
		//trying to resolve google map bug on unloading page that slow hard navigation
		//$(this).off('unload');
		if (window.google !== undefined && google.maps !== undefined){
			delete google.maps;
			$('script').each(function () {
				if (this.src.indexOf('googleapis.com/maps') >= 0
						|| this.src.indexOf('maps.gstatic.com') >= 0
						|| this.src.indexOf('earthbuilder.googleapis.com') >= 0) {
					// console.log('removed', this.src);
					$(this).remove();
				}
			});
		}
	});
	*/
});