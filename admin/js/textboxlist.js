/*
  Proto!MultiSelect 0.2
  - Prototype version required: 6.0

  Credits:
  - Idea: Facebook + Apple Mail
  - Caret position method: Diego Perini <http://javascript.nwbox.com/cursor_position/cursor.js>
  - Guillermo Rauch: Original MooTools script
  - Ran Grushkowsky/InteRiders Inc. : Porting into Prototype and further development

  Changelog:
  - 0.1: translation of MooTools script
  - 0.2: renamed from Proto!TextboxList to Proto!MultiSelect, added new features/bug fixes
        added feature: support to fetch list on-the-fly using AJAX    Credit: Cheeseroll
        added feature: support for value/caption
        added feature: maximum results to display, when greater displays a scrollbar   Credit: Marcel
        added feature: filter by the beginning of word only or everywhere in the word   Credit: Kiliman
        added feature: shows hand cursor when going over options
        bug fix: the click event stopped working
        bug fix: the cursor does not 'travel' when going up/down the list   Credit: Marcel
*/

/* Copyright: InteRiders <http://interiders.com/> - Distributed under MIT - Keep this message! */

/*
== Loren Johnson, Venado Partners, LLC Modifications -- 2/27/08 ==
bug fix: moved class variables into initialize so they happen per instance. This allows multiple controls per page
bug fix: added id_base attribute so that multiple controls on the same page have unique list item ids (won't work otherwise)
feature: Added newValues option and logic to allow new values to be created when ended with a comma (tag selector use case)
mod: removed ajax fetch file happening on every search and moved it to initialization to laod all results immediately and not keep polling
mod: added "fetchMethod" option so I could better accomodate my RESTful ways and set a "get" for retrieving
mod: added this.update to the add and dispose methods to keep the target input box values always up to date
mod: moved ResizableTextBox, TextBoxList and FaceBookList all into same file
mod: added extra line breaks and fixed-up some indentation for readability
mod: spaceReplace option added to allow handling of new tag values when the tagging scheme doesn't allow spaces,
     this is set as blank by default and will have no impact
*/
/*
== Zuriel Barron, severelimitation.com -- 3/1/08 ==
bug fix: fixed bug where it was not loading initial list values
bug fix: new values are not added into the autocomplete list upon removal
bug fix: improved browser compatibility (Safari, IE)
*/
/*
== Aleksandar Ruzicic, krcko.net -- 19/6/09 ==
mod: renamed FaceBookList class to AutocompleteTextboxList
mod: attribute name used to read value of existing items moved to options hash
mod: replaced Element.addMethods with custom object (txblist_helper) to prevent conflicts with older versions of calendar_date_select script
mod: moved cache object out of global scope (it's now stored in txblist_helper object)
feature: added support for "categorized" items
feature: added textboxlistItems object (to control multiple instances)
*/

// Added key contstant for COMMA watching happiness
Object.extend(Event, { KEY_COMMA: 188, KEY_SPACE: 32 });

var txblist_helper = {

  cache: {},

  cacheData: function(element, key, value) {
	var id = (element = $(element)).identify();
	if ( !(id in this.cache) || !Object.isHash(this.cache[id]) ) {
	  this.cache[id] = $H();
	}
	this.cache[id].set(key, value);
	return element;
  },

  retrieveData: function(element, key) {
	return this.cache[$(element).identify()].get(key);
  },

  getCaretPosition: function(element) {
    if (element.createTextRange) {
      var r = document.selection.createRange().duplicate();
        r.moveEnd('character', element.value.length);
        if (r.text === '') return element.value.length;
        return element.value.lastIndexOf(r.text);
    } else return element.selectionStart;
  },

  onInputBlur: function(el,obj) {
      obj.lastinput = el;
      if(!obj.curOn) {
          obj.blurhide = obj.autoHide.bind(obj).delay(0.1);
      }
  },

  onInputFocus: function(el,obj) { obj.autoShow(); },

  onBoxDispose: function(item,obj) {
    // Set to not to "add back" values in the drop-down upon delete if they were new values
	item = txblist_helper.retrieveData(item, 'text');
	if(!item.newValue) {
    	obj.autoFeed(item);
	}
	obj.selected_data = obj.selected_data.select(function(data){ return data.value != item.value; });
  },

  re_quote: function(str) {
	return (str + '').replace(/([\\\.\+\*\?\[\^\]\$\(\)\{\}\=\!<>\|\:])/g, "\\$1");
  }
};

var textboxlistItems = [];

Event.observe(window, 'load', function() {

	var items = $A(textboxlistItems);

	textboxlistItems = {

		items: {},

		push: function(item, el) {
			this.setup($(el || item.input_id), item.feedurl, item.category_id, item.default_category);
		},

		setup: function(input, feedurl, category_id, default_category) {

			var div = input.up('.autocomplete-input'), id;

			return this.items[id = input.identify()] = new AutocompleteTextboxList(
				div.down('.input-text'),
				div.down('.autolist'), {
					valueAttribute: 'rel',
					results: 20,
					fetchFile: feedurl,
					newValues: !!default_category,
					newValueDelimiters: ['[', ']'],
					defaultCategory: default_category || 'Keywords',
					categoryRestriction: category_id << 0,
					onchange: function(auto) {
						input.writeAttribute('value', auto.bits.values().toString());
					}
				}
			)
			.setId(id);
		},

		get: function(id) {
			return this.items[id];
		},

		dispose: function(what) {

			if (what.element) {
				what = what.id;
			}

			delete this.items[what];
		}
	};

	items.each(textboxlistItems.push, textboxlistItems);
});

var ResizableTextbox = Class.create({

  initialize: function(element, options) {
    var that = this;
    this.options = $H({
      min: 5,
      max: 500,
      step: 7
    });
    this.options.update(options);
    this.el = $(element);
    this.width = this.el.offsetWidth;
    this.el.observe(
      'keyup', function() {
        var newsize = that.options.get('step') * $F(this).length;
        if(newsize <= that.options.get('min')) newsize = that.width;
        if(! ($F(this).length == txblist_helper.retrieveData(this, 'rt-value') || newsize <= that.options.min || newsize >= that.options.max))
          this.setStyle({'width': newsize});
      }).observe('keydown', function() {
        txblist_helper.cacheData(this, 'rt-value', $F(this).length);
      });
  }
});

var TextboxList = Class.create({

  initialize: function(element, options) {
    this.options = $H({/*
      onFocus: $empty,
      onBlur: $empty,
      onInputFocus: $empty,
      onInputBlur: $empty,
      onBoxFocus: $empty,
      onBoxBlur: $empty,
      onBoxDispose: $empty,*/
      resizable: {},
      className: 'bit',
      separator: ',',
      extrainputs: true,
      startinput: true,
      hideempty: true,
      newValues: false,
	  newValueDelimiters: ['[',']'],
      spaceReplace: '',
      fetchFile: undefined,
      fetchMethod: 'get',
      results: 10,
      wordMatch: false,
	  valueAttribute: 'value',
	  categoryAttribute: 'category',
	  defaultCategory: '',
	  onchange: Prototype.emptyFunction,
	  categoryRestriction: false
    });
    this.options.update(options);
    this.element = $(element).hide();
    this.bits = new Hash();
    this.events = new Hash();
    this.count = 0;
    this.current = false;
    this.maininput = this.createInput({'class': 'maininput'});
    this.holder = new Element('ul', {
      'class': 'holder'
    }).insert(this.maininput);
    this.element.insert({'before':this.holder});
    this.holder.observe('click', function(event){
      event.stop();
      if(this.maininput != this.current) this.focus(this.maininput);
    }.bind(this));
    this.makeResizable(this.maininput);
    this.setEvents();
  },

  setEvents: function() {
    document.observe(Prototype.Browser.IE ? 'keydown' : 'keypress', function(e) {
      if (!this.current) return;
      if (txblist_helper.retrieveData(this.current, 'type') == 'box' && e.keyCode == Event.KEY_BACKSPACE) {
		e.stop();
		return false;
	  }
    }.bind(this));
    document.observe(
      'keyup', function(e) {
        e.stop();
		e.preventDefault && e.preventDefault();
        if(!this.current) return null;
        switch(e.keyCode){
          case Event.KEY_LEFT: return this.move('left');
          case Event.KEY_RIGHT: return this.move('right');
          case Event.KEY_DELETE:
          case Event.KEY_BACKSPACE: return this.moveDispose() && false;
        }
		return true;
      }.bind(this)).observe(
      'click', function() { document.fire('blur'); }.bindAsEventListener(this)
    );
  },

  update: function() {
    this.element.value = this.bits.values().join(this.options.get('separator'));
	this.options.get('onchange')(this);
    return this;
  },

  add: function(text, html) {
    var id = this.id_base + '-' + this.count++;
    var el = this.createBox($pick(html, text), {'id': id, 'class': this.options.get('className'), 'newValue' : text.newValue || false});
    (this.current || this.maininput).insert({'before':el});
    el.observe('click', function(e) {
      e.stop();
      this.focus(el);
    }.bind(this));
    this.bits.set(id, text.value);
    // Dynamic updating... why not?
    this.update();
    if(this.options.get('extrainputs') && (this.options.get('startinput') || el.previous())) this.addSmallInput(el,'before');
    return el;
  },

  addSmallInput: function(el, where) {
    var input = this.createInput({'class': 'smallinput'});
    el.insert({}[where] = input);
    txblist_helper.cacheData(input, 'small', true);
    this.makeResizable(input);
    if(this.options.get('hideempty')) input.hide();
    return input;
  },

  dispose: function(el) {
    this.bits.unset(el.id);
    // Dynamic updating... why not?
    this.update();
    if(el.previous() && txblist_helper.retrieveData(el.previous(), 'small')) el.previous().remove();
    if(this.current == el) this.focus(el.next());
    if(txblist_helper.retrieveData(el, 'type') == 'box') txblist_helper.onBoxDispose(el, this);
    el.remove();
    return this;
  },

  focus: function(el, nofocus) {
    if(! this.current) el.fire('focus');
    else if(this.current == el) return this;
    this.blur();
    el.addClassName(this.options.get('className') + '-' + txblist_helper.retrieveData(el, 'type') + '-focus');
    if(txblist_helper.retrieveData(el, 'small')) el.setStyle({'display': 'block'});
    if(txblist_helper.retrieveData(el, 'type') == 'input') {
      txblist_helper.onInputFocus(el, this);
      if(! nofocus) this.callEvent(txblist_helper.retrieveData(el, 'input'), 'focus');
    }
    else el.fire('onBoxFocus');
    this.current = el;
    return this;
  },

  blur: function(noblur) {
    if(! this.current) return this;
    if(txblist_helper.retrieveData(this.current, 'type') == 'input') {
      var input = txblist_helper.retrieveData(this.current, 'input');
      if(! noblur) this.callEvent(input, 'blur');
      txblist_helper.onInputBlur(input, this);
    }
    else this.current.fire('onBoxBlur');
    if(txblist_helper.retrieveData(this.current, 'small') && ! input.get('value') && this.options.get('hideempty'))
      this.current.hide();
    this.current.removeClassName(this.options.get('className') + '-' + txblist_helper.retrieveData(this.current, 'type') + '-focus');
    this.current = false;
    return this;
  },

  createBox: function(text, options) {
	return txblist_helper.cacheData(new Element('li', options).addClassName(this.options.get('className') + '-box').update((this.options.get('categoryRestriction') == 0 ? ('<span class="category">' + text.category + '</span> ') : '') + text.caption), 'type', 'box');
  },

  createInput: function(options) {
    var li = new Element('li', {'class': this.options.get('className') + '-input'});
    var el = new Element('input', Object.extend(options,{'type': 'text'}));
    el.observe('click', function(e) { e.stop(); }).observe('focus', function(e) { if(! this.isSelfEvent('focus')) this.focus(li, true); }.bind(this)).observe('blur', function() { if(! this.isSelfEvent('blur')) this.blur(true); }.bind(this)).observe('keydown', function(e) { txblist_helper.cacheData(this, 'lastvalue', this.value); txblist_helper.cacheData(this, 'lastcaret', txblist_helper.getCaretPosition(this)); });
    var tmp = txblist_helper.cacheData(txblist_helper.cacheData(li, 'input', el), 'type', 'input').insert(el);
    return tmp;
  },

  callEvent: function(el, type) {
    this.events.set(type, el);
    el[type]();
  },

  isSelfEvent: function(type) {
    return (this.events.get(type)) ? !! this.events.unset(type) : false;
  },

  makeResizable: function(li) {
    var el = txblist_helper.retrieveData(li, 'input');
    txblist_helper.cacheData(el, 'resizable', new ResizableTextbox(el, Object.extend(this.options.get('resizable'),{min: el.offsetWidth, max: (this.element.getWidth()?this.element.getWidth():0)})));
    return this;
  },

  checkInput: function() {
    var input = txblist_helper.retrieveData(this.current, 'input');
    return (!txblist_helper.retrieveData(input, 'lastvalue') || (txblist_helper.getCaretPosition(input) === 0 && txblist_helper.retrieveData(input, 'lastcaret') === 0));
  },

  move: function(direction) {
    var el = this.current[(direction == 'left' ? 'previous' : 'next')]();
    if(el && (!txblist_helper.retrieveData(this.current, 'input') || ((this.checkInput() || direction == 'right')))) this.focus(el);
    return this;
  },

  moveDispose: function() {
    if(txblist_helper.retrieveData(this.current, 'type') == 'box') return this.dispose(this.current);
    if(this.checkInput() && this.bits.keys().length && this.current.previous()) return this.focus(this.current.previous());
  }

});

function $pick(){for(var B=0,A=arguments.length;B<A;B++){if(!Object.isUndefined(arguments[B])){return arguments[B];}}return null;}

/*
  Proto!MultiSelect 0.2
  - Prototype version required: 6.0

  Credits:
  - Idea: Facebook
  - Guillermo Rauch: Original MooTools script
  - Ran Grushkowsky/InteRiders Inc. : Porting into Prototype and further development

  Changelog:
  - 0.1: translation of MooTools script
  - 0.2: renamed from Proto!TextboxList to Proto!MultiSelect, added new features/bug fixes
        added feature: support to fetch list on-the-fly using AJAX    Credit: Cheeseroll
        added feature: support for value/caption
        added feature: maximum results to display, when greater displays a scrollbar   Credit: Marcel
        added feature: filter by the beginning of word only or everywhere in the word   Credit: Kiliman
        added feature: shows hand cursor when going over options
        bug fix: the click event stopped working
        bug fix: the cursor does not 'travel' when going up/down the list   Credit: Marcel
*/

/* Copyright: InteRiders <http://interiders.com/> - Distributed under MIT - Keep this message! */

var AutocompleteTextboxList = Class.create(TextboxList, {

  initialize: function($super,element, autoholder, options, func) {
    $super(element, options);
    this.loptions = $H({
      autocomplete: {
        'opacity': 1,
        'maxresults': 10,
        'minchars': 1
      }
    });

    this.id_base = $(element).identify() + "_" + this.options.get("className");

	this.loader_visibility = 0;
	this.loader = autoholder.up('.autocomplete-input').select('.loader')[0];
    this.data = $A();
    this.autoholder = $(autoholder).setOpacity(this.loptions.get('autocomplete').opacity);
    this.autoholder.observe('mouseover',function() {this.curOn = true;}.bind(this)).observe('mouseout',function() {this.curOn = false;}.bind(this));
    this.autoresults = this.autoholder.select('ul').first();
	this.selected_data = $A();
	var children = this.autoresults.select('li');
    children.each(function(el) {
		var result;
		this.add(result = {value:el.readAttribute(this.options.get('valueAttribute')),caption:el.innerHTML,category:el.readAttribute(this.options.get('categoryAttribute'))});
		this.selected_data.push(result);
	}, this);

  },

  autoShow: function(search) {

	if (search && this.options.get('newValues') && search.length > 1) {
		this.autoFeed({
			caption: search,
			category: this.options.get('defaultCategory'),
			value: this.options.get('newValueDelimiters')[0] + search + this.options.get('newValueDelimiters')[1],
			newValue: true,
			add: true
		});
	}

	if (search && search.length >= 2 && this.options.get('fetchFile')) {

	  if (!('qcache' in this)) {
		this.qcache = {};
	  }

	  var first_time = (this.qhist || (this.qhist = '|')).indexOf('|' + search.substr(0, 2)) < 0;

	  if (first_time || ((search in this.qcache) && !this.qcache[search].running)) {
		var q = search, search_params = {q: q};
		if (first_time) {
		  this.qhist += search + '|';
		  search_params.offset = 0;
		} else {
		  this.qcache[q].running = true;
		  search_params.offset = this.qcache[q].offset;
		}
		if (this.options.get('categoryRestriction') > 0) {
			search_params.restrict = this.options.get('categoryRestriction');
		}
		this.loader_visibility++;
		this.loader.show();

		new Ajax.Request(this.options.get('fetchFile'), {
		  method: 'get',
		  parameters: search_params,
		  onSuccess: function(transport) {
			var response = transport.responseText.evalJSON();
			response.items.each(function(item) {
			  this.autoFeed(item);	// item: {value: '', caption: '', category: ''}
			  this.autoShow(q);
			}.bind(this));
			if (response.total > response.items.length) {
			  if (!(q in this.qcache)) {
				this.qcache[q] = {
				  offset: response.items.length,
				  running: false
				};
			  } else {
				this.qcache[q].offset += response.items.length;
			  }
			} else {
			  delete this.qcache[q];
			}
		  }.bind(this),
		  onComplete: function() {
			if (!--this.loader_visibility) {
			  this.loader.hide();
			}
			if (q in this.qcache) {
			  this.qcache[q].running = false;
			}
		  }.bind(this)
		});
	  }
	}

    this.autoholder.setStyle({'display': 'block'});
    this.autoholder.descendants().each(function(e) { e.hide() });
    if(! search || ! search.strip() || (! search.length || search.length < this.loptions.get('autocomplete').minchars))
    {
      this.autoholder.select('.default').first().setStyle({'display': 'block'});
      this.resultsshown = false;
    } else {
      this.resultsshown = true;
      this.autoresults.setStyle({'display': 'block'}).update('');
      if (this.options.get('wordMatch'))
        var regexp = new RegExp("(^|\\s)"+ txblist_helper.re_quote(search),'i')
      else
        var regexp = new RegExp(txblist_helper.re_quote(search),'i')
      var count = 0;
      this.data.filter(
        function(str) { return str ? regexp.test(str.caption) : false; }).each(
            function(result, ti) {
              count++;
              //if(ti >= this.loptions.get('autocomplete').maxresults) return;
              var that = this;
              var el = new Element('li');
              el.observe('click',function(e) {
                e.stop();
                that.autoAdd(this);
            }
          ).observe('mouseover', function() { that.autoFocus(this); } ).update(
			(result.add ? '<span style="font-weight:normal;">Add</span> ' : '') +
			(this.options.get('categoryRestriction') > 0 ? '' :
			('<span class="category">' + result.category + '</span> ')) +
            this.autoHighlight(result.caption, search)
          );
          this.autoresults.insert(el);
          txblist_helper.cacheData(el, 'result', result);
          if(ti == 0) this.autoFocus(el);
        },
        this
      );
    }
    if (count > this.options.get('results'))
      this.autoresults.setStyle({'height': (this.options.get('results')*24)+'px'});
    else
      this.autoresults.setStyle({'height': (count*24)+'px'});
    return this;
  },

  autoHighlight: function(html, highlight) {
    return html.gsub(new RegExp(highlight,'i'), function(match) {
      return '<em>' + match[0] + '</em>';
    });
  },

  autoHide: function() {
    this.resultsshown = false;
    this.autoholder.hide();
    return this;
  },

  autoFocus: function(el) {
    if(! el) return;
    if(this.autocurrent) this.autocurrent.removeClassName('auto-focus');
    this.autocurrent = el.addClassName('auto-focus');
	try{this.autocurrent.scrollIntoView();}catch(e){}
    return this;
  },

  autoMove: function(direction) {
    if(!this.resultsshown) return;
    this.autoFocus(this.autocurrent[(direction == 'up' ? 'previous' : 'next')]());
    this.autoresults.scrollTop = this.autocurrent.positionedOffset()[1]-this.autocurrent.getHeight();
    return this;
  },

  autoFeed: function(text) {
	if (text.add) {
		(this.data || $A()).splice(0, (this.data.length && this.data[0].add) << 0, text);
	} else {
		var check = function(item){ return item.value == text.value; };
		if (!this.data.find(check) && !this.selected_data.find(check)) {
			this.data.push(text);
		}
	}
    return this;
  },

  getItemIndex: function(needle) {
	var index = -1;
	this.data.each(function(item, i){
	  if (item.value == needle.value) {
		index = i;
		throw $break;
	  }
	});
	return index;
  },

  autoAdd: function(el) {

	var result;
    if(this.newvalue && this.options.get('newValues') && el.value.length > 1) {
      this.add(result = {
			caption: el.value,
			category: this.options.get('defaultCategory'),
			value: this.options.get('newValueDelimiters')[0] + el.value + this.options.get('newValueDelimiters')[1],
			newValue: true
	  });
      var input = el;
	  this.selected_data.push(result);
    } else if(!el || !(result = txblist_helper.retrieveData(el, 'result'))) {
      return;
    } else {
      this.add(result);
	  this.selected_data.push(result);
	  this.data = this.data.select(function(data){ return data.value != result.value; });
      var input = this.lastinput || txblist_helper.retrieveData(this.current, 'input');
    }
    this.autoHide();
    input.clear().focus();
	this.autoShow();
    return this;
  },

  createInput: function($super,options) {
    var li = $super(options);
    var input = txblist_helper.retrieveData(li, 'input');
    input.observe('keydown', function(e) {
        this.dosearch = false;
        this.newvalue = false;

        switch(e.keyCode) {
          case Event.KEY_UP: e.stop(); return this.autoMove('up');
          case Event.KEY_DOWN: e.stop(); return this.autoMove('down');
//		  case Event.KEY_SPACE:
          case Event.KEY_COMMA:
            if(this.options.get('newValues')) {
              new_value_el = txblist_helper.retrieveData(this.current, 'input');
			  if (!new_value_el.value.endsWith('<')) {
				keep_input = "";
				new_value_el.value = new_value_el.value.strip().gsub(",","").escapeHTML().strip();
                if(new_value_el.value.length > 1) {
					e.stop();
					this.newvalue = true;
					this.current_input = keep_input.escapeHTML().strip();
					this.autoAdd(new_value_el);
					input.value = keep_input;
					this.update();
				  }
				}
            }
            break;
          case Event.KEY_RETURN:
            e.stop();
            if(! this.autocurrent) break;
            this.autoAdd(this.autocurrent);
            this.autocurrent = false;
            this.autoenter = true;
            break;
          case Event.KEY_ESC:
            this.autoHide();
            if(this.current && txblist_helper.retrieveData(this.current, 'input'))
              txblist_helper.retrieveData(this.current, 'input').clear();
            break;
          default: this.dosearch = true;
        }
    }.bind(this));
    input.observe('keyup',function(e) {

        switch(e.keyCode) {
          case Event.KEY_UP:
          case Event.KEY_DOWN:
          case Event.KEY_RETURN:
          case Event.KEY_ESC:
            break;
          default:
            if(this.dosearch) this.autoShow(input.value);
        }
    }.bind(this));
    input.observe(Prototype.Browser.IE ? 'keydown' : 'keypress', function(e) {
      if(this.autoenter) e.stop();
      this.autoenter = false;
    }.bind(this));
    return li;
  },

  createBox: function($super,text, options) {
    var li = $super(text, options);
    li.observe('mouseover',function() {
        this.addClassName('bit-hover');
    }).observe('mouseout',function() {
        this.removeClassName('bit-hover')
    });
    var a = new Element('a', {
      'href': '#remove',
      'class': 'closebutton'
      }
    );
    a.observe('click',function(e) {
      e.stop();
      if(! this.current) this.focus(this.maininput);
      this.dispose(li);
    }.bind(this));
    txblist_helper.cacheData(li.insert(a), 'text', text);
    return li;
  },

  setId: function(id){
	this.id = id;
	return this;
  }
});
