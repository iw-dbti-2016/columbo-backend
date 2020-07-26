export default [
	{
		path: '/app',
		name: 'home',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/HomeComponent.vue').default,
	},
	{
		path: '/app/users/:username',
		name: 'showProfile',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/profile/ShowProfileComponent.vue').default,
	},
	{
		path: '/app/trips/create',
		name: 'createTrip',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/trips/CreateTripComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/edit',
		name: 'editTrip',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/trips/EditTripComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId',
		name: 'showTrip',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/trips/ShowTripComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/create',
		name: 'createReport',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/reports/CreateReportComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/:reportId/edit',
		name: 'editReport',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/reports/EditReportComponent.vue').default,
	},
	{
		path: '/app/trips/:tripId/reports/:reportId',
		name: 'showReport',
		meta: { requiresAuthentication: true, requiresVerification: true, showMenu: true },
		component: require('./components/reports/ShowReportComponent.vue').default,
	},
	{
		path: '/app/auth/login',
		name: 'login',
		meta: { requiresAuthentication: false, requiresVerification: null, showMenu: false },
		component: require('./screens/auth/LoginComponent.vue').default,
	},
	{
		path: '/app/auth/register',
		name: 'register',
		meta: { requiresAuthentication: false, requiresVerification: null, showMenu: false },
		component: require('./screens/auth/RegisterComponent.vue').default,
	},
	{
		path: '/app/auth/verify-email',
		name: 'verify-email',
		meta: { requiresAuthentication: true, requiresVerification: false, showMenu: false },
		component: require('./screens/auth/VerifyEmailComponent.vue').default,
	},
	{
		path: '/app/404',
		name: '404',
		meta: { requiresAuthentication: null, requiresVerification: null, showMenu: false },
		redirect: '*',
	},
	{
		path: '/app/*',
		meta: { requiresAuthentication: null, requiresVerification: null, showMenu: false },
		component: require('./components/errors/404Component.vue').default,
	}
]
