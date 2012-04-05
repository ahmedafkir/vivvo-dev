<vte:template>
	<vte:header type="css" href="{VIVVO_THEME}css/features_stripe.css" />
	<vte:header type="script" href="{VIVVO_URL}js/framework/effects.js" />
	<vte:header type="script" href="{VIVVO_URL}js/glider.js" />
	<div id="{BOX_ID}">
		<div id="{BOX_ID}_stripe_body" class="box_stripes">
			<div class="controls"> 
				<span class="section_next"><a href="#{BOX_ID}_section6"><img src="{VIVVO_THEME}img/scroller_next.gif" alt="Next" title="Next" /></a></span> <a href="#{BOX_ID}_section1"><img src="{VIVVO_THEME}img/scroller_back.gif" alt="Previous" title="Previous" /></a>
			</div>
			<div class="scroller">
				<div style="width:10000px;">
					<vte:for from="{article_list}" step="5" key="strip_index">
						<div id="{BOX_ID}_section{strip_index}" class="section">
							<vte:foreach item = "article" key="index" from = "{article_list}" start="{strip_index}" loop="5">
								<vte:if test="{index|mod:'2'}">
									<div class="stripe_summary_holder">
										
										<vte:if test="{article.image}">
											<div class="image"><a href="{article.get_href}"><img src="{VIVVO_URL}thumbnail.php?file={article.image}&amp;size=summary_large" alt="image" /></a></div>
										</vte:if>
										<h3><a href="{article.get_href}"><vte:value select="{article.get_title}" /></a></h3>
									</div>
									<vte:else>
										<div class="stripe_summary_holder stripe_summary_text_holder">
											<h3><a href="{article.get_href}"><vte:value select="{article.get_title}" /></a></h3>
											<div class="summary">
												<vte:value select="{article.get_summary}" />
											</div>...
										</div>
									</vte:else>
								</vte:if>
							</vte:foreach>
						</div>
					</vte:for>
				</div>
			</div>
		</div>
	</div>
	<script>
		$$('#<vte:value select="{BOX_ID}" />_box_stripes .stripe_summary_text_holder').each(
			function (short){
				var summary = short.getElementsByClassName('summary')[0];
				if (summary) resizeShort(short, summary);
			}
		);
		var my_glider = new Glider('<vte:value select="{BOX_ID}" />_stripe_body', {duration:0.5});
	</script>
<vte:template>