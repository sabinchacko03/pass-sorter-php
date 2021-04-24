<?php session_start(); ?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

    <title>Pass Sorter PHP</title>
    </head>
    <body>  
    <?php
        if($_POST){
            $json = $_POST['json'];
            if($json == '')
                $_SESSION['error'] = 'Please Enter A JSON';
            else{
                $input = json_decode($json);
                if(json_last_error() == JSON_ERROR_NONE){
                    shuffle($input);        // ------- Shuffle the input list
                    $output = sortFunction($input);
                }else{
                    $_SESSION['error'] = 'Please Enter A Valid JSON';
                }                
            }
        }
    
    ?>
        <div class="container mt-5">
            <div class="row">
                <div class="col-md-6 offset-md-3">
                    <form action="" method="post">
                        <h1 class="display-4 text-center">Pass Sorter</h1>
                        <?php if(isset($_SESSION['error']) && $_SESSION['error'] != ''){ ?>
                            <div class="alert alert-danger">
                                <?= $_SESSION['error'] ?>
                            </div>
                            <?php $_SESSION['error'] = ''; ?>
                        <?php } ?>

                        <?php if(isset($output)){ ?>
                            <h4 class="text-success text-center">Here is the Correct Route</h4>
                            <div class="mb-5">
                                <ul class="list-group">
                                    <?php foreach($output as $row){ ?>
                                        <li class="list-group-item"><?= $row ?></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        <?php } ?>
                        <div class="form-group text-center">
                            <label for="exampleFormControlTextarea1">Paste a valid JSON array</label>
                            <textarea class="form-control" name="json" required id="exampleFormControlTextarea1" rows="12"></textarea>
                            
                            <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
                            Find Route
                            </button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js" integrity="sha384-JEW9xMcG8R+pH31jmWH6WWP0WintQrMb4s7ZOdauHnUtxwoG2vI5DkLtS3qm9Ekf" crossorigin="anonymous"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  </body>
</html>

<?php    

    function sortFunction($input){
        $from = array_column($input, 'from');
        $to = array_column($input, 'to');
        $start = array_diff($from, $to);

        $sorted = [$input[key($start)]];
        $current = $input[key($start)];
        $start = $input[key($start)]->to;        
        $counter = 1;
        while(true){            
            $step = $counter++ .'. ';
            if($current->type == 'airplane'){
                $step .= 'From '. $current->from .', take '.$current->number.' to '.$current->to.'. Gate '. $current->gate.', seat '. $current->seat .'. ';

                if($current->counter){
                    $step .= 'Baggage drop at ticket counter '. $current->counter .'.';
                }else{
                    $step .= 'Baggage will be automatically transferred from your last leg.';
                }
            }else{
                if($current->type == 'bus'){
                    $step .= 'Take the '. $current->number . ' ' . $current->type;                    
                }else{
                    $step .= 'Take train '. $current->number;
                }

                $step .= ' from '.$current->from. ' to '. $current->to .'. ';
                if($current->seat){
                    $step .= 'Sit in seat '. $current->seat.'. ';
                }else{
                    $step .= 'No seat assignment.';
                }
            }                        
            $output[] = $step;

            $key = array_search($start, $from);
            if($key === false)
                break;
            $sorted[] = $input[$key];
            $start = $input[$key]->to;
            $current = $input[$key];
        }
        $output[] = $counter .'. You have arrived at your final destination.';
        return $output;
    }
?>