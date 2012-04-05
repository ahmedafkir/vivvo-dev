<vte:template>
	<vte:header type="script" href="{VIVVO_URL}js/bookmark.js" />
	<div class="footer">
		<vte:box module="box_sections">
			<vte:params>
				<vte:param name="id" value="{VIVVO_ROOT_CATEGORY}" />
			</vte:params>
			<vte:template>
				<ul>
					<vte:foreach item="category" from="{categories}">
						<vte:if test="{category.view_subcat}">
							<li>
								<a>
									<vte:attribute name="href">
                                        <vte:if test="{category.redirect}">
                                            <vte:value select="{category.redirect}" />
                                            <vte:else>
                                                <vte:value select="{category.get_href}" />
                                            </vte:else>
                                        </vte:if>
                                    </vte:attribute>
                                    <vte:value select="{category.category_name}" />
								</a>
							</li>
						</vte:if>
					</vte:foreach>
				</ul>
			</vte:template>
		</vte:box>
		<div class="static_footer">
			<img src="{VIVVO_PROXY_URL}cron_image.html" style="display:none;" alt="cron" title="cron" />
			<a href="{VIVVO_URL}"><vte:value select="{LNG_GO_HOME}" /></a> |
			<a onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('{VIVVO_URL}');" href="#"><vte:value select="{LNG_SET_HOMEPAGE}" /></a> |
			<a href="javascript:bookmarksite('{TITLE}',document.URL);"><vte:value select="{LNG_ADD_FAVORITES}" /></a>
			<vte:if test="{VIVVO_MODULES_FEED}">
				| <a href="{VIVVO_PROXY_URL|switch_format:'rss'}">Rss</a> / <a href="{VIVVO_PROXY_URL|switch_format:'atom'}">Atom</a>
			</vte:if>
			<vte:if test="{VIVVO_MODULES_PLAINTEXT}">
				| <a href="{VIVVO_PROXY_URL|switch_format:'txt'}"><vte:value select="{LNG_PLAIN_TEXT}" /></a>
			</vte:if>
			<vte:if test="{VIVVO_MODULES_ARCHIVE_VIEW}">
				| <a href="{VIVVO_PROXY_URL}archive"><vte:value select="{LNG_ARCHIVE}" /></a>
			</vte:if>
			<vte:if test="{CURRENT_USER.can|'ACCESS_ADMIN'}">
				| <a href="{VIVVO_URL}{VIVVO_FS_ADMIN_DIR}index.php"><vte:value select="{LNG_SITE_ADMINISTRATION}" /></a>
			</vte:if>
		</div>
		<div class="corner_bottom"><!--  --></div>
	</div>
	<div class="bottom_corners"><!--  --></div>
	<vte:if test="{VIVVO_ANALYTICS_TRACKER_ID}">
		<script type="text/javascript">(function(){var ga=document.createElement('script');ga.type='text/javascript';ga.async=true;ga.src=('https:'==document.location.protocol?'https://ssl':'http://www')+'.google-analytics.com/ga.js';(document.getElementsByTagName('head')[0]||document.getElementsByTagName('body')[0]).appendChild(ga);})();</script>
	</vte:if>
</vte:template>
