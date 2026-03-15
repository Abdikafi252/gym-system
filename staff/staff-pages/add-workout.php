<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('location:../index.php');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>Gym System Admin - Workout Plan</title>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/bootstrap-responsive.min.css" />
    <link rel="stylesheet" href="../../css/matrix-style.css" />
    <link rel="stylesheet" href="../../css/matrix-media.css" />
    <link href="../../font-awesome/css/fontawesome.css" rel="stylesheet" />
    <link href="../../font-awesome/css/all.css" rel="stylesheet" />
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700,800' rel='stylesheet' type='text/css'>

    <!-- Alpine.js & Emoji Mapper -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <script src="../../js/emoji-mapper.js?v=1.5"></script>

    <style>
        body {
            background: #f1f5f9;
        }

        .workout-container {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin: 20px auto;
            max-width: 1000px;
            overflow: hidden;
            font-family: 'Open Sans', sans-serif;
        }

        .workout-header {
            background: #fff;
            padding: 20px 25px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .workout-title-input {
            font-size: 24px;
            font-weight: 700;
            color: #1e293b;
            border: none;
            border-bottom: 2px dashed #cbd5e1;
            padding: 5px 0;
            outline: none;
            width: 100%;
            max-width: 350px;
            background: transparent;
        }

        .workout-title-input:focus {
            border-bottom-color: #3b82f6;
        }

        .workout-meta {
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

        .days-header {
            display: flex;
            background: #f1f5f9;
            border-bottom: 2px solid #e2e8f0;
        }

        .day-tab {
            flex: 1;
            text-align: center;
            padding: 12px 5px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 3px solid transparent;
            font-size: 13px;
        }

        .day-tab:hover {
            background: #fff7ed;
            color: #c2410c;
        }

        .day-tab.active {
            color: #3b82f6;
            border-bottom-color: #3b82f6;
            background: #fff;
        }

        .workout-body {
            padding: 0;
            background: #fff;
        }

        .cat-row {
            display: flex;
            border-bottom: 1px solid #f8fafc;
        }

        .cat-name-col {
            width: 150px;
            padding: 20px 15px;
            border-right: 1px solid #f1f5f9;
            background: #fafaf9;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        .cat-name {
            font-size: 14px;
            font-weight: 800;
            color: #334155;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .ex-col {
            flex: 1;
            padding: 10px 15px;
        }

        .ex-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px dashed #e2e8f0;
            gap: 15px;
        }

        .ex-item:last-child {
            border-bottom: none;
        }

        .ex-name-wrap {
            flex: 2;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #f8fafc;
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid #cbd5e1;
        }

        .ex-emoji {
            font-size: 18px;
        }

        .ex-input {
            border: none;
            background: transparent;
            width: 100%;
            outline: none;
            font-size: 14px;
            color: #1e293b;
            font-weight: 700;
        }

        .ex-details {
            display: flex;
            gap: 10px;
            flex: 3;
            align-items: center;
        }

        .detail-box {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #f1f5f9;
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            color: #475569;
            flex: 1;
            border: 1px solid #e2e8f0;
        }

        .detail-box input {
            width: 100%;
            border: none;
            background: transparent;
            font-weight: 600;
            outline: none;
            color: #0f172a;
            font-size: 13px;
        }

        .btn-remove-ex {
            color: #ef4444;
            background: transparent;
            border: none;
            cursor: pointer;
            padding: 4px;
            opacity: 0.5;
            transition: opacity 0.2s;
        }

        .btn-remove-ex:hover {
            opacity: 1;
        }

        .add-ex-btn {
            background: transparent;
            border: 1px dashed #cbd5e1;
            color: #64748b;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            transition: all 0.2s;
        }

        .add-ex-btn:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #334155;
        }

        .workout-footer {
            background: #f8fafc;
            padding: 15px 25px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: flex-end;
            align-items: center;
        }

        .save-btn {
            background: #0f172a;
            color: white;
            border: none;
            padding: 12px 35px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            box-shadow: 0 4px 6px -1px rgba(15, 23, 42, 0.3);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .save-btn:hover {
            background: #1e293b;
            transform: translateY(-1px);
        }

        .add-cat-wrap {
            padding: 15px;
            text-align: center;
            background: #fafaf9;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
    </style>
</head>

<body>

    <?php include '../includes/header-content.php'; ?>
    <?php include '../includes/header.php' ?>
    <?php $page = 'workout-plan';
    include '../includes/sidebar.php' ?>

    <div id="content">
        <div id="content-header">
            <div id="breadcrumb">
                <a href="index.php" title="Go to Home" class="tip-bottom"><i class="fas fa-home"></i> Home</a>
                <a href="manage-workout.php" class="tip-bottom">Workout Plans</a>
                <a href="#" class="current">Build Workout</a>
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
        <div class="container-fluid" x-data="workoutApp()">
            <form id="workoutForm" action="add-workout-req.php" method="POST">
                <input type="hidden" name="member_id" value="<?php echo $member_id; ?>">

                <!-- Hidden inputs to submit structured data -->
                <input type="hidden" name="plan_name" x-bind:value="planName">
                <input type="hidden" name="plan_duration" x-bind:value="planDuration">
                <input type="hidden" name="plan_goal" x-bind:value="planGoal">
                <textarea name="instruction" style="display:none;" x-text="JSON.stringify(days)"></textarea>

                <div class="workout-container">
                    <!-- Header -->
                    <div class="workout-header">
                        <div>
                            <div style="font-size:12px; color:#64748b; font-weight:700; text-transform:uppercase; margin-bottom:4px;">
                                <i class="fas fa-user-circle"></i> Member: <?php echo $member_name; ?>
                            </div>
                            <input type="text" class="workout-title-input" x-model="planName" placeholder="e.g. Total Body Transformation" required>
                        </div>

                        <div class="workout-meta">
                            <div class="meta-item">
                                <i class="far fa-calendar-alt" style="color:#ea580c;"></i>
                                <select x-model="planDuration">
                                    <option>4 Weeks</option>
                                    <option>8 Weeks</option>
                                    <option>12 Weeks</option>
                                    <option>6 Months</option>
                                    <option>Custom</option>
                                </select>
                            </div>
                            <div class="meta-item">
                                <i class="fas fa-bullseye" style="color:#2563eb;"></i>
                                <select x-model="planGoal">
                                    <option>Muscle Building</option>
                                    <option>Weight Loss</option>
                                    <option>Strength</option>
                                    <option>Endurance</option>
                                    <option>Flexibility</option>
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

                    <!-- Body / Categories -->
                    <div class="workout-body">
                        <template x-for="(day, dIndex) in days" :key="dIndex">
                            <div x-show="activeDay === dIndex">

                                <div class="add-cat-wrap">
                                    <select x-model="newCatName" style="margin:0; width:180px; font-size:13px; border-radius:6px; padding:4px;">
                                        <option value="">-- Workout Phase --</option>
                                        <optgroup label="Upper Body (Murqaha Sare)">
                                            <option>Chest (Pectorals)</option>
                                            <option>Upper Chest</option>
                                            <option>Lower Chest</option>
                                            <option>Back (Dhabarka)</option>
                                            <option>Latissimus Dorsi (Lats)</option>
                                            <option>Traps (Trapezius)</option>
                                            <option>Rhomboids</option>
                                            <option>Shoulders (Deltoids)</option>
                                            <option>Front Delts</option>
                                            <option>Side / Lateral Delts</option>
                                            <option>Rear Delts</option>
                                            <option>Biceps</option>
                                            <option>Biceps Long Head</option>
                                            <option>Biceps Short Head</option>
                                            <option>Triceps</option>
                                            <option>Triceps Long Head</option>
                                            <option>Triceps Lateral Head</option>
                                            <option>Triceps Medial Head</option>
                                            <option>Forearms</option>
                                        </optgroup>
                                        <optgroup label="Core (Bartamaha Jirka)">
                                            <option>Abs (Abdominals)</option>
                                            <option>Upper Abs</option>
                                            <option>Lower Abs</option>
                                            <option>Obliques</option>
                                            <option>Core</option>
                                        </optgroup>
                                        <optgroup label="Lower Body (Murqaha Hoose)">
                                            <option>Legs</option>
                                            <option>Quadriceps</option>
                                            <option>Hamstrings</option>
                                            <option>Glutes</option>
                                            <option>Calves</option>
                                            <option>Inner Thigh (Adductors)</option>
                                            <option>Outer Thigh (Abductors)</option>
                                            <option>Hip Flexors</option>
                                        </optgroup>
                                        <optgroup label="General Phases">
                                            <option>Warm Up</option>
                                            <option>Main Set</option>
                                            <option>Cardio</option>
                                            <option>Cool Down</option>
                                        </optgroup>
                                    </select>
                                    <button type="button" @click="addCategory(dIndex)" class="btn btn-mini btn-warning" style="border-radius:4px;"><i class="fas fa-plus"></i> Add Phase</button>
                                </div>

                                <template x-for="(cat, cIndex) in day.categories" :key="cIndex">
                                    <div class="cat-row">
                                        <!-- Category Name sidebar -->
                                        <div class="cat-name-col">
                                            <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                                                <div class="cat-name" style="display:flex; align-items:center; gap:6px;">
                                                    <span x-html="getEmojiForText(cat.name, 'workout')"></span>
                                                    <span x-text="cat.name"></span>
                                                </div>
                                                <i class="fas fa-times text-error" style="cursor:pointer; font-size:12px;" @click="removeCategory(dIndex, cIndex)" title="Remove Phase"></i>
                                            </div>
                                        </div>

                                        <!-- Exercises List -->
                                        <div class="ex-col">
                                            <template x-for="(ex, eIndex) in cat.exercises" :key="eIndex">
                                                <div class="ex-item">

                                                    <!-- Exercise input & Emoji -->
                                                    <div class="ex-name-wrap">
                                                        <span class="ex-emoji" x-html="getEmojiForText(ex.name, 'workout')"></span>
                                                        <input type="text" class="ex-input" x-model="ex.name" placeholder="Exercise name (e.g. Bench Press)" required>
                                                    </div>

                                                    <!-- Details: Sets, Reps, Rest -->
                                                    <div class="ex-details">
                                                        <div class="detail-box">
                                                            <i class="fas fa-layer-group text-muted"></i>
                                                            <input type="text" x-model="ex.sets" placeholder="Sets (e.g. 3)" required>
                                                        </div>
                                                        <div class="detail-box">
                                                            <i class="fas fa-redo text-muted"></i>
                                                            <input type="text" x-model="ex.reps" placeholder="Reps / Time (e.g. 10-12)" required>
                                                        </div>
                                                        <div class="detail-box">
                                                            <i class="fas fa-stopwatch text-muted"></i>
                                                            <input type="text" x-model="ex.rest" placeholder="Rest (e.g. 60s)">
                                                        </div>

                                                        <button type="button" class="btn-remove-ex" @click="removeExercise(dIndex, cIndex, eIndex)">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </template>

                                            <div style="padding-top:10px;">
                                                <button type="button" class="add-ex-btn" @click="addExercise(dIndex, cIndex)">
                                                    <i class="fas fa-plus"></i> Add Exercise
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Show if no Categories -->
                                <div x-show="day.categories.length === 0" style="padding:40px; text-align:center; color:#94a3b8; font-size:14px; font-weight:600;">
                                    <i class="fas fa-bed" style="font-size:30px; margin-bottom:10px; color:#cbd5e1;"></i><br>
                                    Rest day or no workout phases added for <span x-text="day.name"></span>.
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- Footer -->
                    <div class="workout-footer">
                        <button type="submit" class="save-btn">
                            <i class="fas fa-save"></i>
                            Save Workout Plan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!--Footer-part-->
    <div class="row-fluid">
        <div id="footer" class="span12"> <?php echo date("Y"); ?> &copy; M * A GYM System</div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('workoutApp', () => ({
                planName: 'Total Body Transformation',
                planDuration: '4 Weeks',
                planGoal: 'Muscle Building',
                activeDay: 0,
                newCatName: '',
                days: [{
                        name: 'Monday',
                        categories: []
                    },
                    {
                        name: 'Tuesday',
                        categories: []
                    },
                    {
                        name: 'Wednesday',
                        categories: []
                    },
                    {
                        name: 'Thursday',
                        categories: []
                    },
                    {
                        name: 'Friday',
                        categories: []
                    },
                    {
                        name: 'Saturday',
                        categories: []
                    },
                    {
                        name: 'Sunday',
                        categories: []
                    }
                ],

                addCategory(dayIndex) {
                    if (this.newCatName === '') return;

                    this.days[dayIndex].categories.push({
                        name: this.newCatName,
                        exercises: [{
                            name: '',
                            sets: '',
                            reps: '',
                            rest: ''
                        }]
                    });
                    this.newCatName = '';
                },
                removeCategory(dayIndex, catIndex) {
                    this.days[dayIndex].categories.splice(catIndex, 1);
                },
                addExercise(dayIndex, catIndex) {
                    this.days[dayIndex].categories[catIndex].exercises.push({
                        name: '',
                        sets: '',
                        reps: '',
                        rest: ''
                    });
                },
                removeExercise(dayIndex, catIndex, exIndex) {
                    this.days[dayIndex].categories[catIndex].exercises.splice(exIndex, 1);
                }
            }));
        });
    </script>
</body>

</html>