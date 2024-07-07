  <head>
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <title>Presence System</title>
      <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
      <link rel="icon" href="{{ asset('assets/img/brgm-icon.png') }}" type="image/x-icon" />

      <!-- Fonts and icons -->
      <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
      <script>
          WebFont.load({
              google: {
                  families: ["Public Sans:300,400,500,600,700"]
              },
              custom: {
                  families: [
                      "Font Awesome 5 Solid",
                      "Font Awesome 5 Regular",
                      "Font Awesome 5 Brands",
                      "simple-line-icons",
                  ],
                  urls: ["{{ asset('assets/css/fonts.min.css') }}"],
              },
              active: function() {
                  sessionStorage.fonts = true;
              },
          });
      </script>

      <!-- CSS Files -->
      <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />
      <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/sweetalert2.min.css') }}" />

      <!-- CSS Just for demo purpose, don't include it in your project -->
      <link rel="stylesheet" href="{{ asset('assets/css/demo.css') }}" />

      <!-- Leaflet Packages -->
      <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
      <link rel="stylesheet" href="https://unpkg.com/leaflet-geosearch@3.1.0/dist/geosearch.css" />

      <!-- CSRF -->
      <meta name="token" content="{{ csrf_token() }}">
      <style>
          #map {
              height: 620px;
          }

          .leaflet-control-locate {
              background-color: white;
              border: 2px solid #ccc;
              border-radius: 4px;
              padding: 5px;
              cursor: pointer;
          }
      </style>
  </head>
