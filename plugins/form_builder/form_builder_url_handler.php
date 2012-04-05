<?php
	function form_builder_url_handler(&$sm, $url_array){
		$um =& $sm->get_url_manager();

		require_once(dirname(__FILE__) . '/form_builder.class.php');
		$form_list =& new FormBuilderForms_list($sm);
		$form =& $form_list->get_form_by_url($url_array[0]);
		if ($form) {
			return array('url_module' => $url_array[0], 'search_fid' => $form->id);
		}
		return false;
	}

	function form_builder_content_handler(&$sm){
		$um =& $sm->get_url_manager();
		$sm->set_theme();
		$sm->_template->set_template_file(VIVVO_FS_ROOT . VIVVO_TEMPLATE_DIR . 'frame/default.tpl');

		$content_template = new template($sm, $sm->_template);
		
		require_once(dirname(__FILE__) . '/form_builder.class.php');
		$form_list =& new FormBuilderForms_list($sm);
		$form =& $form_list->get_form_by_id($um->get_param('search_fid'));
		$content_template->assign('form', $form);
		if ($form){
			$form_element_list =& new FormBuilderFields_list($sm);
			$form_element_list->get_elements_by_form_id($form->id);

			$upload = false;
			foreach ($form_element_list->list as $element) {
				if ($element->get_type() == 'file_upload') {
					$upload = true;
					break;
				}
			}

			$content_template->assign('form_elements', $form_element_list->list);
			$content_template->assign('form_has_file_upload', $upload);
		}

        $action = $um->get_param('action');
        if ($upload && !empty($action)) {
            if (!empty($form->message_url)){
                $content_template->assign('message_url', $form->message_url);
            }
            $content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'box/plugin_form_builder.tpl');
        }else{
            $content_template->set_template_file(VIVVO_FS_TEMPLATE_ROOT . VIVVO_TEMPLATE_DIR . 'system/box_default/box_form.tpl');
        }
		
		$sm->_template->assign_template('PAGE_CONTENT', $content_template);
	}
?>
