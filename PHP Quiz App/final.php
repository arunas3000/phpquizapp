<?php include("includes/header.php"); ?>
<?php include("includes/nav.php"); ?>
	



<div class="final-view">
<div class="jumbotron" style="text-align: center">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 start">
            
            
                <h2>You have finished your quiz</h2>
                <div class="final-field">
                <p class="final-text">Your final result is:</p>
                
                    <p class="final-result"><?php echo $_SESSION['score']; ?></p>
                    <a href="question.php?n=1" class=" btn btn-start">New Quiz</a>
                
            </div>
        </div>
    </div>
</div>
</div>



<?php include("includes/footer.php"); ?>