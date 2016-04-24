var HMR = HMR || {};

HMR.MapTest = function() {

  // Div that will hold map
  var mapNest = document.getElementById('map_nest'),
      styles = [
        {
          featureType: 'road.highway',
          elementType: 'all',
          stylers: [
            { hue: '#999999' },
            { saturation: -100 },
            { lightness: -6 },
            { visibility: 'on' }
          ]
        },{
          featureType: 'water',
          elementType: 'geometry.fill',
          stylers: [
            { hue: '#c2c2c2' },
            { saturation: -100 },
            { lightness: 0 },
            { visibility: 'on' }
          ]
        },{
          featureType: 'road.arterial',
          elementType: 'geometry.fill',
          stylers: [
            { hue: '#c1c1c1' },
            { saturation: -100 },
            { lightness: -2 },
            { visibility: 'on' }
          ]
        },{
          featureType: 'water',
          elementType: 'labels.text.stroke',
          stylers: [
            { visibility: 'off' }
          ]
        },{
          featureType: 'poi',
          elementType: 'labels.icon',
          stylers: [
            { visibility: 'off' }
          ]
        },{
          featureType: 'transit',
          elementType: 'labels',
          stylers: [
            { visibility: 'off' }
          ]
        },{
          featureType: 'road',
          elementType: 'labels.text.stroke',
          stylers: [
            { visibility: 'off' }
          ]
        },{
          featureType: 'water',
          elementType: 'labels',
          stylers: [
            { hue: '#000' },
            { saturation: -100 },
            { lightness: 0 },
          ]
        },{
          featureType: 'road',
          elementType: 'labels',
          stylers: [
            { hue: '#000' },
            { saturation: -100 },
            { lightness: 0 },
          ]
        },{
          featureType: 'road',
          elementType: 'geometry.stroke',
          stylers: [
            { visibility: 'off' }
          ]
        },{
          featureType: 'poi',
          elementType: 'geometry.fill',
          stylers: [
            { hue: '#c3c3c3' },
            { saturation: -100 },
            { lightness: -2 },
            { visibility: 'on' }
          ]
        },{
          featureType: 'landscape',
          elementType: 'geometry.fill',
          stylers: [
            { hue: '#e9e9e9' },
            { saturation: -100 },
            { lightness: 22 },
            { visibility: 'on' }
          ]
        },{
          featureType: 'road.arterial',
          elementType: 'labels.icon',
          stylers: [
            { hue: '#e9e9e9' },
            { saturation: -100 },
            { lightness: 22 },
            { visibility: 'off' }
          ]
        }
      ],
      location = {
        name: 'hmr',
        lat: 41.904135, // Latitude of HMR address
        lng: -87.656130 // Longitude of HMR address
      },
      latLng = new google.maps.LatLng( location.lat, location.lng ),
      center = new google.maps.LatLng( 41.904366, -87.662659 ),
      options = {
        zoom: 16,
        styles:styles,
        mapTypeControl: false,
        panControl: false,
        draggable: false,
        maxZoom: 16,
        minZoom: 16,
        zoomControl: false,
        scaleControl: false,
        streetViewControl: false,
        overviewMapControl: false,
        center: center,
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
