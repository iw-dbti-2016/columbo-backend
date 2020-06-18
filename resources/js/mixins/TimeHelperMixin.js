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
		humanTimeDiff(datetime) {
			return moment(datetime).fromNow();
		},
	}
}
