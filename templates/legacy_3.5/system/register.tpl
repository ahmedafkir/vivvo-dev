<vte:template>
	<script type="text/javascript" src="{VIVVO_URL}js/lang.php"> </script>
	<vte:header type="script" href="{VIVVO_URL}js/framework/prototype.js" />
	<vte:header type="script" href="{VIVVO_URL}js/register.js" />
	<div id="register">
		<vte:if test="!{CURRENT_USER}">
			<div class="dump">
				<vte:attribute name="style">
					<vte:if test="!{DUMP_MESSAGES}">display:none;</vte:if>
				</vte:attribute>
				<vte:foreach item = "message" from = "{DUMP_MESSAGES}">
					<span class="{message.type}"><vte:value select="{message.get_message}" />
						<vte:if test="{message.additional_info} != ''">
							: <vte:value select="{message.additional_info}" />
						</vte:if>
					</span>
				</vte:foreach>
			</div>
			
			<vte:if test="!{REGISTRATION}">
				<h1><vte:value select="{LNG_SIGN_UP}" /></h1>
				<div id="error_message"> </div>
				<form id="register_form" name="register_form" action="{VIVVO_PROXY_URL}login.html" method="post" onsubmit="return vivvoRegistrationForm.validate();" >
					<input type="hidden" name="SECURITY_TOKEN" value ="{VIVVO_SECURITY_TOKEN}" />
					<input type="hidden" name="action" value ="login" />
					<input type="hidden" name="cmd" value ="register" />
					<div class="form_line"> 
						<label><vte:value select="{LNG_USER_USERNAME}" />:</label>
						<div class="formElement">
							<vte:if test="{user.get_id}">
								<span><vte:value select="{user.username}" /></span>
								<vte:else>
									<input type="text" class="text" name="USER_username" value ="{user.username}" style=" width: 216px;" />
									<div id="user_availability"> </div>
								</vte:else>
							</vte:if>
						</div>
					</div>
					<div class="form_line"> 
						<label><vte:value select="{LNG_USER_PASSWORD}" />:</label>
						<div class="formElement">
							<input type="password" class="text" name="USER_password" value ="" style=" width: 216px;" />
							<div id="pass_strength"> </div>
						</div>
					</div>
					<div class="form_line"> 
						<label><vte:value select="{LNG_CONFIRM_PASSWORD}" />:</label>
						<div class="formElement">
							<input type="password" class="text" name="USER_retype_password" value ="" style=" width: 216px;" />
							<div id="pass_identical"> </div>
						</div>
					</div>
					<div class="form_line"> 
						<label><vte:value select="{LNG_USER_EMAIL}" />:</label>
						<div class="formElement">
							<input type="text" class="text" name="USER_email_address" value ="{user.email_address}" style=" width: 216px;" />
							<div id="email_valid"> </div>
						</div>
					</div>
					<div class="form_line"><p> </p></div>
					<div class="form_line"> 
						<label><vte:value select="{LNG_USER_FIRST_NAME}" />:</label>
						<div class="formElement">
							<input type="text" class="text" name="USER_first_name" value ="{user.first_name}" style=" width: 216px;" />
						</div>
					</div>
					<div class="form_line"> 
						<label><vte:value select="{LNG_USER_LAST_NAME}" />:</label>
						<div class="formElement">
							<input type="text" class="text" name="USER_last_name" value ="{user.last_name}" style=" width: 216px;" />
						</div>
					</div>
					<div class="form_line"> 
						<label><vte:value select="{LNG_USER_WWW}" />:</label>
						<div class="formElement">
							http:// <input type="text" class="text" name="USER_www" value ="{user.www}" style=" width: 182px;" />
							<div id="www_valid"> </div>
						</div>
					</div>
					<vte:if test="{VIVVO_TOS}">
						<div class="form_line"> 
							<label> </label>
							<div class="formElement">
								<input type="checkbox" name="USER_TOS"/> <span class="tos_label"><vte:value select="{LNG_USER_TOS_REGISTER}" /> <a href="javascript:;" onclick="$('tos').toggle();"><vte:value select="{LNG_USER_TOS}" /></a></span>
                                <div id="tos" class="tos_box" style="display:none;"><pre><vte:value select="{VIVVO_TOS_CONTENT}" /></pre></div>
							</div>
						</div>
					</vte:if>
					<div class="form_line"> 
						<label> </label>
						<div class="formElement submit">
							<input type="submit" class="submit_button" value="{LNG_SUBMIT_BUTTON}" />
						</div>
					</div>
				</form>
				<script type="text/javascript" language="javascript">
					vivvoRegistrationForm = new vivvoRegisterForm('register_form', '<vte:value select="{VIVVO_TOS}" />');
					if ($('tos')) $('tos').hide();
				</script>
			</vte:if>
			<vte:else>
				<vte:value select="{LNG_ALLERADY_LOGGED_IN}" />
			</vte:else>
		</vte:if>
	</div>
</vte:template>