<?php
// Начинаем сессию, если ещё не начата
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
// Выбор активного языка - сейчас 'hi' для полного хинди (Индия)
$active_language = 'hi'; // 'ru' (русский), 'custom' (испанский), 'it' (итальянский), 'hi' (хинди - Индия)

// Параметры стран для каждого языка
$country_settings = [
    'ru' => [
        'html_lang' => 'ru-RU',     // Атрибут lang для HTML
        'phone_code' => '+7',       // Префикс телефона
        'currency_code' => 'RUB',   // Код валюты
        'country_code' => 'ru'      // Код страны для телефонного инпута
    ],
    'custom' => [
        'html_lang' => 'es-MX',     // Атрибут lang для HTML (испанский Мексика)
        'phone_code' => '+52',      // Префикс телефона (Мексика)
        'currency_code' => 'MXN',   // Код валюты (мексиканский песо)
        'country_code' => 'mx'      // Код страны для телефонного инпута (Мексика)
    ],
    'it' => [
        'html_lang' => 'it-IT',
        'phone_code' => '+39',
        'currency_code' => 'EUR',
        'country_code' => 'it'
    ],
    'hi' => [
        'html_lang' => 'hi-IN',        // Хинди (Индия) - hi-IN
        'phone_code' => '+91',         // Код телефона Индии
        'currency_code' => 'INR',      // Индийская рупия
        'country_code' => 'in'         // Код страны для телефонного инпута
    ]
];

// Устанавливаем настройки в зависимости от выбранного языка
$html_lang = $country_settings[$active_language]['html_lang'];
$phone_code = $country_settings[$active_language]['phone_code'];
$currency_code = $country_settings[$active_language]['currency_code'];
$country_code = $country_settings[$active_language]['country_code'];

// Массив с локализованными текстами
$localization = [
    // Русский язык
    'ru' => [
        // Общие
        'site_title' => 'Artex - Как избавиться от боли в суставах в домашних условиях',
        'site_description' => 'Естественный способ избавиться от боли в суставах за 24 часа с Artex',
        
        // Заголовки
        'header_title' => 'КАК ИЗБАВИТЬСЯ ОТ БОЛИ В СУСТАВАХ ДОМА ЗА ДВАДЦАТЬ ЧЕТЫРЕ ЧАСА',
        'featured_on' => 'УПОМИНАЕТСЯ В',
        'comments_title' => 'Комментарии',
        
        // Видео элементы
        'video_click_to_continue' => 'Нажмите, чтобы продолжить просмотр',
        'video_started' => 'Видео началось',
        'video_click_to_listen' => 'Нажмите, чтобы слушать!',
        'sound_button_text' => 'Нажмите чтобы включить звук',
        'popup_limited_offer' => 'Осталось всего 17 упаковок Hemolab.',
        'popup_order_now' => 'Заказать сейчас!',
        
        // Форма заказа
        'form_hurry' => 'Торопитесь! Промо-пакеты скоро закончатся.',
        'form_stock_update' => 'Обновление: Осталось только <span class="form-instock red">17</span> единиц на сегодня',
        'form_discount' => 'Скидка 79%',
        'form_old_price' => 'Старая цена',
        'form_new_price' => 'Новая цена',
        'form_timer_text' => 'Закончится через',
        'form_name_label' => 'Ваше имя:',
        'form_name_placeholder' => 'Имя',
        'form_phone_label' => 'Ваш номер телефона:',
        'form_phone_placeholder' => 'Номер телефона',
        'form_submit_button' => 'Отправить заказ',
        'form_discount_note' => '*Эта скидка применяется к одной упаковке за курс лечения.',
        
        // Комментарии - общие тексты
        'comment_like' => 'Нравится',
        'comment_reply' => 'Ответить',
        
        // Комментарии как массив
        'comments' => [
            [
                'name' => 'Анна Соколова',
                'img' => 'w1.jpg',
                'text' => 'Это немного спорно, но я думаю, что стоит посмотреть и сформировать свое мнение...',
                'likes' => '8',
                'time' => '3 минуты'
            ],
            [
                'name' => 'Ирина Петрова',
                'img' => 'w2.jpg',
                'text' => 'Я только начала смотреть, и это видео полностью изменило мой взгляд на лечение боли в суставах.',
                'likes' => '9',
                'time' => '5 минут'
            ],
            // Остальные комментарии...
        ]
    ],
    
    // Кастомный язык - теперь испанский, с русскими комментариями
    'custom' => [
        // Общие
        'site_title' => 'Artex - Cómo aliviar el dolor de articulaciones en casa', // Название сайта
        'site_description' => 'Método natural para aliviar el dolor de articulaciones en 24 horas con Artex', // Описание сайта
        
        // Заголовки
'header_title' => '¡DEDICA TRES MINUTOS A VER ESTE VIDEO Y <span class="green_bg">ALIVIA EL DOLOR EN TUS ARTICULACIONES EN VEINTICUATRO HORAS!</span>',
        'featured_on' => 'APARECE EN', // Блок "Упоминается в"
        'comments_title' => 'Comentarios', // Заголовок комментариев
        
        // Видео элементы
        'video_click_to_continue' => 'Haz clic para continuar viendo', // Клик для продолжения видео
        'video_started' => 'El video ha comenzado', // Видео началось
        'video_click_to_listen' => 'Haz clic para escuchar!', // Клик для прослушивания
        'popup_limited_offer' => 'Solo quedan 17 paquetes de Artex para aliviar el dolor articular.', // Всплывающее окно: лимитированное предложение
        'popup_order_now' => '¡Haz tu pedido ahora!', // Всплывающее окно: заказать сейчас
        
        // Форма заказа
        'form_hurry' => '¡Date prisa! Los paquetes promocionales se acabarán pronto.', // Срочность
        'form_stock_update' => 'Actualización: Solo quedan <span class="form-instock red">17</span> unidades hoy', // Остаток товара
        'form_discount' => '50% de descuento', // Скидка
        'form_old_price' => 'Precio anterior', // Старая цена
        'form_new_price' => 'Nuevo precio', // Новая цена
        'form_timer_text' => 'Termina en', // Таймер
        'form_name_label' => 'Tu nombre:', // Метка поля имя
        'form_name_placeholder' => 'Nombre', // Плейсхолдер имя
        'form_phone_label' => 'Tu número de teléfono:', // Метка поля телефон
        'form_phone_placeholder' => 'Número de teléfono', // Плейсхолдер телефон
        'form_submit_button' => 'Enviar pedido', // Кнопка отправки
        'form_discount_note' => '*Este descuento se aplica a un paquete por tratamiento.', // Примечание о скидке
        
        // Комментарии - общие тексты
        'comment_like' => 'Me gusta', // Нравится
        'comment_reply' => 'Responder', // Ответить
        
        // Комментарии как массив
        'comments' => [
            [
                'name' => 'Ana López', // Имя
                'img' => 'w1.jpg', // Аватар
                'text' => 'Es un poco controvertido, pero creo que vale la pena verlo y formar mi propia opinión sobre el tratamiento de las articulaciones...', // Текст комментария
                'likes' => '8', // Лайки
                'time' => 'hace 3 minutos' // Время
            ],
            [
                'name' => 'María García',
                'img' => 'w2.jpg',
                'text' => 'Acabo de empezar a verlo y este video cambió completamente mi perspectiva sobre el tratamiento del dolor en las articulaciones.',
                'likes' => '9',
                'time' => 'hace 5 minutos'
            ],
            [
                'name' => 'Carlos Pérez',
                'img' => 'm1.jpg',
                'text' => 'Voy a compartir este video con otros. Conozco a varias personas que sufren de dolor en las articulaciones y esto podría mejorar sus vidas.',
                'likes' => '11',
                'time' => 'hace 7 minutos'
            ],
            [
                'name' => 'Lucía Fernández',
                'img' => 'w3.jpg',
                'text' => 'Al principio era escéptica, pero después de 3 días usando Artex, noté la diferencia. El dolor en las articulaciones y la inflamación disminuyeron. Seguiré con el tratamiento y veré el resultado final.',
                'likes' => '15',
                'time' => 'hace 9 minutos'
            ],
            [
                'name' => 'Miguel Torres',
                'img' => 'm2.jpg',
                'text' => 'No me gusta mucho el formato del video... ¡pero lo entiendo completamente! Confío en la eficacia.',
                'likes' => '17',
                'time' => 'hace 10 minutos'
            ],
            [
                'name' => 'Sofía Romero',
                'img' => 'w4.jpg',
                'text' => '¡Guau, @Lucía! Empecé hoy junto a mi esposo... ambos sufrimos de dolor en las articulaciones.',
                'likes' => '22',
                'time' => 'hace 12 minutos'
            ],
            [
                'name' => 'Javier Ruiz',
                'img' => 'm3.jpg',
                'text' => 'Siempre hay una solución... pero nadie la ve, ¡gracias por traer esperanza!',
                'likes' => '19',
                'time' => 'hace 14 minutos'
            ],
            [
                'name' => 'Elena Martínez',
                'img' => 'w5.jpg',
                'text' => 'Nunca había oído hablar de esto antes, suena raro, pero tiene mucho sentido... ¡lo estoy probando y en los primeros días ya funciona!',
                'likes' => '30',
                'time' => 'hace 16 minutos'
            ],
            [
                'name' => 'Pedro Sánchez',
                'img' => 'm4.jpg',
                'text' => '¿Esto realmente funciona?',
                'likes' => '27',
                'time' => 'hace 18 minutos'
            ],
            [
                'name' => 'Carmen Díaz',
                'img' => 'w6.jpg',
                'text' => 'El médico me recomendó cirugía por mis problemas de articulaciones, pero tenía miedo. Finalmente probé Artex como última opción. ¡Y funcionó! Después de 3 semanas, el dolor en mis articulaciones casi desapareció sin cirugía. ¡El Dr. Tony es un salvador!',
                'likes' => '31',
                'time' => 'hace 20 minutos'
            ]
        ]
    ]
    ,

    // Хинди — нейтральные тексты (обезличено для публичного репозитория)
    'hi' => [
        'site_title' => 'Demo Product | Best Solution',
        'site_description' => 'Natural and effective solution for your needs.',

        'header_title' => 'मैंने <span class="green_bg">कई समाधान आजमाए</span> — और यह काम करता है…',
        'featured_on' => 'में दिखाया गया',
        'comments_title' => 'उपयोगकर्ता समीक्षाएं',

        'video_click_to_continue' => 'देखना जारी रखने के लिए क्लिक करें',
        'video_started' => 'वीडियो शुरू हो गया',
        'video_click_to_listen' => 'सुनने के लिए क्लिक करें!',
        'sound_button_text' => 'ध्वनि चालू करने के लिए क्लिक करें',

        'popup_limited_offer' => 'केवल 17 पैक हमारे पास बचे हैं!',
        'popup_order_now' => 'अभी ऑर्डर करें!',

        'form_hurry' => 'जल्दी करें! ऑफर खत्म हो रहा है।',
        'form_stock_update' => 'अपडेट: आज केवल <span class="form-instock red">17</span> यूनिट बचे हैं',
        'form_discount' => '50% छूट',
        'form_old_price' => 'पुरानी कीमत',
        'form_new_price' => 'नई कीमत',
        'form_timer_text' => 'समाप्त होने में',
        'form_name_label' => 'आपका नाम:',
        'form_name_placeholder' => 'नाम',
        'form_phone_label' => 'आपका फोन नंबर:',
        'form_phone_placeholder' => 'फोन नंबर',
        'form_submit_button' => 'ऑर्डर भेजें',
        'form_discount_note' => '*यह छूट एक कोर्स के लिए एक पैक पर लागू होती है।',

        'comment_like' => 'लाइक',
        'comment_reply' => 'जवाब दें',

        'comments' => [
            ['name' => 'राजेश कुमार', 'img' => 'm1.jpg', 'text' => 'उत्पाद ने मेरी समस्या का समाधान कर दिया। पहले परेशानी थी, अब सब ठीक है।', 'likes' => '45', 'time' => '5 मिनट पहले'],
            ['name' => 'प्रिया शर्मा', 'img' => 'w1.jpg', 'text' => 'बहुत बढ़िया उत्पाद। परिवार में सभी इस्तेमाल कर रहे हैं।', 'likes' => '38', 'time' => '12 मिनट पहले'],
            ['name' => 'अमित सिंह', 'img' => 'm2.jpg', 'text' => 'कई चीज़ें आजमाईं, यह सबसे प्रभावी निकला। कीमत अच्छी है।', 'likes' => '52', 'time' => '18 मिनट पहले'],
            ['name' => 'कविता गुप्ता', 'img' => 'w2.jpg', 'text' => 'तीन दिन में ही आराम महसूस हुआ। अब रोज इस्तेमाल करती हूं।', 'likes' => '29', 'time' => '25 मिनट पहले'],
            ['name' => 'संजय यादव', 'img' => 'm3.jpg', 'text' => 'सलाह पर लिया। बहुत अच्छा असर। दोस्तों को भी सुझाया।', 'likes' => '41', 'time' => '32 मिनट पहले'],
            ['name' => 'मीनाक्षी पटेल', 'img' => 'w3.jpg', 'text' => 'क्वालिटी अच्छी। पैकेजिंग प्रोफेशनल। जल्दी मिला।', 'likes' => '35', 'time' => '40 मिनट पहले'],
            ['name' => 'रवि वर्मा', 'img' => 'm4.jpg', 'text' => 'मेरे लिए बहुत काम की चीज़। समस्या दूर हुई। वैल्यू अच्छी।', 'likes' => '48', 'time' => '48 मिनट पहले'],
            ['name' => 'अनिता राणा', 'img' => 'w4.jpg', 'text' => 'पहले डर लगता था, अब कॉन्फिडेंस आ गया। थैंक्यू।', 'likes' => '33', 'time' => '55 मिनट पहले'],
            ['name' => 'विकास जैन', 'img' => 'm5.jpg', 'text' => 'रिसर्च करके लिया। अच्छा फैसला। दोबारा जरूर लूंगा।', 'likes' => '27', 'time' => '1 घंटा पहले'],
            ['name' => 'पूजा अग्रवाल', 'img' => 'w5.jpg', 'text' => 'परिवार के लिए जरूरी उत्पाद। सभी को इस्तेमाल करना चाहिए।', 'likes' => '39', 'time' => '1 घंटा पहले']
        ]
    ],

    // Итальянский язык — ABSlim (похудение)
    'it' => [
        // Общие
        'site_title' => 'ABSlim — dimagrimento sano e naturale',
        'site_description' => 'Percorso semplice per la snellitezza: ABSlim aiuta a perdere peso in modo naturale e confortevole.',
        // Заголовки
'header_title' => 'IL <span class="green_bg">1 GIUGNO 2025</span> GLI SCIENZIATI HANNO RIVELATO <span class="green_bg">LA VERA CAUSA DEL SOVRAPPESO</span>… GUARDA IL VIDEO ORA PER CONOSCERE <span class="green_bg">LA VERITÀ</span>',
        'featured_on' => 'MENZIONATO SU',
        'comments_title' => 'Commenti',
        // Видео элементы
        'video_click_to_continue' => 'Tocca per continuare la visione',
        'video_started' => 'Il video è iniziato',
        'video_click_to_listen' => 'Tocca per ascoltare!',
        'sound_button_text' => 'Premi per attivare l\'audio',
        'popup_limited_offer' => 'Offerta limitata: rimangono 17 confezioni di ABSlim.',
        'popup_order_now' => 'Ordina adesso!',
        // Форма заказа
        'form_hurry' => 'Affrettati! I pacchetti promozionali stanno finendo.',
        'form_stock_update' => 'Aggiornamento: Rimangono solo <span class="form-instock red">17</span> pezzi per oggi',
        'form_discount' => 'Sconto 50%',
        'form_old_price' => 'Prezzo precedente',
        'form_new_price' => 'Prezzo attuale',
        'form_timer_text' => 'Termina tra',
        'form_name_label' => 'Il tuo nome:',
        'form_name_placeholder' => 'Nome e cognome',
        'form_phone_label' => 'Il tuo numero di telefono:',
        'form_phone_placeholder' => 'Numero di telefono',
        'form_submit_button' => 'Invia l’ordine',
        'form_discount_note' => '*Lo sconto si applica a una confezione per il corso.',
        // Комментарии — короткий набор примеров
        'comment_like' => 'Mi piace',
        'comment_reply' => 'Rispondi',
        'comments' => [
            ['name' => 'Anna Rossi','img' => 'w1.jpg','text' => 'Con ABSlim ho perso 6 kg in tre settimane, senza diete rigide. Mi sento leggera e dormo meglio.','likes' => '24','time' => '3 minuti fa'],
            ['name' => 'Luca Bianchi','img' => 'm1.jpg','text' => 'Appetito sotto controllo già dal 2° giorno. Energia su, peso giù: -3,2 kg in 10 giorni.','likes' => '18','time' => '5 minuti fa'],
            ['name' => 'Giulia Verdi','img' => 'w2.jpg','text' => 'Pancia sgonfia e circonferenza vita -5 cm in due settimane. Facilissimo da seguire.','likes' => '31','time' => '9 minuti fa'],
            ['name' => 'Marco Esposito','img' => 'm2.jpg','text' => 'Avevo superato i 100 kg. Con ABSlim ho iniziato a perdere 1–2 kg ogni 24 ore all’inizio. Sorprendente.','likes' => '27','time' => '12 minuti fa'],
            ['name' => 'Valentina Ricci','img' => 'w3.jpg','text' => 'Ho 41 anni: metabolismo lento. Eppure il grasso sui fianchi è sceso 3 volte più veloce rispetto alle mie diete passate.','likes' => '22','time' => '14 minuti fa'],
            ['name' => 'Paolo Conti','img' => 'm3.jpg','text' => 'Niente palestra estrema. Camminate leggere + ABSlim = -7,4 kg in 3 settimane.','likes' => '16','time' => '16 minuti fa'],
            ['name' => 'Elisa Romano','img' => 'w4.jpg','text' => 'Zero nervosismo da “dieta”. Finalmente un metodo naturale che non mi fa rinunciare a tutto.','likes' => '29','time' => '18 minuti fa'],
            ['name' => 'Davide Moretti','img' => 'm4.jpg','text' => 'La bilancia si muove ogni giorno. Colpisce soprattutto la pancia: -6 cm in 10 giorni.','likes' => '21','time' => '21 minuti fa'],
            ['name' => 'Chiara Galli','img' => 'w5.jpg','text' => 'Mi piace l’effetto “benessere”: sonno profondo, meno fame serale, pelle più luminosa.','likes' => '25','time' => '24 minuti fa'],
            ['name' => 'Federico Greco','img' => 'm5.jpg','text' => 'Ho provato di tutto. Questo è l’unico metodo che mi ha dato risultati stabili senza effetto yo‑yo.','likes' => '33','time' => '27 minuti fa']
        ]
    ]
];

// Выбираем массив текстов для текущего языка
$texts = $localization[$active_language];

// Сохраняем выбранный язык в сессии для использования на других страницах
$_SESSION['active_language'] = $active_language;
$_SESSION['country_code'] = $country_code;
// Функция для получения текста по ключу
function t($key) {
    global $texts;
    return isset($texts[$key]) ? $texts[$key] : "[$key]";
}