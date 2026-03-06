var htmlLang = document.documentElement.lang.split('-');
var lang = htmlLang[0];
var country = htmlLang[1];

// Настройка поля имени
$('input[name="name"]').attr({
    "autocomplete": "name",
    "required": "required",
    "minlength": "2",
    "pattern": "[a-zA-Zа-яА-ЯёЁ\\s]{2,}"
});

// Настройка поля телефона
$('input[name="phone"]').attr({
    "autocomplete": "tel",
    "required": "required",
    "type": "tel"
});

// Мексиканские телефонные коды (основные города)
var mexicanAreaCodes = [
    '55',   // Ciudad de México
    '33',   // Guadalajara
    '81',   // Monterrey
    '442',  // Querétaro
    '664',  // Tijuana
    '998',  // Cancún
    '744',  // Acapulco
    '999',  // Mérida
    '222',  // Puebla
    '444',  // León
    '312',  // Morelia
    '477',  // San Luis Potosí
    '667',  // Culiacán
    '662',  // Hermosillo
    '614',  // Chihuahua
    '618',  // Durango
    '921',  // Veracruz
    '229',  // Xalapa
    '871',  // Torreón
    '834',  // Tampico
    '866',  // Reynosa
    '899',  // Villahermosa
    '981',  // Tuxtla Gutiérrez
    '271',  // Pachuca
    '443',  // Aguascalientes
    '449',  // Zacatecas
    '492',  // Colima
    '322',  // Puerto Vallarta
    '322',  // Tepic
    '753',  // La Paz
    '612',  // Los Mochis
    '631',  // Mazatlán
    '755',  // Cabo San Lucas
    '461',  // Celaya
    '462',  // Irapuato
    '463',  // Salamanca
    '464',  // Guanajuato
    '465',  // Silao
    '466',  // León (alternativo)
    '467',  // Dolores Hidalgo
    '468',  // San Miguel de Allende
    '469',  // San Luis de la Paz
    '472',  // Salamanca (alternativo)
    '473',  // Salvatierra
    '474',  // Yuriria
    '475',  // Valle de Santiago
    '476',  // Acámbaro
    '477',  // Tarandacuao
    '478',  // Jerécuaro
    '479',  // Coroneo
    '481',  // San Luis Potosí (alternativo)
    '482',  // Rioverde
    '483',  // Ciudad Valles
    '484',  // Tamazunchale
    '485',  // Tamuín
    '486',  // Ébano
    '487',  // Xilitla
    '488',  // Aquismón
    '489',  // Tanquián
    '491',  // Matehuala
    '492',  // Salinas
    '493',  // Ciudad del Maíz
    '494',  // Doctor Arroyo
    '495',  // Vanegas
    '496',  // Villa de Reyes
    '497',  // Zaragoza
    '498',  // Villa Hidalgo
    '499',  // Armadillo de los Infante
    '821',  // Matamoros
    '822',  // Reynosa (alternativo)
    '823',  // Nuevo Laredo
    '824',  // Camargo
    '825',  // Guerrero
    '826',  // Mier
    '827',  // Miguel Alemán
    '828',  // Roma
    '829',  // Díaz Ordaz
    '831',  // Nuevo Laredo (alternativo)
    '832',  // Reynosa (alternativo)
    '833',  // Matamoros (alternativo)
    '834',  // Valle Hermoso
    '835',  // San Fernando
    '836',  // Burgos
    '837',  // Méndez
    '838',  // Cruillas
    '839',  // General Bravo
    '841',  // Matamoros (alternativo)
    '842',  // Reynosa (alternativo)
    '843',  // Río Bravo
    '844',  // Nuevo Laredo (alternativo)
    '845',  // Ciudad Miguel Alemán
    '846',  // Guerrero (alternativo)
    '847',  // Camargo (alternativo)
    '848',  // Díaz Ordaz (alternativo)
    '849',  // Valle Hermoso (alternativo)
    '851',  // Nuevo Laredo (alternativo)
    '852',  // Reynosa (alternativo)
    '853',  // Matamoros (alternativo)
    '854',  // Río Bravo (alternativo)
    '855',  // Ciudad Miguel Alemán (alternativo)
    '856',  // Guerrero (alternativo)
    '857',  // Camargo (alternativo)
    '858',  // Díaz Ordaz (alternativo)
    '859',  // Valle Hermoso (alternativo)
    '861',  // Matamoros (alternativo)
    '862',  // Reynosa (alternativo)
    '863',  // Río Bravo (alternativo)
    '864',  // Nuevo Laredo (alternativo)
    '865',  // Ciudad Miguel Alemán (alternativo)
    '866',  // Guerrero (alternativo)
    '867',  // Camargo (alternativo)
    '868',  // Díaz Ordaz (alternativo)
    '869',  // Valle Hermoso (alternativo)
    '871',  // Torreón (alternativo)
    '872',  // Gómez Palacio
    '873',  // Lerdo
    '874',  // Matamoros (Coahuila)
    '875',  // Francisco I. Madero
    '876',  // San Pedro
    '877',  // Viesca
    '878',  // Parras
    '879',  // Cuatro Ciénegas
    '881',  // Monclova
    '882',  // Frontera
    '883',  // Nava
    '884',  // Sabinas
    '885',  // Candela
    '886',  // Castaños
    '887',  // Abasolo
    '888',  // Villa Unión
    '889',  // Juárez (Coahuila)
    '891',  // Piedras Negras
    '892',  // Nava (alternativo)
    '893',  // Zaragoza (Coahuila)
    '894',  // Allende (Coahuila)
    '895',  // Guerrero (Coahuila)
    '896',  // Hidalgo (Coahuila)
    '897',  // Jiménez (Coahuila)
    '898',  // Morelos (Coahuila)
    '899',  // Muzquiz
    '911',  // Chilpancingo
    '912',  // Taxco
    '913',  // Iguala
    '914',  // Teloloapan
    '915',  // Arcelia
    '916',  // Ciudad Altamirano
    '917',  // Coyuca de Benítez
    '918',  // Acapulco (alternativo)
    '919',  // Pungarabato
    '921',  // Veracruz (alternativo)
    '922',  // Córdoba
    '923',  // Orizaba
    '924',  // Poza Rica
    '925',  // Tuxpan
    '926',  // Martínez de la Torre
    '927',  // San Andrés Tuxtla
    '928',  // Cosamaloapan
    '929',  // Tierra Blanca
    '931',  // Xalapa (alternativo)
    '932',  // Veracruz (alternativo)
    '933',  // Córdoba (alternativo)
    '934',  // Orizaba (alternativo)
    '935',  // Poza Rica (alternativo)
    '936',  // Tuxpan (alternativo)
    '937',  // Martínez de la Torre (alternativo)
    '938',  // San Andrés Tuxtla (alternativo)
    '939',  // Cosamaloapan (alternativo)
    '941',  // Pachuca (alternativo)
    '942',  // Tulancingo
    '943',  // Tula
    '944',  // Tepeji
    '945',  // Actopan
    '946',  // Ixmiquilpan
    '947',  // Zimapan
    '948',  // Jacala
    '949',  // Huichapan
    '951',  // Puebla (alternativo)
    '952',  // Tehuacán
    '953',  // Atlixco
    '954',  // Acatlán
    '955',  // Izúcar de Matamoros
    '956',  // Huauchinango
    '957',  // Zacatlán
    '958',  // Tetela de Ocampo
    '959',  // Huejotzingo
    '961',  // Tlaxcala
    '962',  // Apizaco
    '963',  // Zacatelco
    '964',  // Huamantla
    '965',  // Calpulalpan
    '966',  // Tlaxco
    '967',  // Chiautempan
    '968',  // Contla
    '969',  // Tepetitla
    '971',  // Cuernavaca
    '972',  // Cuautla
    '973',  // Jiutepec
    '974',  // Yautepec
    '975',  // Puente de Ixtla
    '976',  // Temixco
    '977',  // Xochitepec
    '978',  // Emiliano Zapata
    '979',  // Zacatepec
    '981',  // Tuxtla Gutiérrez (alternativo)
    '982',  // Tapachula
    '983',  // Comitán
    '984',  // San Cristóbal de las Casas
    '985',  // Villaflores
    '986',  // Chiapa de Corzo
    '987',  // Ocosingo
    '988',  // Palenque
    '989',  // Pichucalco
    '991',  // Mérida (alternativo)
    '992',  // Valladolid
    '993',  // Tizimín
    '994',  // Motul
    '995',  // Izamal
    '996',  // Ticul
    '997',  // Felipe Carrillo Puerto
    '998',  // Cancún (alternativo)
    '999',  // Mérida (alternativo)
];

$('input[name="phone"]').each(function() {
    var input = this;
    var iti = window.intlTelInput(input, {
        initialCountry: 'in', // Жестко задаем Индию (+91)
        allowDropdown: false,
        nationalMode: true,
        separateDialCode: true,
        autoPlaceholder: 'polite',
        formatOnDisplay: true,
        hiddenInput: 'phone',
        utilsScript: 'https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.15/js/utils.min.js'
    });

    // Базовая валидация с дополнительными проверками для разных стран
    input.addEventListener('input', function() {
        input.setCustomValidity('');

        // Проверяем, является ли номер валидным через intl-tel-input
        if (!iti.isValidNumber()) {
            var selectedCountry = iti.getSelectedCountryData().iso2;

            // Специфическая валидация для Индии
            if (selectedCountry === 'in') {
                var phoneNumber = iti.getNumber();
                var nationalNumber = phoneNumber.replace(/^\+91/, '');

                // Индийские номера должны быть ровно 10 цифр
                if (nationalNumber.length !== 10) {
                    input.setCustomValidity('मोबाइल नंबर 10 अंकों का होना चाहिए'); // Мобильный номер должен быть 10 цифр
                    return;
                }

                // Только цифры
                if (!/^\d{10}$/.test(nationalNumber)) {
                    input.setCustomValidity('केवल अंक ही दर्ज करें'); // Введите только цифры
                    return;
                }

                // Индийские мобильные номера должны начинаться с 6, 7, 8, 9
                if (!/^[6-9]/.test(nationalNumber)) {
                    input.setCustomValidity('मोबाइल नंबर 6, 7, 8 या 9 से शुरू होना चाहिए'); // Мобильный номер должен начинаться с 6, 7, 8 или 9
                    return;
                }

                // Проверяем на повторяющиеся цифры (спам фильтр)
                if (/^(.)\1+$/.test(nationalNumber)) {
                    input.setCustomValidity('कृपया वैध मोबाइल नंबर दर्ज करें'); // Пожалуйста, введите действительный мобильный номер
                    return;
                }

                // Проверяем на последовательные цифры (1234567890, 9876543210 и т.д.)
                if (/^(0123456789|1234567890|2345678901|3456789012|4567890123|5678901234|6789012345|7890123456|8901234567|9012345678|9876543210|8765432109|7654321098|6543210987|5432109876|4321098765|3210987654|2109876543|1098765432|0987654321)$/.test(nationalNumber)) {
                    input.setCustomValidity('कृपया वैध मोबाइल नंबर दर्ज करें'); // Пожалуйста, введите действительный мобильный номер
                    return;
                }

                // Проверяем на известные тестовые номера
                var testNumbers = ['9999999999', '8888888888', '7777777777', '6666666666', '5555555555', '4444444444', '3333333333', '2222222222', '1111111111', '0000000000'];
                if (testNumbers.includes(nationalNumber)) {
                    input.setCustomValidity('कृपया वैध मोबाइल नंबर दर्ज करें'); // Пожалуйста, введите действительный мобильный номер
                    return;
                }

                // Дополнительная проверка: не должно быть более 3 одинаковых цифр подряд
                if (/(.)\1{3,}/.test(nationalNumber)) {
                    input.setCustomValidity('कृपया वैध मोबाइल नंबर दर्ज करें'); // Пожалуйста, введите действительный мобильный номер
                    return;
                }

                // Если все проверки прошли - очищаем ошибки
                input.setCustomValidity('');
            }
            // Специфическая валидация для Мексики (существующий код)
            else if (selectedCountry === 'mx') {
                var phoneNumber = iti.getNumber();
                var nationalNumber = phoneNumber.replace(/^\+52/, '');

                // Проверяем длину номера (мексиканские мобильные номера обычно 10 цифр)
                if (nationalNumber.length < 10) {
                    input.setCustomValidity('El número debe tener al menos 10 dígitos');
                    return;
                }

                if (nationalNumber.length > 10) {
                    input.setCustomValidity('El número no debe exceder 10 dígitos');
                    return;
                }

                // Проверяем, что номер начинается с правильного кода области
                var firstDigits = nationalNumber.substring(0, 3);
                if (!mexicanAreaCodes.includes(firstDigits) && !mexicanAreaCodes.includes(nationalNumber.substring(0, 2))) {
                    // Не строгий валидатор - просто предупреждаем, но не блокируем
                    console.log('Número con código de área no estándar:', firstDigits);
                }

                // Проверяем, что это не фиктивный номер
                if (/^(.)\1+$/.test(nationalNumber)) {
                    input.setCustomValidity('Ingresa un número de teléfono válido');
                    return;
                }

                // Если базовая валидация прошла - очищаем ошибки
                input.setCustomValidity('');
            } else {
                input.setCustomValidity('Invalid phone number');
            }
        }
    });

    // Обработка изменения страны
    var iso2 = iti.getSelectedCountryData().iso2;
    input.addEventListener('countrychange', function() {
        iti.setCountry(iso2);
        // Очищаем ошибки при смене страны
        input.setCustomValidity('');
    });

    // Дополнительная валидация при потере фокуса
    input.addEventListener('blur', function() {
        if (input.value && !iti.isValidNumber()) {
            var selectedCountry = iti.getSelectedCountryData().iso2;
            if (selectedCountry === 'in') {
                input.setCustomValidity('कृपया सही भारतीय फोन नंबर दर्ज करें'); // Пожалуйста, введите правильный индийский номер телефона
            } else if (selectedCountry === 'mx') {
                input.setCustomValidity('Por favor ingresa un número de teléfono mexicano válido'); // Пожалуйста, введите правильный мексиканский номер телефона
            } else {
                input.setCustomValidity('Please enter a valid phone number'); // Пожалуйста, введите правильный номер телефона
            }
        }
    });
});