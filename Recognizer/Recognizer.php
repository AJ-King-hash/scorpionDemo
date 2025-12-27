<?php

namespace Recognizer;
use Chart\Chart;
use Chart\makeChart;
use Doctrine\Common\Collections\ArrayCollection;
use Neurals\Adapter\Neurals;
error_reporting(E_ALL & ~E_WARNING);
require_once __DIR__ . "/../vendor/autoload.php";


class InvalidRecognizeException extends \Exception
{

    public static function InvalidControlTableDimension()
    {
        return new static("the dimension of this array should be : (trianglesOfSystem1*trianglesOfSystem2)");
    }
    // public static function OutOfMaximunTries() {
    //     return new static("Out Of Maximum tries to classify this datas");
    // }
}

trait classifierParts
{
    public const LOW = "Low";
    public const Medium = "Medium";
    public const High = "High";
    public const BelongPerc = [
        self::LOW => [0, 0.250, 0.500],
        self::Medium => [0.250, 0.500, 0.750],
        self::High => [0.500, 0.750, 1.000]
    ];

}
interface FuzzySystem
{
    public function fuzzyLvlX(FuzzyDirector $newBuilder);
    public function getBuilder();
    /**
     * return the final result of the designed system
     * @return fuzzyPlan
     */
    public function Recognize();
}
class FuzzyLvl1 implements FuzzySystem
{
    public FuzzyDirector $builder;
    public function fuzzyLvlX(FuzzyDirector $newBuilder)
    {
        $this->builder = $newBuilder;
        return $this;
    }
    public function getBuilder()
    {
        return $this->builder;
    }
    public function Recognize()
    {
        return $this->builder->getCurrentFuzzy();
    }
}

interface Command
{
    public function enable(FuzzyDirector $builderName);
}
class EnableFuzzyLvl1 implements Command
{

    public function __construct(private FuzzySystem $theSystem = new FuzzyLvl1())
    {

    }
    public function enable(FuzzyDirector $builderName)
    {

        return $this->theSystem->fuzzyLvlX($builderName);
    }

}

/**
 * Summary of EnableButton
 * @method FuzzyDirector getBuilder()
 * @method void Recognize()
 
 */
class EnableButton
{
    public Command $command;
    public FuzzyDirector $FuzzyDirector;
    public function __construct(public string $commander = "Lvl1")
    {
        $this->command = match ($commander) {
            "Lvl1" => new EnableFuzzyLvl1(),
            default => ""
        };
    }
    /**
     * Summary of pressLvl1
     * @param FuzzyDirector $Director
     * @return FuzzySystem
     */

    public function pressLvlx(FuzzyDirector $Director)
    {
        $this->FuzzyDirector = $Director;

        return $this->command->enable($this->FuzzyDirector);
    }
    public function __call($method, $args)
    {
        return $method(...$args);
    }
    public function getDirector()
    {
        return $this->FuzzyDirector;
    }
    public function getCommand()
    {
        return $this->command;
    }
}
interface FuzzyPlan
{
    public function study(array $datas, string $data1, string $data2, string $determined_id);
    public function setSystem1(string $systemName, array $system_info);
    public function setSystem2(string $systemName, array $system_info);
    public function setControlTable(array $controlTable);
    public function getResult();
    /**
     * get all the information of this system,including the output "final_result"
     * @return array
     */
    public function get();

    /**
     * Convert the hard output to the fuzzyOutput
     * this can be used when you apply a fuzzyLvl2 System and you need to be an input fuzzy values
     * @return static
     */
    public function fuzzy_output();
}
class Fuzzy1 extends makeChart implements FuzzyPlan
{
    private string $systemName1;
    private array $system_info1;
    private string $systemName2;
    private array $system_info2;
    private array $table;
    private string $determined_id;
    private int $col_data1;
    private int $col_data2;
    private array $datas;

    private array $rows;
    private array $oldTable;
    private array $columns;

    private array $finalResult;
    public function study(array $datas, string $data1, string $data2, string $determined_id)
    {
        $this->datas = $datas;
        $indexes = array_keys($this->datas);
        $this->determined_id = $indexes[array_search($determined_id, $indexes)];
        $this->col_data1 = $datas[$data1];
        $this->col_data2 = $datas[$data2];
    }
    public function setSystem1(
        string $systemName,
        array $system_info
    ) {



        $this->systemName1 = $systemName;
        $this->system_info1 = $system_info;
        foreach ($this->system_info1 as $key => $comRange) {

            $Range = range($comRange[0], end($comRange), 1);
            if (in_array($this->col_data1, $Range)) {
                $YComVals[$this->determined_id][$key] = $this->getYValuesForXPart($this->col_data1, $this->makeRangesForLines($comRange));
            }
        }
        $this->rows = $YComVals;
    }
    public function setSystem2(
        string $systemName,
        array $system_info
    ) {
        $this->systemName2 = $systemName;
        $this->system_info2 = $system_info;

        // print_r($system_info);
        // echo "fsdfdf ".$this->col_data2.PHP_EOL;

        foreach ($this->system_info2 as $key => $comRange) {
            // print_r($this->system_info2);
            // exit;
            $Range = range($comRange[0], end($comRange), 1);

            // print_r($Range);              
            // echo "is in rang?: ".in_array($this->col_data2,$Range).PHP_EOL;

            if (in_array($this->col_data2, $Range)) {
                $YComVals[$this->determined_id][$key] = $this->getYValuesForXPart($this->col_data2, $this->makeRangesForLines($comRange));
            }
            // print_r($this->makeRangesForLines($comRange));
            // exit;
        }
        // print_r($YComVals);
        // exit;

        $this->columns = $YComVals;

    }
    public function setControlTable(array $controlTable)
    {
        $this->oldTable = $controlTable;
        foreach ($controlTable as $control) {
            $s += count($control);
        }

        if ($s != count($this->system_info1) * count($this->system_info2)) {
            throw InvalidRecognizeException::InvalidControlTableDimension();
        }
        $info1_keys = array_keys($this->system_info1);
        $info2_keys = array_keys($this->system_info2);
        // if(count($this->system_info1)*count($this->system_info2)!=)
        for ($i = 0; $i < count($this->system_info1); $i++) {
            for ($j = 0; $j < count($this->system_info2); $j++) {
                $controlTableNew[$info1_keys[$i]][$info2_keys[$j]] = $controlTable[$i][array_keys($controlTable[$i])[$j]];
            }
        }

        $this->table = $controlTableNew;
    }
    public function getResult()
    {
        foreach ($this->rows[$this->determined_id] as $krows => $rvals) {
            foreach ($this->columns[$this->determined_id] as $kcols => $cvals) {
                foreach ($this->oldTable as $row) {
                    if (in_array($this->table[$krows][$kcols], $row)) {
                        $finals[array_keys($this->table[$krows][$kcols])[0]] = min($rvals, $cvals);
                        // print_r($row);
                        // echo"ff";
                        // exit;
                        $fin[] = $this->table[$krows][$kcols];
                        // print_r(array_keys($this->table[$krows][$kcols]));
                        break;
                    }
                }
            }
        }
        $groupedValues = [];
        $nonSimilarValues = [];
        // print_r($finals);
        // exit;
        foreach ($finals as $key => $value) {

            $prefix = explode('_', $key)[0];
            if (!isset($groupedValues[$prefix])) {
                $groupedValues[$prefix] = [];
            }
            $groupedValues[$prefix][$key] = $value;
        }

        // Step 2: Find the minimum value for each group
        $maxValues = [];
        foreach ($groupedValues as $prefix => $values) {

            $maxKey = array_keys($values, max($values))[0];
            $maxValues[$maxKey] = $values[$maxKey];
        }
        // Step 3: Identify non-similar values
        $allPrefixes = array_keys($groupedValues);
        foreach ($finals as $key => $value) {
            $prefix = explode('_', $key)[0];
            if (!in_array($prefix, $allPrefixes)) {
                $nonSimilarValues[$key] = $value;
            }
        }
        // Step 4: Merge the maximum values and non-similar values
        // print_r($nonSimilarValues);
        $mergedValues = array_merge($maxValues, $nonSimilarValues);
        // Step 5: Find the maximum value from the merged values
        $sum = 0;
        foreach ($fin as $k => $val) {
            $key = array_keys($val)[0];
            if (isset($val[$key])) {
                $sum += ($mergedValues[$key]) * $val[$key];
            }
        }
        // print_r(array_sum($mergedValues));
        $val = [explode("_", array_search(max($mergedValues), $mergedValues))[0] => number_format($sum / (array_sum($mergedValues)!=0?array_sum($mergedValues):1), 2)];
        $this->finalResult = $val;
        return $this;
    }
    public function fuzzy_output()
    {
        foreach ($this->finalResult as $key => $val) {
            $fuzzy_output[$key] = [$val - $val * 0.5, $val, $val + $val * 0.5];
        }
        $this->finalResult = [];
        $this->finalResult = $fuzzy_output;
        return $this;
    }
    public function get()
    {
        // echo "hi";
        return [
            "id" => $this->determined_id,
            "input_data" => $this->datas,
            "final_result" => $this->finalResult,
            "system1_name" => $this->systemName1,
            "system1_info" => $this->system_info1,
            "system2_name" => $this->systemName2,
            "system2_info" => $this->system_info2,
            "oldTable" => $this->oldTable,
            "control_table" => $this->table,
            "FuzzyLvl" => "Lv1"
        ];
    }
}

interface FuzzyBuilder
{

    public function get_study_input(array $datas, string $data1, string $data2, string $determined_id);
    public function makeSystem1(string $systemName, array $system_info);
    public function makeSystem2(string $systemName, array $system_info);

    /**
     * 
     * @param array|string $controlTable
     * the dimension of this array is should be : (trianglesOfSystem1*trianglesOfSystem2)
     * 
     * @param mixed $is_controlTable
     * 
     * this paramter should change whether you have a control table on your system or not
     * 

     * 
     */
    public function makeTable(array|string $controlTable);
    public function getSystem();
    public function get_study_output();
}
class Fuzzy1Builder implements FuzzyBuilder
{
    public Fuzzy1 $final;

    public function __construct(private FuzzyPlan $fuzzyPlan = new Fuzzy1())
    {

    }
    public function get_study_input(array $datas, string $data1, string $data2, string $determined_id)
    {
        $this->fuzzyPlan->study($datas, $data1, $data2, $determined_id);
    }
    public function makeSystem1(
        string $systemName = "system1",
        array $system_info = [
            "low" => [0, 250, 500],
            "medium" => [250, 500, 750],
            "high" => [500, 750, 1000]
        ]
    ) {
        $this->fuzzyPlan->setSystem1($systemName, $system_info);
    }
    public function makeSystem2(
        string $systemName = "system2",
        array $system_info = [
            "low_pre" => [100, 200, 300],
            "medium_pre" => [200, 400, 650],
            "high_pre" => [400, 650, 1000]
        ]
    ) {
        $this->fuzzyPlan->setSystem2($systemName, $system_info);
    }
    public function makeTable(
        array|string $controlTable = [
            [["toobad_1" => 40], ["bad_1" => 100], ["good_1" => 120]],
            [["bad_2" => 100], ["good_2" => 120], ["nice_1" => 160]],
            [["nice_2" => 200], ["perfect_1" => 300], ["perfect_2" => 500]]
        ],

    ) {
        $this->fuzzyPlan->setControlTable($controlTable);
    }
    public function getSystem()
    {
        return $this->final;
    }
    public function get_study_output()
    {
        $this->final = $this->fuzzyPlan->getResult();
    }

}


class FuzzyDirector
{
    public function __construct(private FuzzyBuilder $fuzzyBuilder)
    {

    }
    public function getCurrentFuzzy()
    {
        return $this->fuzzyBuilder->getSystem();
    }


}
class Recognizer
{
    public static ?FuzzyBuilder $fuzzyBuilder1 = null;
    private function __construct()
    {
        if (self::$fuzzyBuilder1 == null) {

            self::$fuzzyBuilder1 = new Fuzzy1Builder();
        }
    }
    /**
     * Summary of DriverRecognition
     * @param callable $RecognizerDirector
     * @return FuzzySystem
     */
    public static function DriverRecognition(string $fuzzyCommand, callable $RecognizerDirector)
    {
        $singletonRecognizer = new static();
        $fuzzyBuilder = $RecognizerDirector(match ($fuzzyCommand
        ) {
            "Lvl1" => $singletonRecognizer::$fuzzyBuilder1,
            default => ""
        });

        $fuzzyDirector = new FuzzyDirector($fuzzyBuilder);
        $enableButton = new EnableButton($fuzzyCommand);

        return $enableButton->pressLvlx($fuzzyDirector);
    }
}


?>