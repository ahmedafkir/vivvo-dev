<div class="box_content">
	<vte:header type="css" href="{VIVVO_THEME}css/plugin_video.css" />
    <vte:header type="script" href="{VIVVO_STATIC_URL}js/jw_player.js" />
    <vte:if test="!{video_holder_id}">
		<vte:variable name="video_holder_id" value="video_playlist" />
        <vte:if test="!{video_width}">
            <vte:variable name="video_width" value="646" />
        </vte:if>
        <vte:if test="!{video_height}">
            <vte:variable name="video_height" value="300" />
        </vte:if>
        <div class="player_container">
			<vte:include file="{VIVVO_TEMPLATE_DIR}system/flash.tpl" id="jwp_{video_holder_id}" width="{video_width}" height="{video_height}" movie="{VIVVO_STATIC_URL}flash/player.swf" flashvars="playerready=jwp_{video_holder_id}_onplayerready&amp;backcolor=181818&amp;frontcolor=EEEEEE&amp;stretching=fill" />
        </div>
    </vte:if>
    <script type="text/javascript">
        new JWPlayer(<vte:value select="{video_holder_id|json_encode}" />, '.box_content', '.item_holder');
    </script>
    <div id="player_article">
        
    </div>
</div>
