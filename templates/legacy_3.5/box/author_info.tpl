<vte:if test="{CURRENT_AUTHOR}">
	<div id="box_users" class="box">
		<div class="box_title_holder"><div class="box_title"><vte:value select="{LNG_AUTHOR_INFO}" /></div></div>
		<div class="box_body">
			<div class="box_content">
				<vte:if test="{CURRENT_AUTHOR.picture}">
					<img src="{CURRENT_AUTHOR.get_picture_href}" alt="image" />
				</vte:if>
				<vte:if test="{CURRENT_AUTHOR.www}">
					<a href="http://{CURRENT_AUTHOR.www}"><vte:value select="{CURRENT_AUTHOR.get_name}" /></a>
					<vte:else>
						<vte:value select="{CURRENT_AUTHOR.get_name}" />
					</vte:else>
				</vte:if>
				<vte:value select="{CURRENT_AUTHOR.get_bio}" />
			</div>
		</div>
	</div>
</vte:if>