<?php 
session_start();

if(!isset($_SESSION["connected"])){ //if login in session is not set
    header("Location: http://127.0.0.1:6969/pages/login.php");
    exit();
}

$dsn = "mysql:host=localhost:3306;dbname=website";
$username = 'root';
$password = 'root';

try {
$pdo = new PDO($dsn, $username, $password);
} catch (PDOException $e) {
    echo $e->getMessage();
    die();
}

?>

<!--
Form bdd todo :
id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, userId INT NOT NULL, name VARCHAR(255) NOT NULL, content TEXT NOT NULL, done BOOLEAN NOT NULL DEFAULT FALSE
Form bdd users :
id INT PRIMARY KEY NOT NULL AUTO_INCREMENT, username VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, admin BOOLEAN DEFAULT false
Form of an todo content serialized : [["task", 1/0 (true or false)], ["task", 1/0], ["etc...", etc]]
-->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
        integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="./css/index.css">
    <title>Home</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                < To-Do's />
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
                aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
                <div class="navbar-nav">
                    <a class="nav-link" href="#">Home</a>
                    <a class="nav-link" href="#">Lists</a>
                    <a class="nav-link" href="./logout.php">Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <form method="POST">
        <ul class="list-group">
            <?php
        $userQuery = "SELECT * FROM users WHERE id =\"" . $_SESSION["uid"] . "\"";

        $resu = $pdo->prepare($userQuery);
        $resu->execute();

        $userinfos = $resu->fetchAll(mode:PDO::FETCH_ASSOC);

        foreach ($userinfos as $user) {

        if ($user["admin"] == 1) {
            $query = "SELECT * FROM todos";
        } else {
            $query = "SELECT * FROM todos WHERE userId=\"". $_SESSION["uid"]."\"";
        }

        $res = $pdo->prepare($query);
        $res->execute();

        $todos = $res->fetchAll(mode:PDO::FETCH_ASSOC);

        foreach ($todos as $list) {?>
            <li class="list-group-item">
                <?php echo $list["done"]==1?"<i class=\"fa-regular fa-circle-check\"></i>":null?> <b
                    class="title"><?= $list["name"]?></b>
                <button type="submit" class="btn btn-danger float-end mb-2 mx-1" name="deleteList-<?= $list["id"]?>"><i
                        class="fa-solid fa-trash-can"></i></button>
                <button type="submit" class="btn btn-info float-end mb-2 mx-1"
                    name="checkList-<?= $list["id"]?>-<?= $list["done"]?>"><i class="fa-solid fa-check"></i></button>
                <?php 
                    $contentArr = unserialize($list["content"]);
                    foreach ($contentArr as $content) {?>
                <p><?=$content[0]?></p>
                <?php } ?><br>
            </li>
            <?php } }?>
        </ul>

        <div class="add">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">Ajouter une
                liste</button>
        </div>

        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Ajout d'une liste</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="mb-3">
                                <label for="recipient-name" class="col-form-label">Nom de la liste :</label>
                                <input type="text" class="form-control" id="recipient-name" name="listName">
                            </div>
                            <div class="mb-3">
                                <label for="message-text" class="col-form-label">Liste des tâches :</label>
                                <h6><i>Attention ! les taches doivent être formattés tel : <code>Task 1;Task 2;Task
                                            3;Task
                                            n</code></i></h6>
                                <textarea class="form-control" id="message-text" name="listData"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-primary" name="addListModal">Confirmer</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="add">
        <button type="button" class="btn btn-info mt-1" data-bs-toggle="modal" data-bs-target="#editModal"><i
                class="fa-regular fa-pen-to-square"></i></button>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Modifier la liste </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST">
                        <div class="mb-3">
                            <select class="form-select" name="listId" aria-label="Default select example">
                                <option selected>Selectionnez une option</option>
                                <?php 
                               $query = "SELECT * FROM todos WHERE userId=\"". $_SESSION["uid"]."\"";

                               $res = $pdo->prepare($query);
                               $res->execute();
                       
                               $todos = $res->fetchAll(mode:PDO::FETCH_ASSOC);
                       
                               foreach ($todos as $list) { 
                            ?>
                                <option value="<?=$list["id"]?>">Liste : <?=$list["name"]?></option>
                                <?php
                               }
                            ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Nom de la liste :</label>
                            <input type="text" class="form-control" name="listName" id="name-id">
                        </div>
                        <div class="mb-3">
                            <label for="message-text" class="col-form-label">Contenu de la liste :</label>
                            <h6><i>Attention ! les taches doivent être formattés tel : <code>Task 1;Task 2;Task
                                            3;Task
                                            n</code></i></h6>
                            <textarea class="form-control" id="message-text" name="listData"></textarea>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" name="editBtn" class="btn btn-primary">Editer</button>
                </div>
                </form>
            </div>
        </div>
    </div>

    <?php
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (str_contains(array_keys($_POST)[0], "editList-")) {
                
            } elseif (str_contains(array_keys($_POST)[0], "deleteList-")) {
                $listId = explode("-", array_keys($_POST)[0])[1];
                $query = "DELETE FROM todos WHERE id=\"" . $listId . "\"";

                $result = $pdo->prepare($query);
                $result->execute();
                print_r("In delete");
            } elseif (str_contains(array_keys($_POST)[0], "checkList-")) {
                $listId = explode("-", array_keys($_POST)[0])[1];
                $checked = explode("-", array_keys($_POST)[0])[2];
                $isDone = 0;
                if ($checked == "0") {
                    $isDone = 1;
                }
                $query = "UPDATE todos SET done = \"". $isDone ."\" WHERE id=\"" . $listId . "\"";

                $result = $pdo->prepare($query);
                $result->execute();
            
            } elseif (str_contains(array_keys($_POST)[count(array_keys($_POST)) - 1], "editBtn")) {
                if ($_POST["listName"] != "" && $_POST["listData"] != "" && $_POST["listId"] != "") {
                    $name = $_POST["listName"];
                    $content = $_POST["listData"];
                    $listId = $_POST["listId"];

                    $finalArray = array();
                    $listDataArray = explode(";", $content);
                    foreach ($listDataArray as $data) {
                        $loopArray = array();
                        array_push($loopArray, $data, "0");
                        array_push($finalArray, $loopArray);
                    }
                
                    $formattedList = serialize($finalArray);

                    $query = "UPDATE todos SET name = :name, content = :content WHERE id= :id";

                    $datas = [
                        "name" => $name,
                        "content" => $formattedList,
                        "id" => $listId
                    ];

                    $result = $pdo->prepare($query);
                    $result->execute($datas);
                } else {
                    echo "Veuillez remplir tout les champs !";
                }

            } else {
                $listName = $_POST["listName"];
                $listData = $_POST["listData"];

                $finalArray = array();
                $listDataArray = explode(";", $listData);
                foreach ($listDataArray as $data) {
                    $loopArray = array();
                    array_push($loopArray, $data, "0");
                    array_push($finalArray, $loopArray);
                }
                
                $formattedList = serialize($finalArray);

                $query = "INSERT INTO todos (userId, name, content, done) VALUES (:uid, :name, :content, :done);";

                $datas = [
                    "uid" => $_SESSION["uid"],
                    "name" => $listName,
                    "content" => $formattedList,
                    "done" => 0
                ];

                $result = $pdo->prepare($query);
                $result->execute($datas);
            }
            echo "<meta http-equiv='refresh' content='0'>";
        }
    ?>

</body>

</html>