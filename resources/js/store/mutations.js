export const mutations = {
	setTrips (state, payload) {
		state.trips = payload;
	},
	addTrip: (state, payload) => {
		state.trips.push(payload.data);
	},
	setReports(state, payload) {
		state.reports = payload;
	},
	addReport: (state, payload) => {
		state.reports.push(payload.data);
	},
	setSections(state, payload) {
		state.sections = payload;
	},
	addSection: (state, payload) => {
		state.sections.push(payload.data);
	}
}

export default {
	mutations,
}
