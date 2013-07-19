<?php

app::uses('Media.MediaAttachment', 'Media.Model');
app::uses('Media.Media', 'Media.Model');


class MediaAttachableBehavior extends ModelBehavior {
			
	public $settings = array();
	
	
	public function setup(Model $model, $config = array()) {
		//Add the HasMany Relationship to the $model
		$model->bindModel(
	        array('hasMany' => array(
	                'MediaAttachments' => array(
						'className' => 'Media.MediaAttachment',
						'foreignKey' => 'foreign_key'
	                	)
	             	)
	     		)
			);
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
	 * @param Model $model Model using this behavior
	 * @return mixed False if the operation should abort. Any other result will continue.
	 */
	public function beforeSave(Model $model) {
		//doing it this way to protect against saveAll
		if(isset($model->data['MediaAttachment'])) {
			$this->data['MediaAttachment'] = $model->data['MediaAttachment'];
			unset($model->data['MediaAttachment']);
		}
		return true;
	}
	
	
		
	/**
	 * afterSave is called after a model is saved.
	 * We use this to save the attachement after the $model is saved
	 *
	 * @param Model $model Model using this behavior
	 * @param boolean $created True if this save created a new record
	 * @return boolean
	 */
	public function afterSave(Model $model, $created) {
		
		$MediaAttachment = new MediaAttachment;
		
		//Removes all Attachment Records so they can be resaved
		if(!$created) {
			$MediaAttachment->deleteAll(array(
							'model' => $model->alias,
							'foreign_key' => $model->data[$model->alias]['id']
							), false);
		}
		
		if(is_array($this->data['MediaAttachment'])) {
			foreach($this->data['MediaAttachment'] as $k => $media) {
				$media['model'] = $model->alias;
				$media['foreign_key'] = $model->data[$model->alias]['id'];
				$this->data['MediaAttachment'][$k] = $media;
			}
		}else {
			$this->data['MediaAttachment']['model'] = $model->alias;
			$this->data['MediaAttachment']['foreign_key'] = $model->data[$model->alias]['id'];
		}
		
		$MediaAttachment->saveAll($this->data);
		
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
	 * @param Model $model Model using this behavior
	 * @param boolean $cascade If true records that depend on this record will also be deleted
	 * @return mixed False if the operation should abort. Any other result will continue.
	 */
	public function beforeDelete(Model $model, $cascade = true) {
		//unbinds the model, so we can handle the delete
		$model->unbindModel(
        	array('hasMany' => array('MediaAttachments'))
    	);
		return true;
	}

	/**
	 * After delete is called after any delete occurs on the attached model.
	 * 
	 * Deletes all attachment records, but keeps the attached data
	 *
	 * @param Model $model Model using this behavior
	 * @return void
	 */
	public function afterDelete(Model $model) {
		
		//Deletes all linked Media
		$MediaAttachment->deleteAll(array(
							'model' => $model->alias,
							'foreign_key' => $model->data[$model->alias]['id']
							), false);
	}
	
	/**
	 * After find callback. Can be used to modify any results returned by find.
	 * 
	 * This is used to attach the actual Media to the $model Data and removes the attachment data
	 * 
	 * @todo There is probable a better way to do this with model binding and associations
	 * 
	 *
	 * @param Model $model Model using this behavior
	 * @param mixed $results The results of the find operation
	 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
	 * @return mixed An array value will replace the value of $results - any other value will be ignored.
	 */
	public function afterFind(Model $model, $results, $primary) {
		//Only attache media if the $model->find() is being called directly	
		if(isset($results['MediaAttachment']) && $primary) {
			$media__ids = array();
			foreach($results['MediaAttachment'] as $medaAttachment) {
				$media__ids = $medaAttachment['media_id'];
			}
			$Media = new Media;
			$results['Media'] = $Media->find('all', array('id' => $media_ids));
		}
	}
		
	
}