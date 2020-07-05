import Swal from 'sweetalert2'

export default {
	name: 'alert-helper-mixin',

	methods: {
		confirmAlert(config) {
			return Swal.fire({
				title: "Are you sure?",
				icon: "warning",
				showCancelButton: true,
				confirmButtonText: 'Yes, I\'m sure!',
				customClass: {
					confirmButton: "green-button",
					cancelButton: "red-button",
				},
				target: document.getElementById('parent-element'),
				...config,
			})
		},
		notifyAlert(config) {
			Swal.fire({
				title: "Successful Action",
				icon: "success",
				target: document.getElementById('parent-element'),
				...config,
			});
		},
	}
}
