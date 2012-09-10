<?php
App::uses('ActivityAppModel', 'Activity.Model');
/**
 * Activity Model
 *
 * @property User $User
 * @property ActivitiesEvent $ActivitiesEvent
 */
class Activity extends ActivityAppModel {

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
		'ActivitiesEvent' => array(
			'className' => 'ActivitiesEvent',
			'foreignKey' => 'activities_event_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);
}
