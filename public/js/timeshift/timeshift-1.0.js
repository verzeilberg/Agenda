(function($) {
    $.fn.timeshift = function(options) {

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
            startTime: '00:00:00'
        };


        /*
         * Filter options. Compare them with the default ones and only use the defailt settings
         */
        var filteredOptions = {};
        $.each(options, function(index, value) {
            if (index in defaults === true) {
                filteredOptions[index] = value;
            }
        });
        /*
         * Create settings array
         */
        var settings = $.extend({}, defaults, filteredOptions);



        /*
         * Create a timesheet index is 24 clock value is 12 clock
         */
        var timeSheet = {
            0: 12,1: 1,2: 2,3: 3,4: 4,5: 5,6: 6,7: 7,8: 8,9: 9,10: 10,
            11: 11,12: 12,13: 1,14: 2,15: 3,16: 4,17: 5,18: 6,19: 7,20: 8,
            21: 9,22: 10,23: 11
        };

        /*
             * Set hidden input field with selected values (hours, minutes and seconds);
             */
        function setInputField(
            hour,
            minutes,
            seconds,
            element,
            minScrollHeightHour,
            maxScrollHeightHour,
            minScrollHeightMinute,
            maxScrollHeightMinute,
            minScrollHeightSecond,
            maxScrollHeightSecond
        )
        {
            if (
                (hour >= minScrollHeightHour && hour <= maxScrollHeightHour) ||
                (minutes >= minScrollHeightMinute && minutes <= maxScrollHeightMinute) ||
                (seconds >= minScrollHeightSecond && seconds <= maxScrollHeightSecond)

            ) {
                var valueHour = $('li#hour' + hour).data('hour');
                var valueMinute = $('li#minute' + minutes).data('minute');
                var valueSecond = $('li#second' + seconds).data('second');
                element.val(valueHour + ':' + valueMinute + ':' + valueSecond);
            }
        }

        return this.each(function (index) {
            var element = $(this);
            /*
             * Set variables for further use
             */
            var scrollHeightHour = 0;
            var minScrollHeightHour = 0;
            var maxScrollHeightHour = 23 * settings.height;
            var scrollHeightMinute = 105;
            var minScrollHeightMinute = 0;
            var maxScrollHeightMinute = 59 * settings.height;
            var scrollHeightSecond = 0;
            var minScrollHeightSecond = 0;
            var maxScrollHeightSecond = 59 * settings.height;

            /**
             * Set start value;
             */
            element.val(settings.startTime);
        /*
         * Create main placeholder
         */
        var mainPlaceholder = $('<div class="main-timeshift-placeholder"></div>');
        /*
         * Create timeshift placeholder
         */
        var timeshiftPlaceholder = $('<div></div>');
        timeshiftPlaceholder
            .attr(settings.wrapperAttrs)
            .css('min-height', settings.height)
            .css('max-height', settings.height)
            .css('height', settings.height)
            .css('width', settings.width);

        /*
         * Create a legend and add css
         */
        var legend = $('' +
            '<div class="legend-placeholder">' +
            '<span class="legend-item">'+settings.hourLegend+'</span>' +
            '<span class="legend-item">'+settings.minuteLegend+'</span>' +
            '<span class="legend-item">'+settings.secondLegend+'</span>' +
            '</div>' +
            '');
        legend.css('width', settings.width);

        /*
         * Create hour element
         */
        var hourElement = $('<ul class="hoursElement" id="hours'+index+'"></ul>');
        hourElement.height(settings.height);
        for (var hour = 0; hour <= 23; hour++) {
            var hourValue = (settings.hourClock === 24 ? hour : timeSheet[hour]);
            if (settings.hourClock === 24 && (hourValue >= 0 && hourValue <= 9)) {
                hourValue = '0'+ hourValue;
            }
            var hourId = hour * settings.height;
            var elementH = $('<li class="timeItem" data-hour="'+hourValue+'" id="hour'+hourId+'">' + hourValue + '</li>');
            elementH.height(settings.height);
            elementH.css("line-height", settings.height + 'px');
            hourElement.append(elementH);
        }

        /*
         * Create minute element
         */
        var minuteElement = $('<ul id="minutes"></ul>');
        minuteElement.height(settings.height)
        for (var minute = 0; minute <= 59; minute++) {
            var minuteId = minute * settings.height;
            if (settings.hourClock === 24 && (minute >= 0 && minute <= 9)) {
                minute = '0'+ minute;
            }
            var elementM = $('<li class="timeItem" data-minute="'+minute+'" id="minute'+minuteId+'">' + minute + '</li>');
            elementM.height(settings.height);
            elementM.css("line-height", settings.height + 'px');
            minuteElement.append(elementM);
        }

        /*
         * Create second element
         */
        var secondElement = $('<ul id="seconds"></ul>');
        secondElement.height(settings.height)
        for (var second = 0; second <= 59; second++) {
            var secondId = second * settings.height;
            if (settings.hourClock === 24 && (second >= 0 && second <= 9)) {
                second = '0'+ second;
            }
            var elementS = $('<li class="timeItem" data-second="'+second+'" id="second'+secondId+'">' + second + '</li>');
            elementS.height(settings.height);
            elementS.css("line-height", settings.height + 'px');
            secondElement.append(elementS);
        }

        hourElement.bind('mousewheel', function(event) {
            var scrollPosition = $(this).scrollTop();
            var inputId = $(this).siblings('input').attr('id');
            if (event.originalEvent.wheelDelta >= 0) {
                if (scrollPosition <= scrollHeightHour) {
                    event.preventDefault();
                    $(this).scrollTop(scrollHeightHour - settings.height);
                    scrollHeightHour = $(this).scrollTop();
                    setInputField(
                        scrollHeightHour,
                        scrollHeightMinute,
                        scrollHeightSecond,
                        element,
                        minScrollHeightHour,
                        maxScrollHeightHour,
                        minScrollHeightMinute,
                        maxScrollHeightMinute,
                        minScrollHeightSecond,
                        maxScrollHeightSecond
                    );
                }
            } else if (event.originalEvent.wheelDelta <= maxScrollHeightHour) {
                if (scrollPosition <= scrollHeightHour) {
                    event.preventDefault();
                    $(this).scrollTop(scrollHeightHour + settings.height);
                    scrollHeightHour = $(this).scrollTop();
                    scrollHeightHour = (scrollHeightHour > maxScrollHeightHour? maxScrollHeightHour: scrollHeightHour);
                    setInputField(
                        scrollHeightHour,
                        scrollHeightMinute,
                        scrollHeightSecond,
                        element,
                        minScrollHeightHour,
                        maxScrollHeightHour,
                        minScrollHeightMinute,
                        maxScrollHeightMinute,
                        minScrollHeightSecond,
                        maxScrollHeightSecond
                    );
                }
            }
        });

        minuteElement.bind('mousewheel', function(event) {
            var scrollPosition = $(this).scrollTop();
            var inputId = $(this).siblings('input').attr('id');
            if (event.originalEvent.wheelDelta >= 0) {
                if (scrollPosition <= scrollHeightMinute) {
                    event.preventDefault();
                    $(this).scrollTop(scrollHeightMinute - settings.height);
                    scrollHeightMinute = $(this).scrollTop();
                    setInputField(
                        scrollHeightHour,
                        scrollHeightMinute,
                        scrollHeightSecond,
                        element,
                        minScrollHeightHour,
                        maxScrollHeightHour,
                        minScrollHeightMinute,
                        maxScrollHeightMinute,
                        minScrollHeightSecond,
                        maxScrollHeightSecond
                    );
                }
            } else if (event.originalEvent.wheelDelta <= maxScrollHeightMinute) {
                if (scrollPosition <= scrollHeightMinute) {
                    event.preventDefault();
                    $(this).scrollTop(scrollHeightMinute + settings.height);
                    scrollHeightMinute = $(this).scrollTop();
                    scrollHeightMinute = (scrollHeightMinute > maxScrollHeightMinute? maxScrollHeightMinute: scrollHeightMinute);
                    setInputField(
                        scrollHeightHour,
                        scrollHeightMinute,
                        scrollHeightSecond,
                        element,
                        minScrollHeightHour,
                        maxScrollHeightHour,
                        minScrollHeightMinute,
                        maxScrollHeightMinute,
                        minScrollHeightSecond,
                        maxScrollHeightSecond
                    );
                }
            }
        });

        secondElement.bind('mousewheel', function(event) {
            var scrollPosition = $(this).scrollTop();
            var inputId = $(this).siblings('input').attr('id');
            if (event.originalEvent.wheelDelta >= 0) {
                if (scrollPosition <= scrollHeightSecond) {
                    event.preventDefault();
                    $(this).scrollTop(scrollHeightSecond - settings.height);
                    scrollHeightSecond = $(this).scrollTop();
                    setInputField(
                        scrollHeightHour,
                        scrollHeightMinute,
                        scrollHeightSecond,
                        element,
                        minScrollHeightHour,
                        maxScrollHeightHour,
                        minScrollHeightMinute,
                        maxScrollHeightMinute,
                        minScrollHeightSecond,
                        maxScrollHeightSecond
                    );
                }
            } else if (event.originalEvent.wheelDelta <= maxScrollHeightSecond) {
                if (scrollPosition <= scrollHeightSecond) {
                    event.preventDefault();
                    $(this).scrollTop(scrollHeightSecond + settings.height);
                    scrollHeightSecond = $(this).scrollTop();
                    scrollHeightSecond = (scrollHeightSecond > maxScrollHeightSecond? maxScrollHeightSecond: scrollHeightSecond);
                    setInputField(
                        scrollHeightHour,
                        scrollHeightMinute,
                        scrollHeightSecond,
                        element,
                        minScrollHeightHour,
                        maxScrollHeightHour,
                        minScrollHeightMinute,
                        maxScrollHeightMinute,
                        minScrollHeightSecond,
                        maxScrollHeightSecond
                    );
                }
            }
        });
            /*
             * Wrap main plaeholder atound timeshoft placeholder
             */
            $(this).wrap(mainPlaceholder).wrap(timeshiftPlaceholder);
            /**
             * Add legend
             */
            if(settings.legend) {
                $(this).parent().parent().prepend(legend);
            }
            /*
             * Append hour- and minute element to timeshift wrapper
             */
            $(this).parent().append(hourElement);
            $(this).parent().append(minuteElement);
            $(this).parent().append(secondElement);

        });
    };
}(jQuery));