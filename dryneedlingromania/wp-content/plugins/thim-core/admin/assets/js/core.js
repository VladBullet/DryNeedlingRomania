(function ($, settings) {
    window.Thim_Core = (function () {
        return {
            check_active: check_active
        };

        function check_active() {
            if (settings.active === 'yes') {
                return true;
            }

            $(window).trigger('thim_core_trigger_open_modal', 'tc-modal-activate-theme');

            return false;
        }
    })();
})(jQuery, window.thim_core_settings);
