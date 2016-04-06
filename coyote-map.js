var map = L.map('map').setView([40.7660646, -73.9760075], 17);
var opencyclemap = L.tileLayer('https://{s}.tile.thunderforest.com/cycle/{z}/{x}/{y}.png');
var refill = Tangram.leafletLayer({ scene: base_url + '/lib/refill-style/refill-style.yaml' }).addTo(map);
L.control.layers({
	"Tangram Refill": refill,
	"OpenCycleMap": opencyclemap
}).addTo(map);
var trace = omnivore.gpx(base_url + '/2016.gpx').addTo(map);

map.on('click', function(e) {
    console.log(e.latlng.lat, e.latlng.lng);
});