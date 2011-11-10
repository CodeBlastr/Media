<?PHP
class MediaController extends AppController {
	
	
	var $name = 'Media';
	#var $uid;
	#var $uses = array('');
	var $allowedActions = array('view', 'notification');
	
	
	/*
	*	show an admin index or the videos of the current user?
	*/
	public function index() {
		$this->Media->paginate();
	}//index()
	
	
	public function add() {
		
		if($this->request->data) {
			#debug($this->request->data);break;
			if ($this->Media->save($this->request->data)) {
			
			} else {
				$this->flash(__('Invalid Upload.', true), array('action'=>'add'));
			}
		}
		
	}//upload()
	
	
	public function view($mediaID = null) {
		
		if($mediaID) {
			$the_media = $this->Media->findById($mediaID);
			$this->set('the_media', $the_media);
		}
		
	}//view()
	
	
	public function notification($outputID = null) {
		
		if($outputID) {
#			$this->Media->notify($data);
			// zencoder is notifying us that a Job is complete
			if($this->request->data['output']['state'] == 'finished') {
				
				echo "w00t!\n";
				
				// If you're encoding to multiple outputs and only care when all of the outputs are finished
				// you can check if the entire job is finished.
				if($this->request->data['job']['state'] == 'finished') {
					echo "Dubble w00t!\n";
				}
				
			} elseif($this->request->data['output']['state'] == 'cancelled') {
				echo "Cancelled!\n";
			} else {
				echo "Fail!\n";
				debug($this->request->data);
				echo $this->request->data['output']['error_message']."\n";
				echo $this->request->data['output']['error_link'];
			}
		}//if($outputID)
		
	}//notification()
	
	
}//class{}