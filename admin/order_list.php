<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
    header('Location: login.php');
}

if($_SESSION['role'] != 1) {
    header ("Location: login.php");
}
?>
<?php include 'header.php';
?>
<!-- Main content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title"> Order Listings</h3>
                    </div>
                    <?php
                    if (!empty($_GET['pageno'])) {
                        $pageno = $_GET['pageno'];
                    } else {
                        $pageno = 1;
                    }
                    $noOfRecordsperPage = 5;
                    // offset example = if pageno is 1-> offset starts from 0 if it is 2 -> offset will start at 1
                    $offset = ($pageno - 1) * $noOfRecordsperPage;

                    $smtm = $pdo->prepare('SELECT * FROM sale_orders ORDER BY id DESC');
                    $smtm->execute();
                    $rawResult = $smtm->fetchALL();
                    // ceil formula 
                    $totalpages = ceil(count($rawResult) / $noOfRecordsperPage);

                    $smtm = $pdo->prepare("SELECT * FROM sale_orders ORDER BY id DESC LIMIT $offset,$noOfRecordsperPage");
                    $smtm->execute();
                    $result = $smtm->fetchALL();


                    ?>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <br />
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>User</th>
                                    <th>Total Price</th>
                                    <th>Order Date</th>
                                    <th style="width: 40px">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                <?php
                                // loop through and show the data from database 
                                if ($result) {
                                    $i = 1;
                                    foreach ($result as $value) { ?>
                                        <?php
                                        $usersmtm = $pdo->prepare("SELECT * FROM users WHERE id=" . $value['user_id']);
                                        $usersmtm->execute();
                                        $user_result = $usersmtm->fetchALL();
                                        ?>
                                        <tr>
                                            <td><?= $i ?></td>
                                            <td><?= escape($user_result[0]['name']) ?></td>
                                            <td><?= escape($value['total_price']) ?></td>
                                            <td><?= escape(date('Y-m-d', strtotime($value['order-date']))) ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <div class="container">
                                                        <a href="order_detail.php?id=<?= $value['id'] ?>" type="button" class="btn btn-warning">View</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table><br>
                        <!-- Card Body -->
                        <nav aria-label="Page navigation example" style="float: right;">
                            <ul class="pagination">
                                <!-- send page number data to the php code with GET method -->
                                <li class="page-item"><a href="?pageno=1" class="page-link">First</a></li>
                                <!-- becuase Previous, only 2 to last number can be navigated, but not 1 or less -->
                                <li class="page-item" <?php if ($pageno <= 1) {
                                                            echo "disabled";
                                                        } ?>>
                                    <a href="<?php if ($pageno <= 1) {
                                                    echo '#';
                                                } else {
                                                    echo "?pageno=" . ($pageno - 1);
                                                } ?>" class="page-link">Previous</a>
                                </li>
                                <li class="page-item">
                                    <a href="#" class="page-link"><?php echo $pageno; ?></a>
                                </li>
                                <li class="page-item" <?php if ($pageno >= $totalpages) {
                                                            echo "disabled";
                                                        } ?>>
                                    <a href="<?php if ($pageno >= $totalpages) {
                                                    echo '#';
                                                } else {
                                                    echo "?pageno=" . ($pageno + 1);
                                                } ?>" class="page-link">Next</a>
                                </li>
                                <li class="page-item">
                                    <a href="?pageno=<?php echo $totalpages ?>" class="page-link">Last</a>
                                </li>
                            </ul>
                        </nav>

                    </div>

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