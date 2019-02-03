<?php


function get_question() {
    
     if($_SERVER['REQUEST_METHOD'] == 'GET') {
          if(isset($_GET['n'])) {
              
              //Set question number
            $number = clean($_GET['n']);

            $sql = "SELECT * FROM questions WHERE question_number = '$number'";

            $result = query($sql);
            
             confirm($result);
            
            $question = fetch_array($result);
            
            
            echo $question['question_text'];

          }
     }
}


function get_choices() {
     if($_SERVER['REQUEST_METHOD'] == 'GET') {
          if(isset($_GET['n'])) {
              
              //Set question number
            $number = clean($_GET['n']);

            $sql = "SELECT * FROM choices WHERE question_number = '$number'";

            $choices = query($sql);
            
             confirm($choices);
            
             while ($row = fetch_array($choices)) {
                 
                  echo "<label>
                        <input type='radio' value='" . $row['id'] . "' name='choice' > <span>" . $row['choice_text'] . "</span>
                    </label>";
                  
             }

          }
     }
}

function set_score() {
    if(isset($_POST['question-submit'])) {
    
        //Get the number from the hidden input
    $number = clean($_POST['number']);
    
    //Check which answer where selected
    $selected_choice = clean($_POST['choice']);
    
    $next = $number + 1;
    
    //Reset the score if the new quiz is started
    if($number == 1) { 
        
        $_SESSION['score'] = 0; 
        
    }
    
    //Get correct choice from the db
    $sql = "SELECT * FROM choices WHERE question_number = '$number' AND is_correct = 1";
    
    $result = query($sql);
    
    $row = fetch_array($result);
    
    //Get the id of the correct score
    $correct_choice = $row['id'];
    
    //Compare the selected answer and add the score to the total score
    //The selected choice is the value we get from the checkbox input which is row['id'] as well.
    if($selected_choice == $correct_choice) {
        
        $_SESSION['score']++;
    }  
    
        //Check it's the last question. TODO will be 20 questions
    if($number == 5){

        redirect("final.php");

    } else {
        redirect("question.php?n=" . $next);
        
    }
}
}

function get_question_number() {
    if($_SERVER['REQUEST_METHOD'] == 'GET') {
          if(isset($_GET['n'])) {
              
            $number = clean($_GET['n']);
            echo $number;
          }
    }
}


function add_question() {
    
    
     if(isset($_POST['add-submit'])) {
         
         $question_number = clean($_POST['question_number']);
         $question_text = clean($_POST['question_text']);
         
         //Put choices into associative array
         $choices = array();
         $choices[1] = clean($_POST['choice1']);
         $choices[2] = clean($_POST['choice2']);
         $choices[3] = clean($_POST['choice3']);
         $choices[4] = clean($_POST['choice4']);
         
         $correct_choice = clean($_POST['correct_choice']);
         
       
     
     $sql = "INSERT INTO questions(question_number, question_text) 
       VALUES('$question_number','$question_text')";
     
     $result = query($sql);
     confirm($result);
        
     
     foreach ($choices as $choice => $answer) {
         if($answer !=''){
             //Check if selected choice is equal to current iteration's array's key
             if($correct_choice == $choice) {
                 //Set the correct answer
                 $is_correct = 1;
         } else {
             $is_correct = 0;
         }
         
         
         
        $sql2 = "INSERT INTO choices(question_number, is_correct, choice_text)
        VALUES('$question_number', '$is_correct', '$answer' )";
        
        $result2 = query($sql2);
        
        confirm($result2);
     }
        
    }
        
        set_message("<p class='bg-success text-center'>Your question has been added</p>");
        
    }
}

function next_question_number() {
    $sql = "SELECT * FROM questions";
    
    $result = query($sql);
    
    confirm($result);
    
    $total = mysqli_num_rows($result);
    
    $next = $total + 1;
    
    echo $next;
     
}

function total_questions() {
    $sql = "SELECT * FROM questions";
    
    $result = query($sql);
    
    confirm($result);
    
    $total = mysqli_num_rows($result);
    
    echo $total;
     
}