var HMR = HMR || {};

HMR.MapTest = function() {

  // Div that will hold map
  var mapNest = document.getElementById('map_nest'),
      location = {
        name: 'hmr',
        lat: 41.904135, // Latitude of HMR address
        lng: -87.656130 // Longitude of HMR address
      },
      latLng = new google.maps.Lat.Lng( location.lat, location.lng ),
      options = {
        zoom: 12,
        // This will need to be the lat/lng of the address
        center: latLng,
        mapTypeId: google.maps.MapTypeId.ROADMAP
      },

      map = new google.maps.Map(mapNest, options),

      marker = new google.maps.Marker({
        flat: true,
        icon: new google.maps.MarkerImage('/assets/map-pin.gif'),
        map: map,
        optimized: false,
        position: latLng,
        visible: true
      });

};