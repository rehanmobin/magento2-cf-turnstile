
define(
    [
        'ko',
        'jquery',
        'uiComponent'
    ],
    function (
        ko,
        $,
        Component
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Mage4_Turnstile/turnstile',
            },
            scriptTagAdded: false,

            initialize: function () {
                this._super();
                window.globalOnTurnstileOnLoadCallback = function () {
                    this.initTurnstile();
                }.bind(this);
                //this.addCloudFlareTurnstileScriptTag();
            },

            addCloudFlareTurnstileScriptTag: function () {
                var element, scriptTag;

                if (!this.scriptTagAdded) {
                    element = document.createElement('script');
                    scriptTag = document.getElementsByTagName('script')[0];
                    element.async = true;
                    element.src = 'https://challenges.cloudflare.com/turnstile/v0/api.js' +
                            '?render=explicit&onload=globalOnTurnstileOnLoadCallback';

                    scriptTag.parentNode.insertBefore(element, scriptTag);
                    this.scriptTagAdded = true;
                }
            },

            initTurnstile: function () {
                let wrapper = $('#wrapper-' + this.getTurnstileId());
                let parentForm = wrapper.parents('form');
                const result = window.turnstile.render('#wrapper-' + this.getTurnstileId(), {
                    sitekey: this.settings.site_key,
                    theme: this.settings.theme,
                    action: this.settings.action,
                    callback: function(token) {
                        this.validateInteractiveTrunstile(parentForm);
                    }.bind(this),
                    'error-callback': function () {
                        this.validateInteractiveTrunstile(parentForm);
                    }.bind(this),
                    'before-interactive-callback': function () {

                        this.validateInteractiveTrunstile(parentForm);
                    }.bind(this),
                });
                if (typeof result === 'undefined') {
                    $('#wrapper-' + this.getTurnstileId()).html($.mage.__('Unable to secure the form'));
                }
            },

            validateInteractiveTrunstile: function (parentForm) {
                parentForm.submit(function (event) {
                    console.info(parentForm.find('input[name="cf-turnstile-response"]').val().length);
                    if (parentForm.find('input[name="cf-turnstile-response"]').val().length === 0) {
                        alert('Unable to verify you are human.');
                        event.preventDefault(event);
                        event.stopImmediatePropagation();
                    }
                }.bind(this));
            },

            getTurnstileId: function () {
                return this.turnstileId;
            }
        });
    }
);
