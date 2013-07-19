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
              <li><a href='#select-all'>Select All</a></li>
              <li><a href='#upload'>Upload</a></li>
        </ul>
      
      </div>
 
	    </div>
	  </div>
	</div>
	
	<div class="content" class="span12">
		<?php foreach($media as $item): ?>
			<?php $this->Media->display($item); ?>
		<?php endforeach; ?>
	</div>

</div>

<style>
	#MediaFileBrowser {
		background: #fff;
		border-radius: 20px;
		margin-top:30px;
	}
	
	#MediaFileBrowser .content {
		padding:30px;
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
</style>