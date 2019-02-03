<nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">PHP Quizzes</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            
            
           <!--Show logout only if logged in session is started-->
            <?php if(logged_in()) {
               if($_SESSION['user_level'] != 1) {
                   echo " 
                       <li><a href='start.php'>New Quiz</a></li> 
            <li><a href='logout.php'>Log Out</a></li>";
               } else {
                    echo " 
            <li><a href='start.php'>New Quiz</a></li>
            <li><a href='add.php'>Add Questions</a></li>
            <li><a href='logout.php'>Log Out</a></li>";
               }
               }  ?>
           
            
      
           
              
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>