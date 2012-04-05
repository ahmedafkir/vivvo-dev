<vte:template>
	<vte:if test="{ajax_output}">
		<div id="newsletter_dump">
			<vte:foreach item = "message" from = "{DUMP_MESSAGES}">
				<span class="{message.type}"><vte:value select="{message.get_message}" />
					<vte:if test="{message.additional_info} != ''">
						: <vte:value select="{message.additional_info}" />
					</vte:if>
				</span>
			</vte:foreach>
			<vte:if test="{action}">
				<script language="JavaScript" type="text/JavaScript">
					if ($('newsletter_form')) $('newsletter_form').hide();
				</script>
			</vte:if>
		</div>
		<vte:else>
			<div id="box_newsletter" class="box">
				<vte:header type="css" href="{VIVVO_THEME}css/plugin_newsletter.css" />
				<div class="box_title"><span><vte:value select="{LNG_PLUGIN_NEWSLETTER_BOX_NAME}" /></span></div>
				<div class="box_body">
					<div id="newsletter_form_holder">
						<vte:if test="!{CURRENT_USER}">
							<form id="newsletter_form" action="" method="post" onsubmit="updateNewsletter();return false;">
								<input type="hidden" name="action" value="newsletter" />
								<input type="hidden" name="cmd" value="subscriberAdd" />
								<vte:value select="{LNG_PLUGIN_NEWSLETTER_EMAIL}" />:
								<input class="text" type="text" name="PLUGIN_NEWSLETTER_email" value="" style=" width: 130px;" />
								<div class="button_holder"><input type="submit" class="submit_button" name="submit" value="{LNG_PLUGIN_NEWSLETTER_SUBSCRIBE}" /></div>
							</form>
							<vte:else>
								<vte:if test="{CURRENT_USER.subscriber}">
									<form id="newsletter_form" action="" method="post" onsubmit="updateNewsletter();return false;">
										<input type="hidden" name="action" value="newsletter" />
										<input type="hidden" name="cmd" value="subscriberUserRemove" />
										<div class="button_holder"><input type="submit" class="submit_button" name="submit" value="{LNG_PLUGIN_NEWSLETTER_UNSUBSCRIBE}" /></div>
									</form>
									<vte:else>
										<form id="newsletter_form" action="" method="post" onsubmit="updateNewsletter();return false;">
											<input type="hidden" name="action" value="newsletter" />
											<input type="hidden" name="cmd" value="subscriberUserAdd" />
											<div class="button_holder"><input type="submit" class="submit_button" name="submit" value="{LNG_PLUGIN_NEWSLETTER_SUBSCRIBE}" /></div>
										</form>
									</vte:else>
								</vte:if>
							</vte:else>
						</vte:if>
					</div>
				</div>
				<script src="{VIVVO_URL}js/plugin_newsletter.js" type="text/javascript"> </script>
			</div>
		</vte:else>
	</vte:if>
</vte:template>