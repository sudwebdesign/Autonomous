$js('jquery',function(){
	$('form#search_panel').submit(function(e){
		e.preventDefault();
		var data,ndata,location,eq,newloc,nlocation,joiner,thematic;
		eq = ':';
		location = decodeURIComponent(document.location.pathname.substr(1));
		location = location.split('+').shift();
		data = $(this).serializeArray();
		ndata = {};
		newloc = [];
		for(var k in data)
			ndata[data[k].name] = data[k].value;
		if(ndata['groupingByAnd'])
			joiner = '&';
		else
			joiner = '+';
		if(ndata['thematic'].trim()){
			ndata['thematic'] = ndata['thematic'].split(' ');
			thematic = [];
			for(var i in ndata['thematic'])
				thematic.push(ndata['thematic'][i]);
			newloc.push(thematic.join(joiner));
		}
		if(ndata['phonemic'])
			newloc.push('phonemic'+eq+ndata['phonemic']);
		if(ndata['geo']!='')
			newloc.push('geo'+eq+ndata['geo']);
		if(ndata['rad'])
			newloc.push('rad'+eq+ndata['rad']);
		if(ndata['lon']!=''&&ndata['lat']!='')
			newloc.push('lat'+eq+ndata['lat']+'+lon'+eq+ndata['lon']);
		if(ndata['proxima'])
			newloc.push('proxima'+eq+ndata['proxima']);
		newloc = newloc.join('+');
		if(newloc)
			newloc = '+'+newloc;
		nlocation = document.location.protocol+'//'+document.location.hostname+'/'+location+newloc;
		document.location = nlocation;
		return false;
	});
});