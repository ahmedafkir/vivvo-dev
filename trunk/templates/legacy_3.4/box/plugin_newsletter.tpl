<vte:template>
	<div id="box_newsletter" class="box">
		<vte:header type="css" href="{VIVVO_THEME}css/plugin_newsletter.css" />
		<div class="box_title"><span>Newsletter<vte:value select="{LNG_NEWSLETTER}" /></span></div>
		<div class="box_body">
			<div id="newsletter_form_holder">
				<form action="" method="post">
					<input type="hidden" name="action" value="newsletter" />
					<input type="hidden" name="cmd" value="subscriberAdd" />
					Email:
					<input class="text" type="text" name="PLUGIN_NEWSLETTER_email" value="" style=" width: 130px;" />
					<div class="button_holder"><input type="submit" class="submit_button" name="submit" value="Subscribe" /></div>
				</form>
			</div>
		</div>
	</div>
</vte:template>