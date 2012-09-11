<?php
App::uses('ActivityAppModel', 'Activity.Model');
/**
 * Activity Model
 *
 * @property User $User
 * @property ActivitiesEvent $ActivitiesEvent
 */
class Activity extends ActivityAppModel {

	public $actsAs = array('Containable');

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ActivityEvent' => array(
			'className' => 'ActivityEvent',
			'foreignKey' => 'activity_event_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
