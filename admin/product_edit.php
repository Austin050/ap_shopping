<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
    header('Location: login.php');
}

// as usual accept with variables
if ($_POST) {

    if (
        empty($_POST['name']) || empty($_POST['description']) ||  empty($_POST['category']) || empty($_POST['quantity'])
        || empty($_POST['price']) || empty($_FILES['image'])
    ) {
        if (empty($_POST['name'])) {
            $nameError =  'Name Cannot be Empty';
        }
        if (empty($_POST['description'])) {
            $descriptionError =  'Description cannot be Empty';
        }
        if (empty($_POST['category'])) {
            $catError =  'Category cannot be Empty';
        }
        if (empty($_POST['quantity'])) {
            $qtyError =  'Quantity cannot be Empty';
        } elseif (is_numeric($_POST['quantity']) != 1) {
            $qtyError =  'Quantity should be integer value';
        }
        if (empty($_POST['price'])) {
            $priceError =  'Price cannot be Empty';
        } elseif (is_numeric($_POST['price']) != 1) {
            $priceError =  'Price should be integer value';
        }
        if (empty($_FILES['image'])) {
            $imgError =  'Image cannot be Empty';
        } 
    }
        else { // validation success code 

                if ($_FILES['image']['name'] != null) { // when image is updated 
                    $file = 'images/' . ($_FILES['image']['name']);
                    $imageType = pathinfo($file, PATHINFO_EXTENSION);

                    if ($imageType != 'jpg' && $imageType != 'png' && $imageType != 'jpeg') {
                        echo "<script>alert('image should be png or jpg or jpeg');</script>";
                    } else { // image validation success
                        $name = $_POST["name"];
                        $description = $_POST["description"];
                        $category = $_POST["category"];
                        $quantity = $_POST["quantity"];
                        $price = $_POST["price"];
                        $image = $_FILES["image"]["name"];
                        $id = $_POST["id"];

                        move_uploaded_file($_FILES["image"]["tmp_name"], $file);

                        $stmt = $pdo->prepare("UPDATE products SET name=:name, description=:description, 
                    category_id=:category_id, quantity=:quantity, price=:price, image=:image WHERE id=:id");
                        $result = $stmt->execute(
                            array(
                                ':name' => $name,
                                ':description' => $description,
                                ':category_id' => $category,
                                ':quantity' => $quantity,
                                ':price' => $price,
                                ':image' => $image,
                                ':id' => $id
                            )
                        );
                        if ($result) {
                            echo "<script>alert('product is added');window.location.href='index.php';</script>";
                        }
                }
            } else { // when image is not updated
                $name = $_POST["name"];
                $description = $_POST["description"];
                $category = $_POST["category"];
                $quantity = $_POST["quantity"];
                $price = $_POST["price"];
                $id = $_POST["id"];


                $stmt = $pdo->prepare("UPDATE products SET name=:name, description=:description, 
                    category_id=:category_id, quantity=:quantity, price=:price WHERE id=:id");
                $result = $stmt->execute(
                    array(
                        ':name' => $name,
                        ':description' => $description,
                        ':category_id' => $category,
                        ':quantity' => $quantity,
                        ':price' => $price,
                        ':id' => $id
                    )
                );
                if ($result) {
                    echo "<script>alert('product is updated successfully');window.location.href='index.php';</script>";
                }
            }
        }
    }
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=" . $_GET['id']);
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
                        <form action="" method="post" enctype="multipart/form-data">
                            <input name="_token" type="hidden" value="<?php echo $_SESSION['_token']; ?>">
                            <input name="id" type="hidden" value="<?php echo $result[0]['id']; ?>">


                            <div class="form-group">
                                <label for="name">Name</label>
                                <p class="text-danger"><?= empty($nameError) ? '' : '*' . $nameError; ?></p>
                                <input type="text" class="form-control" name="name" value="<?php echo escape($result[0]['name']) ?>">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <p class="text-danger"><?= empty($descriptionError) ? '' : '*' . $descriptionError; ?></p>
                                <input class="form-control" name="description" value="<?php echo escape($result[0]['description']) ?>" rows="8" cols="30"></input>
                            </div>
                            <div class="form-group">
                                <?php
                                $catsmtm = $pdo->prepare("SELECT * FROM categories");
                                $catsmtm->execute();
                                $cat_result = $catsmtm->fetchALL();
                                ?>
                                <label for="category">Category</label>
                                <p class="text-danger"><?= empty($catError) ? '' : '*' . $catError; ?></p>
                                <select name="category" class="form-control" id="category">
                                    <?php foreach ($cat_result as $value) { ?>
                                        <?php if ($value['id'] == $result[0]['category_id']) : ?>
                                            <option value="<?= $value['id'] ?>" selected><?= $value['name'] ?></option>
                                        <?php else: ?>
                                            <option value="<?= $value['id'] ?>"><?= $value['name'] ?></option>
                                        <?php endif; ?>
                                    <?php } ?>
                                </select>

                            </div>
                            <div class="form-group">
                                <label for="quanity">Quanity</label>
                                <p class="text-danger"><?= empty($qtyError) ? '' : '*' . $qtyError; ?></p>
                                <input type="number" class="form-control" name="quantity" value="<?php echo escape($result[0]['quantity']) ?>" id="quanity">
                            </div>
                            <div class="form-group">
                                <label for="price">Price</label>
                                <p class="text-danger"><?= empty($priceError) ? '' : '*' . $priceError; ?></p>
                                <input type="number" class="form-control" name="price" value="<?php echo escape($result[0]['price']) ?>" id="price">
                            </div>
                            <div class="form-image mb-3">
                                <label for="image">Image</label>
                                <p class="text-danger"><?= empty($imgError) ? '' : '*' . $imgError; ?></p>
                                <img src="images/<?= $result[0]['image'] ?>" alt="" width="200" height="150"><br>
                                <input type="file" class="" name="image" value="" id="image">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" name="" value="SUBMIT">
                                <a href="index.php" class="btn btn-warning">Back</a>
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