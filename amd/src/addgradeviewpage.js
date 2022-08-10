/**
 * Create chapter js logic.
 *
 * @package
 * @subpackage mod_evokeportfolio
 * @copyright  2021 World Bank Group <https://worldbank.org>
 * @author     Willian Mano <willianmanoaraujo@gmail.com>
 */

/* eslint-disable */
define([
        'jquery',
        'core/config',
        'core/str',
        'core/modal_factory',
        'core/modal_events',
        'core/fragment',
        'core/ajax',
        'mod_evokeportfolio/sweetalert',
        'core/yui'],
    function($, Config, Str, ModalFactory, ModalEvents, Fragment, Ajax, Swal, Y) {
        /**
         * Constructor for the AddGradeViewPage.
         *
         * @param selector The selector to open the modal
         * @param contextid The course module contextid
         */
        var AddGradeViewPage = function(contextid) {
            this.contextid = contextid;

            this.registerEventListeners();
        };

        /**
         * @var {Modal} modal
         * @private
         */
        AddGradeViewPage.prototype.modal = null;

        /**
         * @var {int} contextid
         * @private
         */
        AddGradeViewPage.prototype.contextid = -1;

        /**
         * @var {int} gradebutton
         * @private
         */
        AddGradeViewPage.prototype.gradebutton = -1;

        AddGradeViewPage.prototype.registerEventListeners = function() {
            $(document).on('click', '.grade-portfolio', function(event) {
                this.gradebutton = $(event.currentTarget);

                this.openModal(this.gradebutton.data('portfolioid'), this.gradebutton.data('userid'));
            }.bind(this));
        };

        AddGradeViewPage.prototype.openModal = function(portfolioid, userid) {
            ModalFactory.create({
                type: ModalFactory.types.SAVE_CANCEL,
                title: 'Add grade',
                body: this.getBody({instanceid: portfolioid, userid: userid}),
            }).then(function(modal) {
                this.modal = modal;

                // We want to reset the form every time it is opened.
                this.modal.getRoot().on(ModalEvents.hidden, function() {
                    this.modal.setBody(this.getBody({instanceid: portfolioid, userid: userid}));
                }.bind(this));

                // We want to hide the submit buttons every time it is opened.
                this.modal.getRoot().on(ModalEvents.shown, function() {
                    this.modal.getRoot().append('<style>[data-fieldtype=submit] { display: none ! important; }</style>');
                }.bind(this));

                // We catch the modal save event, and use it to submit the form inside the modal.
                // Triggering a form submission will give JS validation scripts a chance to check for errors.
                this.modal.getRoot().on(ModalEvents.save, this.submitForm.bind(this));
                // We also catch the form submit event and use it to submit the form with ajax.
                this.modal.getRoot().on('submit', 'form', this.submitFormAjax.bind(this));

                this.modal.show();
            }.bind(this));
        };

        /**
         * @method getBody
         *
         * @private
         *
         * @return {Promise}
         */
        AddGradeViewPage.prototype.getBody = function(formdata) {
            if (typeof formdata === "undefined") {
                formdata = {};
            }

            // Get the content of the modal.
            var params = {jsonformdata: JSON.stringify(formdata)};

            return Fragment.loadFragment('mod_evokeportfolio', 'grade_form', this.contextid, params);
        };

        /**
         * @method handleFormSubmissionResponse
         *
         * @private
         *
         * @return {Promise}
         */
        AddGradeViewPage.prototype.handleFormSubmissionResponse = function(data) {
            this.modal.hide();

            // We could trigger an event instead.
            Y.use('moodle-core-formchangechecker', function() {
                M.core_formchangechecker.reset_form_dirty_state();
            });

            var assessment = $("<span class='badge badge-success'>"+data.assessmenttext+"</span>");

            var buttonparent = this.gradebutton.parent();

            buttonparent.append(assessment);

            this.gradebutton.remove();

            var Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 8000,
                timerProgressBar: true,
                onOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            Toast.fire({
                icon: 'success',
                title: data.message
            });
        };

        /**
         * @method handleFormSubmissionFailure
         *
         * @private
         *
         * @return {Promise}
         */
        AddGradeViewPage.prototype.handleFormSubmissionFailure = function(data) {
            // Oh noes! Epic fail :(
            // Ah wait - this is normal. We need to re-display the form with errors!
            this.modal.setBody(this.getBody(data));
        };

        /**
         * Private method
         *
         * @method submitFormAjax
         *
         * @private
         *
         * @param {Event} e Form submission event.
         */
        AddGradeViewPage.prototype.submitFormAjax = function(e) {
            // We don't want to do a real form submission.
            e.preventDefault();

            var changeEvent = document.createEvent('HTMLEvents');
            changeEvent.initEvent('change', true, true);

            // Prompt all inputs to run their validation functions.
            // Normally this would happen when the form is submitted, but
            // since we aren't submitting the form normally we need to run client side
            // validation.
            this.modal.getRoot().find(':input').each(function(index, element) {
                element.dispatchEvent(changeEvent);
            });

            // Now the change events have run, see if there are any "invalid" form fields.
            var invalid = $.merge(
                this.modal.getRoot().find('[aria-invalid="true"]'),
                this.modal.getRoot().find('.error')
            );

            // If we found invalid fields, focus on the first one and do not submit via ajax.
            if (invalid.length) {
                invalid.first().focus();
                return;
            }

            // Convert all the form elements values to a serialised string.
            var formData = this.modal.getRoot().find('form').serialize();

            // Now we can continue...
            Ajax.call([{
                methodname: 'mod_evokeportfolio_gradeportfolio',
                args: {contextid: this.contextid, jsonformdata: JSON.stringify(formData)},
                done: this.handleFormSubmissionResponse.bind(this),
                fail: this.handleFormSubmissionFailure.bind(this, formData)
            }]);
        };

        /**
         * This triggers a form submission, so that any mform elements can do final tricks before the form submission is processed.
         *
         * @method submitForm
         * @param {Event} e Form submission event.
         * @private
         */
        AddGradeViewPage.prototype.submitForm = function(e) {
            e.preventDefault();

            this.modal.getRoot().find('form').submit();
        };

        return {
            init: function(contextid) {
                return new AddGradeViewPage(contextid);
            }
        };
    }
);
