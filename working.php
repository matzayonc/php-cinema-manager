<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezerwanotron</title>
    <style>
        td {
            border: 2px solid black;
            text-align: center;
        }
        .seat{
            width: 25px;
            height: 25px;
            background-color: #1AE54D;
        }
        .taken{
            width: 25px;
            height: 25px;
            background-color: red;
        }
        
    </style>
</head>
<body>
    <p>Rezerwanotron</p>
    <a href="http://localhost">Zacznij od nowa</a>
    <br/><br/>
<?php

    $conn = new mysqli("localhost", "root", "", "cinema");

    if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
    


    function getSelectMovies() {

        $result = "<form>
            <select name=\"seance\" onchange=\"this.form.submit()\">
                <option value=\"\" selected disabled hidden>Wybór seansu</option>";

        
        global $conn;
        $res =  $conn->query('select * from seances');


        if ($res->num_rows > 0) {
            while($record = $res->fetch_assoc()) {
                $result .= "<option value=\"" . $record["id"] . "\">" . $record["movie"]. " " . $record["time"]. "</option>";
            }
        } else {
            die("errrrrrrrrr");
        }

        return $result;
    } 


    if(!isset($_GET["seance"])){
        echo getSelectMovies();
    }
    else if(!isset($_GET["seat"])){

        
        $res =  $conn->query("SELECT seat FROM `reservations` WHERE movieId=" . $_GET["seance"]);
        $taken = [];

        if ($res->num_rows > 0) {
            while($record = $res->fetch_assoc()) {
                $taken[] = $record['seat'];
            }
        }


        echo getSelectMovies();
        echo "<form><input type=\"hidden\" name=\"seance\" value=\"".$_GET["seance"]."\">";
        echo "<table><tr><th colspan=\"20\">Miejsca</th><tr><th></th>";

        for($i = 1; $i < 21; $i++)
            echo "<td>".$i."</td>";
        echo "</tr>";

        for($i = 1; $i < 16; $i++){
            echo "<tr><td>".$i."</td>";

            for($j = 1; $j < 21; $j++){
                $id = ($i-1)*20+$j;
                $class = "seat";

                if(in_array($id, $taken))
                    $class = "taken";


                echo "<td class=\"".$class."\"><input 
                        name=\"seat[]\" 
                        value=\"".$id."\"
                        type=\"checkbox\"></td>";
            }


            echo "</tr>";
        }     

        echo "</tr></table><br/><input value=\"Rezerwuj\" type=\"submit\"/></form>";
    }
    else if(!isset($_GET["name"]) || !isset($_GET["phone"])){
        $seats = $_GET["seat"];

        echo "<form><input type=\"hidden\" name=\"seance\" value=\"".$_GET["seance"]."\"/>";

        for($i = 0; $i < count($seats); $i++)
            echo "<input type=\"hidden\" name=\"seat[]\" value=\"".$seats[$i]."\"/>";


        echo "Imię:<br/><input type=\"text\" name=\"name\"/><br/><br/>";
        echo "Numer telefonu:<br/><input pattern=\"[0-9]{3} [0-9]{3} [0-9]{3,4}\" type=\"tel\" name=\"phone\"/><br/><br/>";

        echo "<input type=\"submit\" value=\"Zarezerwuj\">";

        echo "</form>";
    }
    else{
        
        $seats = $_GET["seat"];

        for($i = 0; $i < count($seats); $i++){

            $sql = "INSERT INTO reservations(movieId, seat, name, number) VALUES (".$_GET['seance'].", ".$seats[$i].", '".$_GET['name']."' , '".$_GET['phone']."')";
            echo $sql;
            

            if ($conn->query($sql) === TRUE)
                echo "New record created successfully";
            else 
                echo "Error: " . $sql . "<br>" . $conn->error . "<br/><br/>";
            
            
            echo $seats[$i];
        }
    }

    $conn->close(); 
?>
    
</body>
</html>