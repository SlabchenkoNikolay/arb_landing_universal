if(data === undefined) {
	var api = '//uniaffcrm.com/api.php';
	var data = new FormData();
	var startTime = new Date();
	var timer, startFullscreen, endFullscreen, endTime, stopTimer, notActive;
	var send = 0;
	$(document).ready(function() {
		data.append('action', 'Analytics');
		data.append('subid', subid);
		data.append('kt_campaign', kt_campaign);
		data.append('kt_offer', kt_offer);
		try{ console.log(kt_landing) } catch(e){ kt_landing = '' }
		data.append('kt_landing', kt_landing);
		data.append('branch_id', 2);
		data.append('url', window.location.href);
		data.append('offer_name', $('[name="offer_name"]').val());
		data.append('country', $('[name="country"]').val());
		data.append('v', 11);
		if($('#video').length) {
			navigator.sendBeacon(api, data);
			data.delete('url');
			timer = setInterval(sendData, 10000)
		} else {
			data.append('novideo', 'novideo');
		}
		$('[type="submit"]').click( () => { send = 1; sendData() });
		$('#play').click(() => { startFullscreen = new Date() });
		$('#close').click(() => { endFullscreen = new Date(); sendData() });
		$(window).on('unload', () => { sendData() } );

		document.addEventListener('visibilitychange', function() {
				if(!notActive && document.hidden) { notActive = true; sendData(); }
			}, false );
		});
}

function sendData() {
	if(notActive || ($('#newsFrame').css('display') == 'block' && !stopTimer)) {
		clearInterval(timer);
		stopTimer = true;
		endTime = new Date();
	}
	if(!stopTimer) { endTime = new Date(); }
	data.set('time_page', Math.round((endTime-startTime)/1000));
	if(startFullscreen) { data.set('time_fullscreen', Math.round(((endFullscreen||endTime)-startFullscreen)/1000)); }
	data.set('send', send);
	navigator.sendBeacon(api, data);
}