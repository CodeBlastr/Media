<?PHP
class Media extends AppModel {
	
	
	var $name = 'Media';
	
	var $actsAs = array('Encoders.Encodable');
	
	var $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id'
		)
	);
	
	
}//class{}