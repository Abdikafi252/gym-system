<?php

function getEmojiForText($text, $context = 'diet')
{
    if (!$text || !is_string($text)) return "<span style='color:#cbd5e1'>🔹</span>";

    $lowerText = strtolower(trim($text));

    $dietMap = [
        // Proteins
        "chicken" => "fluent-emoji:poultry-leg",
        "chicken breast" => "fluent-emoji:poultry-leg",
        "chicken thigh" => "fluent-emoji:poultry-leg",
        "chicken wing" => "fluent-emoji:poultry-leg",
        "turkey" => "fluent-emoji:turkey",
        "beef" => "fluent-emoji:cut-of-meat",
        "steak" => "fluent-emoji:cut-of-meat",
        "ground beef" => "fluent-emoji:cut-of-meat",
        "meat" => "fluent-emoji:cut-of-meat",
        "lamb" => "fluent-emoji:cut-of-meat",
        "goat" => "fluent-emoji:cut-of-meat",
        "camel" => "fluent-emoji:cut-of-meat",
        "fish" => "fluent-emoji:fish",
        "salmon" => "fluent-emoji:fish",
        "tuna" => "fluent-emoji:fish",
        "cod" => "fluent-emoji:fish",
        "tilapia" => "fluent-emoji:fish",
        "shrimp" => "fluent-emoji:shrimp",
        "prawns" => "fluent-emoji:shrimp",
        "crab" => "fluent-emoji:crab",
        "lobster" => "fluent-emoji:lobster",
        "egg" => "fluent-emoji:egg",
        "eggs" => "fluent-emoji:egg",
        "boiled egg" => "fluent-emoji:egg",
        "fried egg" => "fluent-emoji:fried-egg",
        "omelette" => "fluent-emoji:fried-egg",
        "tofu" => "fluent-emoji:bowl-with-spoon",
        "tempeh" => "fluent-emoji:bowl-with-spoon",

        // Grains & Carbs
        "rice" => "fluent-emoji:cooked-rice",
        "brown rice" => "fluent-emoji:cooked-rice",
        "white rice" => "fluent-emoji:cooked-rice",
        "basmati rice" => "fluent-emoji:cooked-rice",
        "bariis" => "fluent-emoji:cooked-rice",
        "pasta" => "fluent-emoji:spaghetti",
        "spaghetti" => "fluent-emoji:spaghetti",
        "noodles" => "fluent-emoji:steaming-bowl",
        "quinoa" => "fluent-emoji:bowl-with-spoon",
        "oats" => "fluent-emoji:bowl-with-spoon",
        "oatmeal" => "fluent-emoji:bowl-with-spoon",
        "porridge" => "fluent-emoji:bowl-with-spoon",
        "bread" => "fluent-emoji:bread",
        "baguette" => "fluent-emoji:baguette-bread",
        "croissant" => "fluent-emoji:croissant",
        "toast" => "fluent-emoji:bread",
        "muufo" => "fluent-emoji:flatbread",
        "sabayad" => "fluent-emoji:flatbread",
        "canjeero" => "fluent-emoji:pancakes",
        "laxox" => "fluent-emoji:pancakes",
        "potato" => "fluent-emoji:potato",
        "baked potato" => "fluent-emoji:potato",
        "sweet potato" => "fluent-emoji:roasted-sweet-potato",
        "fries" => "fluent-emoji:french-fries",
        "cereal" => "fluent-emoji:bowl-with-spoon",

        // Fruits
        "apple" => "fluent-emoji:red-apple",
        "banana" => "fluent-emoji:banana",
        "orange" => "fluent-emoji:tangerine",
        "grapes" => "fluent-emoji:grapes",
        "strawberry" => "fluent-emoji:strawberry",
        "blueberry" => "fluent-emoji:blueberries",
        "mango" => "fluent-emoji:mango",
        "pineapple" => "fluent-emoji:pineapple",
        "watermelon" => "fluent-emoji:watermelon",
        "melon" => "fluent-emoji:melon",
        "kiwi" => "fluent-emoji:kiwi-fruit",
        "pear" => "fluent-emoji:pear",
        "peach" => "fluent-emoji:peach",
        "lemon" => "fluent-emoji:lemon",
        "avocado" => "fluent-emoji:avocado",
        "moos" => "fluent-emoji:banana",
        "tufaax" => "fluent-emoji:red-apple",
        "liin" => "fluent-emoji:tangerine",
        "canab" => "fluent-emoji:grapes",
        "cambe" => "fluent-emoji:mango",

        // Vegetables
        "salad" => "fluent-emoji:green-salad",
        "lettuce" => "fluent-emoji:leafy-green",
        "spinach" => "fluent-emoji:leafy-green",
        "broccoli" => "fluent-emoji:broccoli",
        "carrot" => "fluent-emoji:carrot",
        "cucumber" => "fluent-emoji:cucumber",
        "tomato" => "fluent-emoji:tomato",
        "onion" => "fluent-emoji:onion",
        "garlic" => "fluent-emoji:garlic",
        "pepper" => "fluent-emoji:bell-pepper",
        "chili" => "fluent-emoji:hot-pepper",
        "corn" => "fluent-emoji:ear-of-corn",

        // Dairy & Healthy Fats
        "milk" => "fluent-emoji:glass-of-milk",
        "yogurt" => "fluent-emoji:bowl-with-spoon",
        "cheese" => "fluent-emoji:cheese-wedge",
        "butter" => "fluent-emoji:butter",
        "olive oil" => "fluent-emoji:drop-of-blood",
        "honey" => "fluent-emoji:honey-pot",
        "nuts" => "fluent-emoji:peanuts",

        // Beverages
        "water" => "fluent-emoji:droplet",
        "juice" => "fluent-emoji:cup-with-straw",
        "coffee" => "fluent-emoji:hot-beverage",
        "tea" => "fluent-emoji:teacup-without-handle",
        "shaah" => "fluent-emoji:teacup-without-handle",
        "biyo" => "fluent-emoji:droplet",

        // Snacks & Others
        "pizza" => "fluent-emoji:pizza",
        "burger" => "fluent-emoji:hamburger",
        "sandwich" => "fluent-emoji:sandwich",
        "taco" => "fluent-emoji:taco",
        "sushi" => "fluent-emoji:sushi",
        "soup" => "fluent-emoji:pot-of-food",
        "maraq" => "fluent-emoji:pot-of-food",
        "cookie" => "fluent-emoji:cookie",
        "cake" => "fluent-emoji:shortcake",
        "chocolate" => "fluent-emoji:chocolate-bar",
        "ice cream" => "fluent-emoji:soft-ice-cream",
        "oodkac" => "fluent-emoji:cut-of-meat",
        "suqaar" => "fluent-emoji:stew",
        "kaluun" => "fluent-emoji:fish",
        "baasto" => "fluent-emoji:spaghetti",
    ];

    $workoutMap = [
        // Chest
        "bench press" => "healthicons:exercise-bench-press",
        "chest press" => "healthicons:exercise-bench-press",
        "dumbbell fly" => "healthicons:exercise-bench-press",
        "pushup" => "healthicons:exercise-pushups",
        "dips" => "healthicons:exercise-pullups",

        // Back
        "deadlift" => "healthicons:exercise-deadlift",
        "pullup" => "healthicons:exercise-pullups",
        "lat pulldown" => "healthicons:exercise-pullups",
        "seated row" => "healthicons:exercise-rowing",
        "barbell row" => "healthicons:exercise-rowing",

        // Shoulders
        "military press" => "healthicons:exercise-weights",
        "overhead press" => "healthicons:exercise-weights",
        "shoulder press" => "healthicons:exercise-weights",
        "lateral raise" => "healthicons:exercise-weights",

        // Legs
        "squat" => "healthicons:exercise-squats",
        "leg press" => "healthicons:exercise-squats",
        "leg extension" => "healthicons:exercise-squats",
        "lunge" => "healthicons:exercise-squats",
        "calf raise" => "healthicons:exercise-squats",

        // Arms
        "bicep curl" => "healthicons:exercise-weights",
        "hammer curl" => "healthicons:exercise-weights",
        "tricep pushdown" => "healthicons:exercise-weights",
        "skull crusher" => "healthicons:exercise-weights",

        // Core
        "crunch" => "healthicons:exercise-yoga",
        "situp" => "healthicons:exercise-yoga",
        "plank" => "healthicons:exercise-yoga",
        "leg raise" => "healthicons:exercise-yoga",

        // Cardio
        "run" => "healthicons:exercise-running",
        "treadmill" => "healthicons:exercise-running",
        "walk" => "healthicons:exercise-walk",
        "cycling" => "healthicons:exercise-bicycle",
        "swim" => "healthicons:exercise-swimming",
        "jump rope" => "healthicons:exercise-weights",
        "warm up" => "fluent-emoji:fire",
        "cardio" => "healthicons:exercise-running",
        "cool down" => "fluent-emoji:snowflake"
    ];

    $activeMap = ($context === 'diet') ? $dietMap : $workoutMap;

    // Direct match
    if (isset($activeMap[$lowerText])) {
        return "<iconify-icon icon='{$activeMap[$lowerText]}' width='24' height='24' style='vertical-align:middle; display:inline-block;'></iconify-icon>";
    }

    // Partial match (longest keys first)
    uksort($activeMap, function ($a, $b) {
        return strlen($b) - strlen($a);
    });

    foreach ($activeMap as $key => $icon) {
        if (strpos($lowerText, $key) !== false) {
            return "<iconify-icon icon='{$icon}' width='24' height='24' style='vertical-align:middle; display:inline-block;'></iconify-icon>";
        }
    }

    return "<span style='color:#3b82f6; font-size:18px;'>✦</span>";
}
