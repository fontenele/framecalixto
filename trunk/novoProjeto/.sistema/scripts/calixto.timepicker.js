/*
 * jTimepicker plugin 1.4.0
 *
 * http://www.radoslavdimov.com/jquery-plugins/jquery-plugin-jtimepicker/
 *
 * Copyright (c) 2009 Radoslav Dimov
 *
 * Dual licensed under the MIT and GPL licenses:
 * http://www.opensource.org/licenses/mit-license.php
 * http://www.gnu.org/licenses/gpl.html
 *
 * Depends:
 *      ui.core.js
 *      ui.slider.js
 */

(function($) {
    $.fn.extend({
		input:null,
        timepicker: function(options) {
            var defaults = {
                clockIcon: 'images/icon_clock_2.gif',
                orientation: 'horizontal',
                // set hours
                hourMode: 24,
                hourInterval: 1,
                hourDefaultValue: 0,
                hourSlider: 'hourSlider',
                hourLabel: 'hour',
                // set minutes
                minLength: 60,
                minInterval: 1,
                minDefaultValue: 0,
                minSlider: 'minSlider',
                minLabel: 'min',
                // set seconds
                secView: true,
                secLength: 60,
                secInterval: 1,
                secDefaultValue: 0,
                secSlider: 'secSlider',
                secLabel: 'sec'
            };

            var options = $.extend(defaults, options);

            return this.each(function(i,input) {
				var id = $(input).attr('id');
                var o = options;
                var html = '';
                var orientation = (o.orientation == 'horizontal') ? 'auto' : 'vertical';
                var sliderData = [
					{'label':o.hourLabel, 'slider':o.hourSlider},
					{'label':o.minLabel, 'slider':o.minSlider}
				];
                if (o.secView) {
                    sliderData.push({'label':o.secLabel, 'slider':o.secSlider});
                }
				var createSL = function() {
					var html = '<div id="'+id+'SliderWrap" class="timepicker" >';
					$.each(sliderData, function(i, item) {
						html += '   <div><label>' + item.label + ':</label> <p class="' + item.slider + '"></p></div>';
					});
					html += '</div>';
					return html;
				}
                $(input).after(createSL());
                $(input).after('<img id="imgClock_'+id+'" src="' + o.clockIcon + '" class="clock" />');
				$('#imgClock_'+id).click(function(event,a,i,d) {
					console.log(event);
					console.log(a);
					console.log(i);
					console.log(d);
					/*
					_findPos: function(obj) {
						while (obj && (obj.type == 'hidden' || obj.nodeType != 1)) {
							obj = obj.nextSibling;
						}
						var position = $(obj).offset();
						return position;
					}
					*/
					$('#'+id+'SliderWrap').toggle('slow');
				});
                $('#'+id+'SliderWrap').addClass(orientation);

                $(this).createSlider(id, o.hourSlider, o.hourMode, o.hourCombo, o.hourInterval, o.hourDefaultValue, o.orientation);
                $(this).createSlider(id, o.minSlider, o.minLength, o.minCombo, o.minInterval, o.minDefaultValue, o.orientation);
                if (o.secView) {
                    $(this).createSlider(id, o.secSlider, o.secLength, o.secCombo, o.secInterval, o.secDefaultValue, o.orientation);
                }

                $.each(sliderData, function(i, item) {
                    $('.' + item.combo).change(function() {
                        var val = $(this).val();
                        $('.' + item.slider).slider('option', 'value', val);
                    });
                });
            });
			$('.timepicker').hide();
        }
    });

    $.fn.createSlider = function(id, slider, maxValue, combo, stepValue, defValue, orientation) {
        $('#' + id +'SliderWrap .' + slider).slider({
            orientation: orientation,
            range: "min",
            min: 0,
            max: maxValue - stepValue,
            value: defValue,
            step: stepValue,
            animate: true ,
            slide: function(event, ui) {
				ar = $('#'+id).val().split(':');
				if(ui.value < 10) ui.value = '0'+ui.value;
				switch(slider){
					case('hourSlider'):
		                $('#'+id).val(ui.value+':'+ar[1]+':'+ar[2]);
					break;
					case('minSlider'):
		                $('#'+id).val(ar[0]+':'+ui.value+':'+ar[2]);
					break;
					case('secSlider'):
		                $('#'+id).val(ar[0]+':'+ar[1]+':'+ui.value);
					break;
				}
            }
        });
    }
})(jQuery);
