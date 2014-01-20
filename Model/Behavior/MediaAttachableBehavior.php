<?php
App::uses('MediaAttachment', 'Media.Model');
App::uses('Media', 'Media.Model');

class MediaAttachableBehavior extends ModelBehavior {
			
	public $settings = array();
	
/**
 * Setup method
 */
	public function setup(Model $Model, $config = array()) {
		//Add the HasMany Relationship to the $Model
		$Model->bindModel($this->_bindModel($Model),false);
	}
	
/**
 * beforeSave is called before a model is saved.  Returning false from a beforeSave callback
 * will abort the save operation.
 * 
 * This strips the Media from the request and places it in a variable
 * Uses the AfterSave Method to save the attchement
 * 
 * * @todo Might be a better way to do this with model associations
 *
 * @param Model $Model Model using this behavior
 * @return mixed False if the operation should abort. Any other result will continue.
 */
	public function beforeSave(Model $Model, $options = array()) {
		
		//doing it this way to protect against saveAll
		if(isset($Model->data['MediaAttachment'])) {
			$this->data['MediaAttachment'] = $Model->data['MediaAttachment'];
			unset($Model->data['MediaAttachment']);
		}
		return true;
	}
	
	
		
/**
 * afterSave is called after a model is saved.
 * We use this to save the attachement after the $Model is saved
 *
 * @param Model $Model Model using this behavior
 * @param boolean $created True if this save created a new record
 * @return boolean
 */
	public function afterSave(Model $Model, $created, $options = array()) {
		if(isset($this->data['MediaAttachment'])) {
			$MediaAttachment = new MediaAttachment;
		
			//Removes all Attachment Records so they can be resaved
			if(!$created) {
				$MediaAttachment->deleteAll(array(
								'model' => $Model->alias,
								'foreign_key' => $Model->data[$Model->alias]['id']
								), false);
			}
			
			if(is_array($this->data['MediaAttachment'])) {
				foreach($this->data['MediaAttachment'] as $k => $media) {
					$media['model'] = $Model->alias;
					$media['foreign_key'] = $Model->data[$Model->alias]['id'];
					$this->data['MediaAttachment'][$k] = $media;
				}
			}else {
				$this->data['MediaAttachment']['model'] = $Model->alias;
				$this->data['MediaAttachment']['foreign_key'] = $Model->data[$Model->alias]['id'];
			}
			
			$MediaAttachment->create();
			$MediaAttachment->saveMany($this->data['MediaAttachment']);	
		}
		return true;
	}
	
/**
 * Before delete is called before any delete occurs on the attached model, but after the model's
 * beforeDelete is called.  Returning false from a beforeDelete will abort the delete.
 * 
 * We are unbinding the association model, so we can handle the delete ourselves
 *
 * @todo Might be a better way to do this with model associations
 *
 * @param Model $Model Model using this behavior
 * @param boolean $cascade If true records that depend on this record will also be deleted
 * @return mixed False if the operation should abort. Any other result will continue.
 */
	public function beforeDelete(Model $Model, $cascade = true) {
		//unbinds the model, so we can handle the delete
		$Model->unbindModel(
        	array('hasMany' => array('MediaAttachments'))
    	);
		return true;
	}

/**
 * After delete is called after any delete occurs on the attached model.
 * 
 * Deletes all attachment records, but keeps the attached data
 *
 * @param Model $Model Model using this behavior
 * @return void
 */
	public function afterDelete(Model $Model) {
		$MediaAttachment = new MediaAttachment;// This was added to support UserGroup delete
		//Deletes all linked Media
		$MediaAttachment->deleteAll(array(
			'model' => $Model->alias,
			'foreign_key' => $Model->data[$Model->alias]['id']
			), false);
	}
	
/**
 * After find callback. Can be used to modify any results returned by find.
 * 
 * This is used to attach the actual Media to the $Model Data and removes the attachment data
 * 
 * @todo There is probable a better way to do this with model binding and associations
 * 
 *
 * @param Model $Model Model using this behavior
 * @param mixed $results The results of the find operation
 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return mixed An array value will replace the value of $results - any other value will be ignored.
 */
	public function beforeFind(Model $Model, $query) {
		//Allows us to pass $query['media'] = false to not contain media
		if(isset($query['media']) && !$query['media']) {
			return $query;
		}
		if(empty($Model->hasAndBelongsToMany['Media'])){
			$Model->bindModel($this->_bindModel($Model),false);
		}
		
		$query['contain'][] = 'Media';
		$query['contain'][] = 'MediaThumbnail';
		return $query;		
	}

/**
 * Bind Model method
 * 
 * @param object $Model
 */
	protected function _bindModel($Model){
    	return array('hasAndBelongsToMany' => array(
        	'Media' =>
            	array(
                	'className' => 'Media.Media',
                	'joinTable' => 'media_attachments',
                	'foreignKey' => 'foreign_key',
                	'associationForeignKey' => 'media_id',
                	'conditions' => array(
                		'MediaAttachment.model' => $Model->alias,
                		'OR' => array(
                				array('MediaAttachment.primary' => 0),
                				array('MediaAttachment.primary' => null)
                			)
                		),
            		'order' => array('MediaAttachment.order')
            	),
        	'MediaThumbnail' =>
        		array(
    				'className' => 'Media.Media',
    				'joinTable' => 'media_attachments',
    				'foreignKey' => 'foreign_key',
    				'associationForeignKey' => 'media_id',
    				'conditions' => array('MediaAttachment.model' => $Model->alias, 'MediaAttachment.primary' => true),
        		)
        	)
		);
	}
	
		
	
}