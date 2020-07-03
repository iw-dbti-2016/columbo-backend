import moment from 'moment'

export default {
	name: 'time-helper-mixin',

	methods: {
		formatDate(date) {
			return moment(date).format('DD/MM/YYYY');
		},
		formatDateTime(datetime) {
			return moment(datetime).format('DD/MM/YYYY HH:mm:ss');
		},
		formatDateTimeInput(datetime) {
			return moment(datetime).format('YYYY-MM-DD\\THH:mm');
		},
		humanTimeDiff(datetime) {
			return moment(datetime).fromNow();
		},
		timeIsInFuture(datetime) {
			return moment(datetime).diff(moment(new Date())) > 0;
		},
		toUTC(datetime) {
			return moment(datetime).utc().format('YYYY-MM-DD\\THH:mm');
		}
	}
}
