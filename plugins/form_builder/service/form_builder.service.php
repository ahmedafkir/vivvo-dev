<?php
/* =============================================================================
 * $Revision: 5647 $
 * $Date: 2010-7-21 18:28:26 +0100 (Wed, 21 July 2010) $
 *
 * Vivvo CMS 4.5
 * Copyright 2005-10 SpoonLabs d.o.o.
 * http://www.spoonlabs.com, All Rights Reserved
 *
 * Warning: This program is protected by copyright law. Unauthorized
 * reproduction or distribution of this program, or any portion of it, may
 * result in severe civil and criminal penalties, and will be prosecuted to the
 * maximum extent possible under the law. For more information about this
 * script or other scripts see http://www.spoonlabs.com
 * ============================================================================
 */


	/**
	 * @see  '/../form_builder.class.php'
 	 */
	require_once(dirname(__FILE__) . '/../form_builder.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_service.class.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_service.class.php');

	/**
	 * @see  'lib/vivvo/framework/vivvo_post.php'
 	 */
	require_once(VIVVO_FS_FRAMEWORK . 'vivvo_post.php');

	/**
	 * Plugin form builder service object
	 *
	 * @license		http://www.vivvo.net/license.php
	 * @copyright	SpoonLabs
	 * @package		vivvo_plugins
	 * @subpackage	form_builder
	 * @see			vivvo_service
	 * @author		Vedran Martinovic <vmartinovic@spoonlabs.com>
	 * @author		Slobodan Utvic <utvara@spoonlabs.com>
	 */
	class form_builder_service extends vivvo_service {

		/**
		 * Add new form
		 *
		 * @param	array	$data
		 * @param	array	$elements
		 * @return	boolean	true on succes, or false on fail
		 */
		function add ($data, $elements){

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user and $sm->user->can('MANAGE_PLUGIN', 'form_builder')) {

				if (!vivvo_hooks_manager::call('form_builder_add', array(&$data, &$elements))) {
					return vivvo_hooks_manager::get_status();
				}
				
				if (empty($data['email'])) {
					$this->set_error_code(10714);	// you must enter email
					return false;
				}

				$form = new FormBuilderForms;
				if ($data['action'] == ''){
					$data['action'] = 'form_builder';
				}
				if ($data['cmd'] == ''){
					$data['cmd'] = 'mail';
				}
				
				$data['url'] = make_sefriendly($data['url']);

                if (!preg_match('/^[a-z_0-9\-]*$/', $data['url'])) {
                    $this->set_error_code(10715);
                    return false;
                }

                if (!preg_match('|^([^:]+)://|', $data['message_url']) && trim($data['message_url']) != '') {
                    $data['message_url'] = 'http://' . $data['message_url'];
                }
                    
				if ($form->populate($data, true) === false) {
					$this->set_error_info($form->get_error_info());
					return false;
				}

				$this->_post_master->set_data_object($form);
				if ($this->_post_master->sql_insert()){

					$form_id = $this->_post_master->get_work_id();

					if (is_array($elements) && !empty($elements)) {
						foreach($elements as $k => $v){
							$form_element = new FormBuilderFields;
							$form_element->populate($v);
							$form_element->form_id = $form_id;
							$this->_post_master->set_data_object($form_element);
							$this->_post_master->sql_insert();
						}
					}

					if (!empty($form->url)) {
						$um = $sm->get_url_manager();
						$um->register_url($form->url, 'plugins/form_builder/form_builder_url_handler.php', 'form_builder_url_handler', 'form_builder_content_handler');
					}

					header('X-Vivvo-Form-Builder-FormId: ' . $form_id);

					return true;
				} else {
					$this->set_error_code(10701);
					return false;
				}
			} else {
				$this->set_error_code(10702);
				return false;
			}
		}

		/**
		 * Edit form
		 *
		 * @param	integer	$form_id
		 * @param	array	$data
		 * @param	array	$elements
		 * @return	boolean	true on succes, or false on fail
		 */
		function edit ($form_id, $data, $elements) {

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user and $sm->user->can('MANAGE_PLUGIN', 'form_builder')) {

				if (!vivvo_hooks_manager::call('form_builder_edit', array(&$form_id, &$data, &$elements))) {
					return vivvo_hooks_manager::get_status();
				}
				
				if (empty($data['email'])) {
					$this->set_error_code(10714);	// you must enter email
					return false;
				}

				$form_list = new FormBuilderForms_list;
				$form = $form_list->get_form_by_id($form_id);

				$form_url = $form->url;

				if ($form !== false) {
					if ($data['action'] == '') {
						$data['action'] = 'form_builder';
					}
					if ($data['cmd'] == '') {
						$data['cmd'] = 'mail';
					}
					$data['url'] = make_sefriendly($data['url']);

					if (!preg_match('/^[a-z_0-9\-]*$/', $data['url'])) {
                        $this->set_error_code(10715);
                        return false;
                    }

                    if (!preg_match('|^([^:]+)://|', $data['message_url']) && trim($data['message_url']) != '') {
                        $data['message_url'] = 'http://' . $data['message_url'];
                    }
                    
					if ($form->populate($data, true) === false) {
						$this->set_error_info($form->get_error_info());
						return false;
					}
					$this->_post_master->set_data_object($form);
					if ($this->_post_master->sql_update()) {
						$form_element_list = new FormBuilderFields_list;
						$form_element_list->get_elements_by_form_id($form->id);

						$form_element_list->sql_delete_list($this->_post_master);


						if (is_array($elements) && !empty($elements)) {
							foreach($elements as $k => $v) {

								$form_element = new FormBuilderFields;
								$form_element->populate($v);
								$form_element->form_id = $form->id;
								$this->_post_master->set_data_object($form_element);
								$this->_post_master->sql_insert();
							}
						}

						$um = $sm->get_url_manager();
						if (!empty($form->url) && $form->url != $form_url) {
							$um->unregister_url($form_url);
							$um->register_url($form->url, 'plugins/form_builder/form_builder_url_handler.php', 'form_builder_url_handler', 'form_builder_content_handler');
						} else if (empty($form->url) && $form->url != $form_url) {
							$um->unregister_url($form_url);
						}

						return true;
					} else {
						$this->set_error_code(10704);
						return false;
					}
				} else {
					$this->set_error_code(10705);
					return false;
				}
			} else {
				$this->set_error_code(10706);
				return false;
			}
		}

		/**
		 * Delete form
		 *
		 * @param	integer	$form_id
		 * @return	boolean	true on succes, or false on fail
		 */
		function delete ($form_id){

			$sm = vivvo_lite_site::get_instance();

			if ($sm->user and $sm->user->can('MANAGE_PLUGIN', 'form_builder')) {

				if (!vivvo_hooks_manager::call('form_builder_delete', array(&$form_id))) {
					return vivvo_hooks_manager::get_status();
				}

				$form_list = new FormBuilderForms_list;
				$form = $form_list->get_form_by_id($form_id);

				if ($form !== false) {
					$this->_post_master->set_data_object($form);
					if ($this->_post_master->sql_delete()) {
						if ($form->url) {
							$sm->get_url_manager()->unregister_url($form->url);
						}
						return true;
					} else {
						$this->set_error_code(10708);
						return false;
					}
				} else {
					$this->set_error_code(10709);
					return false;
				}
			} else {
				$this->set_error_code(10710);
				return false;
			}
		}

		/**
		 * Mail form
		 *
		 * @param	integer	$form_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function mail($form_id, $data) {

			$sm = vivvo_lite_site::get_instance();
			
            if (!vivvo_hooks_manager::call('form_builder_mail', array(&$form_id, &$delete))) {
                return vivvo_hooks_manager::get_status();
            }

            require_once(VIVVO_FS_FRAMEWORK . 'PEAR/Mail.php');

            $form_list = new FormBuilderForms_list;
            $form = $form_list->get_form_by_id($form_id);

            $data = array_map('urldecode', $data);

            $attachments = array();

            if ($form !== false) {
                $form_element_list = new FormBuilderFields_list($this->_site_manager);
                $form_element_list->get_elements_by_form_id($form->id);
                $message = '';
                
                foreach ($form_element_list->list as $field) {
                    
                    if ($field->required && !key_exists($field->name, $data) && $data[$field->name] !== '' && $field->type != 'file_upload') {
                        $this->set_error_code(10712);
                        return false;
                    }else if ($field->required && $field->type == 'file_upload' && empty($_FILES['PFB_' . $field->name]['name'])) {
                        $this->set_error_code(10712);
                        return false;
                    }

                    if (key_exists($field->name, $data)) {
                        if ($field->reg_exp) {
                            if (!preg_match('/^' . $field->reg_exp.'$/', $data[$field->name])) {
                                $this->set_error_code(10713);
                                return false;
                            }
                        }
                        if ($field->type != 'submit') {
                            if ($field->label != ''){
                                $message .= $field->label . ': ' . $data[$field->name] . "\n";
                            }else{
                                $message .= $field->name . ': ' . $data[$field->name] . "\n";
                            }
                        }
                    }

                    if ($field->type == 'file_upload' && isset($_FILES['PFB_' . $field->name])) {
                        $attachments[] = 'PFB_' . $field->name;
                    }
                }

                if ($form->email) {

                    $files = array();
                    if (count($attachments)) {
                        foreach ($attachments as $attachment) {
                            if ($_FILES[$attachment]['error'] == 0 &&
                                $_FILES[$attachment]['size'] > 0 &&
                                $_FILES[$attachment]['size'] <= 2 * 1024 * 1024 &&
                                in_array(substr($_FILES[$attachment]['name'], strrpos($_FILES[$attachment]['name'], '.') + 1), array_map('trim', explode(',', VIVVO_ALLOWED_EXTENSIONS))) &&
                                is_uploaded_file($_FILES[$attachment]['tmp_name'])) {
                                $filename = VIVVO_FS_INSTALL_ROOT . 'cache/' . md5(uniqid(mt_rand(), true)) . basename($_FILES[$attachment]['name']);
                                if (move_uploaded_file($_FILES[$attachment]['tmp_name'], $filename)) {
                                    $files[] = array($filename, basename($_FILES[$attachment]['name']));
                                }
                            }
                        }
                    }

                    $headers['From'] = VIVVO_ADMINISTRATORS_EMAIL;
                    $headers['Subject'] = "=?UTF-8?B?".base64_encode($form->title)."?=";
                    if (!count($files)) {
                        $headers['Content-Type'] = "text/plain; charset=UTF-8;";
                    } else {
                        require_once VIVVO_FS_INSTALL_ROOT . 'lib/vivvo/framework/PEAR/Mail/mime.php';
                        $mime = new Mail_mime();
                        $mime->setTXTBody($message);
                        foreach ($files as $file) {
                            $mime->addAttachment($file[0], 'application/octet-stream', $file[1]);
                        }
                        $message = $mime->get();
                        $headers = $mime->headers($headers);
                        foreach ($files as $file) {
                            @unlink($file[0]);
                        }
                    }

                    if (VIVVO_EMAIL_SMTP_PHP == 1) {
                        $mail_object = new Mail();
                        $mail_object->send($form->email, $headers, $message);
                    } else {
                        $mail_options['driver']    = 'smtp';
                        $mail_options['host']      = VIVVO_EMAIL_SMTP_HOST;
                        $mail_options['port']      = VIVVO_EMAIL_SMTP_PORT;
                        $mail_options['localhost'] = 'localhost';

                        if (VIVVO_EMAIL_SMTP_PASSWORD != '' && VIVVO_EMAIL_SMTP_USERNAME != ''){
                            $mail_options['auth'] = true;
                            $mail_options['username']  = VIVVO_EMAIL_SMTP_USERNAME;
                            $mail_options['password']  = VIVVO_EMAIL_SMTP_PASSWORD;
                        } else {
                            $mail_options['auth'] = false;
                            $mail_options['username']  = '';
                            $mail_options['password']  = '';
                        }

                        $mail_object = Mail::factory('smtp', $mail_options);
                        $mail_object->send($form->email, $headers, $message);
                    }
                }
                if ($form->message) {
                    return $form->message;
                } else {
                    return ' ';
                }
            }
		}

		/**
		 * My action
		 *
		 * @param	integer	$form_id
		 * @param	array	$data
		 * @return	boolean	true on succes, or false on fail
		 */
		function my_action($form_id, $data) {
			//All data from form
			//print_r($data);
			return 'Message';
		}
	}
?>
