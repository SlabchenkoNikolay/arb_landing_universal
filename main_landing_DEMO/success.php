<?php
$log_dir = __DIR__ . '/logs';
if (!is_dir($log_dir)) {
    mkdir($log_dir, 0755, true);
}
$log_entry = [
    'timestamp' => date('Y-m-d H:i:s'),
    'get' => $_GET,
    'server' => [
        'request_uri' => $_SERVER['REQUEST_URI'] ?? '',
        'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? '',
        'http_referer' => $_SERVER['HTTP_REFERER'] ?? ''
    ]
];
file_put_contents($log_dir . '/success_debug.log', json_encode($log_entry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);
?>
<!DOCTYPE html>
<html lang="hi-IN">
<head>
    <!-- Основные метаданные -->
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="description" content="आपका ऑर्डर सफलतापूर्वक प्राप्त हो गया!">
    <title>ऑर्डर सफल - Demo Product</title>
    
    <!-- Подключение шрифтов и стилей -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;500;700&display=swap" rel="stylesheet">
    
    <style>
        body{margin:0;font-family:Roboto,sans-serif;font-weight:300;color:#141011}canvas{width:100%;height:100%;position:absolute}#particles-js{position:fixed;width:100%;height:100%;padding:0;background-color:#f2f2f2;background-repeat:no-repeat;z-index:999}@keyframes twist{0%{transform:scale(0)}60%{transform:scale(1.2)}70%{transform:scale(.9)}85%{transform:scale(1.1)}100%{transform:scale(1)}}#wrapper{position:relative;width:100vw;height:100vh}#content{position:absolute;top:0;bottom:0;left:0;right:0;max-width:520px;width:100%;height:430px;margin:auto;z-index:99999;text-align:center}.success-page-header{box-shadow:0px 0px 80px #E2E2E2;background:#fff;padding:39px 0;border-radius:20px}h2,h3{margin:0;font-weight:400}h2{font-size:24px;margin-top:20px;margin-bottom:5px}h3{font-size:16px}hr{background-color:#e2e2e2;height:1px;border:none;margin:32px 0}.success-page__text{font-size:24px;font-weight:500;line-height:30px;padding:0 50px;margin:0}.phone{font-weight:700;font-size:32px}.list-info{margin:10px 0;display:flex;justify-content:center;align-items:center}.edit-btn{display:flex;justify-content:center;align-items:center;text-decoration:none;background:#64aa75;color:#fff;width:75px;height:30px;font-size:16px;font-weight:400;margin-left:16px;border-radius:16px}.edit-btn span{margin-right:9px}.success-page-footer a{color:#64aa75;text-decoration:none}@media screen and (max-width:425px){#content{width:90%}}@media screen and (max-width:375px){.phone{font-size:26px}.success-page__text{padding:0 10px;font-size:22px}}@media screen and (max-width:320px){.phone{font-size:22px}}
    </style>
    
    <script>
    // Декодируем номер телефона когда страница загрузится
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const phone = urlParams.get('phone');
        
        if (phone) {
            // Декодируем и очищаем номер
            let cleanPhone = decodeURIComponent(phone);
            cleanPhone = cleanPhone.replace('%2B', '+');
            
            // Находим элемент с номером телефона и обновляем его
            const phoneElement = document.querySelector('.phone');
            if (phoneElement) {
                phoneElement.textContent = cleanPhone;
            }
        }
    });
    </script>
</head>

<body>

<!-- Facebook Pixel Code -->
<?php if (!empty($_GET['pixel']) && is_numeric($_GET['pixel'])): ?>
<script>
  !function(f,b,e,v,n,t,s)
  {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
  n.callMethod.apply(n,arguments):n.queue.push(arguments)};
  if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
  n.queue=[];t=b.createElement(e);t.async=!0;
  t.src=v;s=b.getElementsByTagName(e)[0];
  s.parentNode.insertBefore(t,s)}(window, document,'script',
  'https://connect.facebook.net/en_US/fbevents.js');

  fbq('init', '<?php echo htmlspecialchars($_GET['pixel']); ?>');
  fbq('track', 'Lead');
</script>
<noscript><img height="1" width="1" style="display:none"
  src="https://www.facebook.com/tr?id=<?php echo htmlspecialchars($_GET['pixel']); ?>&ev=Lead&noscript=1"
/></noscript>
<?php endif; ?>
<!-- End Facebook Pixel Code -->
    
<div id="particles-js"></div>
<div id="wrapper">
    <div id="content">
        <div class="mod success-page">
            <div class="success-page-header">
                <div class="success_icon">
                    <svg width="54" height="40" viewBox="0 0 54 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M54 5.88L19.92 40L0 20.06L5.87 14.18L19.91 28.24L48.13 0L54 5.88Z" fill="#F1A900"/>
                    </svg>
                </div>
                <h2 class="success-page__title">आपका ऑर्डर सफलतापूर्वक प्राप्त हो गया!</h2>
                <h3>हम जल्द ही आपसे संपर्क करेंगे</h3>
            </div>
            <div class="success-page-body">
                <hr>
                <div class="success-page__message">
                    <p class="success-page__text">कृपया अपना फोन नंबर जांचें:</p>
                    <div class="list-info">
                        <div class="phone"><?php echo isset($_GET['phone']) ? htmlspecialchars($_GET['phone']) : ''; ?></div>
                        <a href="javascript:history.back()" class="edit-btn">
                            <span>
                                <svg width="13" height="13" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M0 10.2733V12.9997H2.72533L10.7573 4.96775L8.032 2.24242L0 10.2733ZM12.8067 2.91842C13.0644 2.66075 13.0644 2.24508 12.8067 1.98742L11.0127 0.193417C10.755 -0.0642502 10.3393 -0.0642502 10.0817 0.193417L8.68267 1.59242L11.408 4.31775L12.8067 2.91842Z" fill="white"/>
                                </svg>
                            </span>
                            संपादित करें
                        </a>
                    </div>
                </div>
            </div>
            <div class="success-page-footer">
                <hr>
                <a href="#" class="success-page__message_fail__link">
                    <p class="success-page__message_fail">
                        आप इस पेज को बंद कर सकते हैं
                    </p>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Particles.js для фоновых частиц -->
<script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Применяем настройки цветов
        $('.success-page-header').css("box-shadow", '0px 0px 80px #E2E2E2');
        $('.success-page__message_fail__link').css("color", '#64aa75');
        $('.edit-btn').css("background", '#64aa75').css("color", '#fff');
        
        // Инициализация particles.js
        particlesJS("particles-js", {
            "particles": {
                "number": {
                    "value": 80,
                    "density": {
                        "enable": true,
                        "value_area": 800
                    }
                },
                "color": {
                    "value": '#739c3e'
                },
                "shape": {
                    "type": "circle"
                },
                "opacity": {
                    "value": 0.5,
                    "random": false
                },
                "size": {
                    "value": 3,
                    "random": true
                },
                "line_linked": {
                    "enable": true,
                    "distance": 150,
                    "color": '#739c3e',
                    "opacity": 0.4,
                    "width": 1
                },
                "move": {
                    "enable": true,
                    "speed": 6,
                    "direction": "none",
                    "random": false,
                    "straight": false,
                    "out_mode": "out",
                    "bounce": false
                }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": {
                    "onhover": {
                        "enable": true,
                        "mode": "repulse"
                    },
                    "onclick": {
                        "enable": true,
                        "mode": "push"
                    },
                    "resize": true
                }
            },
            "retina_detect": true
        });
    });
</script>
</body>
</html>
