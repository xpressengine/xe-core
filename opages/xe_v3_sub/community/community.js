// <![CDATA[
jQuery(function($){
	$('.community')
		.find('li:eq(1)')
			.addClass('next')
			.find('.summary, .date, .hour').remove();

	// change date format
	$('#content span.date').each(function(){
		var $date = $(this), $hour = $date.next('span.hour'), now, now_s, time, time_s, dates, hours, diff, diff_s, formats, text;

		dates = $date.text().split('-');
		hours = $hour.text().split(':');

		time = new Date(dates[0], dates[1]-1, dates[2], hours[0], hours[1], 0, 0);
		now  = new Date();
		diff = Math.floor( (now.getTime() - time.getTime())/1000 ); // in seconds

		time_s = new Date(dates[0], dates[1]-1, dates[2], 0, 0, 0, 0);
		now_s  = new Date(now.getFullYear(), now.getMonth(), now.getDate(), 0, 0, 0, 0);
		diff_s = Math.floor( (now_s.getTime() - time_s.getTime())/(1000*3600*24) ); // in days

		function format(formats, num) {
			return (num+1 < formats.length)?formats[num]:formats[formats.length-1].replace(/%d/, num);
		}

		if (diff_s > 0) {
			text = format(['','어저께','그저께','그끄저께','%d일 전'], diff_s);
		} else if (diff < 60) {
			text = '지금 막';
		} else if ((diff = Math.floor(diff/60)) < 60) { // in minutes
			text = diff+'분 전';
		} else {
			diff = Math.floor(diff/60); // in hours
			text = diff+'시간 전';
		}

		$date.attr('title', $date.text()+' '+$hour.text()).text(text);
		$hour.hide();
	});
});
// ]]>
