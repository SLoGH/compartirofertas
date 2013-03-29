function event_countdown () {
	var time_left = 0; //number of seconds for countdown
	var keep_counting = 1;
	var inited = false;
	var expTimer = '';

	function countdown() {
		if(time_left < 2) {
			keep_counting = 0;
		}

		time_left = time_left - 1;
	}

	function add_leading_zero(n) {
		if(n.toString().length < 2) {
			return '0' + n;
		} else {
			return n;
		}
	}

	function formatDate (inner_string) {
		var return_string = '';
		var resource_string = inner_string.toString();
		for (var i = 0 ; i < resource_string.length ; i++) {
			return_string += '<span>'+resource_string.charAt(i)+'</span>';
		}
		return return_string;
	}

	function format_output() {
		var hours, minutes, seconds , days;

		seconds = time_left % 60;
		minutes = Math.floor(time_left / 60) % 60;

		hours = Math.floor(time_left / 3600) % 24;
		days = Math.floor((time_left / 3600)/24);
		seconds = add_leading_zero( seconds );
		minutes = add_leading_zero( minutes );
		hours = add_leading_zero( hours );
		days = add_leading_zero( days );
		return {
			'days'    :days,
			'hours'   :hours,
			'minutes' :minutes,
			'seconds' :seconds
		};
	}

	function show_time_left() {
		var timeLeft = format_output();
			jQuery('.scale-1',expTimer).html(formatDate(timeLeft.days.toString()));
			jQuery('.scale-2',expTimer).html(formatDate(timeLeft.hours.toString()));
			jQuery('.scale-3',expTimer).html(formatDate(timeLeft.minutes.toString()));
			jQuery('.scale-4',expTimer).html(formatDate(timeLeft.seconds.toString()));
	}

	var count = function () {
		countdown();
		show_time_left();
	};

	var timer =  function () {
		count();

		if(keep_counting) {
			setTimeout(timer, 1000);
		} else {
			onFinish()
		}
	};
	var isInited =  function() {
		return inited;
	};
	onFinish = function() {
		console.log(expTimer);	
//		window.location.reload();
	};
	
	this.init =  function (div, time) {
		expTimer = jQuery('#'+div+' .expiration-timer');
		time_left = time;
		if (time_left > 0) {
			inited = true;
			timer();
		}
	};
} 