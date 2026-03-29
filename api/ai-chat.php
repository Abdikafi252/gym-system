<?php
header('Content-Type: application/json');

$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);
$message = strtolower($data['message'] ?? '');

$response = "Waan ka xumahay, su'aashaas ma fahmin. Fadlan weydii wax ku saabsan jimicsiga (gym) ama cuntada (diet).";

// Somali Gym Knowledge Base
$kb = [
    'gym' => 'Gym-ka M*A GYM waa meesha ugu fiican ee aad ku dhisato jidhkaaga. Waxaan leenahay qalab casri ah.',
    'jimicsi' => 'Maanta waxaad samayn kartaa jimicsiga xabadka (Chest) iyo gacmaha (Triceps) haddii aad tahay qof cusub.',
    'cunto' => 'Cuntada ugu fiican ee muruqa dhisaysa waa mid leh Protein badan sida: Ukunta, Digaagga, Kaluunka, iyo Digirta.',
    'protein' => 'Protein-ku waa dhismaha muruqa. Isku day inaad qaadato 1.5g ilaa 2g oo protein ah halkii kiilo oo miisaankaaga ah.',
    'biyo' => 'Biyaha waa muhiim! Cab ugu yaraan 3-4 litir oo biyo ah maalintii si aad u qoyanaato.',
    'miisaan' => 'Haddii aad rabto inaad miisaan dhinto (Weight Loss), samee Cardio badan (Orodka) iyo cunto leh Carbohydrates yar.',
    'muruq' => 'Muruq dhisiddu waxay u baahan tahay hurdo ku filan (7-8 saac) iyo tababar joogto ah oo culus.',
    'leg day' => 'Leg Day waa maalinta ugu muhiimsan! Ha ilaawin inaad samayso Squats iyo Lunges.',
    'chest' => 'Jimicsiga xabadka (Chest) waxaa ugu fiican Bench Press iyo Push-ups.',
    'back' => 'Jimicsiga dhabarka (Back) wuxuu ku siinayaa qaabka V-shape. Samee Pull-ups iyo Deadlifts.',
    'waqti' => 'Waqtiga ugu fiican ee la jimicsado waa markaad dareento tamar, inta badan subaxdii hore ama galabtii.',
    'salaan' => 'Asc! Ku soo dhowoow M*A GYM AI Assistant. Maxaan kaa caawiyaa maanta?',
    'hi' => 'Hi! I am your Somali Gym AI. How can I help you today with your fitness journey?',
    'abdi' => 'Abdikafi waa maamulaha gym-ka, qof kasta ayaana jecel!',
    'mahad' => 'Adaa mudan! Jimicsi wacan.',
];

// Simple keyword matching
foreach ($kb as $key => $val) {
    if (strpos($message, $key) !== false) {
        $response = $val;
        break;
    }
}

echo json_encode(['response' => $response]);
?>
