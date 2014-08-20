$js('jquery',function(){
	$('form#search_panel').submit(function(e){
		e.preventDefault();
		var data,ndata,location,eq,newloc,nlocation;
		eq = ':';
		location = decodeURIComponent(document.location.pathname.substr(1));
		location = location.split('+').shift();
		data = $(this).serializeArray();
		ndata = {};
		newloc = [];
		for(var k in data)
			ndata[data[k].name] = data[k].value;
		console.log(ndata);
		ndata['thematic'] = ndata['thematic'].split(' ');
		for(var i in ndata['thematic'])
			newloc.push(ndata['thematic'][i]);
		if(ndata['phonemic'])
			newloc.push('phonemic'+eq+ndata['phonemic']);
		if(ndata['xownGeopoint[lon]']!=''&&ndata['xownGeopoint[lat]']!='')
			newloc.push('geo'+eq+'('+ndata['xownGeopoint[lat]']+','+ndata['xownGeopoint[lon]']+')');
		else if(ndata['xownGeopoint[label]']!='')
			newloc.push('geo'+eq+ndata['xownGeopoint[label]']);
		if(ndata['xownGeopoint[radius]']!='')
			newloc.push('rad'+eq+ndata['xownGeopoint[radius]']);
		newloc = newloc.join('+');
		if(newloc)
			newloc = '+'+newloc;
		nlocation = document.location.protocol+'//'+document.location.hostname+'/'+location+newloc;
		document.location = nlocation;
		return false;
	});
});