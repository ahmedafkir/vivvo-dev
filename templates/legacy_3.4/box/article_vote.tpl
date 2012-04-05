<vte:if test="{article.show_poll}">
	<vte:header type="script" href="{VIVVO_URL}js/stars.js" />
	<div id="box_article_rating" class="box">
		<div class="box_title_holder">
			<div class="box_title">
				<vte:value select="{LNG_RATE_ARTICLE}" />
			</div>
		</div>
		<div class="box_body">
			<div class="box_content">
				<div class="article_rating"><vte:value select="{article.get_vote_average|2}" /></div>
				<div id="stars"> </div>
			</div>
		</div>
		<script type="text/javascript" language="javascript">
			new Starry('stars', {
				showNull: false,
				startAt: <vte:value select="{article.get_vote_average}" />,
				voted: <vte:if test="{article.is_voted}">true<vte:else>false</vte:else></vte:if>,
				callback: <vte:if test="{article.is_voted}">Prototype.emptyFunction<vte:else>function (index) {
					var voteParam = {};
					voteParam.action = 'article';
					voteParam.cmd = 'vote';
					voteParam.ARTICLE_id = <vte:value select="{article.get_id}" />;
					voteParam.ARTICLE_vote = index;
					voteParam.template_output = 'box/article_vote';

					new Ajax.Updater('box_article_rating', document.location.toString(), {
						parameters: voteParam,
						evalScripts: true,
						insertion: Element.replace
						<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
						,onSuccess: function(xhr) {
							if (xhr.getResponseHeader('X-Vivvo-Action-Status') == 1) {
								_gaq.push(['_trackEvent', 'Article', 'Rate', '<vte:value select="{article.get_id}" />', index]);
							}
						}
						</vte:if>
					});
				}</vte:else></vte:if>,
				sprite: '<vte:value select="{VIVVO_THEME}" />img/stars.gif'
			});
		</script>
	</div>
</vte:if>