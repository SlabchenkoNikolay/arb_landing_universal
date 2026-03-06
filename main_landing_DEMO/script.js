$(document).ready(function() {
	// timer
	var [m, s] = ($('#timer').html() ?? '').split(':');
	var second = $('#sec').html() ?? s ?? 60, minute = $('#min').html() ?? m ?? 10;

	// Улучшения UX для формы
	setupFormUX();
	setInterval(function() {
			if (second <= 1) {
				second = 60;
				if (minute <= 1) minute = 10;
				m = String(--minute).padStart(2, '0');
				$('#min').html(()=> m);
			}
			s = String(--second).padStart(2, '0');
			$('#sec').html(()=> s);
			$('#timer').html(m + ":" +s);
	}, 1000);

	// dateChange
	var lang = document.documentElement.lang ?? 'default';
	var date1 = new Date(); date1.setDate(date1.getDate() - 6);
	var date2 = new Date(); date2.setDate(date2.getDate());
	const options = { year: 'numeric', month: 'long', day: 'numeric' };
	$('.date1').html(date1.toLocaleDateString('default', options));
	$('.date2').html(date2.toLocaleDateString('default', options));
	$('[daysago]').each( function() {
		var date = new Date();
		date.setDate(date.getDate() - $(this).attr('daysago'));
		$(this).html(date.toLocaleDateString(lang, options));
	});
	$('[d]').each( function() {
		var date = new Date();
		date.setDate(date.getDate() + Number($(this).attr('d')));
		$(this).html(date.toLocaleDateString(lang, options));
	});

	// monthChange
	var date1 = new Date();
	var date2 = new Date(date1.getFullYear(), (date1.getMonth()+1)%12, 1);
	var date3 = new Date(date1.getFullYear(), (date1.getMonth()-1)%12, 1);
	$('.month1').html(date1.toLocaleString(lang, { month: 'long' }));
	$('.month2').html(date2.toLocaleString(lang, { month: 'long' }));
	$('.prev_month').html(date3.toLocaleString(lang, { month: 'long' }));
	$('.month').html(date1.toLocaleString(lang, { month: 'long' }));
	$('.month-next').html(date2.toLocaleString(lang, { month: 'long' }));
	$('.month-prev').html(date3.toLocaleString(lang, { month: 'long' }));

	// linksScrollForm
	$('a').click(function(e) {
		if(!$(this).attr('noprevent')) {
			e.preventDefault();
			var form = $("#order-now").length ? $("#order-now") : $("#form-wrap").length ? $("#form-wrap") :  $("#form");
			if(form.length) { $('html, body').animate({ scrollTop: form.offset().top-5 }, 200); }
		}
	});

	// Функция улучшения UX формы
	function setupFormUX() {
		// Автофокус на поле имени при открытии формы
		$('#order').on('show', function() {
			setTimeout(function() {
				$('input[name="name"]').focus();
			}, 500);
		});

		// Предотвращение отправки формы при нажатии Enter в поле имени
		$('input[name="name"]').on('keypress', function(e) {
			if (e.which === 13) {
				e.preventDefault();
				$('input[name="phone"]').focus();
			}
		});

		// Автоматический переход к телефону после заполнения имени
		$('input[name="name"]').on('input', function() {
			if ($(this).val().length >= 2) {
				// Можно добавить подсказку для перехода к телефону
				$(this).addClass('input-filled');
			} else {
				$(this).removeClass('input-filled');
			}
		});

		// Предотвращение отправки формы при нажатии Enter в поле телефона
		$('input[name="phone"]').on('keypress', function(e) {
			if (e.which === 13) {
				e.preventDefault();
				$('#order button[type="submit"]').focus();
			}
		});

		// Визуальная обратная связь при вводе
		$('input[name="phone"], input[name="name"]').on('input', function() {
			var $input = $(this);
			if ($input.val().length > 0) {
				$input.addClass('has-content');
			} else {
				$input.removeClass('has-content');
			}
		});

		        // Предотвращение множественных отправок формы
        $('#order').on('submit', function(e) {
            var $form = $(this);
            var $submitBtn = $form.find('button[type="submit"]');

            // Проверяем, не отправляется ли форма уже
            if ($submitBtn.prop('disabled')) {
                e.preventDefault();
                return false;
            }

            // Отключаем кнопку и показываем индикатор загрузки
            $submitBtn.prop('disabled', true);
            $submitBtn.html('<span>Enviando...</span>');

            // Восстанавливаем кнопку через 10 секунд (на случай ошибки)
            setTimeout(function() {
                $submitBtn.prop('disabled', false);
                $submitBtn.html('Enviar pedido');
            }, 10000);
        });

        // Отслеживание пользовательских взаимодействий для аналитики
        function trackUserInteraction(action, details) {
            console.log('User interaction:', action, details);
            // Здесь можно добавить отправку данных в аналитику
            // Например, в Google Analytics, Facebook Pixel и т.д.
        }

        // Отслеживание фокуса на полях формы
        $('input[name="name"], input[name="phone"]').on('focus', function() {
            var fieldName = $(this).attr('name');
            trackUserInteraction('form_field_focus', { field: fieldName });
        });

        // Отслеживание заполнения полей
        $('input[name="name"], input[name="phone"]').on('blur', function() {
            var fieldName = $(this).attr('name');
            var hasValue = $(this).val().length > 0;
            trackUserInteraction('form_field_blur', {
                field: fieldName,
                hasValue: hasValue,
                length: $(this).val().length
            });
        });

        // Отслеживание попыток отправки формы
        $('#order').on('submit', function() {
            trackUserInteraction('form_submit_attempt', {
                nameLength: $('input[name="name"]').val().length,
                phoneLength: $('input[name="phone"]').val().length
            });
        });

        // Отслеживание кликов по кнопке воспроизведения видео
        $(document).on('click', '#play, #open-video, #popup', function() {
            var elementId = $(this).attr('id');
            trackUserInteraction('video_interaction', {
                element: elementId,
                isMobile: window.innerWidth <= 768
            });
        });
	}


	var names = {
		"him": ["а¤°а¤µа¤ї а¤•аҐЃа¤®а¤ѕа¤°","а¤…а¤°аҐЌа¤њаҐЃа¤Ё а¤Їа¤ѕа¤¦а¤µ","а¤µа¤їа¤¶а¤ѕа¤І а¤®аҐ‡а¤№а¤¤а¤ѕ","а¤•а¤°а¤Ј а¤њаҐ‹а¤¶аҐЂ","а¤ња¤Ї а¤№а¤ѕа¤‚а¤ЎаҐ‡","а¤…а¤‚а¤•аҐЃа¤° а¤ёа¤їа¤‚а¤","а¤…а¤®а¤їа¤¤ а¤°а¤ѕа¤Ї","а¤¦аҐЂа¤Єа¤• а¤—аҐЃа¤ЄаҐЌа¤¤а¤ѕ","а¤Ёа¤µаҐЂа¤Ё а¤Їа¤ѕа¤¦а¤µ","а¤°а¤ѕа¤њ а¤¶а¤°аҐЌа¤®а¤ѕ"], // Rajesh Kumar, Arjun Yadav, Vishal Mehta, Karan Joshi, Vijay Singh, Ankur Singh, Amit Rai, Deepak Gupta, Naveen Yadav, Raj Sharma
		"hiw": ["а¤®аҐЂа¤°а¤ѕ а¤¶а¤°аҐЌа¤®а¤ѕ","а¤¦а¤їа¤µаҐЌа¤Їа¤ѕ а¤Єа¤ѕа¤џа¤їа¤І","а¤…а¤ЁаҐЃа¤·аҐЌа¤•а¤ѕ а¤њаҐ‹а¤¶аҐЂ","а¤°а¤їа¤Їа¤ѕ а¤®аҐ‡а¤№а¤¤а¤ѕ","а¤ёаҐ‹а¤ЁаҐЂ а¤№а¤ѕа¤‚а¤ЎаҐ‡","а¤€а¤¶а¤ѕ а¤ёа¤їа¤‚а¤","а¤…а¤Іа¤їа¤Їа¤ѕ а¤°а¤ѕа¤Ї","а¤Єа¤Іа¤• а¤—аҐЃа¤ЄаҐЌа¤¤а¤ѕ","а¤ёаҐЃа¤№а¤ѕа¤Ќа¤Ёа¤ѕ а¤Їа¤ѕа¤¦а¤µ","а¤°аҐ‹а¤№а¤їа¤ЈаҐЂ а¤¶а¤°аҐЌа¤®а¤ѕ"], // Meera Sharma, Divya Patel, Anushka Joshi, Riya Mehta, Soni Singh, Aasha Singh, Alia Rai, Palak Gupta, Suhana Yadav, Rohini Sharma
		"esm": ["Santiago Rosales","Mateo Guerrero","Alejandro Castillo","Daniel Montes","SebastiГЎn Morales","Juan Valdez","Diego Cabrera","NicolГЎs Franco","Lucas GГіmez","AndrГ©s Paredes"],
		"esw": ["SofГ­a Herrera","Valentina Escobar","Isabella Navarro","Camila RГ­os","Emma Delgado","Martina Peralta","LucГ­a Vega","Antonella Mendoza","Victoria Rojas","Natalia Fuentes"],
		"itm": ["Marco Russo","Giovanni Bianco","Luca Esposito","Matteo Romano","Alessandro Ferrari","Davide Conti","Andrea Rossi","Fabio De Luca","Antonio Marino","Francesco Morelli"],
		"itw": ["Sofia Rossi","Giulia Bianchi","Aurora Esposito","Martina Ricci","Francesca Moretti","Sara Conti","Elena Romano","Alice Ferrari","Valentina Marino","Laura De Luca"],
		"dem": ["Maximilian Schmidt","Paul Fischer","Elias Schneider","Leon Wagner","Ben Hoffmann","Luca Keller","Noah Weber","Jonas Vogt","Felix Braun","Lukas Hartmann"],
		"dew": ["Sophia MГјller","Emma Becker","Hannah Fischer","Emilia Schneider","Claudia Henning","Peggy Adler-Hoppe","Renata SchГјtte","Paula BГ¶hme","Isolde Heine","Brigitte Klose"],
		"hum": ["SzЕ±cs Bence","LukГЎcs ГЃron","Szekeres SzervГЎc","OrbГЎn BalГЎzs","BГЎlint KornГ©l","Vincze Hunor","Kozma Albert","Sipos OlivГ©r","FaragГі Patrik","TamГЎs ErnЕ‘"],
		"huw": ["HegedГјs MihГЎlynГ©","Kocsis Gizella","Szekeres OlГ­via","BalГЎzs Gitta","PГЎsztor KristГіfnГ©","Kelemen MГЎrton","LukГЎcs Fanni","Lengyel Marietta","FГЎbiГЎn ValГ©ria","Szalai Rebeka"],
		"rom": ["Tudor Gheorghiu","Dorel Pana","Toma Florescu","Vasile Cozma","Nichifor Nica","Casian Manolache","Avram Chirila","Stancu Ignat","Albert Simon","GicДѓ Trandafir"],
		"row": ["Veta Miron","Lia Macovei","Gabriela Stefanescu","Ramona Zaharia","GraИ›iana Radulescu","Nadia Ardelean","Petronela Moise","Tudosia Coman","Marcheta Muresan","Astrid Parvu"],
		"ptm": ["Isaac Ivo Rodrigues Melo","Edgar Mendes Ramos","Afonso Azevedo de Henriques","Bernardo ClГЎudio","Nuno MГЎrcio Lima","LuГ­s Matheus Coelho","Filipe AraГєjo Silva","TomГЎs Silva Faria","Xavier Paulo de Maia","Rafael Gustavo de Antunes"],
		"ptw": ["Madalena Neto Vieira","Teresa Melissa Ribeiro de Freitas","Bruna Ramos Macedo","JГ©ssica Iris Marques","Andreia Benedita Nunes","Carlota Mota","Viviane Erica Nunes","Г‰rica Campos Nogueira","Mafalda Teixeira","Daniela Ana Soares de Fernandes"],
		"trm": ["Ali AkyГјz","Burak Abadan","Ferid KarabГ¶cek","Barlas AkyГјrek","Ali Solmaz","Canberk Akman","Г‡aДџan BakД±rcД±oДџlu","Atakan Eronat","Cem BeЕџerler","ArmaДџan Denkel"],
		"trw": ["Ada AlnД±aГ§Д±k","Ebru NumanoДџlu","Ећahnur TГјzГјn","Ећahnur AkyГјz","Burcu KoГ§","Ece Baykam","Ећahnur ErГ§etin","Sinem AyaydД±n","Ebru Erginsoy","RГјya LimoncuoДџlu"],
		"film": ["Jerrold Bradtke","Elmo Erdman","Alexis Pacoch","Consuelo Ruecker","Ernest Terry","Melvin Huel","Dexter Bogisich","Dwight Runolfsdottir","Coty Emard","Michale Hauck"],
		"filw": ["Gina Tillman","Anjali Marks","Estefania Mante","Lauren Kohler","Rosetta Runolfsdottir","Maiya Willms","Angie Ziemann","Henriette Dickens","Alberta Von","Delpha Greenfelder"],
		"fam": ["ШЇШ§Ш±Ш§ ЩЃШ±Ш¬","Ш№Ш·Ш§ Щ…ЫЊШЇШ±ЫЊ","ШЇШ§ШЄЫЊШі Щ‚Ш§Щ†Щ€Щ†ЫЊ","Щ…Щ‡ЫЊЩ…Щ† Щ„Щ†Ъ©Ш±Ш§Щ†ЫЊ","ШЁЩ‡Щ†ЫЊШ§ Щ…Ш±ШЄШ¶Щ€ЫЊ","ШЁШ®ШЄЫЊШ§Ш± Щ€Ш§Ш№Шё","ШіШ§Щ…ЫЊ ШµЩЃЩ€ЫЊ","Щ‡Щ€ШґШ§Щ† Ъ©Ш±ЫЊЩ…ЫЊ","Ш±ШґЫЊШЇ ШЇШ§Щ€Ш±","ШЁЩ‡Ш§Щ…ЫЊЩ† Щ‚Щ‡ШіШЄШ§Щ†ЫЊ	"],
		"faw": ["ШўЩЃШ±ЫЊ Щ…Щ†Щ€Ъ†Щ‡Ш±ЫЊ","Щ†Ш§ШІЩ€ Ш­Ъ©ЫЊЩ…ЫЊ","Ш±Ш§ШґЫЊЩ† ШЄЩ€ШіЩ„ЫЊ","Щ…Щ„Ъ©Щ‡ Ш¬Щ‡Ш§Щ†ЫЊ","Ш±Щ€ШЇШ§ШЁЩ‡ ЩѕЫЊЩ€Щ†ШЇЫЊ","Щ…Щ€Щ†Ш§ ШЄЩ€Ъ©Щ„ЫЊШ§Щ†","Щ†ЪЇШ§Ш±ЫЊЩ† ШЇШ±ЫЊ","ЩЃШ±Щ€Шє Щ‚Щ‡Ш±Щ…Ш§Щ†ЫЊШ§Щ†","ШіЫЊЩ…Ш§ Щ…ЫЊШ±ШЁШ§Щ‚Ш±ЫЊ","ЪЇЩ„ШґЩ† ШІШ±ШґЩ†Ш§Ші"],
		"arm": ["ШЈШ­Щ…ШЇ Щ…Ш±Ш§ШЇ","ШіШ№ЩЉШЇ ШЇШ§Щ€ШЇ","ШЁШ§ШіЩ„ Ш±Ш§ЩЃЩЉ","Ш§ШіШ§Щ… ЩѓШ§Щ…Щ„","Щ†Ш§ШІЩѓ ШЁШЇЩ€Ш§Щ€ЩЉ","ШЈШЁЩ€ Ш¬Щ‡Ш§ШЇ","ЩЃЩ€ШІЩЉ Щ…Щ†ШµЩ€Ш±","вЂЏШ№Щ‚ЩЉЩ„","вЂЏШ¬Ш§ШІЩ…","вЂЏЩ…ЩЉЩ…Щ€Щ†"],
		"arw": ["ШЈЩЉШ§ШЄ Щ„Ш§Ш±ЩЉ","Ш№Ш§Ш¦ШґШ©","ШЁШ§ШЇЩЉШ© ШєЩ€ШІЩЉЩ„Ш§","ШєШ±Ш§Щ… ЩЃЩЉШ±Щ€ШІШ©","Ш¬Щ€Ш±ЩЉ","вЂЏвЂЏШєШ§ШЇШ©","ШіЩ„ЩЉЩ…Ш© ЩЉЩ„ШЇЩ€ШІ","Ш­Щ…ШЇШ§Щ† Ш№ЩЉШіЩ‰"],
		"ZAm": ["Mzwandile Zwazwa","Philani Ngwenya","Tommy Lee Sparta","Maxamed Nuur Afaan Cabdulaahi","Dries De Wet","Bullet Manala","Minhajul Hayat","Stivo Mothupi","Nhlanhla Dube","Annetjie Thythus"],
		"ZAw": ["Marilyn Lathwood","Zandy Mayisela","Nokuzola Pinky","Precious Sithole","Lana Nolte","Sussanna Lewies","Clara Dean","Gail Less","Beauty Masuku","Mantsho"],
		"KEm": ["Adams Maina","Semekal Mose","Mwalimu Ngetich","Nako Memei","Jay Jay Okocha","Moiz Peter","Ronald Okoth","Magata Eric","Davido Kamaa","Immanuel Mwanzia"],
		"KEw": ["Florence Wangeci","Elizabeth Biwott","Ivy Njoroge","Elenwa Wairimu Maina","Anchelina Mueni","Cynthia Mudave","Kamammy Terry","Shila Chebet","Bree","Kiongozi Mayamba"],
		"CIm": ["ГЏsmГЈГ«l Fanny","Robert Sode","Mermoz Ndri","Goua Kekre","Koala Inoussa","Kone Nanourou","Blessings Venance","Noel Jordan","Bonkoungou Julliette","Lee Boss Poutine","Medo le Nabab","Sam Sam"],
		"CIw": ["Mirabelle MoyГ©","GКЂГў Kouakou","Albertine Akpacheme","Tata JosГ©","Lou IriГ© Augustine IriГ©","Dame N'gbesso","Dorine Amenan","Maman MoГЇse","Ange Blon","Ornella Affi","Barra Djeneba Soro"],
	};
	var htmlLang = document.documentElement.lang.split('-');
	var lang = htmlLang[0];
	var country = htmlLang[1];
	if(lang == "en" || lang == "fr") { lang = country; }
	if(names[lang+"m"] === undefined) { lang = "es"; }
	for(let i = 1; i <= 10; i++) {
		$('.m'+i).html(names[lang+"m"][i-1]);
		$('.w'+i).html(names[lang+"w"][i-1]);
	}

	if(typeof doc === "undefined") {
		doc = {"hi":"а¤ЎаҐ‰. а¤…а¤µа¤їа¤Ёа¤ѕа¤¶ а¤®а¤їа¤¶аҐЌа¤°а¤ѕ", "es":"Dr. Hugo LГіpez-Gatell"}[lang];
	}
	if(!doc) { doc = "Doctor"; }
	$('.doc').html(doc);
	if(typeof product === "undefined" || !product) { product = "Product"; }
	$('.product').html(product);
	if(typeof currency === "undefined" || !currency) {
		currency = {"IN":"INR", "MX":"\$"}[country];
	}

	$('.currency').html(currency);
	if(typeof priceOld != "undefined") { $('.price-old').html(priceOld); }
	if(typeof priceNew != "undefined") { $('.price-new').html(priceNew); }



});