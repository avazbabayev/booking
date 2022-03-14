<?php
spl_autoload_register(function ($class) {
    require_once('class/' . $class . '.php');
});

$dataController = new DataController();

if ($_SERVER['REQUEST_METHOD'] == 'POST' and isset($_POST['import_json'])) {
    $dataController->import_json();
}
$data = $dataController->get_Bookings();
$datevon = (isset($_POST['veranstaltungsdatumvon'])) ? date('Y-m-d', strtotime($_POST['veranstaltungsdatumvon'])) : date('Y-m-d', strtotime('2018-01-01'));
$datebis = (isset($_POST['veranstaltungsdatumbis'])) ? date('Y-m-d', strtotime($_POST['veranstaltungsdatumbis'])) : date('Y-m-d', strtotime("+1 week"));
$pageTitle = 'Booking';
include_once('includes/head.php');
?>
    <div class="row">
        <div class="container ">
            <div class="col-md-11 mx-auto">
                <div class="row">
                    <form method="post">
                        <input type="hidden" name="import_json">
                        <input type="submit" value="Json Load">
                    </form>
                    <div class="col-md-12 col-xs-12 text-center">
                        <a href="./index.php"><h2 class="heading-section">Buchungen</h2></a> <br>
                        <?php
                        if (isset($_GET['ms'])) {
                            if ($_GET['ms'] == 'success') {
                                echo '<div class="alert alert-success">Json ist hochgeladen  </div>';
                            } elseif ($_GET['ms'] == 'error') {
                                echo '<div class="alert alert-danger">Fehler beim Hochladen</div>';
                            }
                        }
                        ?>
                        <br>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 col-xs-12">

                        <div class="col-md-4">
                            <form name="booking" method="post">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="Mitarbeitername">Mitarbeitername</label>
                                            <input type="text" name="mitarbeitername" class="form-control"
                                                   value="<?php echo (isset($_POST['mitarbeitername'])) ? $_POST['mitarbeitername'] : ''; ?>"
                                                   id="Mitarbeitername"
                                                   placeholder="Mitarbeitername">
                                        </div>
                                        <div class="form-group">
                                            <label for="Veranstaltung">Veranstaltung</label>
                                            <input type="text" name="veranstaltung" class="form-control"
                                                   value="<?php echo (isset($_POST['veranstaltung'])) ? $_POST['veranstaltung'] : ''; ?>"
                                                   id="Veranstaltung"
                                                   placeholder="Veranstaltung">
                                        </div>
                                    </div>
                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label for="veranstaltungsdatumvon">Veranstaltungsdatum Von</label>
                                            <input type="date" name="veranstaltungsdatumvon" class="form-control"
                                                   id="veranstaltungsdatumvon"
                                                   value="<?php echo $datevon; ?>"
                                                   placeholder="Veranstaltungsdatum">
                                        </div>
                                        <div class="form-group">
                                            <label for="veranstaltungsdatumbis">Veranstaltungsdatum Bis</label>
                                            <input type="date" name="veranstaltungsdatumbis" class="form-control"
                                                   id="veranstaltungsdatumbis"
                                                   value="<?php echo $datebis; ?>"
                                                   placeholder="Veranstaltungsdatum">
                                        </div>
                                    </div>
                                </div>

                                <br>
                                <button type="submit" class="btn btn-primary">Filter</button>
                            </form>
                        </div>
                        <br>
                        <div class="table-wrap">
                            <table class="table">
                                <thead class="thead-primary">
                                <tr>
                                    <th>#</th>
                                    <th>Mitarbeitername</th>
                                    <th>Email</th>
                                    <th>Veranstaltung</th>
                                    <th>Veranstaltungsdatum</th>
                                    <th>Teilnahmegebühr</th>
                                    <th>Erstellt</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if ($data and count($data) > 0) {
                                    $c = 0;
                                    foreach ($data as $item) {
                                        $c++;
                                        echo "<tr>";
                                        echo "<td>" . $c . "</td>";
                                        echo "<td>" . $item['employee_name'] . "</td>";
                                        echo "<td>" . $item['employee_mail'] . "</td>";
                                        echo "<td>" . $item['event_name'] . "</td>";
                                        echo "<td>" . $item['event_date'] . "</td>";
                                        echo "<td>" . $item['participation_fee'] . "</td>";
                                        echo "<td>" . $item['created'] . "</td>";
                                        echo "</tr>";
                                    }
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php

include_once('includes/foot.php');
