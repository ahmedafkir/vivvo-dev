
Element.addMethods({pickClassName: function(element, classNames, index) {
	element = $(element);
	for (var i = 0; i < classNames.length; i++) {
		element[['addClassName', 'removeClassName'][(index != i) << 0]](classNames[i]);
	}
	return element.addClassName('validation');
}});

vivvo.admin.userEdit = {

	username: null,
	username_info: null,
	password: null,
	password_info: null,
	password_retype: null,
	password_retype_info: null,
	email: null,
	email_info: null,
	www: null,
	www_info: null,

	classes: ['valid', 'not_valid', 'checking'],

	initialize: function() {

		vivvo.mainNav.select('users');

		if ((this.username = $('USER_username'))) {
			this.username
				.observe('blur', this.username_onblur.bind(this))
				.observe('keyup', this.username_onkeyup.bindAsEventListener(this));
			this.username_info = $('username_info');
		}

		(this.password = $('USER_password'))
			.observe('blur', this.password_onblur.bind(this))
			.observe('keyup', this.password_onkeyup.bind(this));
		this.password_info = $('password_info');

		(this.password_retype = $('USER_retype_password'))
			.observe('blur', this.password_onblur.bind(this))
			.observe('keyup', this.password_onblur.bind(this));
		this.password_retype_info = $('password_retype_info');

		(this.email = $('USER_email_address'))
			.observe('blur', this.email_onblur.bind(this))
			.observe('keyup', this.email_onkeyup.bindAsEventListener(this));
		this.email_info = $('email_info');

		(this.www = $('USER_www')).observe('keyup', this.www_onkeyup.bind(this));
		this.www_info = $('www_info');

		$$('.info_help').each(function(link) {
			new Tooltip(link, {mouseFollow: false});
		});
	},

	submit: function(action) {
		$('user_edit_form').submit();
	},

	username_onblur: function() {
		return this.check('username.length') && this.check('username.valid') && this.check('username.available');
	},

	username_onkeyup: function(e, message) {
		if (!this.username.value.strip()) {
			return false;
		}
		if (!this.check('username.length')) {
			this.username_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_USERNAME_TOO_SHORT'));
		} else if (!this.check('username.valid')) {
			this.username_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_USERNAME_INVALID'));
		} else {
			if (message) {
				this.username_info.pickClassName(this.classes, 2).update(message);
			} else {
				this.username_info.pickClassName(this.classes, 0).update(vivvo.admin.lang.get('LNG_USERNAME_VALID'));
			}
			return true;
		}
		return false;
	},

	password_onblur: function() {
		if (this.check('passwords.length')) {
			if (!this.check('passwords.identical')) {
				this.password_retype_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_PASSWORDS_ARE_NOT_IDENTICAL'));
			} else {
				this.password_retype_info.pickClassName(this.classes, 0).update(vivvo.admin.lang.get('LNG_PASSWORDS_ARE_IDENTICAL'));
			}
		}
	},

	password_onkeyup: function() {

		var password = this.password.value,
			strength = 0;

		if (password.length < 6) {
			strength = -1;
		} else if (this.username && password.toLowerCase().match(this.username.value.toLowerCase()) && this.username.value != '') {
			strength = -2;
		} else {

			var repeats = 0, chars = password.split('');

			for (var i = 0; i < chars.length; i++) {
				for (var j = 0; j < chars.length; j++) {
					if (Math.abs(j - i) == 1) {
						if (chars[j] == chars[i]) {
							repeats++;
						}
					}
				}
			}

			repeats = parseInt(repeats / 2);

			if (repeats != 0 && repeats == password.length - 1) {
				strength = -3;
			} else {
				strength = 6 + (password.length < 16 ? 4 : 8)						// length
						 + 5 * !!password.match(/[a-z]/)							// lower case
						 + 5 * !!password.match(/[A-Z]/)							// upper case
						 + 5 * !!password.match(/[0-9]/)							// numbers
						 + 2 * !!password.match(/[a-z]{2}/)							// multi lower
						 + 2 * !!password.match(/[A-Z]{2}/)							// multi upper
						 + 2 * !!password.match(/[0-9]{2}/)							// multi numbers
						 + 5 * !!password.match(/[a-z]+.*[A-Z]+.*[0-9]+/)			// multi combination (separate)
						 + 5 * !!password.match(/[!,\\\/\-"@#$%^&\s*?_~]/)			// special
						 + 5 * !!password.match(/[!,\\\/\-"@#$%^&\s*?_~]{2}/);		// multi special

				strength = parseInt(strength * 1.2);

				if (strength > 50) {
					strength = 50;
				} else if (strength < 0) {
					strength = 1;
				}
			}
		}

		if (strength > 0 && strength <= 50) {
			strength += strength;
			this.password_info.pickClassName(this.classes, -1);
			if (strength >= 16 && strength <= 35) {
				this.password_info.setStyle({color: '#f3735d'}).update(vivvo.admin.lang.get('LNG_VERY_WEAK'));
			} else if (strength > 35 && strength <= 52) {
				this.password_info.setStyle({color: '#b35545'}).update(vivvo.admin.lang.get('LNG_WEAK'));
			} else if (strength > 52 && strength <= 69) {
				this.password_info.setStyle({color: '#6b99c5'}).update(vivvo.admin.lang.get('LNG_GOOD'));
			} else if (strength > 69 && strength <= 86) {
				this.password_info.addClassName('valid').setStyle({color: '#8b9241'}).update(vivvo.admin.lang.get('LNG_STRONG'));
			} else if (strength > 86) {
				this.password_info.addClassName('valid').setStyle({color: '#80ca73'}).update(vivvo.admin.lang.get('LNG_VERY_STRONG'));
			}
		} else if (strength == -1) {
			this.password_info.pickClassName(this.classes, 1).setStyle({color: '#f3735d'}).update(vivvo.admin.lang.get('LNG_PASSWORD_TOO_SHORT'));
		} else if (strength == -2) {
			this.password_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_PASSWORD_MUST_NOT_CONTAIN_USERNAME'));
		} else if (strength == -3) {
			this.password_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_TOO_MANY_REPEATED_CHARACTERS'));
		} else if (this.password.value.length != 0) {
			this.password_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_PASSWORD_INVALID'));
		}
	},

	email_onkeyup: function(e, message) {
		if (this.email.value.strip()) {
			if (!this.check('email.valid')) {
				this.email_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_EMAIL_ADDRESS_NOT_VALID'));
			} else if (message) {
				this.email_info.pickClassName(this.classes, 2).update(message);
			} else {
				this.email_info.pickClassName(this.classes, 0).update(vivvo.admin.lang.get('LNG_EMAIL_ADDRESS_VALID'));
			}
		}
	},

	email_onblur: function() {
		return this.check('email.valid') && this.check('email.available');
	},

	www_onkeyup: function() {
		if (this.www.value.length) {
			if (this.check('url.valid')) {
				this.www_info.pickClassName(this.classes, 0).update(vivvo.admin.lang.get('LNG_REGISTER_VALID_URL'));
			} else {
				this.www_info.pickClassName(this.classes, 1).update(vivvo.admin.lang.get('LNG_REGISTER_INVALID_URL'));
			}
		}
	},

	check: function(what) {

		switch (what) {

			case 'username.length':
				return this.username.value.length >= 3;

			case 'username.valid':
				return this.username.value.match(/^[a-zA-Z0-9\_\-]+$/);

			case 'username.available':
				return this.available('username');

			case 'passwords.length':
				return this.password.value.strip() && this.password_retype.value.strip();

			case 'passwords.identical':
				return this.password.value == this.password_retype.value;

			case 'email.valid':
				return this.email.value.match(/^[-_a-zA-Z0-9]+(\.[-_a-zA-Z0-9]+)*@[-a-zA-Z0-9]+(\.[-a-zA-Z0-9]+)*\.[a-zA-Z]{2,6}$/);

			case 'email.available':
				return this.available('email');

			case 'url.valid':
				return this.www.value.match(/^(www\.)?([a-zA-Z0-9\-]*)\.([a-zA-Z0-9\-]*)\.?([\w]{2,3})(\.[A-Za-z]{1,3})?([\/~A-Za-z\d]+)?$/);
		}

		return false;
	},

	available: function(what) {

		var value, data, params = {
			action: 'user',
			SECURITY_TOKEN: vivvo.token
		};

		switch (what) {

			case 'username':
				params.cmd = 'checkUsername';
				value = params.USER_username = this.username.value;
				this.username_onkeyup(true, vivvo.admin.lang.get('LNG_CHECKING_USERNAME_AVAILABILITY'));
				data = {
					elem: this.username_info,
					taken: vivvo.admin.lang.get('LNG_USERNAME_TAKEN'),
					available: vivvo.admin.lang.get('LNG_USERNAME_AVAILABLE')
				};
			break;

			case 'email':
				params.cmd = 'checkEmail';
				value = params.USER_email = this.email.value;
				this.email_onkeyup(true, vivvo.admin.lang.get('LNG_CHECKING_EMAIL_AVAILABILITY'));
				data = {
					elem: this.email_info,
					taken: vivvo.admin.lang.get('LNG_EMAIL_TAKEN'),
					available: vivvo.admin.lang.get('LNG_EMAIL_ADDRESS_VALID')
				};
			break;

			default:
				return;
		}

		new Ajax.Request('index.php', {
			method: 'POST',
			parameters: params,
			onSuccess: function(transport) {
				var taken = 'N/A', response = String(transport.responseText);
				if (response.isJSON()) {
					taken = response.evalJSON();
				}
				if (taken === true) {
					data.elem.pickClassName(this.classes, 1).update(data.taken);
				} else if (taken === false) {
					data.elem.pickClassName(this.classes, 0).update(data.available);
				} else {
					data.elem.pickClassName(this.classes, 2).update(vivvo.admin.lang.get('LNG_COULD_NOT_CHECK_AVAILABILITY'));
				}
			}.bind(this)
		});
	}
};

Event.observe(window, 'load', vivvo.admin.userEdit.initialize.bind(vivvo.admin.userEdit));