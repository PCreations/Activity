<?php

class ActivityHelper extends AppHelper {
	
	public $helpers = array('Pusher.Pusher', 'Html', 'Js');

	public function init($subscribedChannels) {
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