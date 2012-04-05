<?php
/* =============================================================================
 * $Revision: 5385 $
 * $Date: 2010-05-25 11:51:09 +0200 (Tue, 25 May 2010) $
 *
 * Vivvo CMS v4.5.2r (build 6084)
 *
 * Copyright (c) 2010, Spoonlabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * =============================================================================
 */

	/**
	 * XML Node types
	 */
	define('VIVVO_XML_TEXT_NODE',		'#TXT');
	define('VIVVO_XML_COMMENT_NODE',	'#REM');
	define('VIVVO_XML_DOCUMENT_NODE',	'#DOC');
	define('VIVVO_XML_ELEMENT_NODE',	'#ELM');


	/**
	 * VivvoXMLNode class
	 *
	 * @copyright	Spoonlabs
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class VivvoXMLNode {

		/**
		 * @var	string	Node name (type)
		 */
		public $name;

		/**
		 * @var	array	Node attributes
		 */
		public $attributes;

		/**
		 * @var string	Node contents (in case of text and comment nodes)
		 */
		public $text;

		/**
		 * @var	array	Node children
		 */
		public $children = array();

		/**
		 * @var	&VivvoXMLNode	Reference to parent node
		 */
		public $parentNode = null;

		/**
		 * @var	string	Document type (<!DOCTYPE or <?xml declaration), only available on documentNode
		 */
		protected $documentType;

		/**
		 * @var	int		VivvoXMLNode instance
		 */
		protected $instanceId;


		/**
		 * Creates new VivvoXMLNode instance
		 *
		 * @param	string	$name		Node name (type)
		 * @param	array	$attributes	Node attributes
		 * @param	string	$text		Node contents (in case of text and comment nodes)
		 * @return	VivvoXMLNode
		 */
		public function __construct($name, array $attributes = array(), $text = null) {
			$this->name = $name;
			$this->attributes = $attributes;
			$this->text = $text;
			$this->parentNode = null;
			$this->instanceId = ++self::$counter;
		}

		/**
		 * Returns new VivvoXMLNode instance
		 *
		 * @param	string	$name		Node name (type)
		 * @param	array	$attributes	Node attributes
		 * @param	string	$text		Node contents (in case of text and comment nodes)
		 * @return	VivvoXMLNode
		 */
		public static function factory($name, array $attributes = array(), $text = null) {
			return new VivvoXMLNode($name, $attributes, $text);
		}

		/**
		 * Appends child node
		 *
		 * @param	&VivvoXMLNode	$child	Node to add to children collection
		 * @return	VivvoXMLNode
		 */
		public function appendChild(&$child) {
			if ($child->parentNode != $this) {
				$this->children[] =& $child;
				$child->remove()->parentNode =& $this;
			}
			return $this;
		}

		/**
		 * Appends node to parent node
		 *
		 * @param	VivvoXMLNode	$parent	Parent node
		 * @return	VivvoXMLNode
		 */
		public function appendTo($parent) {
			$parent->appendChild($this);
			return $this;
		}

		/**
		 * Prepends child node
		 *
		 * @param	&VivvoXMLNode	$child	Node to add to children collection
		 * @return	VivvoXMLNode
		 */
		public function prependChild(&$child) {
			if ($child->parentNode != $this) {
				array_splice($this->children, 0, 0, array(&$child));
				$child->remove()->parentNode =& $this;
			}
			return $this;
		}

		/**
		 * Prepends node to parent node
		 *
		 * @param	VivvoXMLNode	$parent	Parent node
		 * @return	VivvoXMLNode
		 */
		public function prependTo($parent) {
			$parent->prependChild($this);
			return $this;
		}

		/**
		 * Inserts child node before existing one
		 *
		 * @param	&VivvoXMLNode	$child	Node to add to children collection
		 * @param	VivvoXMLNode	$node	Existing node to use as reference
		 * @return	VivvoXMLNode
		 */
		public function insertBefore(&$child, $node) {
			if ($node->parentNode == $this) {
				$this->insertAt($child, $node->getIndex());
			}
			return $this;
		}

		/**
		 * Inserts child node after existing one
		 *
		 * @param	&VivvoXMLNode	$child	Node to add to children collection
		 * @param	VivvoXMLNode	$node	Existing node to use as reference
		 * @return	VivvoXMLNode
		 */
		public function insertAfter(&$child, $node) {
			if ($node->parentNode == $this) {
				$this->insertAt($child, $node->getIndex() + 1);
			}
			return $this;
		}

		/**
		 * Inserts child node before specified index
		 *
		 * @param	&VivvoXMLNode	$child	Node to add to children collection
		 * @param	int				$index	Index in children collection to insert node after
		 * @return	VivvoXMLNode
		 */
		public function insertAt(&$child, $index) {
			if ($child->parentNode != $this) {
				array_splice($this->children, $index, 0, array($child));
				$child->remove()->parentNode =& $this;
			}
			return $this;
		}

		/**
		 * Returns node's index in it's parents's children collection
		 *
		 * @return	mixed	bool false if node has no parent or int >= 0
		 */
		public function getIndex() {
			if ($this->parentNode && $this->parentNode->name != VIVVO_XML_DOCUMENT_NODE) {
				$index = array_search($this, $this->parentNode->children);
				if ($index !== false) {
					return (int)$index;
				}
			}
			return false;
		}

		/**
		 * Removes node from children collection
		 *
		 * @param	VivvoXMLNode	$child	Node to remove from collection
		 * @return	VivvoXMLNode
		 */
		public function removeChild($child) {
			if ($child->parentNode == $this) {
				array_splice($this->children, $child->getIndex(), 1);
				unset($child->parentNode);
			}
			return $this;
		}

		/**
		 * Returns first element which matches specified name
		 *
		 * @param	string	$name
		 * @param	int		$depth	How deep to traverse the tree (0 traverses only immediate children, -1 traverses trough all children)
		 * @return	mixed	VivvoXMLNode or null
		 */
		public function getElementByNodeName($name, $depth = 0) {

			$node = null;

			if ($name == VIVVO_XML_ELEMENT_NODE) {

				foreach ($this->children as &$child) {

					if ($child->name != VIVVO_XML_TEXT_NODE and
						$child->name != VIVVO_XML_COMMENT_NODE and
						$child->name != VIVVO_XML_DOCUMENT_NODE) {
						$node = $child;
						break;
					}

					if ($depth != 0 and ($node = $child->getElementByNodeName($name, $depth - 1)) !== null) {
						break;
					}
				}
				unset($child);
			} else {

				foreach ($this->children as &$child) {

					if ($child->name == $name) {
						$node = $child;
						break;
					}

					if ($depth != 0 and ($node = $child->getElementByNodeName($name, $depth - 1)) !== null) {
						break;
					}
				}
				unset($child);
			}

			return $node;
		}

		/**
		 * Returns all elements that match specified name
		 *
		 * @param	string	$name
		 * @param	int		$depth	How deep to traverse the tree (0 traverses only immediate children, -1 traverses trough all children)
		 * @return	array
		 */
		public function getElementsByNodeName($name, $depth = 0) {

			$nodes = array();

			if ($name == VIVVO_XML_ELEMENT_NODE) {

				foreach ($this->children as &$child) {

					if ($child->name != VIVVO_XML_TEXT_NODE and
						$child->name != VIVVO_XML_COMMENT_NODE and
						$child->name != VIVVO_XML_DOCUMENT_NODE) {
						$nodes[] = $child;
					}

					if ($depth != 0) {
						$nodes = array_merge($nodes, $child->getElementByNodeName($name, $depth - 1));
					}
				}
				unset($child);
			} else {

				foreach ($this->children as &$child) {

					if ($child->name == $name) {
						$nodes[] = $child;
					}

					if ($depth != 0) {
						$nodes = array_merge($nodes, $child->getElementByNodeName($name, $depth - 1));
					}
				}
				unset($child);
			}

			return $nodes;
		}

		/**
		 * Returns concatenated values of all textual subnodes
		 *
		 * @param	bool	$stripWhite
		 * @return	string
		 */
		public function getTextContents($stripWhite = false) {

			$contents = '';

			if ($this->name == VIVVO_XML_TEXT_NODE) {

				$contents = $stripWhite ? trim(preg_replace('/\s+/', ' ', $this->text)) : $this->text;

			} elseif ($this->name != VIVVO_XML_DOCUMENT_NODE and $this->name != VIVVO_XML_COMMENT_NODE) {

				foreach ($this->children as $child) {
					$contents .= $child->getTextContents($stripWhite);
				}
			}

			return $contents;
		}

		/**
		 * Returns parent element (immediate, or first parent node for which $filter returns true)
		 *
		 * @param	callback	$filter
		 * @return	mixed		VivvoXMLNode or null
		 */
		public function getParentNode($filter = false) {

			if (!is_callable($filter)) {
				return $this->parentNode;
			}

			$parent = $this->parentNode;

			while ($parent) {

				if (call_user_func_array($filter, array(&$parent))) {
					return $parent;
				}

				$parent = $parent->parentNode;
			}

			return null;
		}

		/**
		 * Removes node from it's parent's children collection
		 *
		 * @return	VivvoXMLNode
		 */
		public function remove() {
			if ($this->parentNode) {
				$this->parentNode->removeChild($this);
			}
			return $this;
		}

		/**
		 * Getter method for rootNode and doctype on VIVVO_XML_DOCUMENT_NODE
		 *
		 * @param	string		$name
		 * @return	mixed		VivvoXMLNode, string or null
		 */
		public function __get($name) {
			if ($this->name == VIVVO_XML_DOCUMENT_NODE) {
				if ($name == 'rootNode') {
					return $this->getElementByNodeName(VIVVO_XML_ELEMENT_NODE);
				} elseif ($name == 'doctype') {
					return $this->documentType;
				}
			}
			return null;
		}

		/**
		 * Setter method for doctype on VIVVO_XML_DOCUMENT_NODE
		 *
		 * @param	string	$name
		 * @param	mixed	$value
		 * @return	mixed
		 */
		public function __set($name, $value) {
			if ($this->name == VIVVO_XML_DOCUMENT_NODE and $name == 'doctype') {
				$this->documentType = $value;
			}
		}

		/**
		 * Returns node's instance id
		 *
		 * @return	int
		 */
		public function getInstanceId() {
			return $this->instanceId;
		}

		/**
		 * @var	int		Instance counter
		 */
		public static $counter = 0;
	}

	/**
	 * XMLParser class
	 *
	 * @copyright	Spoonlabs
	 * @version		$Revision: 5385 $
	 * @author		Aleksandar Ruzicic <aruzicic@spoonlabs.com>
	 */
	class VivvoXMLParser {

		/**
		 * @var	resource		XML Parser instance
		 */
		protected $parser;

		/**
		 * @var	VivvoXMLNode	Reference to document (root) node
		 */
		protected $documentNode;

		/**
		 * @var	array			Node stack used when constructing document tree
		 */
		protected $nodeStack;

		/**
		 * @var	array 			Params used for creating XML Parser
		 */
		protected $params;

		/**
		 * @var array			Default parameters for creating new instance
		 */
		public static $defaultParams = array(
			'encoding' 			=> '',				// (string)	input encoding, empty string -> auto-detect
			'nodeClass' 		=> 'VivvoXMLNode',	// (string)	node class' name
			'skipWhitespace' 	=> false,			// (bool)	when true whitespace-only nodes are ignored
			'skipComments'		=> false			// (bool)	when true comment nodes are ignored
		);


		/**
		 * Creates new VivvoXMLParser instance
		 *
		 * @param	array		$params		Parameters used to create parser
		 * @return	XMLParser
		 */
		public function __construct(array $params = array()) {
			$this->params = array_merge(self::$defaultParams, $params);
		}

		/**
		 * Return new VivvoXMLParser instance
		 *
		 * @param	array		$params		Parameters used to create parser
		 * @return	XMLParser
		 */
		public static function factory(array $params = array()) {
			return new VivvoXMLParser($params);
		}

		/**
		 * Object destructor, frees XML Parser resource
		 */
		public function __destruct() {
			if (is_resource($this->parser)) {
				xml_parser_free($this->parser);
				unset($this->parser);
			}
			unset($this->nodeStack);
			unset($this->documentNode);
		}

		/**
		 * Parses XML string
		 *
		 * @param	string			$xmlstring		XML string to parse
		 * @return	VivvoXMLNode
		 */
		public function parseString($xmlstring) {

			if (!strlen($xmlstring = trim($xmlstring))) {
				throw new VivvoXMLParserException('Nothing to parse.', XML_ERROR_NOTHING_TO_PARSE);
			}

			$this->resetParser();

			if (((substr($xmlstring, 0, 10) == '<!DOCTYPE ') and ($end = strpos($xmlstring, '>')) !== false) ||
				((substr($xmlstring, 0, 6) == '<?xml ') and ($end = strpos($xmlstring, '>')) !== false)) {

				$this->nodeStack[0]->doctype = substr($xmlstring, 0, $end + 1);
			}

			if (!xml_parse($this->parser, $xmlstring, true)) {
				throw new VivvoXMLParserException($this->parser);
			}

			$this->documentNode = array_pop($this->nodeStack);

			foreach ($this->documentNode->children as &$child) {
				$child->parentNode = null;
			} unset($child);

			return $this->documentNode;
		}

		/**
		 * Parses XML file
		 *
		 * @param	string			$filename		XML file to parse
		 * @return	VivvoXMLNode
		 */
		public function parseFile($filename) {

			$contents = file_get_contents($filename);

			if ($contents === false) {
				throw new VivvoXMLParserException("Failed to open file for reading: '$filename'.", XML_ERROR_FILE_NOT_READABLE);
			}

			return $this->parseString($contents);
		}

		/**
		 * Resets internal XML Parser instance
		 */
		protected function resetParser() {

			if (is_resource($this->parser)) {
				xml_parser_free($this->parser);
				$this->parser = null;
			}

			$this->documentNode = null;
			$this->nodeStack = array(new $this->params['nodeClass'](VIVVO_XML_DOCUMENT_NODE));

			$this->parser = xml_parser_create($this->params['encoding']);

			xml_parser_set_option($this->parser, XML_OPTION_SKIP_WHITE, 1);
			xml_parser_set_option($this->parser, XML_OPTION_CASE_FOLDING, 0);

			xml_set_element_handler($this->parser, array(&$this, 'handlerNodeStart'), array(&$this, 'handlerNodeEnd'));
			xml_set_character_data_handler($this->parser, array(&$this, 'handlerTextNode'));
			xml_set_default_handler($this->parser, array(&$this, 'handlerDefault'));

			xml_set_end_namespace_decl_handler($this->parser, false);
			xml_set_external_entity_ref_handler($this->parser, false);
			xml_set_notation_decl_handler($this->parser, false);
			xml_set_processing_instruction_handler($this->parser, false);
			xml_set_start_namespace_decl_handler($this->parser, false);
			xml_set_unparsed_entity_decl_handler($this->parser, false);

			VivvoXMLNode::$counter = 0;
		}

		/**
		 * Element start handler
		 */
		protected function handlerNodeStart($parser, $name, $attributes) {

			if (!empty($this->nodeStack) and is_string(end($this->nodeStack))) {

				$contents = array_pop($this->nodeStack);

				if (!($this->params['skipWhitespace'] and !trim($contents))) {

					$text = new $this->params['nodeClass'](VIVVO_XML_TEXT_NODE, array(), $contents);

					$this->nodeStack[count($this->nodeStack) - 1]->appendChild($text);
				}
			}

			$this->nodeStack[] = new $this->params['nodeClass']($name, $attributes);
		}

		/**
		 * Element end handler
		 */
		protected function handlerNodeEnd($parser, $name) {

			if (!empty($this->nodeStack) and is_string(end($this->nodeStack))) {

				$contents = array_pop($this->nodeStack);

				if (!($this->params['skipWhitespace'] and !trim($contents))) {

					$text = new $this->params['nodeClass'](VIVVO_XML_TEXT_NODE, array(), $contents);

					$this->nodeStack[count($this->nodeStack) - 1]->appendChild($text);
				}
			}

			$last = array_pop($this->nodeStack);
			$count = count($this->nodeStack);
			$this->nodeStack[$count - 1]->appendChild($last);
		}

		/**
		 * Text node handler
		 */
		protected function handlerTextNode($parser, $data) {
			if (!is_string(end($this->nodeStack))) {
				$this->nodeStack[] = $data;
			} else {
				$this->nodeStack[count($this->nodeStack) - 1] .= $data;
			}
		}

		/**
		 * Default handler (handles comments)
		 */
		protected function handlerDefault($parser, $data) {

			if (!empty($this->nodeStack) and
				(substr($data, 0, 4) == '<!--') and
				(substr($data, -3) == '-->') and
				($this->params['skipComments'] !== true)) {

				if (is_string(end($this->nodeStack))) {

					$contents = array_pop($this->nodeStack);

					if (!($this->params['skipWhitespace'] and !trim($contents))) {

						$text = new $this->params['nodeClass'](VIVVO_XML_TEXT_NODE, array(), $contents);

						$this->nodeStack[count($this->nodeStack) - 1]->appendChild($text);
					}
				}

				$this->nodeStack[count($this->nodeStack) - 1]->appendChild(
					new $this->params['nodeClass'](VIVVO_XML_COMMENT_NODE, array(), substr($data, 4, -3))
				);
			}
		}
	}

	/**
	 * "custom" XML Parser errors
	 */
	define('XML_ERROR_FILE_NOT_READABLE', 	10001);
	define('XML_ERROR_NOTHING_TO_PARSE', 	10002);

	/**
	 * VivvoXMLParserException that gets thrown on errors in VivvoXMLParser
	 */
	class VivvoXMLParserException extends Exception {

		public function __construct($message, $code = 0) {

			if (is_resource($message) or ($message === null and $code != 0)) {
				if ($message !== null) {
					$code = xml_get_error_code($message);
				}
				$message = xml_error_string($code) . ' at line ' . intval(@xml_get_current_line_number($message));
			}

			parent::__construct($message, $code);
		}
	}

#EOF