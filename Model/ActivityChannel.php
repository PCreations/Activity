<?php
App::uses('ActivityAppModel', 'Activity.Model');
/**
 * ActivityChannel Model
 *
 * @property ActivityEvent $ActivityEvent
 * @property User $User
 */
class ActivityChannel extends ActivityAppModel {

	public $actsAs = array('Containable');

/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'name';

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'ActivityEvent' => array(
			'className' => 'ActivityEvent',
			'foreignKey' => 'activity_channel_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);


/**
 * hasAndBelongsToMany associations
 *
 * @var array
 */
	public $hasAndBelongsToMany = array(
		'User' => array(
			'className' => 'User',
			'joinTable' => 'activity_channels_users',
			'with' => 'ActivityChannelsUser',
			'foreignKey' => 'activity_channel_id',
			'associationForeignKey' => 'user_id',
			'unique' => 'keepExisting',
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'finderQuery' => '',
			'deleteQuery' => '',
			'insertQuery' => ''
		)
	);


	public function channelExists($channelName) {
		$result = $this->find('first', array(
			'conditions' => array(
				'ActivityChannel.name' => $channelName,
			)
		));
		return !empty($result);
	}
}
