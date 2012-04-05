<vte:if test="{VIVVO_MODULES_FEATURED_AUTHOR}">
	<vte:box module="box_users">
		<vte:params>
			<vte:param name="search_sort_by" value="random" />
			<vte:param name="search_limit" value="1" />
			<vte:param name="search_user_type" value="{VIVVO_MODULES_FEATURED_AUTHOR_GROUPS}" />
		</vte:params>
		<vte:template>
			<div id="box_users" class="box">
					<div class="box_title_holder"><div class="box_title"><vte:value select="{LNG_FEATURED_AUTHOR}" /></div></div>
					<div class="box_body">
						<div class="box_content">
							<vte:foreach item = "user" from = "{user_list}">
								<a href="{user.get_href}"><vte:value select="{user.get_name}" /></a>
								<vte:if test="{user.picture}">
									<img src="{user.get_picture_href|'summary_medium'}" alt="image" />
								</vte:if>
								<vte:value select="{user.get_bio}" />
							</vte:foreach>
						</div>
					</div>
				</div>
		</vte:template>
	</vte:box>
</vte:if>