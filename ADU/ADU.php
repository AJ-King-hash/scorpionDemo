<?php

namespace ADU;

use PDO;
// require_once dir(__DIR__)."/Classifier/Classifier.php";
class PersonException  extends \Exception
{
    protected  $message = "";
    public static function EmptyInput()
    {
        // self::$message="EmptyInput";
        return "empty Input";
    }
    public static function ErrorExecute()
    {
        // self::$message="ExecutedError";
        return "executedError";
    }
}
/**
 * Summary of 
 * Easy CRUD Ways for FuzzySystems
 */
class ADU
{

    public static ?PDO $pdo=null;
    public function __construct() {
        if(static::$pdo==null) {
            static::$pdo=new PDO('mysql:host=localhost;dbname=fuzzyDesign','root','');

        }
    
    }
    public static function pdo() {
        return static::$pdo;
    }
    
    public static function equality(array $conditions)
    {
        $equals = [];
        $whats = [];
        foreach ($conditions as $condition) {
            if (is_array($condition)) {
                $equals[] = $condition[0] . '=' . $condition[1];
            }
            if (!is_array($condition)) {
                $whats[] = $condition . '=' . '?';
                $equals[] = $condition . '=' . '?';
            }
        }
        $EQ = array_reduce($equals, fn($acc, $curr) => $acc . $curr . ' AND ', ' WHERE ') . ')s';
        // $total=array_reduce($invoiceItem,fn($sum,$item)=>$sum+$item['price']*$item['qty'],100);

        $EQU['select'] = explode('AND )', $EQ)[0];
        //
        $EQ2 = array_reduce($whats, fn($acc, $curr) => $acc . $curr . ' AND ', ' SET ') . ')s';
        $EQU['update'] = explode('AND )', $EQ2)[0];
        return $EQU;
    }



    public function Add($setVALUE, array $datas, string $table, ?array $execCondition = null, ?string $page = null, ?string $avatar = null)
    {
        echo "dsdf";
        // include(__DIR__ . "/../connection.php");
        $addData = [];
        if (isset($setVALUE)) {

            if ($execCondition == null) {

                foreach ($datas as $data) {
                    $check = !empty($_POST[$data]) ? 1 : 0;
                    if ($check == 0) {
                        // echo "f";
                        $msg = PersonException::EmptyInput();
                        //    header("Location:".$page.".php?msg=".$msg);

                    }
                    // echo "d";    
                    if ($data == "avatar") {
                        $$data = $avatar;
                        $addData[] = $$data;
                    } else {
                        $$data = trim($_POST[$data]);
                        $addData[] = $$data;
                    }
                }
                if (in_array("email", $datas)) {
                    $key = array_search("email", $addData);
                    if (empty($addData[$key + 1])) {
                        $msg = "empty email";
                        header("Location:" . $page . ".php?msg=" . $msg);
                    }

                    $likes = $this->Show(1, ['email'], ["users"], null, null, ["email", $addData[$key + 1]]) != null ?? ($this->Show(1, ['email'], ["users"], null, null, ["email", $addData[$key + 1]]) != null ?? ($this->Show(1, ['email'], ["users"], null, null, ["email", $addData[$key + 1]]) != null ?? null));


                    if ($likes != null) {
                        $msg = "email Already exists";
                        header("Location:" . $page . ".php?msg=" . $msg);
                    }
                    if (in_array("pwd2", $datas)) {
                        $key1 = array_search("pwd", $datas);
                        $key2 = array_search("pwd2", $datas);

                        if ($addData[$key1] != $addData[$key2]) {
                            $msg = "password didn't Match";
                            header("Location:" . $page . ".php?msg=" . $msg);
                        }
                    }
                }

                // echo in_array("pwd2",$datas)?1:"fddf";
                array_pop($addData);
                array_pop($datas);
            }

            $col = array_reduce($datas, fn($acc, $curr) => $acc . $curr . ',', '(') . ')';
            $colu = explode(',)', $col)[0];
            $columns = $colu . ')';

            $wha = array_reduce($datas, fn($acc, $curr) => $acc . ($curr ? '?' : '') . ',', '(') . ')';
            $what = explode(',)', $wha)[0];
            $whats = $what . ')';

            if (in_array("email", $datas)) {
                if (!empty($addData[$key + 1]) && $likes == null) {
                    $msg = 'added Successfully !';
                    $stmt = static::$pdo->prepare("INSERT INTO $table $columns VALUES $whats");
                    echo "INSERT INTO $table $columns VALUES $whats";
                    $stmt->execute($execCondition != null ? $execCondition : $addData);
                    if ($page != null) {
                        header("Location:" . $page . ".php?msg=" . $msg);
                    }
                }
            } else {

                $msg = 'added Successfully !';
                // print_r($execCondition);
                $stmt = static::$pdo->prepare("INSERT INTO $table $columns VALUES $whats");
                echo  "INSERT INTO $table $columns VALUES $whats";
                $stmt->execute($execCondition != null ? $execCondition : $addData);
                if ($page != null) {
                    header("Location:" . $page . ".php?msg=" . $msg);
                }
            }
        }
    }

    public function Delete($deleteId, string $table, array $colCond, ?string $page = null)
    {

        $id = $deleteId;
        if (isset($id)) {
            // include(__DIR__ . "/../connection.php");

            $selects = self::equality($colCond)['select'];
            // echo "DELETE FROM $table where $selects";
            $stmt = static::$pdo->prepare("DELETE FROM $table $selects");
            if ($page != null) {
                if (!($stmt->execute())) {
                    $msg =  PersonException::ErrorExecute();
                } else {
                    $msg = "Deleted Successfully";
                }
                header("Location:" . $page . ".php?msg=" . $msg);
            }
        }
    }
    public function Show($selectId, array $selectedValues, array $tables, ?array $colCond = null, ?array $execCondition = null, ?array $like = null, ?string $limit = null)
    {
        $id = $selectId;
        $lim = ($limit != null) ? $limit : "";
        if (isset($id)) {
            // include(__DIR__ . "/../connection.php");
            $sel = array_reduce($selectedValues, fn($acc, $curr) => $acc . $curr . ',') . ')s';
            $selected = explode(',)', $sel)[0];
            $tab = array_reduce($tables, fn($acc, $curr) => $acc . $curr . ',') . ')s';
            $table = explode(',)', $tab)[0];
            ///
            $selects = '';
            if ($colCond != null) {
                $selects = self::equality($colCond)['select'];
                //  print_r($selects);
            }
            ///
            if ($like == null) {
                // echo "SELECT $selected FROM $table $selects";
                $stmt = static::$pdo->prepare("SELECT $selected FROM $table $selects $lim");
                if ($execCondition == null) {
                    $stmt->execute();
                } else {
                    $stmt->execute($execCondition);
                }
                // $theTable=$$table;
                $$table = null;
                if ($stmt->rowCount() > 1) {
                    $$table = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else {
                    $$table = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                return $$table;
            }
            if ($like != null) {
                if (count($tables) == 1) {
                    $check1 = $selects != null ? ("$selects and") : "where";
                    // echo "SELECT $selected FROM $table  $check1 $like[0] Like '$like[1]'";
                    $stmt = static::$pdo->prepare("SELECT $selected FROM $table $check1 $like[0] Like '$like[1]' $lim");
                }
                if (count($tables) > 1) {
                    $check1 = $selects != null ? ("$selects and") : "where";
                    echo "SELECT $selected FROM $table  $check1 $like[0] Like '$like[1]'";
                    $stmt = static::$pdo->prepare("SELECT $selected FROM $table $check1 $like[0] Like '$like[1]' $lim");
                }
                if ($execCondition == null) {
                    $stmt->execute();
                } else {
                    $stmt->execute($execCondition);
                }
                // $theTable=$$table;
                $$table = null;
                if ($stmt->rowCount() > 1) {
                    $$table = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
                if ($stmt->rowCount() == 1) {
                    $$table = $stmt->fetch(PDO::FETCH_ASSOC);
                }
                // echo 'f';
                return $$table;
            }
        }
    }
    public function Update($updatebutton, array $settedVal, string $table, array $colCond, ?array $execCondition = null, ?string $headerPage = null, ?int $hiddenId = null, ?array $datas = null)
    {
        $id = $updatebutton;
        $hide = $hiddenId;
        if (isset($id)) {

            if ($datas != null) {
                foreach ($datas as $data) {
                    if ($execCondition == null) {
                        $$data = trim($_POST[$data]);
                        $addUpdate[] = $$data;
                    }
                }
            }
            $up = self::equality($settedVal)['update'];
            $selects = self::equality($colCond)['select'];
            // echo "update $table $up $selects";
            // print_r($execCondition);
            $stmt = static::$pdo->prepare("update $table $up $selects");
            $stmt->execute($execCondition != null ? $execCondition : $appUpdate);
            $msg = "Updated successfully!";
            if ($headerPage != null) {
                header("Location:" . $headerPage . ".php?msg=" . $msg);
            }
        }
    }
}
