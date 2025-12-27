<?php
// declare(strict_types=1);
namespace Classifier;
error_reporting(E_ALL & ~E_WARNING); //display all errors but not the warning

class InvalidClassificationException extends \Exception
{

    public static function UnclassifiedData()
    {
        return new static("Unclassified data,please make sure that you have more data,otherwise,less Classifiers");
    }
    public static function OutOfMaximunTries()
    {
        return new static("Out Of Maximum tries to classify this datas");
    }
}

/* 
the "Classifier\\" is the psr-4 of the composer
the "Classifier/ is that where that directory"
 */

class ArraySorter
{
    private $values;
    private $xy;
    private $categories;

    public function __construct($values, $xy, $categories)
    {
        $this->values = $values;
        $this->xy = $xy;
        $this->categories = $categories;    
    }

    // Function to calculate the product of x * y * value
    private function calculateProduct()
    {
        $result = [];
        foreach ($this->values as $category => $items) {
            foreach ($items as $key => $value) {
                if (isset($this->xy[$key])) {
                    $x = current($this->xy[$key]);
                    $y = next($this->xy[$key]);
                    // Calculate x * y * value
                    $result[$key] = $x * $y * $value;
                }
            }
        }
        return $result;
    }

    // Sort values based on the calculated product
    public function sortArray()
    {
        $calculatedProducts = $this->calculateProduct();
        asort($calculatedProducts); // Sort calculated values in ascending order

        // Distribute sorted keys into categories dynamically
        $sortedArray = [];
        $index = 0;
        $totalItems = count($calculatedProducts);
        $numCategories = count($this->categories);
        $itemsPerCategory = intval($totalItems / $numCategories);
        $remainingItems = $totalItems % $numCategories;

        foreach ($this->categories as $category) {
            $itemCount = $itemsPerCategory + ($remainingItems > 0 ? 1 : 0);
            $remainingItems--;

            $sortedArray[$category] = array_slice($calculatedProducts, $index, $itemCount, true);
            $index += $itemCount;
        }

        // Rebuild sorted array with original values
        foreach ($sortedArray as $category => $sortedKeys) {
            foreach ($sortedKeys as $key => $calculatedValue) {
                $sortedArray[$category][$key] = $this->getValueFromOriginalArray($key);
            }
        }

        return $sortedArray;
    }

    // Helper function to retrieve original values
    private function getValueFromOriginalArray($key)
    {
        foreach ($this->values as $category => $items) {
            if (isset($items[$key])) {
                return $items[$key];
            }
        }
        return null;
    }
}




class Classifier
{
    public static int $classifiersCount = -2;
    public static Orginizer $orginiator;
    public static Caretaker $caretaker;

    public function __construct(public array $classifier = [])
    {

        self::$classifiersCount++;

    }
    public function getClassifiers()
    {
        return $this->classifier;
    }


    /**
     *
     *  
     * @var Orginizer $orginiator 
     * @var Caretaker $caretaker 
     * @param array $datas
     * @param string $classifier1
     * @param string $classifier2
     * @param string $data_primary_key
     * 
     */

    public static function create($data_primary_key, $datas, $classifier1, $classifier2)
    {
        self::$orginiator = new Orginizer($datas, $data_primary_key);
        self::$caretaker = new Caretaker();
        self::$orginiator->set($datas, $classifier1, $classifier2);
        self::$caretaker->addClassifier(self::$orginiator->storeInClassifier());
        return self::$orginiator;
    }


    public function __get($name)
    {
        if (isset(self::$$name)) {
            return self::$$name;
        }

    }

}
class Orginizer
{
    private array $classifier;
    public array $Circles;
    public int $maxTries;

    public array $currentBelongsCarries;
    public function __construct(public array $datas, public string $data_primary_key)
    {

    }
    public function set($datas, string $classifier1, string $classifier2)
    {
        $x1 = array_column($datas, $classifier1);
        $y1 = array_column($datas, $classifier2);
        foreach ($x1 as $key => $xx) {
            $x['x' . ($key + 1)] = $xx;
        }

        foreach ($y1 as $key => $yy) {
            $y['y' . ($key + 1)] = $yy;

        }

        for ($i = 1; $i < count($x) + 1; $i++) {
            if (!isset($points[$i])) {
                $points[$i] = ['x' . $i => $x['x' . $i], 'y' . $i => $y['y' . $i]];
            }
        }

        $this->classifier = $points;
    }
    public array $condoms=[];
    public function perform(array $condoms)
    {
        $this->condoms=$condoms;
        foreach ($condoms as $key => $condom) {
            foreach ($this->classifier as $kp => $point) {
                // static $i=0;    
                // if(array_reduce($point,fn($acc,$curr)=>$acc+$curr)%2==0) {

                //         $getInitial[] = 0+$i;
                //     }
                //     else {
                //         $getInitial[] = 1+$i;
                //     }
                //     $i++;
                $getInitial[] = random_int(0, 1);

            }
            $init["c" . ($key + 1)] = $getInitial;
            $currentBelongsCarries["c" . ($key + 1)] = [$this->classifier];
            $getInitial = [];
        }
        $this->currentBelongsCarries = $currentBelongsCarries;


        $result = $this->getFinal($init, $this->classifier, $init);
        $finalClassifiers = $this->getClassification($result, $condoms);

        $sorter = new ArraySorter($finalClassifiers, $this->classifier_keys, $condoms);
        // $sortedArray = $sorter->sortArray();

        // Instead of returning finalClassifiers, we return the sortedArray
        return $finalClassifiers;

    }
    public $classifier_keys = [];
    public array $PwithNames;

    public function getClassification($result, $condoms)
    {

        $d = [];
        foreach ($result as $key => $res) {
            $arr = array_column($this->datas, $this->data_primary_key);
            // sort($arr);
            // print_r($arr);
            $newResult[$key] = array_combine($arr, $res);
            foreach (array_column($this->datas, $this->data_primary_key) as $k => $r) {
                $d[$r] = [];
            }
        }
        $filter = array_combine($condoms, $newResult);
        foreach ($filter as $filterKey => $dataValues) {
            foreach ($dataValues as $k => $val) {
                array_push($d[$k], $val);
            }
        }
        // print_r($filter);
        // print_r($d);
        foreach ($d as $key => $val) {
            $dd[$key] = max($val);
        }
        // print_r($this->Circles);
        // exit;

        $nFilter = [];
        $this->classifier_keys = array_combine(array_column($this->datas, $this->data_primary_key), $this->classifier);
        foreach ($filter as $filterKey => $dataValues) {
            foreach ($dataValues as $k => $val) {
                if (array_search($dd[$k], $dataValues)) {
                    $nFilter[$filterKey][$k] = $val;

                }
            }
        }
        $sortedCircles = $this->sortCircles(array_values($condoms), $this->Circles);

        // echo "fdfsf".PHP_EOL;
        // print_r($nFilter);
        // echo "sortedCircles".PHP_EOL;
        // print_r($sortedCircles);
        // echo "distant".PHP_EOL;
        //$nFiter:is the initial Cluster with their objects
        //$sortedCircles: is the x,y coord for the circles
        //calculate the distance beteween the objects of the $nFilter and the $sortedCircels and then makeThe new Values 
        $Filter2 = $this->kMeans($nFilter, $sortedCircles, $this->PwithNames);


        // try {

            $i = 0;
            foreach ($Filter2 as $filterKey => $dataValues) {
                foreach ($Filter2 as $newk => $d) {

                    if (array_diff($dataValues, $d)) {
                        $i++;
                    }
                }
                if ($i != count($Filter2) - 1) {
                    // throw InvalidClassificationException::UnclassifiedData();
                }
                $i = 0;
            }


            return $Filter2;
        // } catch (InvalidClassificationException $e) {
        //     echo "////////////" . PHP_EOL;
        //     echo "////////////" . PHP_EOL;
        //     echo "////////////" . PHP_EOL;
        //     echo $e->getMessage() . PHP_EOL;
        //     echo "the condoms you have: " . PHP_EOL;
        //     print_r($condoms);
        //     echo "the data you have: " . count($this->datas) . PHP_EOL;
        //     echo "////////////" . PHP_EOL;
        //     echo "////////////" . PHP_EOL;
        //     echo "////////////" . PHP_EOL;
        // } finally {
        //     return $Filter2;
        // }
    }
    public function kMeans($oldFilter, $sortedCircles, $pWithNames)
    {
        $distant = [];
        $countc=1;
        foreach ($sortedCircles as $key => $circle) {
            $count = 1;
            foreach ($pWithNames as $k => $point) {
                //  echo  "sqrt(pow(".$point["x".$count]."-".$circle["xc".$countc].",2)+pow(".$point["x".$count]."-".$circle["xc".$countc].",2))".PHP_EOL;
                $distant['d' . "_" . $key . "_" . $k."_"] = number_format(sqrt(pow(number_format($point['x' . $count] - $circle['xc' . $countc], 3), 2) + pow(number_format($point['y' . $count] - $circle['yc' . $countc], 3), 2)), 2);
                $count++;
            }
            $count = 1;
            $countc++;
        }

        $cutter=[];
        $cutter2=[];
        $countc=1;
        foreach($this->condoms as $k=>$cval) {
            
            foreach(array_keys($pWithNames) as $k=>$pval) {
                foreach($distant as $kd=>$vd) {
                    if(strstr($kd,"d"."_".$cval."_".$pval."_")) {
                        $cutter[$pval][$cval]=$vd;
                        if($cutter[$pval][$cval]==min($cutter[$pval])){
                            $cutter2[$cval]=min($cutter[$pval]);

                        }
                    }
                }
            } 
        }
        // print_r($cutter);
        $finally=[];
        $finally2=[];
        foreach($cutter as $k=>$val) {
            $finally[$k]=min($val);
            foreach($val as $kc=>$vc) {
                if($finally[$k]==$vc) {
                    $finally2[$kc][$k]=0;
                }
            }
        } 
        $newFilter=[];
        foreach($oldFilter as $k=>$val) {
            foreach($finally2 as $kp=>$vp) {
                    foreach($vp as $kp2=>$vp2) {
                            if(isset($oldFilter[$k][$kp2])) {
                                    $newFilter[$kp][$kp2]=$oldFilter[$k][$kp2];
                            }
                    }
                    // print_r($newFilter);
                    // exit;
                // if(array_keys($val)[0]===array_keys($vp)[0]) {
                //         // echo "DSfd".PHP_EOL;
                //         // print_r(array_diff_key($val,$vp));
                //         // print_r($vp);
                //             // print_r($val);
                //             // print_r($vp);
                //         // echo "DSfd".PHP_EOL;
                //         $newFilter[$kp]=array_combine(array_keys($vp),array_values($val));
                //     }
                }
        }
        // print_r($newFilter);
        return $newFilter;
    }
    public function sortCircles($condoms, $OriginCircles)
    {
        
        $count = 1;
        $absDist = [];
        foreach ($OriginCircles as $key => $val) {
            $absDist[$key] = number_format(sqrt(pow($val["xc" . $count], 2) + pow($val["yc" . $count], 2)), 3);
            $count++;
        }
        asort($absDist);

        $final = [];
        foreach ($absDist as $k => $val) {
            $final[$k] = $OriginCircles[$k];
        }
        $count = 1;
        foreach ($final as $k => $val) {
            $indexes = ["xc" . $count, "yc" . $count];
            $final[$k] = array_combine($indexes, $val);
            $count++;
        }
        $count = 1;

        return array_combine($condoms, $final);
    }
    // the chaaaange isss  to make all the $curr is : $curr!="inf"?(float) $curr:1
    public function CircleCenter($Initial, $Cpoints)
    {
        foreach ($Initial as $key => $val) {


            $sumMew1 = (number_format(array_reduce($val, fn($acc, $curr) => $acc + pow($curr!="inf"?(float) $curr:1, 2)), 3)) != 0 ? number_format(array_reduce($val, fn($acc, $curr) => $acc + pow($curr, 2)), 3) : 1;



            $centerC[$key]["x$key"] = number_format(array_reduce($val, function ($acc, $curr) use ($Cpoints) {
                static $k = 1;
                $result = $acc + pow($curr!="inf"?(float) $curr:1, 2) * $Cpoints[$k]["x" . $k];
                $k++;
                return $result;
            }) / $sumMew1, 3);
            $centerC[$key]["y$key"] = number_format(array_reduce($val, function ($acc, $curr) use ($Cpoints) {
                static $k = 1;
                $result = $acc + pow($curr!="inf"?(float) $curr:1, 2) * $Cpoints[$k]["y" . $k];
                $k++;
                return $result;
            }) / $sumMew1, 3);


        }
        foreach ($centerC as $k => $circle) {

            $array[$k] = $circle;
        }
        return $array;

    }
    public array $points;
    public array $distant;
    public function getFinal($Initial, $points, $previous)
    {
        $circles = $this->CircleCenter($Initial, $points);
        $this->Circles = $circles;
        $this->points = $points;
        foreach ($circles as $key => $circle) {
            foreach ($points as $k => $point) {
                $distant['d' . explode("c", $key)[1] . $k] = number_format(sqrt(pow(number_format($point['x' . $k] - $circle['x' . $key], 3), 2) + pow(number_format($point['y' . $k] - $circle['yc' . $key], 3), 2)), 2);
            }

        }

        $this->PwithNames = array_combine(array_column($this->datas, $this->data_primary_key), $this->points);
        /**
         * 
         * Initialize the fuzzy values array
         * 
         */
        for ($i = 1; $i < count($circles) + 1; $i++) {
            for ($j = 1; $j < count($points) + 1; $j++) {
                $m['m' . $i . $j] = 0;
            }
        }
        foreach ($m as $pointsKeys => $mi) {

            foreach ($circles as $key => $circle) {
                $circleNumber = explode("c", $key)[1];

                if (strstr($pointsKeys, "m" . $circleNumber)) {
                    $currentPoint = explode("m$circleNumber", $pointsKeys)[1];
                    $mew[$pointsKeys] = number_format(pow($this->getPowToDistance($currentPoint, count($circles), $pointsKeys, $distant), -1), 3);

                }

                /* 
                1-the count circle: is to see how many divide value i should put in the mew
                2-the circleNumber: is needed to stop when this circle get all it is point that belongs to it
                3-the pointsKeys:   is to get the current points that we are handle it
                */

            }


        }
        for ($i = 1; $i <= count($circles); $i++) {
            $result["c$i"] = array();
            foreach ($mew as $key => $value) {
                if (strpos($key, "m$i") === 0) {
                    $result["c$i"][] = $value;
                }
            }
        }
        // print_r($result);
        static $counter = 0;
        //the max tries you should do instead of 200:
        // $this->maxTries=

        if (array_values($result) == array_values($previous)) {
            // print_r($this->Circles);
            return $result;

        } else {
            $counter++;

            if ($counter > 200) {
                return $result;
            }

            return $this->getFinal($result, $points, $Initial);


        }
    }

    public function getPowToDistance($currentPoint, $circles, $pointsKeys, $distant)
    {
        $sumDistance = 0;
        $val1 = $distant["d" . explode("m", $pointsKeys)[1]];
        for ($key = 1; $key <= $circles; $key++) {
            $val2 = $distant["d$key$currentPoint"];

            if ($val2 <= 0) {
                $sumDistance += 0;

            } else {
                $sumDistance += pow($val1 / $val2, 2);
            }

        }
        return $sumDistance;
    }

    public function storeInClassifier(): Classifier
    {

        return new Classifier($this->classifier);
    }
    public function restorePreviousClassifier(Classifier $classifier)
    {

        $this->classifier = $classifier->getClassifiers();
        return $this->classifier;
    }

}
class Caretaker
{
    public array $savedClassifiers = [];
    public function addClassifier(Classifier $classifier)
    {
        $this->savedClassifiers[] = $classifier;
    }
    public function getClassifier(int $index)
    {
        return $this->savedClassifiers[$index];
    }

}




?>