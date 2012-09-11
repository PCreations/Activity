<?php
App::uses('ActivityAppModel', 'Activity.Model');
/**
 * ActivityEvent Model
 *
 * @property ActivityChannel $ActivityChannel
 */
class ActivityEvent extends ActivityAppModel {

	public $actsAs = array('Containable');
	
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'ActivityChannel' => array(
			'className' => 'ActivityChannel',
			'foreignKey' => 'activity_channel_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function eventExists($name, $channelID, $model, $foreign_key) {
		$result = $this->find('first', array(
			'conditions' => array(
				'ActivityEvent.name' => $name,
				'ActivityEvent.activity_channel_id' => $channelID,
				'ActivityEvent.model' => $model,
				'ActivityEvent.foreign_key' => $foreign_key,
			)
		));
		return !empty($result);
	}
}
