jQuery(function() {
	jQuery('label.GMap input').each(function() {

		var coordinates = this.value.match(/[-\d.]+/g) || [37.4419, -122.1419], // default location
		    container   = this.parentNode.appendChild(document.createElement('span')),
		    replacement = document.createElement('input');

		replacement.type  = 'hidden';
		replacement.name  = this.name;
		replacement.value = this.value;

		this.parentNode.replaceChild(replacement, this);

		container.style.padding = 0;
		container.style.height  = Math.round(0.6 * container.offsetWidth) + "px";

		var map      = new GMap2(container),
		    center   = new GLatLng(coordinates[0], coordinates[1]),
		    marker   = new GMarker(center, {draggable: true}),
			geocoder = new GClientGeocoder();
		
		map.setCenter(center, 13);
		map.addControl(new GSmallMapControl());
		map.addOverlay(marker);

		jQuery(replacement.form).submit(function() {
			var point = marker.getLatLng();
			replacement.value = point.lat() + ", " + point.lng();
		});

		jQuery(window).bind("unload", GUnload);
	});
});