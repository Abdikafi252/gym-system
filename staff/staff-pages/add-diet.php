<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gym System Admin - Diet Plan</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/matrix-style.css" />
    <link rel="stylesheet" href="../../css/matrix-media.css" />
    <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../../font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <script src="../../js/emoji-mapper.js?v=1.5"></script>

    <style>
        body {
            background: #f1f5f9;
        }

        .diet-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin: 20px auto;
            max-width: 1000px;
            overflow: hidden;
            font-family: 'Open Sans', sans-serif;
        }

        .diet-header {
            background: #fff;
            padding: 20px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .diet-title-input {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            border: none;
            border-bottom: 2px dashed #cbd5e1;
            padding: 5px 0;
            outline: none;
            width: 100%;
            max-width: 300px;
            background: transparent;
        }

        .diet-title-input:focus {
            border-bottom-color: #8b5cf6;
        }

        .diet-meta {
            display: flex;
            gap: 20px;
            align-items: center;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #475569;
            font-size: 14px;
            font-weight: 600;
        }

        .meta-item select,
        .meta-item input {
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            padding: 4px 8px;
            outline: none;
            font-size: 13px;
        }

        .diet-tags {
            background: #f8fafc;
            padding: 12px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: center;
            gap: 25px;
        }

        .diet-tag {
            font-size: 13px;
            font-weight: 600;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .days-header {
            display: flex;
            background: #ede9fe;
            border-bottom: 2px solid #ddd6fe;
        }

        .day-tab {
            flex: 1;
            text-align: center;
            padding: 12px 5px;
            font-weight: 700;
            color: #6b7280;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 3px solid transparent;
            font-size: 13px;
        }

        .day-tab:hover {
            background: #f5f3ff;
            color: #8b5cf6;
        }

        .day-tab.active {
            color: #7c3aed;
            border-bottom-color: #7c3aed;
            background: #fff;
        }

        .diet-body {
            padding: 0;
            background: #fff;
        }

        .meal-row {
            display: flex;
            border-bottom: 1px solid #f1f5f9;
        }

        .meal-name-col {
            width: 150px;
            padding: 20px 15px;
            border-right: 1px solid #f1f5f9;
            background: #fafaf9;
        }

        .meal-name {
            font-size: 14px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 4px;
        }

        .meal-time {
            font-size: 12px;
            color: #94a3b8;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .foods-col {
            flex: 1;
            padding: 10px 15px;
        }

        .food-item {
            display: flex;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px dashed #f1f5f9;
            gap: 15px;
        }

        .food-item:last-child {
            border-bottom: none;
        }

        .food-name-wrap {
            flex: 1.5;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #e2e8f0;
        }

        .food-emoji {
            font-size: 18px;
        }

        .food-input {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            font-size: 14px;
            color: #1e293b;
            font-weight: 600;
        }

        .food-unit-wrap {
            flex: 1;
        }

        .unit-input {
            width: 100%;
            border: 1px solid #e2e8f0;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 13px;
            outline: none;
        }

        .food-macros {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .macro-box {
            display: flex;
            align-items: center;
            gap: 4px;
            background: #f1f5f9;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
        }

        .macro-box input {
            width: 35px;
            border: none;
            background: transparent;
            font-weight: 700;
            text-align: center;
            outline: none;
            color: #0f172a;
        }

        .macro-box.calories {
            background: #fee2e2;
            color: #b91c1c;
        }

        .macro-box.calories input {
            color: #b91c1c;
        }

        .btn-remove-food {
            color: #ef4444;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .btn-remove-food:hover {
            opacity: 1;
        }

        .add-food-btn {
            background: transparent;
            border: 1px dashed #cbd5e1;
            color: #64748b;
            padding: 8px 15px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .add-food-btn:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #334155;
        }

        .diet-footer {
            background: #f8fafc;
            padding: 15px 25px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .total-nutrition {
            font-size: 13px;
            font-weight: 700;
            color: #1e293b;
        }

        .total-val {
            margin-left: 10px;
            color: #475569;
            font-weight: 600;
        }

        .total-val span {
            color: #0f172a;
            font-weight: 800;
        }

        .save-btn {
            background: #8b5cf6;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            box-shadow: 0 4px 6px -1px rgba(139, 92, 246, 0.3);
        }

        .save-btn:hover {
            background: #7c3aed;
        }

        .add-meal-wrap {
            padding: 15px;
            text-align: center;
            background: #fafaf9;
            border-bottom: 1px solid #f1f5f9;
        }
    </style>
</head>

<body>

    <?php include '../includes/header-content.php'; ?>
    <?php include '../includes/header.php' ?>
    <?php $page = 'diet-plan';
    include '../includes/sidebar.php' ?>

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb">
                <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a>
                <a href="manage-diet.php" class="tip-bottom">Diet Plans</a>
                <a href="#" class="current">Build Diet</a>
            </div>
        </div>

        <?php
        include 'dbcon.php';
        $member_id = $_GET['id'];
        $qry = "SELECT fullname FROM members WHERE user_id='$member_id'";
        $res = mysqli_query($con, $qry);
        $member_name = mysqli_fetch_assoc($res)['fullname'];
        ?>

        <!-- Alpine App Container -->
        <div class="container-fluid" x-data="dietApp()">
            <form id="dietForm" action="add-diet-req.php" method="POST">
                <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">

                <!-- Hidden inputs to submit structured data -->
                <input type="hidden" name="plan_name" x-bind:value="planName">
                <input type="hidden" name="plan_duration" x-bind:value="planDuration">
                <input type="hidden" name="plan_goal" x-bind:value="planGoal">
                <textarea name="instruction" style="display:none;" x-text="JSON.stringify(days)"></textarea>

                <div class="diet-container">
                    <!-- Header -->
                    <div class="diet-header">
                        <div>
                            <div style="font-size:12px; color:#64748b; font-weight:700; text-transform:uppercase; margin-bottom:4px;">
                                <i class="fas fa-user-circle"></i> Member: <?php echo $member_name; ?>
                            </div>
                            <input type="text" class="diet-title-input" x-model="planName" placeholder="e.g. Total Body Transformation" required>
                        </div>

                        <div class="diet-meta">
                            <div class="meta-item">
                                <i class="far fa-calendar-alt" style="color:#ef4444;"></i>
                                <select x-model="planDuration">
                                    <option>7 Days</option>
                                    <option>14 Days</option>
                                    <option>30 Days</option>
                                    <option>3 Months</option>
                                    <option>Custom</option>
                                </select>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-bullseye" style="color:#f59e0b;"></i>
                                <select x-model="planGoal">
                                    <option>Muscle Building</option>
                                    <option>Weight Loss</option>
                                    <option>Fat Burn</option>
                                    <option>Endurance</option>
                                    <option>Maintenance</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Days Header -->
                    <div class="days-header">
                        <template x-for="(day, index) in days" :key="index">
                            <div class="day-tab"
                                :class="activeDay === index ? 'active' : ''"
                                @click="activeDay = index"
                                x-text="day.name"></div>
                        </template>
                    </div>

                    <!-- Body / Meals -->
                    <div class="diet-body">
                        <template x-for="(day, dIndex) in days" :key="dIndex">
                            <div x-show="activeDay === dIndex">

                                <div class="add-meal-wrap">
                                    <select x-model="newMealName" style="margin:0; width:150px; font-size:13px; border-radius:6px; padding:4px;">
                                        <option value="">-- Select Meal --</option>
                                        <option>Breakfast</option>
                                        <option>Morning Snacks</option>
                                        <option>Lunch</option>
                                        <option>Evening Snacks</option>
                                        <option>Dinner</option>
                                    </select>
                                    <button type="button" @click="addMeal(dIndex)" class="btn btn-mini btn-info" style="border-radius:4px; margin-left:10px;"><i class="fas fa-plus"></i> Add Meal</button>
                                </div>

                                <template x-for="(meal, mIndex) in day.meals" :key="mIndex">
                                    <div class="meal-row">
                                        <!-- Meal Name sidebar -->
                                        <div class="meal-name-col">
                                            <div style="display:flex; justify-content:space-between; align-items:center;">
                                                <div class="meal-name" x-text="meal.name"></div>
                                                <i class="fas fa-times text-error" style="cursor:pointer; font-size:12px;" @click="removeMeal(dIndex, mIndex)" title="Remove Meal"></i>
                                            </div>
                                            <div class="meal-time"><i class="far fa-clock"></i> <input type="time" x-model="meal.time" style="width:75px; border:none; background:transparent; font-size:11px; padding:0; color:#94a3b8; outline:none; font-weight:600;"></div>
                                        </div>

                                        <!-- Foods List -->
                                        <div class="foods-col">
                                            <template x-for="(food, fIndex) in meal.foods" :key="fIndex">
                                                <div class="food-item">

                                                    <!-- Food input & Emoji -->
                                                    <div class="food-name-wrap">
                                                        <span class="food-emoji" x-html="getEmojiForText(food.name, 'diet')"></span>
                                                        <input type="text" class="food-input" x-model="food.name" placeholder="e.g Bananas" required>
                                                    </div>

                                                    <!-- Unit -->
                                                    <div class="food-unit-wrap">
                                                        <input type="text" class="unit-input" x-model="food.unit" placeholder="e.g 2 medium" required>
                                                    </div>

                                                    <!-- Macros -->
                                                    <div class="food-macros">
                                                        <div class="macro-box" title="Protein (g)">
                                                            🥩 <input type="number" x-model.number="food.protein" min="0">
                                                        </div>
                                                        <div class="macro-box" title="Carbs (g)">
                                                            🥖 <input type="number" x-model.number="food.carbs" min="0">
                                                        </div>
                                                        <div class="macro-box" title="Fat (g)">
                                                            🧈 <input type="number" x-model.number="food.fat" min="0">
                                                        </div>
                                                        <div class="macro-box calories" title="Calories">
                                                            🔥 <input type="number" x-model.number="food.calories" min="0" required>
                                                        </div>
                                                        <button type="button" class="btn-remove-food" @click="removeFood(dIndex, mIndex, fIndex)">
                                                            <i class="fas fa-times-circle"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            <div style="padding-top:10px;">
                                                <button type="button" class="add-food-btn" @click="addFood(dIndex, mIndex)">
                                                    <i class="fas fa-plus"></i> Add Food Item
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Show if no meals -->
                                <div x-show="day.meals.length === 0" style="padding:40px; text-align:center; color:#94a3b8; font-size:14px; font-weight:600;">
                                    <i class="fas fa-utensils" style="font-size:30px; margin-bottom:10px; color:#cbd5e1;"></i><br>
                                    No meals added for <span x-text="day.name"></span> yet.
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="diet-footer">
                        <div class="total-nutrition">
                            Total Day Nutrition :
                            <span class="total-val">Protein <span x-text="calcDailyTotal(activeDay, 'protein')"></span>g</span>
                            <span class="total-val">Carbs <span x-text="calcDailyTotal(activeDay, 'carbs')"></span>g</span>
                            <span class="total-val">Fat <span x-text="calcDailyTotal(activeDay, 'fat')"></span>g</span>
                            <span class="total-val" style="color:#b91c1c;">Calories <span x-text="calcDailyTotal(activeDay, 'calories')" style="color:#b91c1c;"></span></span>
                        </div>
                        <button type="submit" class="save-btn"><i class="fas fa-save"></i> Save Diet Plan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--Footer-part-->
    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M*A GYM System</div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('dietApp', () => ({
                planName: 'Muscle Gain',
                planDuration: '7 Days',
                planGoal: 'Muscle Building',
                activeDay: 0,
                newMealName: '',
                days: [{
                        name: 'Sunday',
                        meals: []
                    },
                    {
                        name: 'Monday',
                        meals: []
                    },
                    {
                        name: 'Tuesday',
                        meals: []
                    },
                    {
                        name: 'Wednesday',
                        meals: []
                    },
                    {
                        name: 'Thursday',
                        meals: []
                    },
                    {
                        name: 'Friday',
                        meals: []
                    },
                    {
                        name: 'Saturday',
                        meals: []
                    }
                ],

                addMeal(dayIndex) {
                    if (this.newMealName === '') return;
                    let defaultTime = "08:00";
                    if (this.newMealName === 'Lunch') defaultTime = "13:00";
                    if (this.newMealName === 'Dinner') defaultTime = "20:00";

                    this.days[dayIndex].meals.push({
                        name: this.newMealName,
                        time: defaultTime,
                        foods: [{
                            name: '',
                            unit: '',
                            calories: 0,
                            protein: 0,
                            carbs: 0,
                            fat: 0
                        }]
                    });
                    this.newMealName = '';
                },
                removeMeal(dayIndex, mealIndex) {
                    this.days[dayIndex].meals.splice(mealIndex, 1);
                },
                addFood(dayIndex, mealIndex) {
                    this.days[dayIndex].meals[mealIndex].foods.push({
                        name: '',
                        unit: '',
                        calories: 0,
                        protein: 0,
                        carbs: 0,
                        fat: 0
                    });
                },
                removeFood(dayIndex, mealIndex, foodIndex) {
                    this.days[dayIndex].meals[mealIndex].foods.splice(foodIndex, 1);
                },
                calcDailyTotal(dayIndex, macro) {
                    let total = 0;
                    if (this.days[dayIndex] && this.days[dayIndex].meals) {
                        this.days[dayIndex].meals.forEach(meal => {
                            if (meal.foods) {
                                meal.foods.forEach(food => {
                                    total += parseFloat(food[macro] || 0);
                                });
                            }
                        });
                    }
                    return total;
                }
            }));
        });
    </script>
</body>

</html>