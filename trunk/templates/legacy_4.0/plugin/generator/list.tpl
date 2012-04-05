<div id="{BOX_ID}" class="box">
	<div class="box_title"><span><vte:value select="{TITLE}" /></span></div>
	<div class="box_line">
		<!--  -->
	</div>
	<div class="box_body">
		<ul>
			<vte:foreach item = "article" from = "{article_list}">
				<li>
					<a href="{article.get_href}">
						<vte:value select="{article.get_title}" />
					</a>
				</li>
			</vte:foreach>
		</ul>
	</div>
</div>