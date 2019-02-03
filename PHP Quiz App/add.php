<?php include("includes/header.php"); ?>
<?php include("includes/nav.php"); ?>
	
<?php
//Make sure that only administrator can access this page
if(!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 1) {
    
    redirect("start.php");
    
    die();
}
?>
<?php add_question(); ?>


<div class="add-view">
<div class="jumbotron" style="text-align: center">
    <div class="row">
        <div class="col-md-6 col-md-offset-3 start">
            
            <div class="add-field">         
                <h2>Add a question</h2>
                <?php display_message(); ?>
                <form method="post">
                     <div class="form-group">
                        <label for="question_number">Question number</label>
                        <input type="number" value="<?php next_question_number(); ?>" name="question_number" class="form-control" id="question_number" required>
                      </div>
                      <div class="form-group">
                        <label for="question_text">Question text</label>
                        <input type="text" name="question_text" class="form-control" id="question_text" required>
                      </div> 
                    <div class="form-group">
                        <label for="choice1">Choice #1</label>
                        <input type="text" name="choice1" class="form-control" id="choice1" required>
                      </div> 
                    <div class="form-group">
                        <label for="choice2">Choice #2</label>
                        <input type="text" name="choice2" class="form-control" id="choice2" required>
                      </div> 
                    <div class="form-group">
                        <label for="choice3">Choice #3</label>
                        <input type="text" name="choice3" class="form-control" id="choice3" required>
                      </div> 
                    <div class="form-group">
                        <label for="choice4">Choice #4</label>
                        <input type="text" name="choice4" class="form-control" id="choice4" required>
                      </div> 
                    <div class="form-group">
                        <label for="correct_choice">Correct choice number</label>
                        <input type="number" name="correct_choice" class="form-control" id="correct_choice" required>
                      </div> 
                     <input type="submit" name="add-submit" class="btn btn-add" value="Submit">
                </form>
                </div>   
        </div>
    </div>
</div>
</div>



<?php include("includes/footer.php"); ?>