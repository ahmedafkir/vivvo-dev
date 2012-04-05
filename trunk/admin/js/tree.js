vivvo.controls.tree = Class.create ();

Object.extend (vivvo.controls.tree.prototype,
	{
		initialize : function (el, dragEnable){
			this.rootElement = $(el);
			this.dragEnable = dragEnable;
			this.initSubTree(this.rootElement, 1);	
			Draggables.addObserver(this);
		},

		initSubTree: function (root, depth){
			if (depth != undefined){
				depth <<= 1;
				if (root != this.rootElement){
					if (!root.up().next()){
						depth += 1;
					}
				}
				root.depth = depth;
			}else{
				depth = root.depth;
			}
			
			var _ = this;
			root.immediateDescendants().each(
				function (li){
					var div = li.down('div');
					if (div){
						var drag = div.down('.draggable');
						var indent = div.down('.tree_indent');
					}
					
					var ul = li.down('ul');
					
					if (_.dragEnable){
						new Draggable(drag,{ghosting:true, revert:true});
	
						Droppables.add(div, 
							{
								accept: 'draggable',
								overlap: '2',
								hoverclass: 'droppable_hover',
								onHover: _.onHover.bind(_)
							}
						);
					}
					
					if (indent) {
						var new_depth = depth;
						var tree_position_img;
						indent.update();
						while (new_depth > 2){
							tree_position_img = document.createElement('img');
							if (new_depth % 2 == 1){
								tree_position_img.src = 'img/tree_empty.gif';
								indent.insert({top:tree_position_img});
							}else{
								tree_position_img.src = 'img/tree_line.gif';
								indent.insert({top:tree_position_img});
							}
							new_depth >>= 1;
						}
						tree_position_img = document.createElement('img');
						
						if (li.next()){
							tree_position_img.src = 'img/tree_middle.gif';
						}else{
							tree_position_img.src = 'img/tree_end.gif';
						}
						indent.insert({bottom:tree_position_img});
					}
					
					if (ul)	_.initSubTree(ul, depth);
				}
			);
		},
		
		onStart : function (eventName, draggable, event){
			element = draggable.element;
			element.addClassName('draggable_dragging');
			element.setStyle ({width:'auto', height:'auto'});
		},
		
		onEnd : function (eventName, draggable, event){
			element = draggable.element;
			element.removeClassName('draggable_dragging');
			element.removeClassName('draggable_before');
			element.removeClassName('draggable_append');
			element.removeClassName('draggable_after');
			element.removeClassName('draggable_forbidden');
			element.removeClassName('draggable_between');	
			
			this.overElement.removeClassName('droppable_before');
			this.overElement.removeClassName('droppable_append');
			this.overElement.removeClassName('droppable_after');					
			
			if (this.overElement && this.overElement.hasClassName('droppable_hover')){
				
				Position.relativize(element);
				element.setStyle({top:0, left:0});
	
				if (this.action != 'forbidden'){
					var new_element = element.up('li');				
					new_element = new_element.remove();
					
					if (this.action == 'before'){
						var new_parent = this.overElement.up('ul');
						new_parent.insertBefore(new_element, this.overElement.up('li'));
					}else if (this.action == 'after'){
						var new_parent = this.overElement.up('ul');
						var after_element =  this.overElement.up('li').next();
						if (after_element){
							new_parent.insertBefore(new_element, after_element);
						}else{
							new_parent.appendChild(new_element);
						}
					}else if (this.action == 'append'){
						var new_parent = this.overElement.next('ul');
						new_parent.appendChild(new_element);
					}
					this.initSubTree(this.rootElement, 1);
				}
				this.saveTree();
			}
			this.action = null;
			this.overElement = null;
		},
		saveTree: function (){
			var params = {};
			params.action = 'category';
			params.cmd = 'reorder';
			params.ajax_dump = 1;
			params.category_list = this.serializeObject();
			vivvo.admin.utils.ajaxAction(params);
		},
		onHover: function (element, droppableElement) {
			if (this.overElement){
				this.overElement.removeClassName('droppable_before');
				this.overElement.removeClassName('droppable_append');
				this.overElement.removeClassName('droppable_after');
			}
			
			this.overElement = droppableElement;
			
			var elo = Position.cumulativeOffset(element);
			var delo = Position.cumulativeOffset(droppableElement);
			
			var dy = elo[1] - delo[1];
			
			var className = '';
			
			if (dy < (-droppableElement.getHeight()/6)){
				if (this.overElement.up().previous()){
					className = 'between';
				}else{
					className = 'before';
				}
				this.action = 'before';
			}else if (dy < droppableElement.getHeight()/6){
				className = 'append';
				this.action = 'append';
			}else{
				if (this.overElement.up().next()){
					className = 'between';
				}else{
					className = 'after';
				}
				this.action = 'after';
			}
			
			element.removeClassName('draggable_before');
			element.removeClassName('draggable_append');
			element.removeClassName('draggable_after');
			element.removeClassName('draggable_forbidden');
			element.removeClassName('draggable_between');
			
			if (Element.descendantOf(this.overElement, element.up().up())){
				element.addClassName('draggable_forbidden');
				this.action = 'forbidden';
			}else{
				element.addClassName('draggable_' + className);
				this.overElement.addClassName('droppable_' + this.action);
			}
		},
		serializeJSON: function (root, parent_id){
			var _ = this;
			if (!root){
				root = this.rootElement;
				parent_id = 0;
			}
			var serialized_output = [];
			root.immediateDescendants().each(
				function (li){
					var div = li.down('div');
					var ul = li.down('ul');
					if (ul) {
						serialized_output.push (_.serializeJSON(ul, div.id.split('_').last()));
					}
				}
			);
			return {parent_id : serialized_output};
		},
		serializeObject: function (root){
            root = root || this.rootElement;
            
			var stringOutput = '{';
			root.immediateDescendants().each(
				function (li, index){
					var div = li.down('div');
					var ul = li.down('ul');
					stringOutput += '"' + div.id.split('_').last() + '":';
					if (ul) {
						stringOutput += this.serializeObject(ul);
					} else {
                        stringOutput += '{}';
					}
					stringOutput += ',';
				}.bind(this)
			);
			stringOutput = stringOutput.replace(/,$/, '');
			stringOutput += '}';
			return stringOutput;
		},
		serialize: function (root, prefix){
			var _ = this;
			if (!root){
				root = this.rootElement;
				prefix = 'category_list';
			}
			var serialized_output = '';
			root.immediateDescendants().each(
				function (li, index){
					var div = li.down('div');
					var ul = li.down('ul');
					serialized_output += '&' + prefix + '[' +	div.id.split('_').last() + "]=";
					if (ul) {
						serialized_output += _.serialize(ul, prefix + '[' +	div.id.split('_').last() + ']');
					}
					
				}
			);
			return serialized_output;
		}		
	}
);

