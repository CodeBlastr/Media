<?PHP
/**
 * @author <joel@razorit.com>
 */
class Media extends MediaAppModel {


	var $name = 'Media';

	var $actsAs = array(
            'Encoders.Encodable' => array(
                'type' => 'Zencoder'
            ),
            'Ratings.Ratable' => array(
                'saveToField' => true,
                //'field' => 'rating'
                //'modelClass' => 'Media.Media'
            )
        );

	var $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id'
		)
	);


        function afterRate($data) {
            #debug($data);
            
        }//afterRate()
        
    function _generateUUID() {
        $uuid = $this->query('SELECT UUID() AS uuid');
        return $uuid[0][0]['uuid'];
    }//_generateUUID()
        
        
}//class{}