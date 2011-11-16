<?PHP
/**
 * @author <joel@razorit.com>
 */
class Media extends AppModel {


	var $name = 'Media';

	var $actsAs = array(
            'Encoders.Encodable' => array(
                'type' => 'Zencoder'
            )
        );

	var $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id'
		)
	);


    function _generateUUID() {
        $uuid = $this->query('SELECT UUID() AS uuid');
        return $uuid[0][0]['uuid'];
    }//_generateUUID()
        
        
}//class{}