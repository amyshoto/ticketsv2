<?php
$token = '7415711714:AAExmriQzLiQb39zsW4ahbZmE2RL4TsBhMI';
$apiUrl = "https://api.telegram.org/bot$token/";

$host = '10.25.96.155';
$port = '5432';
$dbname = 'dbtickets';
$user = 'postgres';
$pass = '';

$dsn = "pgsql:host=$host;port=$port;dbname=$dbname;user=$user;password=$pass";

try {
    $pdo = new PDO($dsn);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Conexión exitosa a la base de datos\n";
} catch (PDOException $e) {
    echo "Error en la conexión: " . $e->getMessage();
    exit();
}

function sendMessage($chat_id, $text, $reply_markup = null) {
    global $apiUrl;
    $url = $apiUrl . "sendMessage?chat_id=" . $chat_id . "&text=" . urlencode($text);
    if ($reply_markup) {
        $url .= "&reply_markup=" . urlencode($reply_markup);
    }
    file_get_contents($url);
}

function getNextFolio($pdo) {
    $stmt = $pdo->query("SELECT MAX(Folio) as max_folio FROM Ticket");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return str_pad($row['max_folio'] + 1, 3, "0", STR_PAD_LEFT);
}

function getResponseMessage($folio) {
    date_default_timezone_set('America/Mexico_City');
    $currentHour = date('G');
    if ($currentHour >= 8 && $currentHour < 16) {
        return "🎉 ¡Gracias!, tu reporte ha sido registrado con el folio $folio, en seguida lo atenderemos. Puedes revisar su estatus en: 'utickets.com' 🌐";
    } else {
        return "⏰ ¡Gracias!, tu reporte ha sido registrado con el folio $folio, te atenderemos en las horas establecidas de 8am a 4pm. Puedes revisar su estatus en: 'utickets.com' 🌐";
    }
}

function truncateString($string, $maxLength) {
    return substr($string, 0, $maxLength);
}

function handleUpdate($update, $pdo) {
    $message = isset($update['message']) ? $update['message'] : "";
    $callback_query = isset($update['callback_query']) ? $update['callback_query'] : "";
    $chat_id = isset($message['chat']['id']) ? $message['chat']['id'] : (isset($callback_query['message']['chat']['id']) ? $callback_query['message']['chat']['id'] : "");
    $text = isset($message['text']) ? $message['text'] : (isset($callback_query['data']) ? $callback_query['data'] : "");

    if ($text == "/start") {
        $state = @file_get_contents("state_$chat_id.txt");

        if ($state && $state != "completed") {
            sendMessage($chat_id, "Ya has iniciado una conversación anteriormente. ¿Deseas terminarla o continuar?", json_encode([
                'inline_keyboard' => [
                    [['text' => 'Terminar', 'callback_data' => 'finish']],
                    [['text' => 'Continuar', 'callback_data' => 'continue']]
                ]
            ]));
            return;
        }

        startNewConversation($chat_id, $pdo);
    } elseif ($callback_query && $callback_query['data'] == 'finish') {
        file_put_contents("state_$chat_id.txt", "completed");
        sendMessage($chat_id, "Conversación finalizada. Usa /start para iniciar nuevamente.");
    } elseif ($callback_query && $callback_query['data'] == 'continue') {
        $state = @file_get_contents("state_$chat_id.txt");
        sendMessage($chat_id, "Continuamos con la conversación donde la dejamos.");
        proceedWithNextStep($state, $chat_id);
    } elseif ($text == "/ayuda") {
        $helpMessage = "Reporta tus problemas técnicos 💻 de una manera más sencilla utilizando telegram, aquí tienes la lista 🗒️ de comandos disponibles en el chat.

/start : Utiliza este comando para reportar un problema. Proporciona la información solicitada para que podamos atenderte adecuadamente 📋

/info: Utiliza este comando para detalles sobre el bot 🤖

/horario: Utiliza este comando para detalles de horario ⏰

/status: Utiliza este comando para saber cómo consultar el estatus de tu reporte 🤔
";
        sendMessage($chat_id, $helpMessage);
    } elseif ($text == "/info") {
        $infoMessage = "Este chatbot 🤖 está diseñado para ayudar a los usuarios a reportar problemas técnicos 💻. Sigue las instrucciones para poder reportar y que se realice correctamente tu reporte para ser atendido/a.";
        sendMessage($chat_id, $infoMessage);
    } elseif ($text == "/horario") {
        $horarioMessage = "⏰ Podrás hacer tus reportes 24/7 con ayuda de este bot\n\n⏰ El horario para que tu reporte sea resuelto personalmente por un especialista es de 8 A.M a 4 P.M.";
        sendMessage($chat_id, $horarioMessage);
    } elseif ($text == "/status") {
        $statusMessage = "🗂️ Para consultar el estatus de tu reporte ingresa al siguiente link —-------- e ingresa tu correo y contraseña proporcionados en este chat.";
        sendMessage($chat_id, $statusMessage);
    } else {
        $state = @file_get_contents("state_$chat_id.txt");
        handleUserResponse($text, $chat_id, $state, $pdo, $callback_query);
    }
}

function handleUserResponse($text, $chat_id, $state, $pdo, $callback_query) {
    if ($callback_query) {
        if (($state == "waiting_for_unit" && $callback_query['message']['text'] == "🏢 Selecciona tu dirección / unidad:") ||
            ($state == "waiting_for_issue" && $callback_query['message']['text'] == "📝 ¿Qué deseas reportar?")) {
            // Proceso la respuesta solo si estamos en el estado correcto y el mensaje del teclado inline coincide
        } else {
            return; // Ignora cualquier callback_query no esperada
        }
    }
    
    if ($state == "waiting_for_name") {
        $name = $text;
        file_put_contents("data_$chat_id.txt", json_encode(['name' => $name]));
        sendMessage($chat_id, "📧 Ahora ingresa tu correo:", json_encode(['remove_keyboard' => true]));
        file_put_contents("state_$chat_id.txt", "waiting_for_email");
    } elseif ($state == "waiting_for_email") {
        $email = truncateString($text, 50);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data = json_decode(file_get_contents("data_$chat_id.txt"), true);
            $data['email'] = $email;
            file_put_contents("data_$chat_id.txt", json_encode($data));
            sendMessage($chat_id, "🔑 Ahora ingresa una contraseña:", json_encode(['remove_keyboard' => true]));
            file_put_contents("state_$chat_id.txt", "waiting_for_password");
        } else {
            sendMessage($chat_id, "⚠️ El correo proporcionado no es válido. Por favor, ingresa un correo electrónico válido:", json_encode(['remove_keyboard' => true]));
        }
    } elseif ($state == "waiting_for_password") {
        $password = truncateString($text, 50);
        $data = json_decode(file_get_contents("data_$chat_id.txt"), true);
        $data['password'] = password_hash($password, PASSWORD_BCRYPT);
        file_put_contents("data_$chat_id.txt", json_encode($data));
        sendMessage($chat_id, "✅ Contraseña registrada.\n\n🏢Selecciona tu dirección / unidad:");
        $state = "waiting_for_unit";
        file_put_contents("state_$chat_id.txt", $state);
        proceedWithNextStep($state, $chat_id);
    } elseif ($state == "waiting_for_unit" && isset($callback_query['data']) && $callback_query['message']['text'] == "🏢 Selecciona tu dirección / unidad:") {
        $unit = $callback_query['data'];
        $data = json_decode(file_get_contents("data_$chat_id.txt"), true);
        $data['unit'] = $unit;
        file_put_contents("data_$chat_id.txt", json_encode($data));
        sendMessage($chat_id, "📍Has seleccionado unidad: ".$unit);
        $state = "waiting_for_issue";
        file_put_contents("state_$chat_id.txt", $state);
        proceedWithNextStep($state, $chat_id);
    } elseif ($state == "waiting_for_issue" && isset($callback_query['data']) && $callback_query['message']['text'] == "📝 ¿Qué deseas reportar?") {
        $issue = $callback_query['data'];
        $data = json_decode(file_get_contents("data_$chat_id.txt"), true);
        $data['issue'] = $issue;
        file_put_contents("data_$chat_id.txt", json_encode($data));
        sendMessage($chat_id, "Has seleccionado: " . $issue);
        sendMessage($chat_id, "🖊️ Describe brevemente tu problema:", json_encode(['remove_keyboard' => true]));
        $state = "waiting_for_description";
        file_put_contents("state_$chat_id.txt", $state);
    } elseif ($state == "waiting_for_description") {
        $description = truncateString($text, 80);
        $data = json_decode(file_get_contents("data_$chat_id.txt"), true);
        $data['description'] = $description;
        file_put_contents("data_$chat_id.txt", json_encode($data));
        sendMessage($chat_id, "🔢 ¿Cuál es tu número de estación?", json_encode(['remove_keyboard' => true]));
        $state = "waiting_for_station";
        file_put_contents("state_$chat_id.txt", $state);
    } elseif ($state == "waiting_for_station") {
        $station = truncateString($text, 50);
        $data = json_decode(file_get_contents("data_$chat_id.txt"), true);
        $data['station'] = $station;
        file_put_contents("data_$chat_id.txt", json_encode($data));
        $folio = getNextFolio($pdo);
        $data['folio'] = $folio;
        file_put_contents("data_$chat_id.txt", json_encode($data));
        sendMessage($chat_id, getResponseMessage($folio));

        // Insertar datos en la base de datos
        try {
            $stmt = $pdo->prepare("INSERT INTO usuario (correo, nombre, contrasena, chat_id) VALUES (:correo, :nombre, :contrasena, :chat_id) ON CONFLICT (chat_id) DO NOTHING RETURNING id");
            $stmt->bindParam(':correo', $data['email']);
            $nombre = isset($data['name']) ? truncateString($data['name'], 50) : null;
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':contrasena', $data['password']);
            $stmt->bindParam(':chat_id', $chat_id);
            $stmt->execute();
            $userId = $stmt->fetchColumn();
            
            if (!$userId) {
                // Si el usuario ya existe, obtener su ID
                $stmt = $pdo->prepare("SELECT id FROM usuario WHERE chat_id = :chat_id");
                $stmt->bindParam(':chat_id', $chat_id);
                $stmt->execute();
                $userId = $stmt->fetchColumn();
            }

            $stmt = $pdo->prepare("INSERT INTO Ticket (Folio, Asunto, Nombre, Encargado, Problema, fechaEntrada, Estado, Gerencia, Ubicacion, idusuario) 
            VALUES (:Folio, :Asunto, :Nombre, '-', :Problema, CURRENT_DATE, 'Nuevo', :Gerencia, :Ubicacion, :idusuario)");
            $stmt->bindParam(':Folio', $data['folio']);
            $stmt->bindParam(':Asunto', $data['issue']);
            $stmt->bindParam(':Nombre', $nombre);
            $problema = truncateString($data['description'], 80);
            $stmt->bindParam(':Problema', $problema);
            $gerencia = truncateString($data['unit'], 50);
            $stmt->bindParam(':Gerencia', $gerencia);
            $ubicacion = truncateString($data['station'], 50);
            $stmt->bindParam(':Ubicacion', $ubicacion);
            $stmt->bindParam(':idusuario', $userId);
            $stmt->execute();

            sendMessage($chat_id, "✅ Tu información ha sido guardada exitosamente. 📌 ¿Deseas registrar otro reporte? Usa el comando /start para comenzar de nuevo.");
        } catch (PDOException $e) {
            sendMessage($chat_id, "⚠️ Hubo un error al guardar tus datos. Por favor, intenta de nuevo.");
            error_log($e->getMessage());
        }

        file_put_contents("state_$chat_id.txt", "completed");
    }
}

function startNewConversation($chat_id, $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE chat_id = :chat_id");
    $stmt->bindParam(':chat_id', $chat_id);
    $stmt->execute();
    $userExists = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($userExists) {
        sendMessage($chat_id, "👋 ¡Hola nuevamente! Ya estás registrado. Puedes proceder a reportar un problema.");
        $state = "waiting_for_unit";
        file_put_contents("state_$chat_id.txt", $state);
        proceedWithNextStep($state, $chat_id);
    } else {
        sendMessage($chat_id, "👋 ¡Hola! Bienvenid@ a 'utickets' 🎟️ Para empezar proporciona los siguientes datos para tu registro.");
        sendMessage($chat_id, "✏️ Ingresa tu nombre:", json_encode(['remove_keyboard' => true]));
        file_put_contents("state_$chat_id.txt", "waiting_for_name");
    }
}

function proceedWithNextStep($state, $chat_id) {
    if ($state == "waiting_for_unit") {
        $keyboard = [
            [['text' => '💼AG', 'callback_data' => '💼AG']],
            [['text' => '🖥UTIC', 'callback_data' => '🖥UTIC']],
            [['text' => '⚖️OIC', 'callback_data' => '⚖️OIC']],
            [['text' => '📄DA', 'callback_data' => '📄DA']],
            [['text' => '💼🔎DJYT', 'callback_data' => '💼🔎DJYT']],
            [['text' => '🚴🏻‍♀️DMA', 'callback_data' => '🚴🏻‍♀️DMA']],
            [['text' => '🚦DSYS', 'callback_data' => '🚦DSYS']],
            [['text' => '📸DFI', 'callback_data' => '📸DFI']],
        ];
        sendMessage($chat_id, "🏢 Selecciona tu dirección / unidad:", json_encode(['inline_keyboard' => $keyboard]));
    } elseif ($state == "waiting_for_issue") {
        $keyboard = [
            [['text' => '🖥️Computadora', 'callback_data' => '🖥️Computadora']],
            [['text' => '🖨️Impresora', 'callback_data' => '🖨️Impresora']],
            [['text' => '📇Scanner', 'callback_data' => '📇Scanner']],
            [['text' => '🧑🏻‍💻Software', 'callback_data' => '🧑🏻‍💻Software']],
            [['text' => '⌨️ Monitor, Mouse o Teclado', 'callback_data' => '⌨️ Monitor, Mouse o Teclado']],
            [['text' => '💾Memoria USB', 'callback_data' => '💾Memoria USB']],
            [['text' => '📞Telefonía', 'callback_data' => '📞Telefonía']],
            [['text' => '🌐Internet', 'callback_data' => '🌐Internet']],
            [['text' => '🎲Otro', 'callback_data' => '🎲Otro']],
        ];
        sendMessage($chat_id, "📝 ¿Qué deseas reportar?", json_encode(['inline_keyboard' => $keyboard]));
    } elseif ($state == "waiting_for_description") {
        sendMessage($chat_id, "🖊️ Describe brevemente tu problema:", json_encode(['remove_keyboard' => true]));
    } elseif ($state == "waiting_for_station") {
        sendMessage($chat_id, "🔢 ¿Cuál es tu número de estación y/o ubicación física?", json_encode(['remove_keyboard' => true]));
    }
}

set_time_limit(0);  // Esto elimina el límite de tiempo de ejecución del script

$last_update_id = null;  // Inicializa el último ID de actualización

// Limpiar actualizaciones pendientes
try {
    $contextOptions = [
        "ssl" => [
            "verify_peer" => false,
            "verify_peer_name" => false,
        ],
    ];
    // Obtener y descartar todas las actualizaciones pendientes
    $response = file_get_contents($apiUrl . "getUpdates?offset=-1&timeout=1", false, stream_context_create($contextOptions));
    $updates = json_decode($response, true);
    if (isset($updates['result'])) {
        foreach ($updates['result'] as $update) {
            $last_update_id = $update['update_id'];
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

while (true) {
    try {
        $contextOptions = [
            "ssl" => [
                "verify_peer" => false,
                "verify_peer_name" => false,
            ],
        ];
        $response = file_get_contents($apiUrl . "getUpdates?offset=" . ($last_update_id + 1) . "&timeout=30", false, stream_context_create($contextOptions));
        $updates = json_decode($response, true);
        if (isset($updates['result'])) {
            foreach ($updates['result'] as $update) {
                handleUpdate($update, $pdo);
                $last_update_id = $update['update_id'];
            }
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
        sleep(1); // Espera antes de intentar nuevamente para evitar un ciclo rápido en caso de error
    }
}
?>
