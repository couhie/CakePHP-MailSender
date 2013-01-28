<?php
/**
 * MailSenderComponent.php
 * @author kohei hieda
 *
 */
class MailSenderComponent extends Component {

	var $template = '';
	var $layout = 'default';
	var $to = array();
	var $from = array();
	var $subject = '';
	var $helpers = array();

	function initialize(&$controller) {
		parent::initialize($controller);
		$this->_controller = $controller;
		$this->template = $controller->view;
		$this->helpers = $controller->helpers;
	}

	function startup(&$controller) {
	}

	function beforeRender(&$controller) {
	}

	function beforeRedirect(&$controller) {
	}

	function shutdown(&$controller) {
	}

	/**
	 * send
	 * @param $type
	 * 今はtextメールのみ対応
	 */
	function send($type = 'text') {
		App::import('Vendor', 'MailSender.Qdmail', array('file'=>'Qdmail'.DS.'qdmail.php'));

		$View = new View(null);
		$View->viewVars = $this->_controller->viewVars;
		$View->helpers = $this->helpers;

		list($templatePlugin, $template) = pluginSplit($this->template);
		list($layoutPlugin, $layout) = pluginSplit($this->layout);
		if ($templatePlugin) {
			$View->plugin = $templatePlugin;
		} elseif ($layoutPlugin) {
			$View->plugin = $layoutPlugin;
		}

		$View->viewPath = $View->layoutPath = 'Emails' . DS . $type;

		$mail =& new Qdmail();

		if (!is_array($this->from)) {
			$this->from = array($this->from);
		}
		foreach ($this->from as $from) {
			if (!is_array($from)) {
				$from = array($from);
			}
			if (count($from) < 2) {
				$mail->from($from[0], $from[0], true);
			} else {
				$mail->from($from[0], $from[1], true);
			}
		}

		if (!is_array($this->to)) {
			$this->to = array($this->to);
		}
		foreach ($this->to as $to) {
			if (!is_array($to)) {
				$to = array($to);
			}
			if (count($to) < 2) {
				$mail->to($to[0], $to[0], true);
			} else {
				$mail->to($to[0], $to[1], true);
			}
		}

		$mail->subject($this->subject);
		$mail->text($View->render($template, $layout));
		$mail->send();
	}

}