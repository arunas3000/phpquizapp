<?php include("includes/header.php"); ?>
<?php include("includes/nav.php"); ?>
	
<?php 
//Set default session score
if(!isset($_SESSION['score'])) {
    
    $_SESSION['score'] = 0;
} ?>

<?php set_score(); ?>

<div class="question-view">
<div class="jumbotron" style="text-align: center">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 start">
            
            <div class="current">
                <h5>Question <?php get_question_number(); ?> of <?php total_questions(); ?></h5>
                
                <p class="question-text">  <?php get_question(); ?></p>
                
                
               <form method="post">
                <fieldset>
                    <legend>Question <?php get_question_number(); ?></legend>
                    <?php get_choices(); ?>
                    
                    <input type="submit" value="submit" name="question-submit" class=" btn btn-question" >
                    <input type="hidden" name="number" value="<?php get_question_number(); ?>">
                  </fieldset>
                 </form>

                    
                    
                
            </div>
        </div>
    </div>
</div>
</div>



<?php include("includes/footer.php"); ?>