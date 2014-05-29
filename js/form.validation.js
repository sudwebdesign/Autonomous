/*
$js('bootstrap.validator',function(){
$(function(){
		// Generate a simple captcha
		function randomNumber(min, max) {
			return Math.floor(Math.random() * (max - min + 1) + min);
		};
		$('#captchaOperation').html([randomNumber(1, 100), '+', randomNumber(1, 200), '='].join(' '));

		$('form[action]').bootstrapValidator({
	//        live: 'disabled',
			message: 'This value is not valid',
			feedbackIcons: {
				valid: 'glyphicon glyphicon-ok',
				invalid: 'glyphicon glyphicon-remove',
				validating: 'glyphicon glyphicon-refresh'
			},
			fields: {
				firstName: {
					validators: {
						notEmpty: {
							message: 'The first name is required and cannot be empty'
						}
					}
				},
				lastName: {
					validators: {
						notEmpty: {
							message: 'The last name is required and cannot be empty'
						}
					}
				},
				username: {
					message: 'The username is not valid',
					validators: {
						notEmpty: {
							message: 'The username is required and cannot be empty'
						},
						stringLength: {
							min: 6,
							max: 30,
							message: 'The username must be more than 6 and less than 30 characters long'
						},
						regexp: {
							regexp: /^[a-zA-Z0-9_\.]+$/,
							message: 'The username can only consist of alphabetical, number, dot and underscore'
						},
	//                    remote: {
	//                        url: 'remote.php',
	//                        message: 'The username is not available'
	//                    },
						different: {
							field: 'password',
							message: 'The username and password cannot be the same as each other'
						}
					}
				},
				email: {
					validators: {
						emailAddress: {
							message: 'The input is not a valid email address'
						}
					}
				},
				password: {
					validators: {
						notEmpty: {
							message: 'The password is required and cannot be empty'
						},
						identical: {
							field: 'confirmPassword',
							message: 'The password and its confirm are not the same'
						},
						different: {
							field: 'username',
							message: 'The password cannot be the same as username'
						}
					}
				},
				confirmPassword: {
					validators: {
						notEmpty: {
							message: 'The confirm password is required and cannot be empty'
						},
						identical: {
							field: 'password',
							message: 'The password and its confirm are not the same'
						},
						different: {
							field: 'username',
							message: 'The password cannot be the same as username'
						}
					}
				},
				birthday: {
					validators: {
						date: {
							format: 'YYYY/MM/DD',
							message: 'The birthday is not valid'
						}
					}
				},
				gender: {
					validators: {
						notEmpty: {
							message: 'The gender is required'
						}
					}
				},
				'languages[]': {
					validators: {
						notEmpty: {
							message: 'Please specify at least one language you can speak'
						}
					}
				},
				'programs[]': {
					validators: {
						choice: {
							min: 2,
							max: 4,
							message: 'Please choose 2 - 4 programming languages you are good at'
						}
					}
				},
				captcha: {
					validators: {
						callback: {
							message: 'Wrong answer',
							callback: function(value, validator) {
								var items = $('#captchaOperation').html().split(' '), sum = parseInt(items[0]) + parseInt(items[2]);
								return value == sum;
							}
						}
					}
				}
			}
		});

		// Validate the form manually
		$('#validateBtn').click(function() {
			$('#defaultForm').bootstrapValidator('validate');
		});

		$('#resetBtn').click(function() {
			$('#defaultForm').data('bootstrapValidator').resetForm(true);
		});
	});
});
*/
