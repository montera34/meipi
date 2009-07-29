// A ArchiveControl is a GControl that can change between all entries and only active

// We define the function first
function ArchiveControl(option, text) {
	this.option = option;
	this.text = text;
}

// To "subclass" the GControl, we set the prototype object to
// an instance of the GControl object
ArchiveControl.prototype = new GControl();

ArchiveControl.prototype.option = null;
ArchiveControl.prototype.text = null;

// Creates a one DIV for each of the buttons and places them in a container
// DIV which is returned as our control element. We add the control to
// to the map container and return the element for the map class to
// position properly.
ArchiveControl.prototype.initialize = function(map) {
  var container = document.createElement("div");

	var aLink = document.createElement("a");
	aLink.href = this.option;
	this.setButtonStyle_(aLink);
	aLink.appendChild(document.createTextNode(this.text));
	container.appendChild(aLink);

	map.getContainer().appendChild(container);
	return container;
}

// Default position for the control
ArchiveControl.prototype.getDefaultPosition = function() {
	return new GControlPosition(G_ANCHOR_BOTTOM_LEFT, new GSize(7, 7));
}

// Sets the proper CSS for the given button element.
ArchiveControl.prototype.setButtonStyle_ = function(button) {
	button.className = "archiveButton";
}

