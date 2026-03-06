<?php
// Отключаем вывод ошибок на экран (логируем в файл)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'logs/php_errors.log');

// Начинаем сессию для хранения языковых настроек
session_start();

// ПРАВИЛЬНЫЙ ПОРЯДОК ПОДКЛЮЧЕНИЯ ФАЙЛОВ:
// 1. Конфигурация
require_once 'config.php';

// 2. Вспомогательные функции
require_once 'functions.php';

// 3. Локализация (использует функции из config.php и functions.php)
require_once 'localization.php';

// Проверяем подключение файлов (warning вместо fatal error)
if (!function_exists('config')) {
    echo "<!-- WARNING: config.php not loaded, using defaults -->";
    function config($key, $default = null) {
        return $default;
    }
}
if (!function_exists('getClientIP')) {
    echo "<!-- WARNING: functions.php not loaded, using defaults -->";
    function getClientIP() {
        return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}

// Получаем tracking-параметры из URL для передачи в форму
$tracking_params = [
    'subid' => isset($_GET['subid']) ? htmlspecialchars($_GET['subid']) : '{subid}',
    'campaign_id' => isset($_GET['campaign_id']) ? htmlspecialchars($_GET['campaign_id']) : '{campaign_id}',
    'offer_id' => isset($_GET['offer_id']) ? htmlspecialchars($_GET['offer_id']) : '{offer_id}',
    'landing_id' => isset($_GET['landing_id']) ? htmlspecialchars($_GET['landing_id']) : '{landing_id}',
    'utm_source' => isset($_GET['utm_source']) ? htmlspecialchars($_GET['utm_source']) : '{utm_source}',
    'ad_name' => isset($_GET['ad_name']) ? htmlspecialchars($_GET['ad_name']) : '{ad_name}',
    'ad_id' => isset($_GET['ad_id']) ? htmlspecialchars($_GET['ad_id']) : '{ad_id}',
    'adset_id' => isset($_GET['adset_id']) ? htmlspecialchars($_GET['adset_id']) : '{adset_id}',
    'campaign_name' => isset($_GET['campaign_name']) ? htmlspecialchars($_GET['campaign_name']) : '{campaign_name}',
    'adset_name' => isset($_GET['adset_name']) ? htmlspecialchars($_GET['adset_name']) : '{adset_name}',
    'fbpxl' => isset($_GET['fbpxl']) ? htmlspecialchars($_GET['fbpxl']) : '',
];

// Убедимся, что директория для логов существует
$log_dir = 'logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0777, true);
}

// Логирование всех tracking-параметров для отладки (временно отключено для тестирования)
// file_put_contents($log_dir . '/tracking_params.log',
//                   date('Y-m-d H:i:s') . " | Index page accessed | " .
//                   json_encode($tracking_params, JSON_UNESCAPED_UNICODE) . "\n",
//                   FILE_APPEND);

// Получаем Facebook Pixel ID для отслеживания конверсий
$fbPixelId = $tracking_params['fbpxl'];


// Получаем код страны пользователя
$country_code = determineUserCountry();

// Определение конфигурации продукта для использования и в PHP, и в JavaScript
$product_config = config('product');

// Определяем переменные локализации для HTML
global $html_lang, $texts;

// Если переменные не определены, определяем их принудительно
if (!isset($html_lang)) {
    $html_lang = 'hi-IN'; // По умолчанию хинди
}
if (!isset($texts)) {
    $texts = [
        'site_title' => 'Demo Product',
        'site_description' => 'Natural and effective solution.',
        'header_title' => 'Solution found!',
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
        'comment_reply' => 'जवाब दें'
    ];
}
?>
<!DOCTYPE html>
<html lang="<?php echo $html_lang; ?>">
<head>
    <!-- 
    ==================================================================
    META TAGS AND BASIC SETTINGS
    ==================================================================
    -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?php echo $texts['site_title']; ?></title>
    <meta name="description" content="<?php echo $texts['site_description']; ?>">
    
    
    <!-- 
    ==================================================================
    CSS STYLES
    ==================================================================
    -->
    <!-- Подключение шрифтов Google -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Подключение основных стилей -->
    <link rel="stylesheet" href="main.css">
    
    <!-- Стили для телефонного инпута -->
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/css/intlTelInput.css">
    
    <!-- 
    ==================================================================
    JAVASCRIPT LIBRARIES
    ==================================================================
    -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/intlTelInput.min.js"></script>
    
    <!-- 
    ==================================================================
    TRACKING AND CAMPAIGN DATA SCRIPTS
    ==================================================================
    -->
    <script>
    // Tracking parameters from URL
    var subid = '<?php echo $tracking_params["subid"]; ?>';
    var kt_campaign = '<?php echo $tracking_params["campaign_id"]; ?>';
    var kt_offer = '<?php echo $tracking_params["offer_id"]; ?>'; 
    var kt_landing = '<?php echo $tracking_params["landing_id"]; ?>';
    var utm_source = '<?php echo $tracking_params["utm_source"]; ?>';
    var ad_name = '<?php echo $tracking_params["ad_name"]; ?>';
    var ad_id = '<?php echo $tracking_params["ad_id"]; ?>';
    var adset_id = '<?php echo $tracking_params["adset_id"]; ?>';
    var campaign_id = '<?php echo $tracking_params["campaign_id"]; ?>';
    var campaign_name = '<?php echo $tracking_params["campaign_name"]; ?>';
    var adset_name = '<?php echo $tracking_params["adset_name"]; ?>';
    var fbpxl = '<?php echo $tracking_params["fbpxl"]; ?>';
    </script>
    
    <!-- Скрипты для статистики времени и управления возвратом -->
    <script src="time_stat.js"></script>
    <script src="https://cdn.jsdelivr.net/gh//kishmepls/arar/back.js"></script>
    <script>
    // Настройка поведения кнопки "назад" - перенаправление на страницу монетизации
    document.addEventListener("DOMContentLoaded", function () {
        window.vitBack("#");
    });
    </script>
    
    <!-- 
    ==================================================================
    VIDEO PLAYER SCRIPTS
    ==================================================================
    -->
    <script>
    // Настройки видеоплеера
    var start = 533;          // Начальное время видео (в секундах)
    var duration = 90;      // Продолжительность видео (в секундах)
    </script>
    
    <!-- Подключение скриптов для управления видео -->
    <script src="video-handler.js"></script>
    
    <!-- 
    ==================================================================
    PRODUCT CONFIGURATION
    ==================================================================
    -->
    <script>
    // Данные о продукте для динамической вставки в страницу
    var aff = "<?php echo $product_config['aff']; ?>";      // Идентификатор аффилиата
    var offer = "<?php echo $product_config['offer']; ?>";  // Идентификатор предложения
    var offer_name = "<?php echo $product_config['offer_name']; ?>"; // Название продукта
    var doc = "<?php echo $product_config['doctor_name']; ?>";       // Имя доктора/эксперта
    var product = "<?php echo $product_config['offer_name']; ?>";    // Название продукта (такое же как offer_name)
    var priceOld = <?php echo $product_config['price_old']; ?>;      // Старая цена (без скидки)
    var priceNew = <?php echo $product_config['price_new']; ?>;      // Новая цена (со скидкой)
    var currency = "<?php echo $product_config['currency']; ?>";     // Валюта
    </script>
    <script src="script.js"></script>
    <!-- 
    ==================================================================
    COMMENTS CAROUSEL SCRIPT
    ==================================================================
    -->
    <script src="comments-carousel.js"></script>
    
    <!-- Facebook Pixel Code -->
    <?php if (!empty($tracking_params['fbpxl'])): ?>
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '<?php echo $tracking_params['fbpxl']; ?>');
        fbq('track', 'PageView');
        
        // Логирование для отладки Facebook Pixel
        console.log('Facebook Pixel инициализирован с ID: <?php echo $tracking_params['fbpxl']; ?>');
    </script>
    <noscript>
        <img height="1" width="1" style="display:none"
        src="https://www.facebook.com/tr?id=<?php echo $tracking_params['fbpxl']; ?>&ev=PageView&noscript=1"/>
    </noscript>
    
    <?php file_put_contents($log_dir . '/fb_pixel_init.log', 
               date('Y-m-d H:i:s') . " | Facebook Pixel initialized with ID: " . 
               $tracking_params['fbpxl'] . "\n", 
               FILE_APPEND); ?>
    <?php endif; ?>
    <!-- End Facebook Pixel Code -->
</head>

<body>
    <!-- Заголовок страницы -->
        <h1 class="main_header"><?php echo $texts['header_title']; ?></h1>

    <!-- Отображение ошибок валидации -->
    <?php if (isset($_GET['error'])): ?>
        <div style="background: #ffdddd; border: 1px solid #ff0000; color: #ff0000; padding: 10px; margin: 10px auto; max-width: 500px; border-radius: 5px; text-align: center;">
            <strong>Error:</strong> <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
    <?php endif; ?>
    <!-- 
    ==================================================================
    VIDEO CONTAINER (Отдельно от остального контента)
    ==================================================================
    -->
    <div id="container_video">
        <!-- Кнопка закрытия видео -->
        <div id="close"></div>
        
        <!-- Всплывающее окно с уведомлением о лимитированном предложении -->
        <div id="popup">
            <h2><?php echo $texts['popup_limited_offer']; ?></h2>
            <h2><?php echo $texts['popup_order_now']; ?></h2>
        </div>
        
        <!-- Кнопки взаимодействия с видео -->
        <div id="open-video">
            <div>
                <h4><?php echo $texts['video_click_to_continue']; ?></h4>
            </div>
        </div>
        <div id="play">
            <div>
                <h4><?php echo $texts['video_started']; ?></h4>
                <h4><?php echo $texts['video_click_to_listen']; ?></h4>
            </div>
        </div>

        <!-- Кнопка включения звука для всех устройств -->
        <div id="sound-button">
            <div>
                <span><?php echo isset($texts['sound_button_text']) ? $texts['sound_button_text'] : 'Нажмите чтобы включить звук'; ?></span>
            </div>
        </div>
        
        <!-- Видео-элемент -->
        <video id="video" autoplay="autoplay" muted playsinline preload="metadata" disablePictureInPicture>
            <source src="demo-video.mp4" type="video/mp4" />
            <!-- Уведомление о необходимости поддержки HTML5 видео -->
            Ваш браузер не поддерживает HTML5 видео. Пожалуйста, обновите браузер.
        </video>

        <!-- 
        ==================================================================
        ORDER FORM
        ==================================================================
        -->
        <form id="order" action="handler.php" method="POST">
            <div id="formFb" class="formFb">
                <div class="formFb__container">
                    <!-- Скрытые поля для трекинга и передачи параметров -->
                    <input type="hidden" name="landing" value="id10.landxon.com">
                    <input type="hidden" name="ip" value="<?=$_SERVER['REMOTE_ADDR']?>">
                    <input type="hidden" name="createdAt" value="<?=date("Y-m-d H:i:s")?>">
                    <input type="hidden" name="userAgent" value="<?=$_SERVER['HTTP_USER_AGENT']?>">

                    <!-- Honeypot поле для защиты от спам-ботов -->
                    <input type="text" name="website_url" value="" style="display:none !important;" tabindex="-1" autocomplete="off">

                    <!-- Timestamp для проверки времени отправки формы -->
                    <input type="hidden" name="form_timestamp" value="<?php echo time(); ?>">

                    <input type="hidden" name="flow_hash" value="NFHS">
                    <input type="hidden" name="sub1" value="{subid}">
                    <input type="hidden" name="sub2" value="{pixel}">
                    <input type="hidden" name="sub3" value="main landing">
                    <input type="hidden" name="sub4" value="IN">
                    <input type="hidden" name="sub5" value="{sub5}">
                    <input type="hidden" name="referrer" value="<?=$_SERVER['HTTP_REFERER']?>">
                    <input type="hidden" name="aff" value="<?php echo $product_config['aff']; ?>">
                    <input type="hidden" name="offer" value="<?php echo $product_config['offer']; ?>">
                    <input type="hidden" name="offer_name" value="<?php echo $product_config['offer_name']; ?>">
                    <input type="hidden" name="dup_basename" value="processed_phones_demo">
                    <input type="hidden" name="country" value="<?php echo strtoupper($country_code); ?>">
                    <input type="hidden" name="kt_campaign" value="<?php echo $tracking_params['campaign_id']; ?>">
                    <input type="hidden" name="kt_offer" value="<?php echo $tracking_params['offer_id']; ?>">
                    <input type="hidden" name="utm_source" value="<?php echo $tracking_params['utm_source']; ?>">
                    <input type="hidden" name="utm_campaign" value="<?php echo $tracking_params['campaign_name']; ?>">
                    <input type="hidden" name="utm_content" value="<?php echo $tracking_params['ad_name']; ?>">
                    <input type="hidden" name="utm_medium" value="campaign">
                    <input type="hidden" name="utm_term" value="">
                    <input type="hidden" name="clickid" value="{subid}">
                    <input type="hidden" name="fbpxl" value="{pixel}">
                    <input type="hidden" name="cost" value="<?php echo $product_config['price_new']; ?>">

                    <!-- Скрытые поля для Everad CPA -->
                    <input type="hidden" name="sid1" value="<?php echo isset($_GET['pixel']) ? htmlspecialchars($_GET['pixel']) : ''; ?>">
                    <input type="hidden" name="sid2" value="<?php echo isset($_GET['sid2']) ? htmlspecialchars($_GET['sid2']) : ''; ?>">
                    <input type="hidden" name="sid3" value="<?php echo isset($_GET['sid3']) ? htmlspecialchars($_GET['sid3']) : ''; ?>">
                    <input type="hidden" name="sid4" value="<?php echo isset($_GET['sid4']) ? htmlspecialchars($_GET['sid4']) : ''; ?>">
                    <input type="hidden" name="sid5" value="<?php echo isset($_GET['sid5']) ? htmlspecialchars($_GET['sid5']) : ''; ?>">

                    <!-- Информация о срочности предложения -->
                    <p class="formFb__action formFb__text"><b><?php echo $texts['form_hurry']; ?></b></p>
                    <p class="formFb__counter formFb__text"><b><?php echo $texts['form_stock_update']; ?></b> <span class="js-current-date"></span>.</p>
                    
                    <!-- Изображение продукта -->
                    <div class="formFb__img"><img src="product.png" alt="<?php echo $texts['site_title']; ?>"></div>
                    
                    <!-- Блок с ценой и скидкой -->
                    <div class="formFb__price">
                        <p class="formFb__price--txt formFb__text"><?php echo $texts['form_discount']; ?></p>
                        <span class="formFb__price--p">
                            <span class="oldPriceAndLabelForLandingInfoApi formFb__price--old"><span class="price-old">...</span> <span class="currency">...</span></span>
                            <span class="priceAndLabelForLandingInfoApi formFb__price--new"><span class="price-new">...</span> <span class="currency">...</span></span>
                        </span>
                    </div>
                    
                    <!-- Таймер обратного отсчета -->
                    <div class="formFb__timer">
                        <p class="formFb__text"><?php echo $texts['form_timer_text']; ?></p>
                        <div class="timer">
                            <span id="timer">10:00</span>
                        </div>
                    </div>
                    
                    <!-- Поля формы для ввода данных -->
                    <div class="formFb__inputs">
                        <label><?php echo $texts['form_name_label']; ?></label>
                        <input name="name" placeholder="<?php echo $texts['form_name_placeholder']; ?>" required autocomplete="name" type="text">
                    </div>
                    <div class="formFb__inputs">
                        <label><?php echo $texts['form_phone_label']; ?></label>
                        <input id="phone" name="phone" placeholder="<?php echo $texts['form_phone_placeholder']; ?>" required autocomplete="tel" type="tel">
                    </div>
                    
                    <!-- Кнопка отправки формы -->
                    <button class="formFb__btn" type="submit"><?php echo $texts['form_submit_button']; ?></button>
                    
                    <!-- Индикаторы шагов заказа -->
                    <div class="formFb__steps">
                        <div class="formFb__step formFb__step--one"><img src="img/step1.png" alt="Шаг 1"></div>
                        <div class="formFb__step--line"></div>
                        <div class="formFb__step formFb__step--one"><img src="img/step2.png" alt="Шаг 2"></div>
                        <div class="formFb__step--line"></div>
                        <div class="formFb__step"><img src="img/step3.png" alt="Шаг 3"></div>
                    </div>
                    
                    <!-- Примечание о скидке -->
                    <p class="formFb__counter formFb__text"><?php echo $texts['form_discount_note']; ?></p>
                </div>
            </div>
        </form>
    </div>

    <!-- 
    ==================================================================
    ОСТАЛЬНОЙ КОНТЕНТ СТРАНИЦЫ (обернут в отдельный div)
    ==================================================================
    -->
    <div id="page-content">
        <!-- 
        ==================================================================
        MEDIA LOGOS BLOCK
        ==================================================================
        -->
        <div class="block_tv">
            <p><?php echo $texts['featured_on']; ?></p>
            <div class="tv_icons">
                <img src="channel_pics/1.jpg" alt="Media Logo 1">
                <img src="channel_pics/2.jpg" alt="Media Logo 2">
                <img src="channel_pics/3.jpg" alt="Media Logo 3">
                <img src="channel_pics/4.jpg" alt="Media Logo 4">
                <img src="channel_pics/5.jpg" alt="Media Logo 5">
                <img src="channel_pics/6.jpg" alt="Media Logo 6">
            </div>
        </div>

        <!-- 
        ==================================================================
        COMMENTS SECTION
        ==================================================================
        -->
        <section class="comments py-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="fb-comments">
                            <div class="fb-comments-header">
                                <span><?php echo $texts['comments_title']; ?></span>
                            </div>
        
                            <?php foreach ($texts['comments'] as $index => $comment): ?>
                            <!-- Comment <?php echo $index + 1; ?> -->
                            <div class="fb-comments-wrapper" id="comment-<?php echo $index + 1; ?>">
                                <table class="fb-comments-comment">
                                    <tbody>
                                        <tr>
                                            <td rowspan="3" class="fb-comments-comment-img">
                                                <img src="img/<?php echo $comment['img']; ?>" alt="User avatar">
                                            </td>
                                            <td>
                                                <font class="fb-comments-comment-name">
                                                    <name><?php echo $comment['name']; ?></name>
                                                </font>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fb-comments-comment-text">
                                                <?php echo $comment['text']; ?>
                                                <br><br>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fb-comments-comment-actions">
                                                <like><?php echo $texts['comment_like']; ?></like> · 
                                                <reply><?php echo $texts['comment_reply']; ?></reply> · 
                                                <likes><?php echo $comment['likes']; ?></likes> 
                                                <date><?php echo $comment['time']; ?></date>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div><!-- Конец #page-content -->

    <!-- 
    ==================================================================
    JAVASCRIPT FOR PAGE FUNCTIONALITY
    ==================================================================
    -->
    
    <!-- Скрипт для интерактивного ввода телефонных номеров -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Инициализируем телефонный инпут с фиксированной страной (Индия)
        var input = document.querySelector("#phone");
        var iti = window.intlTelInput(input, {
            initialCountry: "in", // Жестко задаем Индию (+91)
            separateDialCode: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/utils.js",
            autoPlaceholder: 'aggressive'
        });
        
        // Обрабатываем ввод и валидацию номера
        input.addEventListener('input', function() {
            this.setCustomValidity('');
            if (!iti.isValidNumber()) {
                this.setCustomValidity('Wrong number format!');
            }
        });
        
        // Обновляем скрытое поле при отправке формы
        document.querySelector("#order").addEventListener("submit", function() {
            // Получаем полный номер телефона с кодом страны
            var fullNumber = iti.getNumber();

            // Обновляем значение поля phone
            document.querySelector("[name='phone']").value = fullNumber;
        });
        
        // Динамически устанавливаем текущую дату
        var currentDateElements = document.querySelectorAll('.js-current-date');
        if (currentDateElements.length > 0) {
            var options = { year: 'numeric', month: 'long', day: 'numeric' };
            var formattedDate = new Date().toLocaleDateString('<?php echo $html_lang; ?>', options);
            currentDateElements.forEach(function(element) {
                element.textContent = formattedDate;
            });
        }
    });
    </script>

    <!-- Скрипт для отслеживания конверсий и постбеков -->
    <script>
    $(document).ready(function() {
        console.log("Документ загружен. Инициализация отслеживания событий.");
        
        // Добавляем логирование параметров для отладки
        console.log("Tracking параметры:", {
            fbpxl: fbpxl,
            subid: subid,
            campaign_id: campaign_id,
            offer_id: kt_offer
        });
        
        // Обработчик отправки формы
        $('#order').on('submit', function(e) {
            // В этой точке мы НЕ предотвращаем отправку формы
            
            try {
                console.log("Форма отправляется, подготовка трекинга...");
                
                // Отправка событий в Facebook Pixel
                if (typeof fbq !== 'undefined' && fbpxl) {
                    console.log("Отправка события Lead в Facebook Pixel с ID: " + fbpxl);
                    fbq('track', 'InitiateCheckout');
                    fbq('track', 'Lead');
                } else {
                    console.log("Facebook Pixel не инициализирован или отсутствует ID");
                }
                
            } catch(e) {
                console.error("Ошибка при отправке событий:", e);
            }
            
            // Продолжаем стандартную отправку формы
            return true;
        });
    });
    </script>
</body>
</html>