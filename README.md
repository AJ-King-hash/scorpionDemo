# Scorpfuzzy-demo 🧠🔥

A modern, lightweight **Fuzzy Logic** library for PHP – perfect for building intelligent scoring systems, loyalty/ranking engines, or any application where decisions are based on **vague, uncertain, or partial truth**.

```bash
composer require scorpion/scorpfuzzy-demo
```
# ✨ Features

Mamdani-style Fuzzy Inference (Level 1)
Fuzzy Classification with custom class labels (Bronze, Silver, Gold, Diamond, etc.)
Fluent, chainable builder interface
Easy input/output handling
Built-in demo data & real-world example (user ratings + payments → loyalty level)
No external dependencies (pure PHP)

# 📊 Demo in Action
1. Fuzzy Logic Level 1 (Mamdani-style reasoning)
```php
PHPuse FuzzyDesign\Recognizer;

$apps = [
    ['user_id' => 2,  'rating' => 5,  'amount_paid' =>  3000],
    ['user_id' => 19, 'rating' => 1,  'amount_paid' => 22000],
    ['user_id' => 22, 'rating' => 6,  'amount_paid' =>  4000],
    ['user_id' => 4,  'rating' => 6,  'amount_paid' => 20000],
    ['user_id' => 16, 'rating' => 10, 'amount_paid' => 33000],
    ['user_id' => 9,  'rating' => 9,  'amount_paid' => 27000],
    ['user_id' => 1,  'rating' => 12, 'amount_paid' => 34000],
    ['user_id' => 3,  'rating' => 8,  'amount_paid' => 50000],
    ['user_id' => 10, 'rating' => 10, 'amount_paid' => 80000],
];

$record = Recognizer::DriverRecognition('Lvl1', function ($fuzzyBuilder) {
    $fuzzyBuilder->get_study_input([
        'data'  => ['id' => 1, 'comments' => 500, 'payments' => 400],
        'data1' => 'comments',
        'data2' => 'payments',
    ]);

    $fuzzyBuilder->makeSystem();
    $fuzzyBuilder->makeSystem2();
    $fuzzyBuilder->makeTable();
    $fuzzyBuilder->get_study_output();

    return $fuzzyBuilder;
})->get();

echo $record['final_result'];  // e.g. 120.00 (defuzzified output)
```
2. Fuzzy Classification (Loyalty / Ranking System)
```php
use Classifier\Classifier;

$classifier = Classifier::create('user_id', $apps, 'amount_paid', 'rating')
->perform(['bronze', 'silver', 'gold',"Diamond"]);
print_r($classifier);
```
# Example Output:
```php
PHPArray
(
    [bronze] => Array
        (
            [2] => 0.931
            [22] => 0.903
        )
    [silver] => Array
        (
            [19] => 0.958
            [4]  => 0.760
            [16] => 0.475
            [9]  => 0.699
        )
    [gold] => Array
        (
            [1] => 0.436
            [3] => 0.752
        )
    [Diamond] => Array
        (
            [10] => 0.895
        )
)
```

# 🚀 Installation

```Bash
composer require scorpion/scorpfuzzy-demo
```
# 📋 Requirements

PHP ≥ 8.1
Composer

# 🛠️ Basic Structure
```bash
scorpfuzzy-demo/
├── src/
│   ├── FuzzyDesign/
│   │   └── Recognizer.php
│   └── Classifier/
│       └── Classifier.php
├── composer.json
└── README.md
```
# 🧩 Roadmap

 Support for more membership functions (Gaussian, Trapezoidal, etc.)
 Sugeno inference engine
 Rule builder / visual editor (future)
 Laravel integration (optional service provider)

# ❤️ Contributing
Pull requests are welcome!
For major changes, please open an issue first.
# 📄 License
MIT License – use it freely.

Made with ❤️ by Ali Yazan Jahjah
Happy fuzzifying! 🚀