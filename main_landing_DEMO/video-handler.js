$(document).ready(function() {
    // Переменные состояния
    var showPopup = false;
    var hidePopup = true;
    var showPlay = true;
    var isFullscreen = false;

    // Обработка изменения ориентации экрана (только для fullscreen)
    window.addEventListener('orientationchange', function() {
        setTimeout(function() {
            if (isFullscreen) {
                updateFullscreenLayout();
            }
        }, 500);
    });

    // Обработка изменения размера окна (только для fullscreen)
    window.addEventListener('resize', function() {
        if (isFullscreen) {
            updateFullscreenLayout();
        }
    });
    
    // Новая унифицированная логика для всех устройств
    console.log('Initializing video handler for all devices');

    // Скрываем старую кнопку play и open-video
    $('#play').hide();
    $('#open-video').hide();

    // Показываем кнопку включения звука
    $('#sound-button').show();

    // Видео начинает играть без звука автоматически
    $('#video').prop('muted', true);

    // Если видео не запустилось автоматически, пробуем запустить
    if ($('#video')[0].paused) {
        $('#video')[0].play().catch(error => {
            console.log('Autoplay failed:', error);
        });
    }
    

    
    // Автоматически запускаем видео без звука при загрузке страницы
    var video = document.getElementById('video');
    if (video) {
        video.muted = true;

        // Запрещаем пользовательский контроль видео
        video.controls = false; // Гарантируем отсутствие контролов
        video.disablePictureInPicture = true; // Отключаем picture-in-picture

        // Запрещаем паузу (кроме нашей программной)
        video.addEventListener('pause', function(e) {
            // Разрешаем паузу только если мы сами ее вызвали (для перехода к форме)
            if (!video.dataset.allowPause) {
                e.preventDefault();
                video.play().catch(console.error);
                console.log('Попытка паузы заблокирована');
            }
        });

        // Запрещаем перемотку
        video.addEventListener('seeked', function(e) {
            // Если перемотка была не нами - возвращаем на текущее время
            if (!video.dataset.allowSeek) {
                var currentTime = video.currentTime;
                // Небольшая задержка чтобы браузер обработал событие
                setTimeout(function() {
                    if (video.currentTime !== currentTime) {
                        video.currentTime = currentTime;
                        console.log('Попытка перемотки заблокирована');
                    }
                }, 10);
            }
        });

        // Блокируем контекстное меню (правый клик)
        video.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            console.log('Контекстное меню заблокировано');
        });

        // Запускаем видео
        video.play().catch(error => {
            console.log('Автоматическое воспроизведение не удалось:', error);
        });
    }
    
    // Обработчик клика на кнопку включения звука (для всех устройств)
    $('#sound-button').on('click', function() {
        console.log('Sound button clicked - enabling sound and fullscreen');

        // Скрываем кнопку звука
        $('#sound-button').fadeOut('fast');

        // Включаем звук
        $('#video').prop('muted', false);

        // Переводим в fullscreen
        $('#container_video').addClass('fullscreen');
        $('body').addClass('noscroll');
        $('#close').fadeIn();

        showPlay = false;
        isFullscreen = true;

        console.log('Video now in fullscreen with sound enabled');
    });
    
    // Модифицированный стиль fullscreen для контейнера в CSS
    function applyFullscreenCSS() {
        var cssRule = `
            #container_video.fullscreen {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                right: 0 !important;
                bottom: 0 !important;
                width: 100vw !important;
                height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
                z-index: 10000 !important;
                background-color: #000 !important;
                overflow: hidden !important;
                display: flex !important;
                justify-content: center !important;
                align-items: center !important;
                padding-bottom: 0 !important;
            }
            #container_video.fullscreen #video {
                width: auto !important;
                height: 100vh !important; /* вмещаем по высоте */
                object-fit: contain !important;
                object-position: center center !important;
                margin: 0 auto !important;
                padding: 0 !important;
                max-width: 100vw !important;
                max-height: 100vh !important;
                border: none !important;
                border-radius: 0 !important;
                box-shadow: none !important;
                overflow: visible !important;
            }
        `;
        
        // Добавляем стили динамически, чтобы быть уверенными, что они применены
        var style = document.createElement('style');
        style.type = 'text/css';
        if (style.styleSheet) {
            style.styleSheet.cssText = cssRule;
        } else {
            style.appendChild(document.createTextNode(cssRule));
        }
        document.head.appendChild(style);
    }
    
    // Применяем стили сразу при загрузке
    applyFullscreenCSS();
    
    // Обработчик клика на кнопку закрытия
    $('#close').on('click', function() {
        console.log('Close button clicked');
        exitFullscreen();
    });
    
    // Функция выхода из нашего CSS fullscreen
    function exitFullscreen() {
        console.log('Exiting fullscreen - returning to compact mode');
        $('#close').hide();

        // Выходим из fullscreen
        $('#container_video').removeClass('fullscreen');
        $('body').removeClass('noscroll');

        // Возвращаем видео в компактный режим
        var $video = $('#video');
        if ($video.length) {
            var videoElement = $video[0];
            // Выключаем звук
            videoElement.muted = true;
            // Продолжаем воспроизведение без звука
            try {
                videoElement.play();
            } catch(e) {
                console.log('Error resuming video playback:', e);
            }
            $video.show();
        }

        // Показываем кнопку включения звука снова
        $('#sound-button').fadeIn('fast');

        // Скрываем всплывающее окно
        $('#popup').fadeOut('fast');

        showPlay = true;
        isFullscreen = false;
        showPopup = false;
        hidePopup = true;

        console.log('Returned to compact mode with sound button visible');
    }
    
    // Обработчик события обновления времени видео
    $('#video').on('timeupdate', function() {
        var currentTime = $(this)[0].currentTime;
        
        // Показываем всплывающую форму в заданное время
        if (!showPlay && !showPopup && currentTime > start) {
            console.log('Showing popup at time:', currentTime);
            $('#popup').fadeIn('fast');
            showPopup = true;
        }
        
        // Скрываем всплывающую форму после заданного времени
        if (showPopup && hidePopup && currentTime > (start + duration)) {
            console.log('Hiding popup at time:', currentTime);
            $('#popup').fadeOut('fast');
            hidePopup = false;
        }
    }).on('ended', function() {
        // Обработчик окончания видео
        console.log('Video ended, showing order form');

        // Всегда показываем форму заказа после окончания видео
        if (isFullscreen) {
            exitFullscreen();
        }
        showOrder();
    });
    
    // Обработчик нажатия на всплывающую форму
    $('#popup').on('click', function() {
        console.log('Popup clicked, showing order form');

        // Показываем форму заказа
        showOrder();
    });
    
    // Обработчик клавиши Escape для выхода из полноэкранного режима
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && isFullscreen) {
            exitFullscreen();
        }
    });
    
    // Функция обновления полноэкранного режима
    function updateFullscreenLayout() {
        if (!isFullscreen) return;

        var container = $('#container_video');
        var video = $('#video')[0];

        // Перерасчет размеров для всех устройств в fullscreen режиме
        var viewportWidth = window.innerWidth;
        var viewportHeight = window.innerHeight;

        // Устанавливаем размеры контейнера (полноэкранный контейнер)
        container.css({
            'width': viewportWidth + 'px',
            'height': viewportHeight + 'px',
            'display': 'flex',
            'justify-content': 'center',
            'alignItems': 'flex-end'
        });

        // Устанавливаем размеры видео
        if (video) {
            video.style.width = 'auto';
            video.style.height = '95vh';
            video.style.objectFit = 'contain';
            video.style.objectPosition = 'center bottom';
        }
    }

    // Функция для отображения формы заказа (унифицированная для всех устройств)
    function showOrder() {
        console.log('showOrder function called - showing order form');

        // Выходим из fullscreen если находимся в нем
        if (isFullscreen) {
            $('#container_video').removeClass('fullscreen');
            $('body').removeClass('noscroll');
            $('#close').hide();
            isFullscreen = false;
        }

        // Скрываем все элементы видео интерфейса
        $('#popup, #sound-button, #close, #play, #open-video').fadeOut('fast');

        // Переключаем состояние контейнера на режим формы
        $('#container_video').addClass('order-visible');

        // Останавливаем видео и скрываем его
        var $video = $('#video');
        if ($video.length) {
            var videoElement = $video[0];
            // Разрешаем паузу для программной остановки
            videoElement.dataset.allowPause = 'true';
            try {
                videoElement.pause();
            } catch(e) {
                console.log('Error pausing video:', e);
            }
            $video.fadeOut('fast');
        }

        // Показываем форму заказа и скроллим к ней
        $('#order').fadeIn('slow', function() {
            // Скроллим к форме с небольшой задержкой для плавности
            setTimeout(function() {
                $('html, body').animate({
                    scrollTop: $('#order').offset().top - 50
                }, 500);
            }, 300);
            console.log('Order form is now visible');
        });
    }
});