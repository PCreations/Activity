<?php
class ActivityException extends CakeException{}

App::uses('String', 'Utility');

class ActivityComponent extends Component {
	
	public $controller;

	public $components = array();

	protected $_channelTemplate = 'private-activity-:model-:id';

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
	}

	public function trigger() {
		if($this->controller->{$this->controller->modelClass}->id == null) {
			throw new ActivityException($this->controller->modelClass . '::id is null');
		}
		$this->_channelRoutines();
	}

	protected function _channelRoutines() {
		$ActivityChannel = ClassRegistry::init('Activity.ActivityChannel', true);
		$this->controller->loadModel('Activity.ActivityChannel');
		var_dump($this->_getChannelName());
		$channelExists = $this->controller->ActivityChannel->channelExists($this->_getChannelName());
		var_dump($channelExists);
	}

	protected function _getChannelName() {
		$currentModel = $this->controller->modelClass;
		return strtolower(String::insert($this->_channelTemplate, array(
			'model' => $currentModel,
			'id' => $this->controller->{$currentModel}->id
		)));
	}

}

?>