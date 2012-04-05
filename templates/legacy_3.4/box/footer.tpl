<vte:template>
	<vte:header type="script" href="{VIVVO_URL}js/bookmark.js" />
	<div class="footer">
		<vte:box module="box_sections">
			<vte:params>
				<vte:param name="id" value="0" />
			</vte:params>
			<vte:template>
				<ul>
					<vte:foreach item = "category" from = "{categories}">
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
			<img src="{VIVVO_URL}cron_image.html" style="display:none;" alt="cron" title="cron" />
			<a href="{VIVVO_URL}"><vte:value select="{LNG_GO_HOME}" /></a> |
			<a onclick="this.style.behavior='url(#default#homepage)';this.setHomePage('{VIVVO_URL}');" href="#"><vte:value select="{LNG_SET_HOMEPAGE}" /></a> |
			<a href="javascript:bookmarksite('{TITLE}',document.URL);"><vte:value select="{LNG_ADD_FAVORITES}" /></a> 
			<vte:if test="{VIVVO_MODULES_FEED}">
				| 
				<vte:if test="{VIVVO_FRIENDY_URL}">
					<a href="{VIVVO_URL|switch_format:'rss'}">Rss</a> / <a href="{VIVVO_URL|switch_format:'atom'}">Atom</a> 
					<vte:else>
						<a href="{VIVVO_PROXY_URL|switch_format:'rss'}">Rss</a> / <a href="{VIVVO_PROXY_URL|switch_format:'atom'}">Atom</a> 
					</vte:else>
				</vte:if>
			</vte:if>
			<vte:if test="{VIVVO_MODULES_PLAINTEXT}">
				| 
				<vte:if test="{VIVVO_FRIENDY_URL}">
					<a href="{VIVVO_URL|switch_format:'txt'}"><vte:value select="{LNG_PLAIN_TEXT}" /></a> 
					<vte:else>
						<a href="{VIVVO_PROXY_URL|switch_format:'txt'}"><vte:value select="{LNG_PLAIN_TEXT}" /></a> 
					</vte:else>
				</vte:if>
			</vte:if>
			<vte:if test="{VIVVO_MODULES_ARCHIVE_VIEW}">
				|
				<vte:if test="{VIVVO_FRIENDY_URL}">
					<a href="{VIVVO_URL}archive"><vte:value select="{LNG_ARCHIVE}" /></a>
					<vte:else>
						<a href="{VIVVO_PROXY_URL}archive"><vte:value select="{LNG_ARCHIVE}" /></a>
					</vte:else>
				</vte:if>
			</vte:if>
			<vte:if test="{CURRENT_USER.can|'ACCESS_ADMIN'}">
				| <a href="{VIVVO_URL}{VIVVO_FS_ADMIN_DIR}index.php"><vte:value select="{LNG_SITE_ADMINISTRATION}" /></a>
			</vte:if>
		</div>
		<div class="corner_bottom"><!--  --></div>
	</div>
	<div class="bottom_corners"><!--  --></div>
</vte:template>