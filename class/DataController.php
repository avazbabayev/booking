<?php
spl_autoload_register(function ($class) {
    require_once('class/' . $class . '.php');
});

class DataController
{
    function get_Bookings()
    {
        $db = DB::instance()->db;
        $sql = "Select * from bookings ";
        $parr = [];
        $bindvals = [];
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            foreach ($_POST as $key => $value) {
                switch ($key) {
                    case 'mitarbeitername':
                        $parr[] = "employee_name like :employee_name";
                        $bindvals['employee_name'] = '%' . $value . '%';
                        break;
                    case 'veranstaltung':
                        $parr[] = "event_name like :event_name";
                        $bindvals['event_name'] = '%' . $value . '%';
                        break;
                    case 'veranstaltungsdatumvon':
                        $parr[] = "event_date >= :event_date_from";
                        $bindvals['event_date_from'] = date("Y-m-d 00:00:00", strtotime($value));
                        break;
                    case 'veranstaltungsdatumbis':
                        $parr[] = "event_date <= :event_date_to";
                        $bindvals['event_date_to'] = date("Y-m-d 00:00:00", strtotime($value));
                        break;
                }
            }
            $sql = $sql . 'where ' . implode(' and ', $parr);

        }
        $statement = $db->prepare($sql);
        foreach ($bindvals as $key => $val) {
            $statement->bindValue(':' . $key, $val);
        }
        $statement->execute();
        $data = $statement->fetchAll();
        return $data;
    }

    function import_json(){

            $string = file_get_contents("data.json");
            $json = json_decode($string, true);
            $db = DB::instance()->db;
            $db->beginTransaction();
            try {
                foreach ($json as $item) {
                    $query = "INSERT INTO bookings (
                participation_id,
                employee_name,
                employee_mail,
                event_name,
                participation_fee,
                event_date,                     
                event_id,      
                version
              ) VALUES (
                :participation_id,
                :employee_name,
                :employee_mail,
                :event_name,
                :participation_fee,
                :event_date,
                :event_id,        
                :version
              )";
                    $statement = $db->prepare($query);
                    if ($this->validateItem($item['participation_id'], 'int') and
                        $this->validateItem($item['event_id'], 'int') and
                        $this->validateItem($item['participation_fee'], 'decimal') and
                        $this->validateItem($item['event_date'], 'datetime')
                    ) {

                        $statement->bindValue(':participation_id', $item['participation_id']);
                        $statement->bindValue(':employee_name', $item['employee_name']);
                        $statement->bindValue(':employee_mail', $item['employee_mail']);
                        $statement->bindValue(':event_name', $item['event_name']);
                        $statement->bindValue(':participation_fee', $item['participation_fee']);
                        $statement->bindValue(':version', $item['version']);
                        $dz = new DateZoneConverter($item['version'], $item['event_date']);
                        $statement->bindValue(':event_date', $dz->get_formatted_date());
                        $statement->bindValue(':event_id', $item['event_id']);
                        $statement->execute();
                    }else{
                        throw new Exception('false Type is given in Json');
                    }
                }
                $db->commit();
                header('Location:index.php?ms=success');
            } catch (Exception $exception) {
                $db->rollBack();
                header('Location:index.php?ms=error');
            }

    }


    function validateItem($item, $type)
    {
        switch ($type) {
            case 'datetime':
                return DateTime::createFromFormat('Y-m-d H:i:s', $item) !== false;
                break;
            case 'int':
                return filter_var($item, FILTER_VALIDATE_INT);
                break;
            case 'decimal':
                return is_numeric($item);
                break;
        }
    }

}