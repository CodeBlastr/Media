<?PHP
/**
 * @author <joel@razorit.com>
 */
class Media extends MediaAppModel {


	public $name = 'Media';

	public $actsAs = array(
            'Encoders.Encodable' => array(
                'type' => 'Zencoder'
            ),
            'Ratings.Ratable' => array(
                'saveToField' => true,
                //'field' => 'rating'
                //'modelClass' => 'Media.Media'
            )
        );

	public $belongsTo = array(
	    'User' => array(
		'className' => 'User',
		'foreignKey' => 'user_id'
	    )
	);


    function afterRate($data) {
		#debug($data);
	}//afterRate()


}//class{}