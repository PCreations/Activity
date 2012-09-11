<?php

class ActivityHelper extends AppHelper {
	
	public $helpers = array('Pusher.Pusher', 'Html', 'Js');

	public function init($subscribedChannels) {
		foreach($subscribedChannels as $channel) {
			$this->Pusher->subscribe($channel['ActivityChannel']['name'], 'private');
			foreach($channel['ActivityEvent'] as $event) {
				$this->Pusher->bindEvent($channel['ActivityChannel']['name'], $event['name'], "console.log(data.message);")
			}
		}
	}

}

?>