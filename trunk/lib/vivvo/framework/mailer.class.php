<?php
require_once(VIVVO_FS_FRAMEWORK . 'PEAR/Mail.php');

class mailer {

	/**
	 * Sender
	 *
	 * @var string
	 */
	var $to;

	/**
	 * From
	 *
	 * @var string
	 */
	var $from;

	/**
	 * Header
	 *
	 * @var array
	 */
	var $header = array();

	/**
	 * Subject
	 *
	 * @var String
	 */
	var $subject;

	/**
	 * Body
	 *
	 * @var string
	 */
	var $body;

	/**
	 * Type of content
	 * 0 - text/plain
	 * 1 - text/html
	 *
	 * @var integer
	 */
	var $content_type;

	/**
	 * New line
	 *
	 * @var string
	 */
	var $nl = "\n\r";

	function set_to($to){
		$this->to = $to;
	}

	function set_from($from){
		$this->from = $from;
	}

	function set_subject($subject){
		$this->subject = "=?UTF-8?B?" . base64_encode($subject) . "?=";
	}

	function set_nl(){
		if ($this->get_content_type()){
			$this->nl = "<br />";
		}else{
			$this->nl = "\n\r";
		}
	}

	function set_header(){
		$this->header['From'] =  $this->get_from();
		$this->header['Reply-To'] = $this->get_from();
		$this->header['MIME-Version'] = "1.0";
		$this->header['Subject'] = $this->get_subject();

		if ($this->get_content_type()){
			$this->header['Content-type']= "text/html; charset=utf-8\r\n";
		}else{
			$this->header['Content-type']= "text/plain; charset=utf-8\r\n";
		}
	}

	function set_body($body){
		if ($this->get_content_type()){
			$this->body = nl2br($body);
		}else{
			preg_replace('/<br\\s*?\/??>/i', "\n", $body);
			$this->body = strip_tags($body);
		}
	}

	function set_content_type($content_type = 0){
		$this->content_type = $content_type;
	}

	function get_to(){
		return $this->to;
	}

	function get_from(){
		return $this->from;
	}

	function get_subject(){
		return $this->subject;
	}

	function get_header(){
		return $this->header;
	}

	function get_body(){
		return $this->body;
	}

	function get_content_type(){
		return $this->content_type;
	}

	function get_nl($no = 1){
		if ($no > 1){
			$output = '';
			while($no >= 1){
				$output .= $this->nl;
				$no--;
			}
			return $output;
		}else{
			return $this->nl;
		}
	}

	function send(){

		if (VIVVO_EMAIL_SMTP_PHP == 1){
			$mail_object = new Mail();
			return $mail_object->send($this->get_to(), $this->get_header(), $this->get_body());
		}else{
			$mail_options['driver']    = 'smtp';
			$mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
			$mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
			$mail_options['localhost'] = 'localhost';

			if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != ''){
				$mail_options['auth'] = true;
				$mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
				$mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
			}else{
				$mail_options['auth'] = false;
				$mail_options['username']  = '';
				$mail_options['password']  = '';
			}

			$mail_object =& Mail::factory('smtp', $mail_options);

			return $mail_object->send($this->get_to(), $this->get_header(), $this->get_body());
		}

	}
}

#EOF