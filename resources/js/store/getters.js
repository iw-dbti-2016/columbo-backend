export const getters = {
	getTripById: (state) => (id) => {
		return state.trips.filter(trip => trip.id == id);
	},
	hasTripWithId: (state, getters) => (id) => {
		return getters.getTripById(id).length;
	},
	getTrips: (state) => {
		return state.trips;
	},
	hasTrips: (state, getters) => {
		return Object.keys(getters.getTrips).length !== 0;
	},
	getReportsForTrip: (state) => (tripId) => {
		return state.reports.filter(report => report.trip_id == tripId);
	},
	getReportById: (state) => (id) => {
		return state.reports.filter(report => report.id == id);
	},
	hasReportWithId: (state, getters) => (id) => {
		return getters.getReportById(id).length;
	},
	getReports: (state) => {
		return state.reports;
	},
	hasReports: (state, getters) => {
		return Object.keys(getters.getSections).length !== 0;
	},
	getSectionById: (state) => (id) => {
		return state.sections.filter(section => section.id == id);
	},
	hasSectionWithId: (state, getters) => (id) => {
		return getters.getSectionById(id).length;
	},
	getSections: (state) => {
		return state.sections;
	},
	hasSections: (state, getters) => {
		return Object.keys(getters.getSections).length !== 0;
	},
}

export default {
	getters,
}
