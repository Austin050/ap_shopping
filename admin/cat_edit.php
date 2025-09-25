<?php
session_start();
require '../config/config.php';
require '../config/common.php';
if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
    header('Location: login.php');
}

// as usual accept with variables
if ($_POST) {

    if (empty($_POST['name']) || empty($_POST['description'])) {
        if (empty($_POST['name'])) {
            $nameEmpty =  'Category Name Cannot be Empty';
        }
        if (empty($_POST['description'])) {
            $descriptionEmpty =  'Content cannot be Empty';
        }
    } else {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $id = $_POST['id'];
        
        $sql = "UPDATE categories SET name=:name, description=:description WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(
            array(
                ":name" => $name,
                ":description" => $description,
                ":id"=> $id
            )
        );
        if ($result) {
            // echo "<script>alert('Successfully Added!');window.location.href='index.php';</script>";
            echo "<script>alert('Category Updated');window.location.href='category.php';</script>";
        }
    }
}
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id=".$_GET['id']);
$stmt->execute();
$result = $stmt->fetchAll();    
?>
<?php include 'header.php';
?>
<!-- Main content -->

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form action="cat_edit.php" method="post">
                            <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">

                            <div class="form-group">
                                <input type="hidden" name="id" value="<?= $result[0]['id']?>">
                                <label for="name">Name</label>
                                <p class="text-danger"><?= empty($nameEmpty) ? '' : '*' . $nameEmpty; ?></p>
                                <input type="text" class="form-control" name="name" value="<?= escape($result[0]['name'])?>">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <p class="text-danger"><?= empty($descriptionEmpty) ? '' : '*' . $descriptionEmpty; ?></p>
                                <input class="form-control" name="description" value="<?= escape($result[0]['description'])?>" rows="8" cols="80"></input>
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" name="" value="SUBMIT">
                                <a href="category.php" class="btn btn-warning">Back</a>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
include 'footer.php';
?>