// A LegendControl is a GControl that has a list of cities or places to
// change zoom loocation

// We define the function first
// cities = [ ... ["title <i>", "city <i>, country <i>", "center address <i>"] ... ] 
function LegendControl(cfg) {
	this.cfg = cfg;
}

// To "subclass" the GControl, we set the prototype object to
// an instance of the GControl object
LegendControl.prototype = new GControl();

LegendControl.prototype.cfg = null;

// Creates a one DIV for each of the buttons and places them in a container
// DIV which is returned as our control element. We add the control to
// to the map container and return the element for the map class to
// position properly.
LegendControl.prototype.initialize = function(map) {
  var container = document.createElement("div");
	$(container).classNames().add("legendControl");

	var contents = "";
	var filterEnabled = this.cfg.filter;
	try
	{
		this.cfg.categories.each(function(category) {
				var filterButton = "";
				try
				{
					if(filterEnabled) {
						filterButton = " <input type=\"checkbox\" onchange=\"javascript:setCategoryFilter('"+category.id+"', this.checked)\"  checked=\"checked\" />";
					}
				} catch(e) {}
				contents += "<div class=\"category_"+category.id+"\"><img src=\""+category.image+"\" /> "+category.name+filterButton+"</div>";
			});

		container.innerHTML = contents;

		map.getContainer().appendChild(container);
		return container;
	}
	catch(e) {}
}

// Default position for the control
LegendControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(10, 40));
}

