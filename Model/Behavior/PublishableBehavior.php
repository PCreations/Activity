<?php

class PublishableBehavior extends ModelBehavior {
	
	public function setup($model, $options = array()) {
		$model->bindModel(
			array(
				'hasAndBelongsToMany' => array(
					'SubscribedChannels' => array(
						'className' => 'Activity.ActivityChannel',
						'joinTable' => 'activity_channels_users',
						'with' => 'Activity.ActivityChannelsUser',
						'foreignKey' => 'user_id',
						'associationForeignKey' => 'activity_channel_id',
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
				),
				'hasMany' => array(
					'Activity' => array(
						'className' => 'Activity.Activity',
						'foreignKey' => 'user_id',
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
				)
			),
			false
		);
	}

}

?>