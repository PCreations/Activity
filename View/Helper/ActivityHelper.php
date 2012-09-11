<?php

App::uses('CakeEvent', 'Event');

class ActivityHelper extends AppHelper {
	
	public $helpers = array('Pusher.Pusher', 'Html', 'Js');

	public $view;

	public function __construct(View $view, $settings = array()) {
		parent::__construct($view, $settings);
		$this->view = $view;
	}

	public function init($subscribedChannels) {
		$this->view->getEventManager()->dispatch(new CakeEvent('Plugin.Activity.beforeInitSubscribedChannels', $subscribedChannels));
		foreach($subscribedChannels as $channel) {
			$this->Pusher->subscribe($channel['name'], 'private');
			foreach($channel['ActivityEvent'] as $event) {
				$this->Pusher->bindEvent('private-' . $channel['name'], $event['name'], "console.log(data.message);");
			}
		}
	}

	public function afterRender($layout) {
		$this->Pusher->afterRender($layout);
	}

}

?>