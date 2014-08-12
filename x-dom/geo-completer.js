$css('/x-dom/geo-completer');

$css('jquery-ui/core');
$css('jquery-ui/menu');
$css('jquery-ui/autocomplete');

$js(true,[
	'jquery',

	'jquery-ui/core',
	'jquery-ui/widget',
	'jquery-ui/menu',
	'jquery-ui/position',
	'jquery-ui/autocomplete',
	
	'string',
	
	'simulate'
],function(){
	var geocallbacks = [];
	$('geo-completer').each(function(){
		var THIS = $(this);
		var geolocal = $('.remodal',THIS);
		var inputLat = $('input[type=number][step=any]:eq(0)',THIS);
		var inputLng = $('input[type=number][step=any]:eq(1)',THIS);
		var inputRadius = $('input[type=number][step][step!=any]:eq(0)',THIS);
		var inputGG = $('input.gg-maps',THIS);
			inputGG.after('<div class="map-wrapper"><div class="map-canvas"></div></div>');
		var theMAP = $('.map-canvas',THIS);
		var inputGeoname = $('input.geoname',THIS);
		
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

		//var updateByGeoname = function(event,ui){
			//var val = ui&&ui.item?ui.item.value:inputGeoname.val();
			//if(val){
				//updatingGeocode(val);
			//}
		//};
		//inputGeoname.on('autocompleteselect',updateByGeoname);
		//inputGeoname.on('autocompletechange',updateByGeoname);
		
		geocallbacks.push(function(){
			var autocomplete;
			var geocoder = new google.maps.Geocoder();
			var autocompleteService = new google.maps.places.AutocompleteService();
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

			var updatingGeocode = function(val){
				autocompleteService.getQueryPredictions({input:val,types:['geocode']},function(predictions, status){
					if(status==google.maps.places.PlacesServiceStatus.OK&&predictions.length){
						geocoder.geocode({address:predictions[0].description,bounds:bounds},function(results,status){
							if(status===google.maps.places.PlacesServiceStatus.OK){
								inputGG.val(results[0].formatted_address);
								theMAP.updatePlace(results[0],true);
							}
						});
					}
				});
			};
			var defaultMapZoom = 17;
			var updateAdresse = function(latLng,updateMark){
				geocoder.geocode({'latLng':latLng},function(results,status){
					if(status==google.maps.places.PlacesServiceStatus.OK){
						inputGG.val(results[0].formatted_address);
						theMAP.updatePlace(results[0],updateMark);
					}				
				});
			};				
			var updatePosition = function(){
				updateAdresse(new google.maps.LatLng(floatFromStr(inputLat.val()), floatFromStr(inputLng.val())),true);
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
			inputGG.keypress(function(e){
				if(e.which==13){
					e.preventDefault();
					inputGG.trigger('focus');
					inputGG.simulate('keydown',{keyCode:$.ui.keyCode.DOWN}).simulate('keydown',{keyCode:$.ui.keyCode.ENTER});
					return false;
				}
			});
			var map = new google.maps.Map(theMAP.get(0),{
				zoom: 8,
				mapTypeId: google.maps.MapTypeId.HYBRID,
				center:new google.maps.LatLng(params.centerLatMainBound,params.centerLngMainBound)
			});
			//map.fitBounds(bounds);
			map.controls[google.maps.ControlPosition.TOP_LEFT].push(inputGG.get(0));
			autocomplete = new google.maps.places.Autocomplete(inputGG.get(0),{
				bounds:bounds,
				region:params.region,
				componentRestrictions:{
					country:params.country
				},
				types: ['geocode']
			});
			//autocomplete.bindTo('bounds', map);
			//var infowindow = new google.maps.InfoWindow();
			var marker = new google.maps.Marker({map: map,draggable:true});
			var circle = new google.maps.Circle({
			  //visible: false,
			  map: map,
			  fillColor: '#AA0000',
			  fillOpacity: 0.5,
			  strokeOpacity:1,
			  strokeWeight:1,
			  strokeColor:'#000',
			  editable:true
			});
			var setRadius = function(place){
				if(place&&place.geometry&&place.geometry.viewport&&(place.types[0]=="locality"||place.types[0]=="administrative_area_level_2")){
					var center = place.geometry.viewport.getCenter();
					var northEast = place.geometry.viewport.getNorthEast();
					var southWest = place.geometry.viewport.getSouthWest();
					var lat = center.lat();
					var lng = center.lng();
					var r = (distance(lat,lng,northEast.lat(),northEast.lng())+distance(lat,lng,southWest.lat(),southWest.lng()))/2.0;
					inputRadius.val(r);
					circle.setRadius(r*1000.0);
					circle.bindTo('center', marker, 'position');
					circle.setVisible(true);
				}
				else{
					circle.setVisible(false);
					inputRadius.val('');
				}
			};
			theMAP.updatePlace = function(place,updateMark){
				setRadius(place);
				if(typeof(place)!='object'||!place.geometry)
					return;
				if(place.geometry.viewport){
					map.fitBounds(place.geometry.viewport);
					//map.fitBounds(place.geometry.bounds);
				}
				else{
					map.setCenter(place.geometry.location);
					map.setZoom(defaultMapZoom);
				}
				if(updateMark)
					updateMarker(place);
				
				if(inputLat.val()!=place.geometry.location.lat())
					inputLat.val(place.geometry.location.lat());
				if(inputLng.val()!=place.geometry.location.lng())
					inputLng.val(place.geometry.location.lng());
				inputGG.trigger('change');
			};
			
			google.maps.event.addListener(circle, 'radius_changed', function(){
				var val = circle.getRadius()/1000.0;
				if(val!=floatFromStr(inputRadius.val()))
					inputRadius.val(val);
			});
			google.maps.event.addListener(marker, 'dragstart', function(e){
				circle.setVisible(false);
			});
			google.maps.event.addListener(marker, 'dragend', function(e){
				var latLng = e.latLng;
				inputLat.val(latLng.lat());
				inputLng.val(latLng.lng());
				updateAdresse(latLng);
			});
			google.maps.event.addListener(map, 'dragend', function(){
				var center = map.getCenter();
				marker.setPosition(center);
				inputRadius.val('');
				circle.setVisible(false);
				inputLat.val(center.lat());
				inputLng.val(center.lng());
				geocoder.geocode({'latLng':center},function(results,status){
					if(status==google.maps.places.PlacesServiceStatus.OK){
						inputGG.val(results[0].formatted_address);
					}
				});
			});
			google.maps.event.addListener(autocomplete, 'place_changed', function(){
				var place = autocomplete.getPlace();
				if(typeof(place)=='object'&&place.geometry){
					if(place.geometry.viewport){
						var center = place.geometry.viewport.getCenter();
						inputLat.val(center.lat());
						inputLng.val(center.lng());
					}
					else{
						inputLat.val(place.geometry.location.lat());
						inputLng.val(place.geometry.location.lng());
					}
				}
				theMAP.updatePlace(place,true);
				
			});
			
		});
	});
	window.geocompleter = function(){
		for(var i in geocallbacks)
			geocallbacks[i]();
	};
	$(document).one("open","[data-remodal-id=map]",function(){
		$js('http://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&callback=geocompleter');
	});
	$js('remodal');
});