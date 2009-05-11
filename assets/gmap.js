UIControl.deploy("label.GMap input", function(input) {
	var coordinates = input.value.match(/[-\d.]+/g) || [37.4419, -122.1419], // default location
	    container   = DOM.insertElement("span", input.parentNode);

	input.type = "hidden";

	container.style.padding = 0;
	container.style.height  = Math.round(0.6 * container.offsetWidth) + "px";

	var map      = new GMap2(container),
	    center   = new GLatLng(coordinates[0], coordinates[1]),
	    marker   = new GMarker(center, {draggable: true}),
		geocoder = new GClientGeocoder();
		
	map.setCenter(center, 13);
	map.addControl(new GSmallMapControl());
	map.addOverlay(marker);

	DOM.Event.addListener(input.form, "submit", function() {
		var point = marker.getLatLng();
		input.value = point.lat() + ", " + point.lng();
	});

	DOM.Event.addListener(window, "unload", GUnload);
});