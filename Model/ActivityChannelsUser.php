<?php
App::uses('ActivityAppModel', 'Activity.Model');
/**
 * ActivityChannelsUser Model
 *
 * @property ActivityChannel $ActivityChannel
 * @property User $User
 */
class ActivityChannelsUser extends ActivityAppModel {

	public $actsAs = array('Containable');

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
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

	public function subscribeUser($channel, $userID) {
		$this->create();
		if(!$this->save(array(
			'ActivityChannelsUser' => array(
				'activity_channel_id' => $channel['ActivityChannel']['id'],
				'user_id' => $userID
			)
		))) {
			throw new ActivityException('Unable to subscribe user to ' . $channel['ActivityChannel']['name']);
		}
	}
}
