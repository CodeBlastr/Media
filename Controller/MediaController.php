<?PHP
/**
 * @author <joel@razorit.com>
 * @property Media $Media
 */
class MediaController extends AppController {


	var $name = 'Media';
	#var $uid;
	#var $uses = array('');
	var $allowedActions = array('index', 'view', 'notification', 'stream');


	/*
	*	show an admin index or the videos of the current user?
	*/
	public function index() {
		$allMedia = $this->Media->find('all', array(
				'conditions' => array('Media.filename !=' => '')
			));

		$this->set('media', $allMedia);
	}//index()


	public function add() {
		#debug($this->request->data);
		#debug($this->request->params);
		if($this->request->data) {
                        $this->request->data['User']['id'] = $this->Auth->user('id');
			#debug($this->request->data);break;
			if ($this->Media->save($this->request->data)) {
				$this->Session->setFlash('Media saved and being encoded.');
                                $this->redirect('/media/media/edit/'.$this->Media->id);
			} else {
				$this->Session->setFlash('Invalid Upload.');
			}
		}

	}//upload()

        
        /**
         *
         * @param char $mediaID The UUID of the media in question.
         */
        public function edit($mediaID = null) {
            /** @todo Finish up the edit code.. put in thumbnails probably */
		if($mediaID) {
			$theMedia = $this->Media->findById($mediaID);
			$this->set('theMedia', $theMedia);
		}
        }//edit()


        /**
         *
         * @param char $mediaID The UUID of the media in question.
         */
        public function view($mediaID = null) {

		if($mediaID) {
                    $theMedia = $this->Media->findById($mediaID);
                    
                    $this->pageTitle = $theMedia['Media']['title'];
                    $this->set('theMedia', $theMedia);
		}

	}//view()


        /**
         * @todo JSON POST's to this URL do not seem to be received...
         */
	public function notification() {
		debug($this->request->data);
		if($this->request->data) {
			$this->request->data = json_decode($this->request->data, true);
#			$this->Media->notify($data);
			// zencoder is notifying us that a Job is complete
			if($this->request->data['output']['state'] == 'finished') {

				#echo "w00t!\n";

				// If you're encoding to multiple outputs and only care when all of the outputs are finished
				// you can check if the entire job is finished.
				if($this->request->data['job']['state'] == 'finished') {
					echo "Dubble w00t!\n";

					// find this zencoder_job_id
					$encoder_job = $this->Media->find('first', array('conditions' => array('Media.zen_job_id' => $this->request->data['job']['id'])));
					# TODO : allow for multiple output URL's....
					$encoder_job['Media']['filename'] = $this->request->data['output']['url'];
					$this->Media->save($encoder_job);
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

		$this->render(false);

	}//notification()

        
        /**
         * This action can stream or download a media file.
         * @param char $mediaID The UUID of the media in question.
         */
        function stream($mediaID = null) {
            
            if($mediaID) {
                // find the filetype
                $theMedia = $this->Media->findById($mediaID);

                if(!empty($theMedia['Media']['type'])) {
                    
                    /** @todo Use the Media.output maybe.. to serve other filetypes.. **/
                    if($theMedia['Media']['type'] == 'audio') {
                        $filetype = array('extension' => 'mp3', 'mimeType' => array('mp3' => 'audio/mp3'));
                    } elseif($theMedia['Media']['type'] == 'video') {
                        $filetype = array('extension' => 'mp4', 'mimeType' => array('mp4' => 'video/mp4'));
                    }

                    $this->viewClass = 'Media'; // <-- magic!
                    $params = array(
                          'id' => $mediaID . '.' . $filetype['extension'], // this is the full filename.. perhaps the one shown to the user if they download
                          'name' => $mediaID, // this is the filename minus extension
                          'download' => false, // if true, then a download box pops up
                          'extension' => $filetype['extension'],
                          'mimeType' => $filetype['mimeType'],
                          'path' => ROOT.DS.SITE_DIR.DS.'View'.DS.'Themed'.DS.'Default'.DS.WEBROOT_DIR . DS . 'media' . DS . 'streams' . DS . $theMedia['Media']['type'] . DS
                   );
                    
                   $this->set($params);
                }//if(Media.type)

            }//if(mediaID)

        }//stream()

}//class{}