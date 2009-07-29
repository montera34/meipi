// A CityControl is a GControl that has a list of cities or places to
// change zoom loocation

// We define the function first
// cities = [ ... ["title <i>", "city <i>, country <i>", "center address <i>"] ... ] 
function CityControl(cities) {
	this.cities = cities;
}

// To "subclass" the GControl, we set the prototype object to
// an instance of the GControl object
CityControl.prototype = new GControl();

CityControl.prototype.cities = null;

CityControl.prototype.getOnSelectCityFunction = function(title, city, address)
{
	return function()
	{
		if((""+address).length>0)
		{
			mapGoToAddress(address+","+city, false);
		}
		else
		{
			mapGoToAddress(city, false);
		}
	}
}

CityControl.prototype.getOnSelectCityFunctionById = function(selectElement, cities)
{
	return function() {
		for(i=0; i<cities.length; i++)
		{
			if(cities[i][1]==selectElement.value)
			{
				if((""+cities[i][2]).length>0)
				{
					mapGoToAddress(cities[i][2]+","+cities[i][1], false);
				}
				else
				{
					mapGoToAddress(cities[i][1], false);
				}
			}
		}
	};
}

// Creates a one DIV for each of the buttons and places them in a container
// DIV which is returned as our control element. We add the control to
// to the map container and return the element for the map class to
// position properly.
CityControl.prototype.initialize = function(map) {
  var container = document.createElement("div");

	var containerContent = document.createElement("select");
	for(i=0; i<this.cities.length; i++)
	{
		var cityOption = document.createElement("option");
		cityOption.value = this.cities[i][1];
		cityOption.appendChild(document.createTextNode(this.cities[i][0], this.cities[i][1]));
		containerContent.appendChild(cityOption);
	}
	GEvent.addDomListener(containerContent, "change", this.getOnSelectCityFunctionById(containerContent, this.cities));
	container.appendChild(containerContent);

	/*for(i=0; i<this.cities.length; i++)
	{
		var cityDiv = document.createElement("div");
		this.setButtonStyle_(cityDiv);
		container.appendChild(cityDiv);
		cityDiv.appendChild(document.createTextNode(this.cities[i][0], this.cities[i][1]));
		GEvent.addDomListener(cityDiv, "click", this.getOnSelectCityFunction(this.cities[i][0], this.cities[i][1], this.cities[i][2]));
	}*/

	map.getContainer().appendChild(container);
	return container;
}

// Default position for the control
CityControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(7, 7));
}

// Sets the proper CSS for the given button element.
CityControl.prototype.setButtonStyle_ = function(button) {
	button.style.textDecoration = "underline";
	button.style.color = "#0000cc";
	button.style.backgroundColor = "white";
	button.style.font = "small Arial";
	button.style.border = "1px solid black";
	button.style.padding = "2px";
	button.style.marginBottom = "3px";
	button.style.textAlign = "center";
	button.style.width = "6em";
	button.style.cursor = "pointer";
}

