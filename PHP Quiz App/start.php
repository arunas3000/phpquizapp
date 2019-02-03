<?php include("includes/header.php"); ?>
<?php include("includes/nav.php"); ?>
	



	<div class="jumbotron start-bg">
		<h1 class="text-center"><?php if(logged_in()) { 
    
        echo "PHP Quizzes"; 
                    } else {
            redirect("login.php");
            }
          ?>
        
        </h1>
	</div>
<div class="jumbotron" style="text-align: center">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 start">
            
            <h2>Quick quiz</h2>
            <p class>Number of questions: <?php total_questions(); ?></p>
            <p class>Estimated time: 20 minutes</p>

            
            <a href="question.php?n=1" class=" btn btn-start">Start</a>
        </div>
    </div>
</div>




<?php include("includes/footer.php"); ?>