<?php
class ActivityException extends CakeException{}

App::uses('String', 'Utility');

class ActivityComponent extends Component {
	
	public $controller;

	public $components = array('Auth', 'Pusher.Pusher');

	protected $_channelTemplate = 'activity-:model-:id';

	protected $_eventTemplate = ':model-:id-:name';

	protected $_privatePrefix = 'private-';

	public function __construct(ComponentCollection $collection, $settings = array()) {
		$this->_setupComponents();
		parent::__construct($collection, $settings);
	}

	protected function _setupComponents() {
		App::uses('PusherComponent', 'Pusher.Controller/Component');
		if (!class_exists('PusherComponent'))
			throw new MissingPluginException('Missing Pusher plugin');
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->controller->loadModel('Activity.ActivityChannel');
		$this->controller->loadModel('Activity.ActivityEvent');
		$this->controller->loadModel('Activity.ActivityChannelsUser');
	}

	public function trigger($eventName, $message) {
		if(!$this->controller->{$this->controller->modelClass}->exists()) {
			throw new ActivityException($this->controller->modelClass . '::id is null');
		}
		list($channel, $event) = $this->_channelRoutines($eventName);
		$this->_subscribeUser($channel);
		
		/* Saving activity */
		$Activity = ClassRegistry::init('Activity.Activity');
		$Activity->create();
		if($Activity->save(array(
			'Activity' => array(
				'id' => String::uuid(),
				'user_id' => $this->Auth->user('id'),
				'message' => $message,
				'activities_event_id' => $event['ActivityEvent']['id'],
			)
		))) {
			$this->Pusher->trigger($this->_privatePrefix . $this->_getChannelName(), $this->_getEventName($eventName), array('message' => $message));	
		}
		else {
			throw new ActivityException('Unable to save Activity');
		}
	}

	protected function _channelRoutines($eventName) {
		$channelExists = $this->controller->ActivityChannel->channelExists($this->_getChannelName());
		$eventID = null;
		var_dump($this->_getChannelName(), $channelExists);
		if(!$channelExists) {
			if($this->controller->ActivityChannel->save(array(
				'ActivityChannel' => array(
					'id' => String::uuid(),
					'name' => $this->_getChannelName()
				)
			))){
				$channel = $this->controller->ActivityChannel->read();
				$this->_addEvent($eventName, $channel['ActivityChannel']['id']);
			}
			else {
				throw new ActivityException('Unable to save ActivityChannel');
			}
		}
		else {
			$channel = $this->controller->ActivityChannel->findByName($this->_getChannelName());
			$this->_addEvent($eventName, $channel['ActivityChannel']['id']);
		}
		return array($channel, $this->controller->ActivityEvent->read());
	}

	protected function _addEvent($eventName, $channelID) {
		$model = $this->controller->modelClass;
		$foreignKey = $this->controller->{$model}->id;
		$eventExists = $this->controller->ActivityEvent->eventExists($this->_getEventName($eventName), $channelID, $model, $foreignKey);
		if(!$eventExists) {
			$this->controller->ActivityEvent->create();
			if(!$this->controller->ActivityEvent->save(array(
					'ActivityEvent' => array(
						'id' => String::uuid(),
						'name' => $this->_getEventName($eventName),
						'activity_channel_id' => $channelID,
						'model' => $this->controller->modelClass,
						'foreign_key' => $this->controller->{$this->controller->modelClass}->id
					)
				))) {
				throw new ActivityException('Unable to save ActivityEvent');
			}
		}
		else {
			$event = $this->controller->ActivityEvent->find('first', array(
				'conditions' => array(
					'ActivityEvent.name' => $this->_getEventName($eventName),
					'ActivityEvent.activity_channel_id' => $channelID,
					'ActivityEvent.model' => $model,
					'ActivityEvent.foreign_key' => $foreignKey,
				)
			));
			$this->controller->ActivityEvent->id = $event['ActivityEvent']['id'];
		}
	}

	protected function _subscribeUser($channel) {
		$user = $this->controller->ActivityChannelsUser->find('first', array(
			'conditions' => array(
				'ActivityChannelsUser.user_id' => $this->Auth->user('id'),
				'ActivityChannelsUser.activity_channel_id' => $channel['ActivityChannel']['id']
			)
		));
		if(!$user) {
			$this->controller->ActivityChannelsUser->create();
			if(!$this->controller->ActivityChannelsUser->save(array(
				'ActivityChannelsUser' => array(
					'activity_channel_id' => $channel['ActivityChannel']['id'],
					'user_id' => $this->Auth->user('id')
				)
			))) {
				throw new ActivityException('Unable to subscribe user to ' . $channel['ActivityChannel']['name']);
			}
		}
	}

	private function _getChannelName() {
		$currentModel = $this->controller->modelClass;
		return strtolower(String::insert($this->_channelTemplate, array(
			'model' => $currentModel,
			'id' => $this->controller->{$currentModel}->id
		)));
	}

	private function _getEventName($eventName) {
		$currentModel = $this->controller->modelClass;
		return strtolower(String::insert($this->_eventTemplate, array(
			'model' => $currentModel,
			'id' => $this->controller->{$currentModel}->id,
			'name' => $eventName
		)));
	}

}

?>