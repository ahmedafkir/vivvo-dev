<div id="box_social_bookmarks">
	<strong><vte:value select="{LNG_SHARE_ON_SOCIAL_BOOKMARKS}" /></strong>:
    <a href="http://www.facebook.com/sharer.php?u={VIVVO_URL}index.php?news={article.get_id}" target="_blank"><img src="{VIVVO_THEME}img/facebook.gif" alt="Post on Facebook" border="0" />
        <vte:attribute name="onclick">
            <vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
                _gaq.push(['_trackEvent', 'Article', 'Share', '<vte:value select="{article.get_id}" />', 1]);
            </vte:if>
        </vte:attribute>
    	<span>Facebook</span>
    </a>
	<a href="http://del.icio.us/post?url={VIVVO_URL}index.php?news={article.get_id}&amp;title={article.get_title}" target="_blank"><img src="{VIVVO_THEME}img/delicious.gif" alt="Add to your del.icio.us" border="0" />
		<vte:attribute name="onclick">
			<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
				_gaq.push(['_trackEvent', 'Article', 'Share', '<vte:value select="{article.get_id}" />', 1]);
			</vte:if>
		</vte:attribute>
		<span>del.icio.us</span>
	</a>
	<a href="http://www.digg.com/submit?phase=2&amp;url={VIVVO_URL}index.php?news={article.get_id}" target="_blank"><img src="{VIVVO_THEME}img/digg_16x16.gif" alt="Digg this story" border="0" />
		<vte:attribute name="onclick">
			<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
				_gaq.push(['_trackEvent', 'Article', 'Share', '<vte:value select="{article.get_id}" />', 1]);
			</vte:if>
		</vte:attribute>
		<span>Digg</span>
	</a>
    <a href="http://www.stumbleupon.com/submit?url={VIVVO_URL}index.php?news={article.get_id}&amp;title={article.get_title}" target="_blank"><img src="{VIVVO_THEME}img/stumbleit.gif" alt="StumbleUpon" />
        <vte:attribute name="onclick">
			<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
				_gaq.push(['_trackEvent', 'Article', 'Share', '<vte:value select="{article.get_id}" />', 1]);
			</vte:if>
		</vte:attribute>
        <span>StumbleUpon</span>
    </a>
    <a href="http://twitter.com/home?status={VIVVO_URL}index.php?news={article.get_id}&amp;title={article.get_title}" target="_blank"><img src="{VIVVO_THEME}img/icon_twitter.png" alt="Twitter" />
        <vte:attribute name="onclick">
			<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
				_gaq.push(['_trackEvent', 'Article', 'Share', '<vte:value select="{article.get_id}" />', 1]);
			</vte:if>
		</vte:attribute>
        <span>Twitter</span>
    </a>
</div>
