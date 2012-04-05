<div id="box_article_tools">
	<div class="box_title_holder"><div class="box_title"> </div></div>
	<div class="box_body">
		<div class="box_content">
			<ul>
				<li>
					<vte:include file="{VIVVO_TEMPLATE_DIR}box/email_to_a_friend.tpl" />
				</li>
				<li>
					<a href="{article.get_print_href}" target="_blank"><img src="{VIVVO_THEME}img/printer_icon.gif" alt="print" /> <vte:value select="{LNG_PRINT_VERSION}" /></a>
				</li>
				<vte:if test="{VIVVO_MODULES_PLAINTEXT}">
					<li>
						<a href="{CURRENT_URL|switch_format:'txt'}"><img src="{VIVVO_THEME}img/plain_text_icon.gif" alt="Plain text" /> <vte:value select="{LNG_PLAIN_TEXT}" /></a>
					</li>
				</vte:if>
			</ul>
		</div>
	</div>
</div>