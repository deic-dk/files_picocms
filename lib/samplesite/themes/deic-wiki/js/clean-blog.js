/*!
 * Clean Blog v1.0.0 (http://startbootstrap.com)
 * Copyright 2014 Start Bootstrap
 * Licensed under Apache 2.0 (https://github.com/IronSummitMedia/startbootstrap/blob/gh-pages/LICENSE)
 */

// Contact Form Scripts

$(function() {

		$("#contactFrom input,#contactForm textarea").jqBootstrapValidation({
				preventSubmit: true,
				submitError: function($form, event, errors) {
						// additional error messages or events
				},
				submitSuccess: function($form, event) {
						event.preventDefault(); // prevent default submit behaviour
						// get values from FORM
						var name = $("input#name").val();
						var email = $("input#email").val();
						var phone = $("input#phone").val();
						var message = $("textarea#message").val();
						var firstName = name; // For Success/Failure Message
						// Check for white space in name for Success/Fail message
						if (firstName.indexOf(' ') >= 0) {
								firstName = name.split(' ').slice(0, -1).join(' ');
						}
						$.ajax({
								url: "././mail/contact_me.php",
								type: "POST",
								data: {
										name: name,
										phone: phone,
										email: email,
										message: message
								},
								cache: false,
								success: function() {
										// Success message
										$('#success').html("<div class='alert alert-success'>");
										$('#success > .alert-success').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
												.append("</button>");
										$('#success > .alert-success')
												.append("<strong>Your message has been sent. </strong>");
										$('#success > .alert-success')
												.append('</div>');

										//clear all fields
										$('#contactForm').trigger("reset");
								},
								error: function() {
										// Fail message
										$('#success').html("<div class='alert alert-danger'>");
										$('#success > .alert-danger').html("<button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;")
												.append("</button>");
										$('#success > .alert-danger').append("<strong>Sorry " + firstName + ", it seems that my mail server is not responding. Please try again later!");
										$('#success > .alert-danger').append('</div>');
										//clear all fields
										$('#contactForm').trigger("reset");
								},
						})
				},
				filter: function() {
						return $(this).is(":visible");
				},
		});

		$("a[data-toggle=\"tab\"]").click(function(e) {
				e.preventDefault();
				$(this).tab("show");
		});
});


/*When clicking on Full hide fail/success boxes */
$('#name').focus(function() {
		$('#success').html('');
});

 // jqBootstrapValidation
 // * A plugin for automating validation on Twitter Bootstrap formatted forms.
 // *
 // * v1.3.6
 // *
 // * License: MIT <http://opensource.org/licenses/mit-license.php> - see LICENSE file
 // *
 // * http://ReactiveRaven.github.com/jqBootstrapValidation/
 

(function( $ ){

	var createdElements = [];

	var defaults = {
		options: {
			prependExistingHelpBlock: false,
			sniffHtml: true, // sniff for 'required', 'maxlength', etc
			preventSubmit: true, // stop the form submit event from firing if validation fails
			submitError: false, // function called if there is an error when trying to submit
			submitSuccess: false, // function called just before a successful submit event is sent to the server
						semanticallyStrict: false, // set to true to tidy up generated HTML output
			autoAdd: {
				helpBlocks: true
			},
						filter: function () {
								// return $(this).is(":visible"); // only validate elements you can see
								return true; // validate everything
						}
		},
		methods: {
			init : function( options ) {

				var settings = $.extend(true, {}, defaults);

				settings.options = $.extend(true, settings.options, options);

				var $siblingElements = this;

				var uniqueForms = $.unique(
					$siblingElements.map( function () {
						return $(this).parents("form")[0];
					}).toArray()
				);

				$(uniqueForms).bind("submit", function (e) {
					var $form = $(this);
					var warningsFound = 0;
					var $inputs = $form.find("input,textarea,select").not("[type=submit],[type=image]").filter(settings.options.filter);
					$inputs.trigger("submit.validation").trigger("validationLostFocus.validation");

					$inputs.each(function (i, el) {
						var $this = $(el),
							$controlGroup = $this.parents(".form-group").first();
						if (
							$controlGroup.hasClass("warning")
						) {
							$controlGroup.removeClass("warning").addClass("error");
							warningsFound++;
						}
					});

					$inputs.trigger("validationLostFocus.validation");

					if (warningsFound) {
						if (settings.options.preventSubmit) {
							e.preventDefault();
						}
						$form.addClass("error");
						if ($.isFunction(settings.options.submitError)) {
							settings.options.submitError($form, e, $inputs.jqBootstrapValidation("collectErrors", true));
						}
					} else {
						$form.removeClass("error");
						if ($.isFunction(settings.options.submitSuccess)) {
							settings.options.submitSuccess($form, e);
						}
					}
				});

				return this.each(function(){

					// Get references to everything we're interested in
					var $this = $(this),
						$controlGroup = $this.parents(".form-group").first(),
						$helpBlock = $controlGroup.find(".help-block").first(),
						$form = $this.parents("form").first(),
						validatorNames = [];

					// create message container if not exists
					if (!$helpBlock.length && settings.options.autoAdd && settings.options.autoAdd.helpBlocks) {
							$helpBlock = $('<div class="help-block" />');
							$controlGroup.find('.controls').append($helpBlock);
							createdElements.push($helpBlock[0]);
					}

					// =============================================================
					//                                     SNIFF HTML FOR VALIDATORS
					// =============================================================

					// *snort sniff snuffle*

					if (settings.options.sniffHtml) {
						var message = "";
						// ---------------------------------------------------------
						//                                                   PATTERN
						// ---------------------------------------------------------
						if ($this.attr("pattern") !== undefined) {
							message = "Not in the expected format<!-- data-validation-pattern-message to override -->";
							if ($this.data("validationPatternMessage")) {
								message = $this.data("validationPatternMessage");
							}
							$this.data("validationPatternMessage", message);
							$this.data("validationPatternRegex", $this.attr("pattern"));
						}
						// ---------------------------------------------------------
						//                                                       MAX
						// ---------------------------------------------------------
						if ($this.attr("max") !== undefined || $this.attr("aria-valuemax") !== undefined) {
							var max = ($this.attr("max") !== undefined ? $this.attr("max") : $this.attr("aria-valuemax"));
							message = "Too high: Maximum of '" + max + "'<!-- data-validation-max-message to override -->";
							if ($this.data("validationMaxMessage")) {
								message = $this.data("validationMaxMessage");
							}
							$this.data("validationMaxMessage", message);
							$this.data("validationMaxMax", max);
						}
						// ---------------------------------------------------------
						//                                                       MIN
						// ---------------------------------------------------------
						if ($this.attr("min") !== undefined || $this.attr("aria-valuemin") !== undefined) {
							var min = ($this.attr("min") !== undefined ? $this.attr("min") : $this.attr("aria-valuemin"));
							message = "Too low: Minimum of '" + min + "'<!-- data-validation-min-message to override -->";
							if ($this.data("validationMinMessage")) {
								message = $this.data("validationMinMessage");
							}
							$this.data("validationMinMessage", message);
							$this.data("validationMinMin", min);
						}
						// ---------------------------------------------------------
						//                                                 MAXLENGTH
						// ---------------------------------------------------------
						if ($this.attr("maxlength") !== undefined) {
							message = "Too long: Maximum of '" + $this.attr("maxlength") + "' characters<!-- data-validation-maxlength-message to override -->";
							if ($this.data("validationMaxlengthMessage")) {
								message = $this.data("validationMaxlengthMessage");
							}
							$this.data("validationMaxlengthMessage", message);
							$this.data("validationMaxlengthMaxlength", $this.attr("maxlength"));
						}
						// ---------------------------------------------------------
						//                                                 MINLENGTH
						// ---------------------------------------------------------
						if ($this.attr("minlength") !== undefined) {
							message = "Too short: Minimum of '" + $this.attr("minlength") + "' characters<!-- data-validation-minlength-message to override -->";
							if ($this.data("validationMinlengthMessage")) {
								message = $this.data("validationMinlengthMessage");
							}
							$this.data("validationMinlengthMessage", message);
							$this.data("validationMinlengthMinlength", $this.attr("minlength"));
						}
						// ---------------------------------------------------------
						//                                                  REQUIRED
						// ---------------------------------------------------------
						if ($this.attr("required") !== undefined || $this.attr("aria-required") !== undefined) {
							message = settings.builtInValidators.required.message;
							if ($this.data("validationRequiredMessage")) {
								message = $this.data("validationRequiredMessage");
							}
							$this.data("validationRequiredMessage", message);
						}
						// ---------------------------------------------------------
						//                                                    NUMBER
						// ---------------------------------------------------------
						if ($this.attr("type") !== undefined && $this.attr("type").toLowerCase() === "number") {
							message = settings.builtInValidators.number.message;
							if ($this.data("validationNumberMessage")) {
								message = $this.data("validationNumberMessage");
							}
							$this.data("validationNumberMessage", message);
						}
						// ---------------------------------------------------------
						//                                                     EMAIL
						// ---------------------------------------------------------
						if ($this.attr("type") !== undefined && $this.attr("type").toLowerCase() === "email") {
							message = "Not a valid email address<!-- data-validator-validemail-message to override -->";
							if ($this.data("validationValidemailMessage")) {
								message = $this.data("validationValidemailMessage");
							} else if ($this.data("validationEmailMessage")) {
								message = $this.data("validationEmailMessage");
							}
							$this.data("validationValidemailMessage", message);
						}
						// ---------------------------------------------------------
						//                                                MINCHECKED
						// ---------------------------------------------------------
						if ($this.attr("minchecked") !== undefined) {
							message = "Not enough options checked; Minimum of '" + $this.attr("minchecked") + "' required<!-- data-validation-minchecked-message to override -->";
							if ($this.data("validationMincheckedMessage")) {
								message = $this.data("validationMincheckedMessage");
							}
							$this.data("validationMincheckedMessage", message);
							$this.data("validationMincheckedMinchecked", $this.attr("minchecked"));
						}
						// ---------------------------------------------------------
						//                                                MAXCHECKED
						// ---------------------------------------------------------
						if ($this.attr("maxchecked") !== undefined) {
							message = "Too many options checked; Maximum of '" + $this.attr("maxchecked") + "' required<!-- data-validation-maxchecked-message to override -->";
							if ($this.data("validationMaxcheckedMessage")) {
								message = $this.data("validationMaxcheckedMessage");
							}
							$this.data("validationMaxcheckedMessage", message);
							$this.data("validationMaxcheckedMaxchecked", $this.attr("maxchecked"));
						}
					}

					// =============================================================
					//                                       COLLECT VALIDATOR NAMES
					// =============================================================

					// Get named validators
					if ($this.data("validation") !== undefined) {
						validatorNames = $this.data("validation").split(",");
					}

					// Get extra ones defined on the element's data attributes
					$.each($this.data(), function (i, el) {
						var parts = i.replace(/([A-Z])/g, ",$1").split(",");
						if (parts[0] === "validation" && parts[1]) {
							validatorNames.push(parts[1]);
						}
					});

					// =============================================================
					//                                     NORMALISE VALIDATOR NAMES
					// =============================================================

					var validatorNamesToInspect = validatorNames;
					var newValidatorNamesToInspect = [];

					do // repeatedly expand 'shortcut' validators into their real validators
					{
						// Uppercase only the first letter of each name
						$.each(validatorNames, function (i, el) {
							validatorNames[i] = formatValidatorName(el);
						});

						// Remove duplicate validator names
						validatorNames = $.unique(validatorNames);

						// Pull out the new validator names from each shortcut
						newValidatorNamesToInspect = [];
						$.each(validatorNamesToInspect, function(i, el) {
							if ($this.data("validation" + el + "Shortcut") !== undefined) {
								// Are these custom validators?
								// Pull them out!
								$.each($this.data("validation" + el + "Shortcut").split(","), function(i2, el2) {
									newValidatorNamesToInspect.push(el2);
								});
							} else if (settings.builtInValidators[el.toLowerCase()]) {
								// Is this a recognised built-in?
								// Pull it out!
								var validator = settings.builtInValidators[el.toLowerCase()];
								if (validator.type.toLowerCase() === "shortcut") {
									$.each(validator.shortcut.split(","), function (i, el) {
										el = formatValidatorName(el);
										newValidatorNamesToInspect.push(el);
										validatorNames.push(el);
									});
								}
							}
						});

						validatorNamesToInspect = newValidatorNamesToInspect;

					} while (validatorNamesToInspect.length > 0)

					// =============================================================
					//                                       SET UP VALIDATOR ARRAYS
					// =============================================================

					var validators = {};

					$.each(validatorNames, function (i, el) {
						// Set up the 'override' message
						var message = $this.data("validation" + el + "Message");
						var hasOverrideMessage = (message !== undefined);
						var foundValidator = false;
						message =
							(
								message
									? message
									: "'" + el + "' validation failed <!-- Add attribute 'data-validation-" + el.toLowerCase() + "-message' to input to change this message -->"
							)
						;

						$.each(
							settings.validatorTypes,
							function (validatorType, validatorTemplate) {
								if (validators[validatorType] === undefined) {
									validators[validatorType] = [];
								}
								if (!foundValidator && $this.data("validation" + el + formatValidatorName(validatorTemplate.name)) !== undefined) {
									validators[validatorType].push(
										$.extend(
											true,
											{
												name: formatValidatorName(validatorTemplate.name),
												message: message
											},
											validatorTemplate.init($this, el)
										)
									);
									foundValidator = true;
								}
							}
						);

						if (!foundValidator && settings.builtInValidators[el.toLowerCase()]) {

							var validator = $.extend(true, {}, settings.builtInValidators[el.toLowerCase()]);
							if (hasOverrideMessage) {
								validator.message = message;
							}
							var validatorType = validator.type.toLowerCase();

							if (validatorType === "shortcut") {
								foundValidator = true;
							} else {
								$.each(
									settings.validatorTypes,
									function (validatorTemplateType, validatorTemplate) {
										if (validators[validatorTemplateType] === undefined) {
											validators[validatorTemplateType] = [];
										}
										if (!foundValidator && validatorType === validatorTemplateType.toLowerCase()) {
											$this.data("validation" + el + formatValidatorName(validatorTemplate.name), validator[validatorTemplate.name.toLowerCase()]);
											validators[validatorType].push(
												$.extend(
													validator,
													validatorTemplate.init($this, el)
												)
											);
											foundValidator = true;
										}
									}
								);
							}
						}

						if (! foundValidator) {
							$.error("Cannot find validation info for '" + el + "'");
						}
					});

					// =============================================================
					//                                         STORE FALLBACK VALUES
					// =============================================================

					$helpBlock.data(
						"original-contents",
						(
							$helpBlock.data("original-contents")
								? $helpBlock.data("original-contents")
								: $helpBlock.html()
						)
					);

					$helpBlock.data(
						"original-role",
						(
							$helpBlock.data("original-role")
								? $helpBlock.data("original-role")
								: $helpBlock.attr("role")
						)
					);

					$controlGroup.data(
						"original-classes",
						(
							$controlGroup.data("original-clases")
								? $controlGroup.data("original-classes")
								: $controlGroup.attr("class")
						)
					);

					$this.data(
						"original-aria-invalid",
						(
							$this.data("original-aria-invalid")
								? $this.data("original-aria-invalid")
								: $this.attr("aria-invalid")
						)
					);

					// =============================================================
					//                                                    VALIDATION
					// =============================================================

					$this.bind(
						"validation.validation",
						function (event, params) {

							var value = getValue($this);

							// Get a list of the errors to apply
							var errorsFound = [];

							$.each(validators, function (validatorType, validatorTypeArray) {
								if (value || value.length || (params && params.includeEmpty) || (!!settings.validatorTypes[validatorType].blockSubmit && params && !!params.submitting)) {
									$.each(validatorTypeArray, function (i, validator) {
										if (settings.validatorTypes[validatorType].validate($this, value, validator)) {
											errorsFound.push(validator.message);
										}
									});
								}
							});

							return errorsFound;
						}
					);

					$this.bind(
						"getValidators.validation",
						function () {
							return validators;
						}
					);

					// =============================================================
					//                                             WATCH FOR CHANGES
					// =============================================================
					$this.bind(
						"submit.validation",
						function () {
							return $this.triggerHandler("change.validation", {submitting: true});
						}
					);
					$this.bind(
						[
							"keyup",
							"focus",
							"blur",
							"click",
							"keydown",
							"keypress",
							"change"
						].join(".validation ") + ".validation",
						function (e, params) {

							var value = getValue($this);

							var errorsFound = [];

							$controlGroup.find("input,textarea,select").each(function (i, el) {
								var oldCount = errorsFound.length;
								$.each($(el).triggerHandler("validation.validation", params), function (j, message) {
									errorsFound.push(message);
								});
								if (errorsFound.length > oldCount) {
									$(el).attr("aria-invalid", "true");
								} else {
									var original = $this.data("original-aria-invalid");
									$(el).attr("aria-invalid", (original !== undefined ? original : false));
								}
							});

							$form.find("input,select,textarea").not($this).not("[name=\"" + $this.attr("name") + "\"]").trigger("validationLostFocus.validation");

							errorsFound = $.unique(errorsFound.sort());

							// Were there any errors?
							if (errorsFound.length) {
								// Better flag it up as a warning.
								$controlGroup.removeClass("success error").addClass("warning");

								// How many errors did we find?
								if (settings.options.semanticallyStrict && errorsFound.length === 1) {
									// Only one? Being strict? Just output it.
									$helpBlock.html(errorsFound[0] + 
										( settings.options.prependExistingHelpBlock ? $helpBlock.data("original-contents") : "" ));
								} else {
									// Multiple? Being sloppy? Glue them together into an UL.
									$helpBlock.html("<ul role=\"alert\"><li>" + errorsFound.join("</li><li>") + "</li></ul>" +
										( settings.options.prependExistingHelpBlock ? $helpBlock.data("original-contents") : "" ));
								}
							} else {
								$controlGroup.removeClass("warning error success");
								if (value.length > 0) {
									$controlGroup.addClass("success");
								}
								$helpBlock.html($helpBlock.data("original-contents"));
							}

							if (e.type === "blur") {
								$controlGroup.removeClass("success");
							}
						}
					);
					$this.bind("validationLostFocus.validation", function () {
						$controlGroup.removeClass("success");
					});
				});
			},
			destroy : function( ) {

				return this.each(
					function() {

						var
							$this = $(this),
							$controlGroup = $this.parents(".form-group").first(),
							$helpBlock = $controlGroup.find(".help-block").first();

						// remove our events
						$this.unbind('.validation'); // events are namespaced.
						// reset help text
						$helpBlock.html($helpBlock.data("original-contents"));
						// reset classes
						$controlGroup.attr("class", $controlGroup.data("original-classes"));
						// reset aria
						$this.attr("aria-invalid", $this.data("original-aria-invalid"));
						// reset role
						$helpBlock.attr("role", $this.data("original-role"));
						// remove all elements we created
						if (createdElements.indexOf($helpBlock[0]) > -1) {
							$helpBlock.remove();
						}

					}
				);

			},
			collectErrors : function(includeEmpty) {

				var errorMessages = {};
				this.each(function (i, el) {
					var $el = $(el);
					var name = $el.attr("name");
					var errors = $el.triggerHandler("validation.validation", {includeEmpty: true});
					errorMessages[name] = $.extend(true, errors, errorMessages[name]);
				});

				$.each(errorMessages, function (i, el) {
					if (el.length === 0) {
						delete errorMessages[i];
					}
				});

				return errorMessages;

			},
			hasErrors: function() {

				var errorMessages = [];

				this.each(function (i, el) {
					errorMessages = errorMessages.concat(
						$(el).triggerHandler("getValidators.validation") ? $(el).triggerHandler("validation.validation", {submitting: true}) : []
					);
				});

				return (errorMessages.length > 0);
			},
			override : function (newDefaults) {
				defaults = $.extend(true, defaults, newDefaults);
			}
		},
		validatorTypes: {
			callback: {
				name: "callback",
				init: function ($this, name) {
					return {
						validatorName: name,
						callback: $this.data("validation" + name + "Callback"),
						lastValue: $this.val(),
						lastValid: true,
						lastFinished: true
					};
				},
				validate: function ($this, value, validator) {
					if (validator.lastValue === value && validator.lastFinished) {
						return !validator.lastValid;
					}

					if (validator.lastFinished === true)
					{
						validator.lastValue = value;
						validator.lastValid = true;
						validator.lastFinished = false;

						var rrjqbvValidator = validator;
						var rrjqbvThis = $this;
						executeFunctionByName(
							validator.callback,
							window,
							$this,
							value,
							function (data) {
								if (rrjqbvValidator.lastValue === data.value) {
									rrjqbvValidator.lastValid = data.valid;
									if (data.message) {
										rrjqbvValidator.message = data.message;
									}
									rrjqbvValidator.lastFinished = true;
									rrjqbvThis.data("validation" + rrjqbvValidator.validatorName + "Message", rrjqbvValidator.message);
									// Timeout is set to avoid problems with the events being considered 'already fired'
									setTimeout(function () {
										rrjqbvThis.trigger("change.validation");
									}, 1); // doesn't need a long timeout, just long enough for the event bubble to burst
								}
							}
						);
					}

					return false;

				}
			},
			ajax: {
				name: "ajax",
				init: function ($this, name) {
					return {
						validatorName: name,
						url: $this.data("validation" + name + "Ajax"),
						lastValue: $this.val(),
						lastValid: true,
						lastFinished: true
					};
				},
				validate: function ($this, value, validator) {
					if (""+validator.lastValue === ""+value && validator.lastFinished === true) {
						return validator.lastValid === false;
					}

					if (validator.lastFinished === true)
					{
						validator.lastValue = value;
						validator.lastValid = true;
						validator.lastFinished = false;
						$.ajax({
							url: validator.url,
							data: "value=" + value + "&field=" + $this.attr("name"),
							dataType: "json",
							success: function (data) {
								if (""+validator.lastValue === ""+data.value) {
									validator.lastValid = !!(data.valid);
									if (data.message) {
										validator.message = data.message;
									}
									validator.lastFinished = true;
									$this.data("validation" + validator.validatorName + "Message", validator.message);
									// Timeout is set to avoid problems with the events being considered 'already fired'
									setTimeout(function () {
										$this.trigger("change.validation");
									}, 1); // doesn't need a long timeout, just long enough for the event bubble to burst
								}
							},
							failure: function () {
								validator.lastValid = true;
								validator.message = "ajax call failed";
								validator.lastFinished = true;
								$this.data("validation" + validator.validatorName + "Message", validator.message);
								// Timeout is set to avoid problems with the events being considered 'already fired'
								setTimeout(function () {
									$this.trigger("change.validation");
								}, 1); // doesn't need a long timeout, just long enough for the event bubble to burst
							}
						});
					}

					return false;

				}
			},
			regex: {
				name: "regex",
				init: function ($this, name) {
					return {regex: regexFromString($this.data("validation" + name + "Regex"))};
				},
				validate: function ($this, value, validator) {
					return (!validator.regex.test(value) && ! validator.negative)
						|| (validator.regex.test(value) && validator.negative);
				}
			},
			required: {
				name: "required",
				init: function ($this, name) {
					return {};
				},
				validate: function ($this, value, validator) {
					return !!(value.length === 0  && ! validator.negative)
						|| !!(value.length > 0 && validator.negative);
				},
				blockSubmit: true
			},
			match: {
				name: "match",
				init: function ($this, name) {
					var element = $this.parents("form").first().find("[name=\"" + $this.data("validation" + name + "Match") + "\"]").first();
					element.bind("validation.validation", function () {
						$this.trigger("change.validation", {submitting: true});
					});
					return {"element": element};
				},
				validate: function ($this, value, validator) {
					return (value !== validator.element.val() && ! validator.negative)
						|| (value === validator.element.val() && validator.negative);
				},
				blockSubmit: true
			},
			max: {
				name: "max",
				init: function ($this, name) {
					return {max: $this.data("validation" + name + "Max")};
				},
				validate: function ($this, value, validator) {
					return (parseFloat(value, 10) > parseFloat(validator.max, 10) && ! validator.negative)
						|| (parseFloat(value, 10) <= parseFloat(validator.max, 10) && validator.negative);
				}
			},
			min: {
				name: "min",
				init: function ($this, name) {
					return {min: $this.data("validation" + name + "Min")};
				},
				validate: function ($this, value, validator) {
					return (parseFloat(value) < parseFloat(validator.min) && ! validator.negative)
						|| (parseFloat(value) >= parseFloat(validator.min) && validator.negative);
				}
			},
			maxlength: {
				name: "maxlength",
				init: function ($this, name) {
					return {maxlength: $this.data("validation" + name + "Maxlength")};
				},
				validate: function ($this, value, validator) {
					return ((value.length > validator.maxlength) && ! validator.negative)
						|| ((value.length <= validator.maxlength) && validator.negative);
				}
			},
			minlength: {
				name: "minlength",
				init: function ($this, name) {
					return {minlength: $this.data("validation" + name + "Minlength")};
				},
				validate: function ($this, value, validator) {
					return ((value.length < validator.minlength) && ! validator.negative)
						|| ((value.length >= validator.minlength) && validator.negative);
				}
			},
			maxchecked: {
				name: "maxchecked",
				init: function ($this, name) {
					var elements = $this.parents("form").first().find("[name=\"" + $this.attr("name") + "\"]");
					elements.bind("click.validation", function () {
						$this.trigger("change.validation", {includeEmpty: true});
					});
					return {maxchecked: $this.data("validation" + name + "Maxchecked"), elements: elements};
				},
				validate: function ($this, value, validator) {
					return (validator.elements.filter(":checked").length > validator.maxchecked && ! validator.negative)
						|| (validator.elements.filter(":checked").length <= validator.maxchecked && validator.negative);
				},
				blockSubmit: true
			},
			minchecked: {
				name: "minchecked",
				init: function ($this, name) {
					var elements = $this.parents("form").first().find("[name=\"" + $this.attr("name") + "\"]");
					elements.bind("click.validation", function () {
						$this.trigger("change.validation", {includeEmpty: true});
					});
					return {minchecked: $this.data("validation" + name + "Minchecked"), elements: elements};
				},
				validate: function ($this, value, validator) {
					return (validator.elements.filter(":checked").length < validator.minchecked && ! validator.negative)
						|| (validator.elements.filter(":checked").length >= validator.minchecked && validator.negative);
				},
				blockSubmit: true
			}
		},
		builtInValidators: {
			email: {
				name: "Email",
				type: "shortcut",
				shortcut: "validemail"
			},
			validemail: {
				name: "Validemail",
				type: "regex",
				regex: "[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\\\.[A-Za-z]{2,4}",
				message: "Not a valid email address<!-- data-validator-validemail-message to override -->"
			},
			passwordagain: {
				name: "Passwordagain",
				type: "match",
				match: "password",
				message: "Does not match the given password<!-- data-validator-paswordagain-message to override -->"
			},
			positive: {
				name: "Positive",
				type: "shortcut",
				shortcut: "number,positivenumber"
			},
			negative: {
				name: "Negative",
				type: "shortcut",
				shortcut: "number,negativenumber"
			},
			number: {
				name: "Number",
				type: "regex",
				regex: "([+-]?\\\d+(\\\.\\\d*)?([eE][+-]?[0-9]+)?)?",
				message: "Must be a number<!-- data-validator-number-message to override -->"
			},
			integer: {
				name: "Integer",
				type: "regex",
				regex: "[+-]?\\\d+",
				message: "No decimal places allowed<!-- data-validator-integer-message to override -->"
			},
			positivenumber: {
				name: "Positivenumber",
				type: "min",
				min: 0,
				message: "Must be a positive number<!-- data-validator-positivenumber-message to override -->"
			},
			negativenumber: {
				name: "Negativenumber",
				type: "max",
				max: 0,
				message: "Must be a negative number<!-- data-validator-negativenumber-message to override -->"
			},
			required: {
				name: "Required",
				type: "required",
				message: "This is required<!-- data-validator-required-message to override -->"
			},
			checkone: {
				name: "Checkone",
				type: "minchecked",
				minchecked: 1,
				message: "Check at least one option<!-- data-validation-checkone-message to override -->"
			}
		}
	};

	var formatValidatorName = function (name) {
		return name
			.toLowerCase()
			.replace(
				/(^|\s)([a-z])/g ,
				function(m,p1,p2) {
					return p1+p2.toUpperCase();
				}
			)
		;
	};

	var getValue = function ($this) {
		// Extract the value we're talking about
		var value = $this.val();
		var type = $this.attr("type");
		if (type === "checkbox") {
			value = ($this.is(":checked") ? value : "");
		}
		if (type === "radio") {
			value = ($('input[name="' + $this.attr("name") + '"]:checked').length > 0 ? value : "");
		}
		return value;
	};

	function regexFromString(inputstring) {
		return new RegExp("^" + inputstring + "$");
	}

	/**
	 * Thanks to Jason Bunting via StackOverflow.com
	 *
	 * http://stackoverflow.com/questions/359788/how-to-execute-a-javascript-function-when-i-have-its-name-as-a-string#answer-359910
	 * Short link: http://tinyurl.com/executeFunctionByName
	**/
	function executeFunctionByName(functionName, context /*, args*/) {
		var args = Array.prototype.slice.call(arguments).splice(2);
		var namespaces = functionName.split(".");
		var func = namespaces.pop();
		for(var i = 0; i < namespaces.length; i++) {
			context = context[namespaces[i]];
		}
		return context[func].apply(this, args);
	}

	$.fn.jqBootstrapValidation = function( method ) {

		if ( defaults.methods[method] ) {
			return defaults.methods[method].apply( this, Array.prototype.slice.call( arguments, 1 ));
		} else if ( typeof method === 'object' || ! method ) {
			return defaults.methods.init.apply( this, arguments );
		} else {
		$.error( 'Method ' +  method + ' does not exist on jQuery.jqBootstrapValidation' );
			return null;
		}

	};

	$.jqBootstrapValidation = function (options) {
		$(":input").not("[type=image],[type=submit]").jqBootstrapValidation.apply(this,arguments);
	};

})( jQuery );

// Floating label headings for the contact form
$(function() {
		$("body").on("input propertychange", ".floating-label-form-group", function(e) {
				$(this).toggleClass("floating-label-form-group-with-value", !!$(e.target).val());
		}).on("focus", ".floating-label-form-group", function() {
				$(this).addClass("floating-label-form-group-with-focus");
		}).on("blur", ".floating-label-form-group", function() {
				$(this).removeClass("floating-label-form-group-with-focus");
		});
});

function getTodayDate(t){
	var monthNames = ["January", "February", "March", "April", "May", "June",
		"July", "August", "September", "October", "November", "December"];
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth();//January is 0!
	var yyyy = today.getFullYear();
	var time = '';
	if(t){
		time = ', '+today.getHours()+':'+ today.getMinutes();
	}
	return dd+' '+monthNames[mm]+' '+yyyy+time;
}

function fixComments(el){
	var date = el.attr('date') || '';
	var author = el.attr('author') || '';
	var user = el.attr('user') || '';
	var html = '<div class="comment_meta"><div class="comment_author">'+author+'</div><div class="comment_date">'+date+'</div></div>';
	if(user && user.length){
		var avatar = '<div class="comment_avatar avatardiv" author="'+user+'"></div>';
		html = avatar + html;
	}
	el.html('<div class="comment_text">'+el.html()+'</div>');
	$(html).prependTo(el);
}

function scrollToEl(el, extraOffset){
  $('html, body').animate({scrollTop: $(el).offset().top+
    (typeof extraOffset!=='undefined'?extraOffset:0)}, 600);
}

// Navigation Scripts to Show Header on Scroll-Up
jQuery(document).ready(function($) {
	
	/*$.ajax({
		url: OC.filePath('files_sharding', 'ajax', 'get_user_server.php'),
		async: false,
		data: {
		},
		success: function (data) {
			OC.oc_current_user =data.user;
			OC.currentUser =data.user;
		},
		failure: function () {
		}
	});*/	
		var MQL = 1170;

		//primary navigation slide-in effect
		if ($(window).width() > MQL) {
				var headerHeight = $('.navbar-custom').height();
				$(window).on('scroll', {
								previousTop: 0
						},
						function() {
								var currentTop = $(window).scrollTop();
								//check if user is scrolling up
								if (currentTop < this.previousTop) {
										//if scrolling up...
										if (currentTop > 0 && $('.navbar-custom').hasClass('is-fixed')) {
												$('.navbar-custom').addClass('is-visible');
										} else {
												$('.navbar-custom').removeClass('is-visible is-fixed');
										}
								} else {
										//if scrolling down...
										$('.navbar-custom').removeClass('is-visible');
										if (currentTop > headerHeight && !$('.navbar-custom').hasClass('is-fixed')) $('.navbar-custom').addClass('is-fixed');
								}
								this.previousTop = currentTop;
						});
		}
		
		//Open file in file/editor view
		$('.edit-button').click(function() {
			var group = $('.edit-button').attr('group') || '';
			var path = $('.edit-button').attr('path');
			var dir_id = $('.edit-button').attr('dir_id') || '';
			var id = $('.edit-button').attr('id') || '';
			var owner = $('.edit-button').attr('owner') || '';
			var pathArr = path.split('/');
			var file = pathArr.pop();
			var dir = pathArr.join('/');
			$.when(window.showFileEditor(dir, file, id, owner)).then(function (){
				var user_home_url = $('.write-post').attr('user_home_url');
				$('.edit-button, .write-post, .upload').hide();
				enableEditorUnsavedWarning(false);
				if(typeof id!=='undefined' && id){
					$('#editor').attr('data-id', id);
				}
				if(typeof owner!=='undefined' && owner){
					$('#editor').attr('data-owner', owner);
				}
				$([document.documentElement, document.body]).animate({
					scrollTop: $("#editor_container").first().parent().offset().top-80
				}, 1000);
				$('#editor_close').click(function(){
					$('.edit-button, .write-post, .upload').show();
					if($('#editor').attr('data-saved')){
						window.location.reload(false); 
					}
				});
			});
		});
		
		// TOC scrolling
	  $('[href^=#toc_head]').click(function(ev){
	    ev.stopPropagation();
	    ev.preventDefault();
	    scrollToEl($(ev.target).attr('href'), -76);
	  });
		
		// Refactor dom to have comments in comments div
		$('comment').each(function(){
			fixComments($(this));
			$(this).prependTo($('#comments'));
		});
		
		$('comment .comment_avatar.avatardiv').each(function(){
			$(this).avatar($(this).parent().attr('user'), 48);
		});

		
		// Hide comments
		
		$('comments').hide();
		$('.laquo').hide();

		$('#show_comments').click(function(ev){
			$('.laquo').toggle();
			$('.raquo').toggle();
			$('comments').toggle('slow');
	});
		
	$('#submit_comment').click(function(ev){
		// Add comment
		var pathArr = $('.comment-post').attr('path').split('/');
		var filename = pathArr[pathArr.length-1];
		pathArr.pop();
		var dir = pathArr.join('/');
		var sharedDirId = $('.comment-post').attr('share');
		var user = $('.comment-post').attr('user') || '';
		var name = $('input#name').val() || '';
		var owner = $('.comment-post').attr('owner') || '';
		var group = $('.comment-post').attr('group') || '';
		var today = getTodayDate(true);
		var comment = $('textarea#comment').val().replace('\n', '<br />\n');
		
		if(!name || !name.length){
			OC.dialogs.alert(t('core', 'Please fill out your name'), t('core', 'Error'));
			return false;
		}
		if(!comment || !comment.length){
			return false;
		}
		
		var content = '\n<comment date="'+today+'" author="'+name+'"'+' user="'+user+'">'+comment+'</comment>\n';
		$.post(
				OC.webroot+'/themes/deic_theme_oc7/apps/files/ajax/newfile.php',
				{
					overwrite: 'append',
					id: sharedDirId,
					dir: dir,
					filename: filename,
					owner: owner,
					group: group,
					content: content
				},
				function(result) {
					if (result.status === 'success') {
						$.when($(content).prependTo($('#comments'))).done(fixComments($('#comments comment').first()));
						$('textarea#comment').val('');
						$('comment .comment_avatar.avatardiv').first().avatar($('comment .comment_avatar').first().attr('author'), 48);
					} else {
						OC.dialogs.alert(result.data.message, t('core', 'Could not create file'));
					}
				}
			);
	});

	$('.post-preview .avatardiv').each(function(){
		$(this).avatar($(this).attr('author'), 48);
	});

	$('.post-header .avatardiv').each(function(){
		$(this).avatar($(this).attr('author'), 64);
	});

	//$('form.navbar-search').hide();
	$('input#write_post').hide();

	$('a.search').click(function(ev){
		ev.preventDefault();
		ev.stopPropagation();
		$('a.search').hide('slow');
		$('form.navbar-search').show('slow');
		$('#search_input').focus();
	});

	$('#search_input').focusout(function(ev){
		ev.preventDefault();
		ev.stopPropagation();
		//$('form.navbar-search').hide('slow');
		//$('a.search').show('slow');
	});

	$('.upload').click(function(ev){
		$('input#upload_file').click();
	});

	$('input#upload_file').change(function(ev) {
		var requesttoken = $('head').attr('data-requesttoken');
		var pathArr = $('.write-post').attr('path').split('/');
		pathArr.pop();
		var dir = pathArr.join('/');
		var dir_id = $('.edit-button').attr('dir_id') || '';
		var owner = $('.write-post').attr('owner') || '';
		var user_home_url = $('.write-post').attr('user_home_url');
		var group = $('.write-post').attr('group') || '';
		var form_data = new FormData();
		form_data.append('requesttoken', requesttoken);
		form_data.append('dir', dir);
		form_data.append('id', dir_id);
		form_data.append('owner', owner);
		form_data.append('requesttoken', requesttoken);
		var file_data = $(this).prop('files')[0];
		form_data.append('files[]', file_data);
		$.ajax({
			processData: false,
			contentType: false,
			url: user_home_url+'/themes/deic_theme_oc7/apps/files/ajax/upload.php',
			data: form_data,
			type: 'post',
			success: function(dat){
				$("#notification").text("File uploaded").fadeOut(3000);
			},
			error: function(dat){
				alert("ERROR");
			}
		});
	});

	$('#manage_files').click(function(ev){
		var pathArr = $('.write-post').attr('path').split('/');
		pathArr.pop();
		var dir = pathArr.join('/');
		var sharedDirId = $('.write-post').attr('share');
		var owner = $('.write-post').attr('owner') || '';
		var user_home_url = $('.write-post').attr('user_home_url');
		var group = $('.write-post').attr('group') || '';
		var link = user_home_url+'/index.php/apps/files/?dir='+dir+'&group='+group+'&owner='+(sharedDirId?owner:'')+'&id='+sharedDirId;
		 //window.location.href = link;
		 var win = window.open(link, '_blank');
		if (win) {
			//Browser has allowed it to be opened
			win.focus();
		}
		else {
			//Browser has blocked it
			alert('Please allow popups for this website');
		}
	});

	$('.write-post').click(function(ev){
		ev.preventDefault();
		ev.stopPropagation();
		if($(window).width()<768){
			$('nav a, .nav.menu, .nav.labels').hide('slow');
		}
		$('.write-post').hide('slow');
		$('input#write_post').show('slow');
		$('input#write_post').focus();
	});

	$('input#write_post').focusout(function(ev){
		ev.preventDefault();
		ev.stopPropagation();
		if($(window).width()<768){
			$('nav a, .nav.menu, .nav.labels').show('slow');
		}
		$('input#write_post').hide('slow');
		$('.write-post').show('slow');
	});

	if(!window.location.href.match(/.*[\?&]index=.*/)){
		$('.leftbar').hide();
		$('button.parent-button').show();
	}
	else{
		$('button.parent-button').hide();
		$('.leftbar').find('a[href="'+decodeURI(window.location.href)+'"]').closest('li').addClass('active');
	}
	
	$('.nav.menu').click(function(ev){
		$('.labelsbar').hide();
		if($('.leftbar:visible').length){
			$('.leftbar').hide();
			$('button.parent-button').show();
			window.history.replaceState({}, document.title, window.location.href.replace(/[\?&]index=[^=&]*/, ''));
		}
		else{
			$('.leftbar').show();
			$('.leftbar').find('a[href="'+decodeURI(window.location.href)+'"]').closest('li').addClass('active');
			$('.leftbar').find('a[href="'+decodeURI(window.location.href)+'?index=1"]').closest('li').addClass('active');
			$('button.parent-button').hide();
		}
		$('.nav.menu').css('cursor', 'pointer');
		ev.stopPropagation();
	});
	$(window).click(function(ev){
		$('.leftbar, .labelsbar').hide();
		$('button.parent-button').show();
		window.history.replaceState({}, document.title, window.location.href.replace(/[\?&]index=[^=&]*/, ''));
	});
	$('.leftbar, .labelsbar, nav .labels').click(function(ev){
		ev.stopPropagation();
	});

	$('.labelsbar').hide();
	$('.nav.labels').click(function(ev){
		$('.labelsbar').toggle();
		$('.leftbar').hide();
		$('button.parent-button').show();
		window.history.replaceState({}, document.title, window.location.href.replace(/[\?&]index=[^=&]*/, ''));
	});
	
	$('button.parent-button').click(function(ev){
		var home =$('a#home').attr('href');
		var parent = window.location.href.replace(/([^\/]+[\?&=][^\/]+)+?/, '').replace(/\/[^\/]+\/*$/, '/');
		if(parent.length>=home.length){
			window.location.href = parent;
		}
	});
		
	$("input#write_post").keyup(function (e) {
		if (e.keyCode == 13) {
			var pathArr = $('.write-post').attr('path').split('/');
			pathArr.pop();
			var dir = pathArr.join('/');
			var sharedDirId = $('.write-post').attr('share');
			var title = $('#write_post').val();
			var filename = title.replace(/[^\w\s]/gi, '').replace(/\s/g, '_')+'.md';
			var user = $('.write-post').attr('user') || '';
			var owner = $('.write-post').attr('owner') || '';
			var user_home_url = $('.write-post').attr('user_home_url');
			var group = $('.write-post').attr('group') || '';
			var today = getTodayDate();
			var content = '---\n\n'+'Title: '+title+'\n'+'Description: '+'\n'+'Date: '+today+'\n'+
				'Author: '+user+'\n'+'Template: page\n'+'Access: private\n'+'Theme: deic-wiki\n'+'Comments: on\n\n'+
				'---\n\n\# \%meta.title\%\n\n';
			if(!filename || !filename.length){
				//window.location.href = user_home_url+'/index.php/apps/files/?dir='+dir+'&group='+group+'&owner='+owner;
			}
			else{
				// Create file
				$.post(
					//OC.filePath('files', 'ajax', 'newfile.php'),
					OC.webroot+'/themes/deic_theme_oc7/apps/files/ajax/newfile.php',
					{
						id: sharedDirId,
						dir: dir,
						filename: filename,
						owner: owner,
						group: group,
						content: content
					},
					function(result) {
						if (result.status === 'success') {
							//window.location.href = user_home_url+'/index.php/apps/files/?dir='+dir+'&id='+sharedDirId+'&group='+group+'&owner='+owner+
							//	'&file='+encodeURIComponent(filename);
							var folder = window.location.href.replace(/\/[^\/]*$/, '');
							window.location.href = folder+'/'+filename.replace(/\.md$/, '');
						} else {
							OC.dialogs.alert(result.data.message, t('core', 'Could not create file. Try logging in.'));
						}
					}
				).fail(function(){OC.dialogs.alert('Could not create file. try logging in.', t('core', 'ERROR'));});
			}
		}
	});

	$('.modal-image').click(function(ev){
		ev.stopPropagation();
		ev.preventDefault();
		var img = $(ev.target).closest('[src]').first();
		var modal = img.parent().find('.modal');
		var modalImg = img.parent().find('.modal-img');
		var caption = img.parent().find('.modal-caption');

		modal.css('display', 'block');
		modalImg.attr('src', img.attr('src'));
		caption.html(img.attr('alt'));
		modal.find(".close").first().click(function() {
			modal.css('display', 'none');
		});
		modal.click(function(el) {
			if(!$(el.target).closest('.modal-img').length && !$(el.target).closest('.modal-caption').length){
				modal.css('display', 'none');
			}
		});
	});

	$('.modal-image').each(function(el){
		$(this).after('\
		<div class="modal">\
			<div class="imagebox">\
				<span class="close">&times;</span>\
				<img class="modal-img">\
				<div class="modal-caption"></div>\
			</div>\
		</div>');
	});
	
	// Move contents or index into place.
	if($('div#dir_index').length==1 && $('index').length==1){
		$('index').replaceWith($('div#dir_index').first());
	}
	if($('div#contents').length==1 && $('contents').length==1){
		$('contents').replaceWith($('div#contents').first());
	}

 	// If a div.toc is in the twig and a toc is in the md, move div.toc in place.
 	$('toc').replaceWith($('div#toc').first());
 	
});
