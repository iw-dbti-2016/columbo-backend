export default [
	{
		path: '/app',
		name: 'home',
		meta: {requiresAuthentication: true,},
		component: require('./components/HomeComponent.vue').default,
	},
	{
		path: '/app/users/:username',
		name: 'showProfile',
		meta: {requiresAuthentication: true,},
		component: require('./components/profile/ShowProfileComponent.vue').default,
	},
	{
		path: '/app/trips/create',
		name: 'createTrip',
		meta: {requiresAuthentication: true,},
		component: require('./components/trips/CreateTripComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/edit',
		name: 'editTrip',
		meta: {requiresAuthentication: true,},
		component: require('./components/trips/EditTripComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId',
		name: 'showTrip',
		meta: {requiresAuthentication: true,},
		component: require('./components/trips/ShowTripComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/create',
		name: 'createReport',
		meta: {requiresAuthentication: true,},
		component: require('./components/reports/CreateReportComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/:reportId/edit',
		name: 'editReport',
		meta: {requiresAuthentication: true,},
		component: require('./components/reports/EditReportComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/:reportId',
		name: 'showReport',
		meta: {requiresAuthentication: true,},
		component: require('./components/reports/ShowReportComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/:reportId/sections/create',
		name: 'createSection',
		meta: {requiresAuthentication: true,},
		component: require('./components/sections/CreateSectionComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/:reportId/sections/:sectionId/edit',
		name: 'editSection',
		meta: {requiresAuthentication: true,},
		component: require('./components/sections/EditSectionComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/:reportId/sections/:sectionId',
		name: 'showSection',
		meta: {requiresAuthentication: true,},
		component: require('./components/sections/ShowSectionComponent.vue').default,
	},
	{
		path: '/app/auth/login',
		name: 'login',
		meta: {requiresAuthentication: false,},
		component: require('./components/auth/LoginComponent.vue').default,
	},
	{
		path: '/app/auth/register',
		name: 'register',
		meta: {requiresAuthentication: false,},
		component: require('./components/auth/RegisterComponent.vue').default,
	},
	{
		path: '/app/404',
		name: '404',
		meta: {requiresAuthentication: null,},
		redirect: '*',
	},
	{
		path: '*',
		meta: {requiresAuthentication: null,},
		component: require('./components/errors/404Component.vue').default,
	}
]
