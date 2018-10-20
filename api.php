<?php
    session_start();
    require_once "connectDB.php";
    
    isset($_REQUEST['action']) && !empty($_REQUEST['action'])? $action = $_REQUEST['action'] : exit("Algo deu errado aqui");
    
    if($action == "read"){
        $query = "SELECT * FROM movies m INNER JOIN genres g ON m.movieGenreId = g.genreId INNER JOIN classifications c ON m.movieClassificationId = c.classificationId ORDER BY m.movieName";
        $select = mysqli_query($con, $query);
        
        header('Content-type: text/html');
        while ($res = mysqli_fetch_array($select)) {
            echo    "<article>
                        <a href='movie.php?id=".$res['movieId']."'><img src='img/".$res['movieGenreId'].".png'></a>
                        <section>
                            <h3>".$res['movieName']."</h3>
                            <span class='CG'>
                                <span>".$res['genreName']."</span>
                                <span><img src='img/".$res['classificationSimbol'].".png' width='24px'></span>
                            </span>
                            <span>".$res['movieDuration']."m</span>
                            ";
                            if(isset($_SESSION['type']) && $_SESSION['type']=='Admin'){
                                echo "<span class='opts'><a class='edit' id='".$res['movieId']."'><i id='".$res['movieId']."' class='material-icons md-light'>edit</i></a>";
                                echo                 "<a class='delete'  onclick='sure(event)' href='crud.php?id=$res[movieId]&action=del'><i class='material-icons md-light'>close</i></a>
                                    </span>";
                            };
            echo       "</section>
                    </article>";
        }
    }
    if($action == "update_r"){

        $id = $_REQUEST['id'];

        $query = "SELECT * FROM movies m INNER JOIN genres g ON m.movieGenreId = g.genreId INNER JOIN classifications c ON m.movieClassificationId = c.classificationId WHERE m.movieId = $id";
        $select = mysqli_query($con, $query);
        
        header('Content-type: text/html');
        $res = mysqli_fetch_array($select);
        
        echo "
                <img src='./img/".$res['genreId'].".png'>
                <form action='crud.php' method='post' onsubmit='validate(event)' id='form'>
                    <input type='hidden' name='mid' value='".$res['movieId']."'>
                    <input type='hidden' name='action' value='edit'>
                    <label for='mname'>
                        <h2>Title:</h2>
                        <input id='mname' type='text' placeholder='Name...' name='mname'  maxlength='300' autofocus value='".$res['movieName']."'>
                    </label>
                    <label for='mdesc'>
                        <h3>Movie Description:</h3>
                        <textarea id='desc' type='text'' placeholder='Desc...' name='mdesc' maxlength='600' >".$res['movieDesc']."</textarea>
                    </label>
                    <label for='mdur'>
                        <h3>Movie Duration:</h3>
                        <input id='mdur' type='number' placeholder='minutes' name='mdur' min='0' maxlength='9999' step='1' value='".$res['movieDuration']."'>
                    </label>
                    <label for='mclassf'>
                        <h3>Movie Classification:</h3>
                        <select name='mclassf' id='mclassf'>";
                            $query = " SELECT * FROM classifications c ORDER BY c.classificationId";
                            $options = mysqli_query($con, $query);
                            
                            while ($option = mysqli_fetch_array($options)) {
                                echo "<option value='".$option['classificationId']."'";
                                echo $option['classificationId'] == $res['movieClassificationId']?" selected>":" >";
                                echo $option['classificationSimbol']."</option>";
                            }
        echo "          </select>
                    </label>

                    <label for='mgenre'>
                        <h3>Movie Genre:</h3>
                        <select name='mgenre' id='mgenre'>".
                            $query = "SELECT * FROM genres g ORDER BY g.genreName";
                            $options = mysqli_query($con, $query);
                            
                            while ($option = mysqli_fetch_array($options)) {
                                echo "<option value='".$option['genreId']."'";
                                echo $option['genreId'] == $res['movieGenreId']? "selected":false;
                                echo ">".$option['genreName']."</option>";
                            }
        echo "          </select>
                    </label>
                    <div id='SC'>
                        <button class='save' type='button'><i class='material-icons md-light'>done</i></button>
                        <button class='close' type='button'><i class='material-icons md-light'>block</i></button>
                    </div>
                </form>";
    }
?>