<?php
class ActivityException extends CakeException{}

App::uses('String', 'Utility');

class ActivityComponent extends Component {
	
	public $controller;

	public $components = array();

	protected $_channelTemplate = 'private-activity-:model-:id';

	protected $_eventTemplate = ':model-:id-:name';

	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
		$this->_setupComponents();
	}

	protected function _setupComponents() {
		App::uses('PusherComponent', 'Pusher.Controller/Component');
		if (class_exists('PusherComponent')) {
			$this->components[] = 'Pusher.Pusher';
		}
		else {
			throw new MissingPluginException('Missing Pusher plugin');
		}
	}

	public function initialize(Controller $controller) {
		$this->controller = $controller;
		$this->controller->loadModel('Activity.ActivityChannel');
		$this->controller->loadModel('Activity.ActivityEvent');
	}

	public function trigger($eventName) {
		if(!$this->controller->{$this->controller->modelClass}->exists()) {
			throw new ActivityException($this->controller->modelClass . '::id is null');
		}
		$this->_channelRoutines($eventName);
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

		debug($channel);
		debug($this->controller->ActivityEvent->read());
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