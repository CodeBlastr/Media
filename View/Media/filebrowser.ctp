<div id="MediaFileBrowser" class="row-fluid">
	<div class="navbar">
  		<div class="navbar-inner">
    		<div class="container">
 
      <!-- .btn-navbar is used as the toggle for collapsed navbar content -->
      <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
      </a>
 
      <a class="brand" href="#">Media Browser</a>
 
      <!-- Everything you want hidden at 940px or less, place within here -->
      <div class="nav-collapse collapse">
      	
      	<ul class="nav">
              <li><a href='#mediaBrowser' class="active">File Browser</a></li>
              <li><a href='#mediaUpload'>Upload</a></li>
        </ul>
      
      </div>
 
	    </div>
	  </div>
	</div>
	
	<div class="content-panels">
	
		<div id="mediaBrowser" class="panel">
			<ul class="thumbnails">
			<?php foreach($media as $item): ?>
				
				<li class="span2 media-item">
					<a href="#" class="thumbnail">
						<?php echo $this->Media->display($item, array('width' => 100, 'height' => 100)); ?>
						<p style="text-align: center;"><?php echo $item['Media']['title']; ?></p>
					</a>
				</li>
				
				
			<?php endforeach; ?>	
			</ul>
		</div>
		
		<div id="mediaUpload" class="media-upload panel">
			<div class="upload-form">
			<?php
	   			 echo $this->Form->create('Media', array('plugin' => 'media', 'controller' => 'media', 'action' => 'add'), array('type' => 'file'));
				
				 echo $this->Form->input('Media.filename', array('type'=>'file', 'label' => 'Upload a file from your computer:')); // , 'accept' => 'audio/* video/*'
				
				 //echo $this->Form->input('Media.submittedurl', array('type'=>'text', 'label' => 'Alternatively enter the URL of a file that is already online:'));
				
				 echo $this->Form->input('Media.title', array('type'=>'text', 'label' => 'Title:'));
				
				 echo $this->Form->input('Media.description', array('type'=>'textarea', 'label' => 'Description:'));
	
	    		echo $this->Form->end('Submit');
	    ?>
	    	</div>
		</div>
		
	</div>
	<div class="loader"></div>
</div>

<style>
	#MediaFileBrowser {
		background: #fff;
		border-radius: 20px;
		margin-top:30px;
		position: relative;
	}
	
	#MediaFileBrowser .content-panels {
		padding:10px;
		min-height:500px;
	}
	
	#MediaFileBrowser .content-panels .panel {
		display: none;
	}
	
	#MediaFileBrowser .navbar .nav > li > a {
	    color: #777777;
	    float: none;
	    padding: 10px 15px;
	    text-decoration: none;
	    text-shadow: 0 1px 0 #FFFFFF;
	}
	#MediaFileBrowser .navbar .brand {
	    color: #777777;
	    display: block;
	    float: left;
	    font-size: 14px;
	    font-weight: 200;
	    margin-left: -20px;
	    padding: 10px 20px;
	    text-shadow: 0 1px 0 #FFFFFF;
	}
	
	#MediaFileBrowser #mediaUpload .upload-form {
		margin: 0 auto;
		text-align:center;
	}
	#MediaFileBrowser .loader {
		background: url(/Media/ajax-loader.gif) no-repeat;
		width: 66px;
		height: 66px;
		top: 45%;
		left: 45%;
		position: absolute;
	}
	#MediaFileBrowser .selected {
		background: #00005F;
	}
</style>
<script type="text/javascript" src="/js/plugins/jquery.form.min.js"></script>
<script type="text/javascript" src="/Media/filebrowser.js"></script>