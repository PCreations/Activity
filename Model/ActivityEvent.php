<?php
App::uses('ActivityAppModel', 'Activity.Model');
/**
 * ActivityEvent Model
 *
 * @property ActivityChannel $ActivityChannel
 */
class ActivityEvent extends ActivityAppModel {
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
}
