$js('go');
$js('jquery',function(){
	$('graph-erm').each(function(){
		// create the model for the E-R diagram
		var nodeDataArray = [];
		var linkDataArray = [];
		var THIS = $(this);
		THIS.find('table').each(function(){
			var items = [];
			$(this).find('col').each(function(){
				var name = $(this).attr('name');
				var figure = $(this).attr('figure');
				var color = $(this).attr('color');
				if(!figure)
					figure = 'MagneticData'; //MagneticData,Cube1,Cube2,Decision,TriangleUp
				if(!color)
					color = 'purple';
				items.push({
					'name':name,
					'iskey':!!$(this).attr('iskey'),
					'figure':figure,
					'color':color,
				});
			});
			nodeDataArray.push({
				'key':$(this).attr('name'),
				'items':items
			});
		});
		THIS.find('link').each(function(){
			linkDataArray.push({
				'from':$(this).attr('from'),
				'to':$(this).attr('to'),
				'text':$(this).attr('text'),
				'toText':$(this).attr('toText'),
			});
		});
		//console.log(nodeDataArray);
		//console.log(linkDataArray);
		$js('go',function(){
			var $ = go.GraphObject.make;  // for conciseness in defining templates
			myDiagram = $(go.Diagram, "myDiagram", {  // must name or refer to the DIV HTML element
				initialContentAlignment: go.Spot.Center,
				allowDelete: false,
				allowCopy: false,
				layout: $(go.ForceDirectedLayout),
				"undoManager.isEnabled": true
			});

			// define several shared Brushes
			var bluegrad = $(go.Brush, go.Brush.Linear, { 0: "rgb(150, 150, 250)", 0.5: "rgb(86, 86, 186)", 1: "rgb(86, 86, 186)" });
			var greengrad = $(go.Brush, go.Brush.Linear, { 0: "rgb(158, 209, 159)", 1: "rgb(67, 101, 56)" });
			var redgrad = $(go.Brush, go.Brush.Linear, { 0: "rgb(206, 106, 100)", 1: "rgb(180, 56, 50)" });
			var yellowgrad = $(go.Brush, go.Brush.Linear, { 0: "rgb(254, 221, 50)", 1: "rgb(254, 182, 50)" });
			var lightgrad = $(go.Brush, go.Brush.Linear, { 1: "#E6E6FA", 0: "#FFFAF0" });

			// the template for each attribute in a node's array of item data
			var itemTempl = $(go.Panel, "Horizontal",
				$(go.Shape,{
					desiredSize: new go.Size(10, 10)
				},
				new go.Binding("figure", "figure"),
				new go.Binding("fill", "color")),
				$(go.TextBlock,{
					stroke: "#333333",
					font: "bold 14px sans-serif"
				},
				new go.Binding("text", "", go.Binding.toString))
			);

			// define the Node template, representing an entity
			myDiagram.nodeTemplate = $(go.Node, "Auto",{  // the whole node panel
				selectionAdorned: true,
				resizable: true,
				layoutConditions: go.Part.LayoutStandard & ~go.Part.LayoutNodeSized,
				fromSpot: go.Spot.AllSides,
				toSpot: go.Spot.AllSides,
				isShadowed: true,
				shadowColor: "#C5C1AA" },
				new go.Binding("location", "location").makeTwoWay(),
				// define the node's outer shape, which will surround the Table
				$(go.Shape, "Rectangle",{ fill: lightgrad, stroke: "#756875", strokeWidth: 3 }),
				$(go.Panel, "Table",{ margin: 8, stretch: go.GraphObject.Fill },
					$(go.RowColumnDefinition, { row: 0, sizing: go.RowColumnDefinition.None }),
					// the table header
					$(go.TextBlock,{ row: 0, alignment: go.Spot.Center,font: "bold 16px sans-serif" }, new go.Binding("text", "key")),
					// the list of Panels, each showing an attribute
					$(go.Panel, "Vertical",{
							row: 1,
							padding: 3,
							alignment: go.Spot.TopLeft,
							defaultAlignment: go.Spot.Left,
							stretch: go.GraphObject.Horizontal,
							itemTemplate: itemTempl
						},
						new go.Binding("itemArray", "items")
					)
				)// end Table Panel
			);  // end Node

			// define the Link template, representing a relationship
			myDiagram.linkTemplate = $(go.Link,  // the whole link panel
				{
					selectionAdorned: true,
					layerName: "Foreground",
					reshapable: true,
					routing: go.Link.AvoidsNodes,
					corner: 5,
					curve: go.Link.JumpOver
				},
				$(go.Shape,{  // the link shape
					isPanelMain: true,
					stroke: "#303B45",
					strokeWidth: 2.5
				}),
				$(go.TextBlock, { // the "from" label
					textAlign: "center",
					font: "bold 14px sans-serif",
					stroke: "#1967B3",
					segmentIndex: 0,
					segmentOffset: new go.Point(NaN, NaN),
					segmentOrientation: go.Link.OrientUpright
				},
				new go.Binding("text", "text")),
				$(go.TextBlock,{  // the "to" label
					textAlign: "center",
					font: "bold 14px sans-serif",
					stroke: "#1967B3",
					segmentIndex: -1,
					segmentOffset: new go.Point(NaN, NaN),
					segmentOrientation: go.Link.OrientUpright
				},
				new go.Binding("text", "toText"))
			);
			myDiagram.model = new go.GraphLinksModel(nodeDataArray, linkDataArray);
		});
	});
});