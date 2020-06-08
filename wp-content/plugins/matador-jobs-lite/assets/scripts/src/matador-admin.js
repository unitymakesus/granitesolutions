/**
 * Matador Jobs Pro
 *
 * @author Jeremy Scott, 2017
 *
 * @since   3.0.0
 */
(function ($) {
    'use strict';

    $(function () {

        /* Setting Page -> Tab Menu */
        $('.matador-nav-tabs a').on("click", function (e) {
            window.location.hash = $(e.target).attr("href").substr(1);
            $('.matador-settings-tab').hide();
            $('.nav-tab-active').removeClass('nav-tab-active');
            $($(this).attr('href')).show();
            $(this).addClass('nav-tab-active');
            return false;
        });

        /* Display Settings Tabs Previous State on Form Submit */
        if (window.location.hash.length > 0) {
            $('.matador-settings-tab').hide();
            $('.nav-tab-active').removeClass('nav-tab-active');
            $(window.location.hash).show();
            $('a[href="' + window.location.hash + '"]').addClass('nav-tab-active');
        }

        /* Show/Hide Password Fields */
        $('.show-password').on("click", function (event) {
            event.preventDefault();
            $(this).toggleClass( 'on', 'addOrRemove' );
            var field = $(this).parent('.matador-field').find('input');

            if (field.prop('type') === 'password') {
                field.attr('type', 'text');
            } else {
                field.prop('type', 'password');
            }
        });

        /* Bullhorn Authorize/Sync */
        $('#matador_action').siblings('button').on("click", function (event) {
            event.preventDefault();
            $('#matador_action').attr('value', $(this).attr('id'));
            $(this).closest('form').submit();
        });

        /* Show/Hide Page Fields */
        var applications_on = '.matador-field-applications_accept';
		var apply_field = '.matador-field-applications_apply_method';
		var confirmation_field = '.matador-field-applications_confirmation_method';

        if ( '0' === $( applications_on ).find('input[type=radio]:checked').val() ) {
			$('#matador-settings-section-applications_general').find('.matador-field-group').each(function(index){
				if ( ! ( $( this ).hasClass('matador-field-applications_accept')
					|| $(this).hasClass('matador-field-applications_apply_page') ) ) {
					$(this).hide();
				}
				if ( $(this).hasClass('matador-field-applications_apply_method') ) {
					$(this).find('select').val('custom');
				}
			});
		}
		$( applications_on ).find('input').on('change', function(){
        	if ( '0' === $( applications_on ).find('input[type=radio]:checked').val() ) {
				$('#matador-settings-section-applications_general').find('.matador-field-group').each(function(index){
					if ( ! ( $( this ).hasClass('matador-field-applications_accept')
						|| $(this).hasClass('matador-field-applications_apply_page') ) ) {
						$(this).hide();
						if ( $(this).hasClass('matador-field-applications_apply_method') ) {
							$(this).find('select').val('custom');
						}
					} else {
						$(this).show();
					}
				});
			} else {
				$('#matador-settings-section-applications_general').find('.matador-field-group').each(function(index) {
					$(this).show();
				});
				if ( 'custom' !== $( confirmation_field ).find('select').val() ) {
					$('.matador-field-applications_confirmation_page').hide();
				} else {
					$('.matador-field-applications_confirmation_page').show();
				}
			}
		});

		if ( $( apply_field ).length && 'custom' !== $( apply_field ).find('select').val() ) {
			$('.matador-field-applications_apply_page').hide();
        }
		$( apply_field ).find('select').on( 'change', function(){
			if ( 'custom' !== $( apply_field ).find('select').val() ) {
				$('.matador-field-applications_apply_page').hide();
			} else {
				$('.matador-field-applications_apply_page').show();
			}
		});

		if ( $( confirmation_field ).length && 'custom' !== $( confirmation_field ).find('select').val() ) {
			$('.matador-field-applications_confirmation_page').hide();
		}
		$( confirmation_field ).find('select').on( 'change', function(){
			if ( 'custom' !== $( confirmation_field ).find('select').val() ) {
				$('.matador-field-applications_confirmation_page').hide();
			} else {
				$('.matador-field-applications_confirmation_page').show();
            }
        });

    });

})(jQuery);