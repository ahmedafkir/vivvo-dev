<vte:template>
	<script type="text/javascript" src="http://maps.google.com/maps?file=api&v=2.x&key={VIVVO_PLUGIN_GEO_POSITION_API_KEY}"> </script>
	<script type="text/javascript">
		var start = new GLatLng(<vte:value select="{VIVVO_PLUGIN_GEO_POSITION_CENTER_LAT}" />, <vte:value select="{VIVVO_PLUGIN_GEO_POSITION_CENTER_LNG}" />);
		var zoomStart = <vte:value select="{VIVVO_PLUGIN_GEO_POSITION_ZOOM}" />;
		var url = '<vte:value select="{VIVVO_URL}" />kml/';
	</script>
	<div id="map" style="width:100%;height:400px;"> </div>
	<script type="text/javascript">
		//<![CDATA[
		var gx = new GGeoXml(url);

		var map = new GMap2(document.getElementById("map"));
		//var start = new GLatLng(65,25);
		map.setCenter(start, zoomStart);

		map.addControl(new GMapTypeControl(1));
		map.addControl(new GLargeMapControl());
		
		map.enableContinuousZoom();
		map.enableDoubleClickZoom();
		
		map.addOverlay(gx);
		//]]>
	</script>
</vte:template>