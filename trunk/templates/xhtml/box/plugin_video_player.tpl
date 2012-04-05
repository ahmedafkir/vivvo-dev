<div class="box_content">
    <vte:header type="css" href="{VIVVO_THEME}css/plugin_video.css" />
	<vte:header type="script" href="{VIVVO_STATIC_URL}js/jw_player.js" />
    <vte:if test="!{video_holder_id}">
		<vte:variable name="video_holder_id" value="video_playlist" />
        <vte:if test="!{video_width}">
            <vte:variable name="video_width" value="600" />
        </vte:if>
        <vte:if test="!{video_height}">
            <vte:variable name="video_height" value="338" />
        </vte:if>
        <div class="player_container">
            <div id="video_player"> </div>
        </div>
        <div id="player_article"> </div>
        <script type="text/javascript">
            jwplayer("video_player").setup({
                flashplayer: "<vte:value select="{VIVVO_URL}" />flash/player.swf",
				width: 600,
                height:338,
                controlbar:'bottom',
                backcolor:'181818',
                frontcolor:'EEEEEE',
                stretching:'fill'
            });
        </script>	
    </vte:if>
</div>