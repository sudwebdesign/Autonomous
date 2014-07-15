$css('qtip');
$js(true,[
	'jquery',
	'qtip',
	'cytoscape',
	'cytoscape-qtip',
],function(){
	$('graph-erm').each(function(){
		
		// create the model for the E-R diagram
		var nodeDataArray = [];
		var linkDataArray = [];
		var THIS = $(this);
		THIS.find('table').each(function(){
			var cols = [];
			$(this).find('col').each(function(){
				cols.push($(this).attr('name'));
			});
			
			var name = $(this).attr('name');
			var figure = $(this).attr('figure');
			var color = $(this).attr('color');
			if(!figure)
				figure = 'ellipse'; //ellipse,rectangle,octagon,triangle
			if(!color)
				color = '#86B342';
			nodeDataArray.push({
				'data':{
					'id':name,
					'name':name,
					//'weight':$(this).attr('weight'),
					//'faveColor':color,
					//'faveShape':figure
					'theCols':cols
				},
			});
		}).remove();
		THIS.find('link').each(function(){
			var strength = $(this).attr('strength');
			//if(!strength)
				//strength = 40;
			linkDataArray.push({
				'data':{
					'source':$(this).attr('from'),
					'target':$(this).attr('to'),
					//'strength':strength,
				}
			});
		}).remove();
		//console.log(nodeDataArray);
		//console.log(linkDataArray);
		
		$('#myDiagram').cytoscape({
			style: cytoscape.stylesheet()
				.selector('node')
					.css({
						'content': 'data(name)',
						'text-valign': 'center',
						'color': 'white',
						'text-outline-width': 1,
						'text-outline-color': '#888',
						'height': '20px',
						'width': '20px',
					})
				.selector('edge')
					.css({
						'target-arrow-shape': 'triangle'
					})
				.selector(':selected')
					.css({
						'background-color': 'black',
						'line-color': 'black',
						'target-arrow-color': 'black',
						'source-arrow-color': 'black'
					})
				.selector('.faded')
					.css({
						'opacity': 0.4,
						'text-opacity': 0.1
					}),
			elements: {
				nodes: nodeDataArray,
				edges: linkDataArray
			},
			layout: {
				name: 				'cose',
				ready               : function() {},
				stop                : function() {},
				refresh             : 0, // Number of iterations between consecutive screen positions update (0 -> only updated on the end)
				fit                 : true,  // Whether to fit the network view after when done
				padding             : 30,  // Padding on fit
				randomize           : true, // Whether to randomize node positions on the beginning
				debug               : false, // Whether to use the JS console to print debug messages
				nodeRepulsion       : 10000, // Node repulsion (non overlapping) multiplier
				nodeOverlap         : 100, // Node repulsion (overlapping) multiplier
				idealEdgeLength     : 10, // Ideal edge (non nested) length
				edgeElasticity      : 100, // Divisor to compute edge forces
				nestingFactor       : 5,  // Nesting factor (multiplier) to compute ideal edge length for nested edges
				gravity             : 250,  // Gravity force (constant)
				numIter             : 100, // Maximum number of iterations to perform
				initialTemp         : 200, // Initial temperature (maximum node displacement)
				coolingFactor       : 0.95,  // Cooling factor (how the temperature is reduced between consecutive iterations
				minTemp             : 1 // Lower temperature threshold (below this point the layout will end)
			},
			ready: function(){
				var cy = this;
				cy.elements().unselectify();
				cy.on('tap', 'node', function(e){
					var node = e.cyTarget; 
					var neighborhood = node.neighborhood().add(node);
					cy.elements().addClass('faded');
					neighborhood.removeClass('faded');
				});
				cy.on('tap', function(e){
					if(e.cyTarget===cy)
						cy.elements().removeClass('faded');
				});
				for(var i in nodeDataArray){
					var v = nodeDataArray[i].data;
					var html = '';
					//html += v.name+':';
					if(v.theCols&&v.theCols.length)
						html += '<ul class="erm-column"><li>'+v.theCols.join('</li><li>')+'</li></ul>';
					if(html)
						cy.elements('#'+v.id).qtip({
							content: html,
							position: {
								my: 'top center',
								at: 'bottom center'
							},
							style: {
								classes: 'qtip-bootstrap',
								tip: {
									width: 16,
									height: 8
								}
							}
						});
				}
			}
		});
		
	});
});