(function ($) {
    $.fn.timeshift = function (options) {


        var currentDate = new Date();
        var currentHours = currentDate.getHours();
        var currentMinutes = currentDate.getMinutes();
        var currentSeconds = currentDate.getSeconds();

        /**
         * Default options.
         */
        var defaults = {
            height: 35,
            width: 200,
            hourClock: 24,
            minuteSteps: 5,
            endTime: 23,
            wrapperAttrs: {
                class: "timeshift-placeholder"
            },
            legend: true,
            hourLegend: 'H',
            minuteLegend: 'M',
            secondLegend: 'S',
            nameHiddenInputElement: 'timeValue',
            idHiddenInputElement: 'timeValue',
            currentTime: true,
            startHour: '00',
            startMinutes: '00',
            startSeconds: '00',
        };


        /**
         * Filter options. Compare them with the default ones and only use the default settings
         */
        var filteredOptions = {};
        $.each(options, function (index, value) {
            if (index in defaults === true) {
                filteredOptions[index] = value;
            }
        });
        /**
         * Create settings array
         */
        var settings = $.extend({}, defaults, filteredOptions);

        /**
         * Create a timesheet index is 24 clock value is 12 clock
         */
        var timeSheet = {
            0: 12, 1: 1, 2: 2, 3: 3, 4: 4, 5: 5, 6: 6, 7: 7, 8: 8, 9: 9, 10: 10,
            11: 11, 12: 12, 13: 1, 14: 2, 15: 3, 16: 4, 17: 5, 18: 6, 19: 7, 20: 8,
            21: 9, 22: 10, 23: 11
        };

        /**
         * Set hidden input field with selected values (hours, minutes and seconds);
         */
        function setInputField(element, valueHour, valueMinute, valueSecond) {
            element.val(valueHour + ':' + valueMinute + ':' + valueSecond);
        }

        function setInputField2(
            hour,
            minutes,
            seconds,
            element,
            minScrollHeightHour,
            maxScrollHeightHour,
            minScrollHeightMinute,
            maxScrollHeightMinute,
            minScrollHeightSecond,
            maxScrollHeightSecond,
            index
        ) {
            if (
                (hour >= minScrollHeightHour && hour <= maxScrollHeightHour) ||
                (minutes >= minScrollHeightMinute && minutes <= maxScrollHeightMinute) ||
                (seconds >= minScrollHeightSecond && seconds <= maxScrollHeightSecond)

            ) {
                var valueHour = $('li#' + index + '_hour' + hour).data('hour');
                var valueMinute = $('li#' + index + '_minute' + minutes).data('minute');
                var valueSecond = $('li#' + index + '_second' + seconds).data('second');
                element.val(valueHour + ':' + valueMinute + ':' + valueSecond);
            }
        }

        /**
         * Loop trough each timeShift Element
         */
        return this.each(function (index, object) {
            var element = $(object);

            /**
             * Set variables for further use
             */
            var scrollHeightHour = 0;
            var minScrollHeightHour = 0;
            var maxScrollHeightHour = 23 * settings.height;
            var scrollHeightMinute = 0;
            var minScrollHeightMinute = 0;
            var maxScrollHeightMinute = 59 * settings.height;
            var scrollHeightSecond = 0;
            var minScrollHeightSecond = 0;
            var maxScrollHeightSecond = 59 * settings.height;

            /**
             * Set start value, check if value is set
             */
            if (element.val() === '') {
                if (settings.currentTime) {
                    element.val(currentHours + ':' + currentMinutes + ':' + currentSeconds);
                } else {
                    element.val(settings.startHour + ':' + settings.startMinutes + ':' + settings.startSeconds);
                }
            } else {
                const timeArray = element.val().split(":");
                settings.startHour = timeArray[0];
                settings.startMinutes = timeArray[1];
                settings.startSeconds = timeArray[2];
            }
            /**
             * Create main placeholder
             */
            var mainPlaceholder = $('<div class="main-timeshift-placeholder"></div>');

            /**
             * Create timeshift placeholder
             */
            var timeshiftPlaceholder = $('<div></div>')
                .attr(settings.wrapperAttrs)
                .css('min-height', settings.height)
                .css('max-height', settings.height)
                .css('height', settings.height)
                .css('width', settings.width);

            /**
             * Create a legend and add css
             */
            var legend = $('' +
                '<div class="legend-placeholder">' +
                '<span class="legend-item">' + settings.hourLegend + '</span>' +
                '<span class="legend-item">' + settings.minuteLegend + '</span>' +
                '<span class="legend-item">' + settings.secondLegend + '</span>' +
                '</div>' +
                '');
            legend.css('width', settings.width);

            /**
             * Create hour element
             */
            var hourElement = $('<input type="text" id="hour' + index + '" class="hoursElement" name="hour' + index + '" disabled="disabled" />');
            hourElement.val(settings.startHour);
            hourElement.height(settings.height);

            /**
             * Create minute element
             */
            var minuteElement = $('<input type="text" id="minutes' + index + '" class="minutesElement" name="minutes' + index + '" disabled="disabled" />');
            minuteElement.val(settings.startMinutes);
            minuteElement.height(settings.height);

            /**
             * Create second element
             */
            var secondElement = $('<input type="text" id="seconds' + index + '" class="secondsElement" name="seconds' + index + '" disabled="disabled" />');
            secondElement.val(settings.startSeconds);
            secondElement.height(settings.height)

            /**
             * Bind the mouse wheel event to the hour element
             */
            hourElement.bind('wheel', function (event) {
                var inputValue = parseInt(this.value);
                if (event.originalEvent.wheelDelta > 0) {
                    if (settings.hourClock === 24) {
                        if (inputValue === 23) {
                            inputValue = '00';
                        } else {
                            inputValue = inputValue + 1;
                            if (inputValue >= 0 && inputValue <= 9) {
                                inputValue = '0' + inputValue;
                            }
                        }
                    } else {
                        if (inputValue === 12) {
                            inputValue = 0;
                        } else {
                            inputValue = inputValue + 1;
                        }
                    }
                } else {
                    if (settings.hourClock === 24) {
                        if (inputValue === 0) {
                            inputValue = '23';
                        } else {
                            inputValue = inputValue - 1;
                            if (inputValue >= 0 && inputValue <= 9) {
                                inputValue = '0' + inputValue;
                            }
                        }
                    } else {
                        if (inputValue === 0) {
                            inputValue = 12;
                        } else {
                            inputValue = inputValue + 1;
                        }
                    }
                }
                this.value = inputValue;
                setInputField(element, inputValue, minuteElement.val(), secondElement.val());
                return false;
            });

            /**
             * Bind the mouse wheel event to the minute element
             */
            minuteElement.bind('mousewheel', function (event) {
                var inputValue = parseInt(this.value);
                if (event.originalEvent.wheelDelta > 0) {
                    if (inputValue === 59) {
                        inputValue = 0;
                    } else {
                        inputValue = inputValue + 1
                        if (inputValue >= 0 && inputValue <= 9) {
                            inputValue = '0' + inputValue;
                        }
                    }
                } else {
                    if (inputValue === 0) {
                        inputValue = 59;
                    } else {
                        inputValue = inputValue - 1
                        if (inputValue >= 0 && inputValue <= 9) {
                            inputValue = '0' + inputValue;
                        }
                    }
                }
                this.value = inputValue;
                setInputField(element, hourElement.val(), inputValue, secondElement.val());
                return false;
            });

            /**
             * Bind the mouse wheel event to the second element
             */
            secondElement.bind('mousewheel', function (event) {
                var inputValue = parseInt(this.value);
                if (event.originalEvent.wheelDelta > 0) {
                    if (inputValue === 59) {
                        inputValue = 0;
                    } else {
                        inputValue = inputValue + 1
                        if (inputValue >= 0 && inputValue <= 9) {
                            inputValue = '0' + inputValue;
                        }
                    }
                } else {
                    if (inputValue === 0) {
                        inputValue = 59;
                    } else {
                        inputValue = inputValue - 1
                        if (inputValue >= 0 && inputValue <= 9) {
                            inputValue = '0' + inputValue;
                        }
                    }
                }

                this.value = inputValue;

                setInputField(element, hourElement.val(), minuteElement.val(), inputValue);
                return false;
            });
            /**
             * Wrap main placeholder around timeshift placeholder
             */
            $(this).wrap(mainPlaceholder).wrap(timeshiftPlaceholder);

            /**
             * Add legend
             */
            if (settings.legend) {
                $(this).parent().parent().prepend(legend);
            }
            /**
             * Append hour-, minute- and second element to timeshift wrapper
             */
            $(this).parent().append(hourElement);
            $(this).parent().append(minuteElement);
            $(this).parent().append(secondElement);

        });
    };
}(jQuery));
