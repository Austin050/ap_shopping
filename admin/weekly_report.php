<?php
session_start();
require '../config/config.php';
require '../config/common.php';

if (empty($_SESSION['user_id']) && empty($_SESSION['logged_in'])) {
    header('Location: login.php');
}

if ($_SESSION['role'] != 1) {
    echo "<script>alert('You are not Admin. Get the hell out of here!!');window.location.href='login.php';</script>";
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
                        <h3 class="card-title"> Weekly Report</h3>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>User ID</th>
                                    <th>Total Amount</th>
                                    <th>Order Date</th>
                                </tr>
                            </thead>
                            <?php
                            $currentDate = date("Y-m-d");
                            $fromDate = date("Y-m-d", strtotime($currentDate . "+1 day"));
                            $toDate = date("Y-m-d", strtotime($currentDate . "-7 day"));
                            // to get the weekly report -> data must be between (today date and last 7 day)
                            $smtm = $pdo->prepare("SELECT * FROM sale_orders WHERE order_date<:fromDate AND order_date>=:toDate ORDER BY id DESC");
                            $smtm->execute(
                                array(
                                    ":fromDate" => $fromDate,
                                    ":toDate" => $toDate
                                )
                            );
                            $result = $smtm->fetchALL();

                            ?>
                            <tbody>
                                <?php
                                if ($result) {
                                    $i = 1;
                                    foreach ($result as $value) { ?>

                                        <?php
                                        $userStmt = $pdo->prepare("SELECT * FROM users WHERE id=" . $value['user_id']);
                                        $userStmt->execute();
                                        $userResult = $userStmt->fetchAll();
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo escape($userResult[0]['name']) ?></td>
                                            <td><?php echo escape($value['total_price']) ?></td>
                                            <td><?php echo escape(date("Y-m-d", strtotime($value['order_date']))) ?></td>
                                        </tr>
                                <?php
                                        $i++;
                                    }
                                }
                                ?>
                            </tbody>
                        </table><br>
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
<?php include('footer.html') ?>

<script>
    $(document).ready(function() {
        $('#d-table').DataTable();
    });
</script>