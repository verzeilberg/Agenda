(function ($) {
    $.fn.dateshift = function (options) {
        /**
         * Default settings
         * @type {{
         *      elementId: string,
         *      showWeekNumbers: number,
         *      dateFormat: string,
         *      nextButtonText: string,
         *      width: number,
         *      preapp: string,
         *      preappelement: string,
         *      previousButtonText: string,
         *      elementName: string,
         *      height: number
         * }}
         */
        var defaults = {
            elementId: 'dateshift',
            elementName: 'dateshift',
            height: 35,
            width: 200,
            preapp: 'app',
            preappelement: '@',
            nextButtonText: 'Volgend',
            previousButtonText: 'Vorige',
            dateFormat: 'yyyy-mm-dd',
            showWeekNumbers: 0
        };


        /*
         * Filter options. Compare them with the default ones and only use the default settings
         */
        var filteredOptions = {};
        $.each(options, function (index, value) {
            if (index in defaults === true) {
                filteredOptions[index] = value;
            }
        });
        /*
         * Create settings array
         */
        var settings = $.extend({}, defaults, filteredOptions);

        /**
         * Create formats array
         * @type {{}}
         */
        var formats = {};
        formats['yyyymmdd'] = /^(y{4}m{2}d{2})\b/gm;
        formats['yyyy-mm-dd'] = /^(y{4}-m{2}-d{2})\b/gm;
        formats['yyyy/mm/dd'] = /^(y{4}\/m{2}\/d{2})\b/gm;
        formats['yy/m/d'] = /^(y{2}\/m{1}\/d{1})\b/gm;
        formats['dd-mm-yyyy'] = /^(d{2}-m{2}-y{4})\b/gm;

        /**
         * Set the right format type
         * @type {null}
         */
        var setFormat = null;
        $.each(formats, function (index, format) {
            if (format.exec(settings.dateFormat) != null) {
                setFormat = index;
            }
        });

        /**
         * Create months array
         */
        var months = {
            1: "Januari",
            2: "Februari",
            3: "Maart",
            4: "April",
            5: "Mei",
            6: "Juni",
            7: "Juli",
            8: "Augustus",
            9: "September",
            10: "Oktober",
            11: "November",
            12: "December"
        };

        /**
         * Create day labels
         * @type {*[]}
         */
        var dayLabels = [
            "M",
            "D",
            "W",
            "D",
            "V",
            "Z",
            "Z"
        ];

        /**
         * Check if date part is greater then 9 to add leading zero
         * @param datePart
         * @returns {string|*}
         */
        function checkDateLenth(datePart) {
            if (datePart > 9) {
                return datePart;
            } else {
                return '0' + datePart;
            }
        }


        /**
         * Get weeks number by date
         * @param d
         * @returns {[number, number]}
         */
        function getWeekNumber(d) {
            // Copy date so don't modify original
            d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            // Set to nearest Thursday: current date + 4 - current day number
            // Make Sunday's day number 7
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay() || 7));
            // Get first day of year
            var yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
            // Calculate full weeks to nearest Thursday
            var weekNo = Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            // Return array of year and week number
            return [d.getUTCFullYear(), weekNo];
        }

        /**
         * Return date string based on date format
         * @param day
         * @param month
         * @param year
         * @returns {string|*}
         */
        function getDateByFormat(day, month, year) {
            switch (setFormat) {
                case 'yyyymmdd':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return year + month + day;
                case 'yyyy-mm-dd':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return year + '-' + month + '-' + day;
                case 'yyyy/mm/dd':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return year + '/' + month + '/' + day;
                case 'yy/m/d':
                    year = year.slice(-2);
                    return year + '/' + month + '/' + day;
                case 'dd-mm-yyyy':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return day + '-' + month + '-' + year;
                default:
                    return year + month + day;
            }
        }

        var today = new Date();
        var currentMonth = today.getMonth() + 1;
        var currentYear = today.getFullYear();


        /**
         * Get total days in month
         * @param month
         * @param year
         * @returns {number}
         */
        function daysInMonth(month, year) {
            return new Date(year, month, 0).getDate();
        }

        /**
         * Get the Dutch number day. Because monday is 0
         * @param numberDay
         * @returns {number}
         */
        function getDutchNumberDay(numberDay) {
            switch (numberDay) {
                case 0:
                    return 6;
                case 1:
                    return 0;
                case 2:
                    return 1;
                case 3:
                    return 2;
                case 4:
                    return 3;
                case 5:
                    return 4;
                case 6:
                    return 5;
            }
        }


        /**
         * Get total weeks in a month
         * @param year
         * @param month
         * @returns {number}
         */
        function weekCount(year, month) {
            var lastOfMonth = new Date(year, month, 0);
            var totalDaysInMonth = lastOfMonth.getDate();
            var totalWeeks = 0;
            for (var dd = 1; dd <= totalDaysInMonth; dd++) {
                var dutchDayNumber = getDutchNumberDay(new Date(year + '-' + month + '-' + dd).getDay());
                if (dutchDayNumber === 6) {
                    totalWeeks++;
                }
                if (totalDaysInMonth === dd) {
                    if (dutchDayNumber < 6) {
                        totalWeeks++;
                    }
                }
            }
            return totalWeeks;
        }

        /**
         * Create the calendar
         * @param month
         * @param year
         * @param calendarBody
         * @returns {*}
         */
        function showCalendar(month, year, calendarBody) {
            //Get total weeks in the given month
            var totalWeeksInMonth = weekCount(year, month);
            //Create date object of the first day of the month
            var startDate = new Date(year + '-' + month + '-1');
            //Get the number of the specific day: 0:Sunday, 1:Monday, 2 Tuesday etc.
            var startNumberDay = startDate.getDay();
            startNumberDay = getDutchNumberDay(startNumberDay);
            var totalDaysInMonth = daysInMonth(month, year);
            $(calendarBody).parent('table').parent('div').parent('div').siblings('div.calendarHeader').children('h3.monthAndYear').html(months[month] + ' ' + year);
            // clearing all previous cells
            $(calendarBody).empty();

            // creating all cells
            var date = 1;
            var weekDay = 1;
            for (var i = 0; i < totalWeeksInMonth; i++) {
                // creates a table row
                var row = $('<tr>');
                if (settings.showWeekNumbers) {
                    var cell = $('<td>');

                    var wknr = getWeekNumber(new Date(year + '-' + month + '-' + weekDay));
                    cell.append(wknr[1]);
                    row.append(cell);
                }
                weekDay = weekDay + 7;
                weekDay = (weekDay > totalDaysInMonth ? totalDaysInMonth : weekDay);

                //creating individual cells, filing them up with data.
                for (var j = 0; j < 7; j++) {
                    if (i === 0 && j < startNumberDay) {

                        var cell = $('<td>');
                        cell.append('&nbsp;');
                        row.append(cell);
                    } else if (date > totalDaysInMonth) {
                        var cell = $('<td>');
                        cell.append('&nbsp;');
                        row.append(cell);
                    } else {
                        var cell = $('<td class="selectDate text-center" date="' + getDateByFormat(date, month, year) + '">');
                        if (date === today.getDate() && year === today.getFullYear() && month === (today.getMonth() + 1)) {
                            cell.addClass("bg-warning");
                        } // color today's date
                        cell.append(date);

                        var clickableCell = $(cell).bind("click", function (event) {
                            calendarBody.parent('table').parent('div').parent('div').parent('div').siblings('div.input-group').children('input').val($(this).attr('date'));
                            calendarBody.parent('table').parent('div').parent('div').parent('div').off('mouseenter mouseleave');
                            calendarBody.parent('table').parent('div').parent('div').parent('div').hide();
                        });

                        row.append(clickableCell);
                        date++;
                    }
                }

                calendarBody.append(row); // appending each row into calendar body.
            }
            return calendarBody;
        }

        /**
         * When clicking on next button set the right month and year in calender
         * @param currentYear
         * @param currentMonth
         * @param calendarTableBody
         */
        function next(currentYear, currentMonth, calendarTableBody) {
            currentMonth = parseInt(currentMonth);
            currentYear = parseInt(currentYear);
            currentYear = (currentMonth === 12) ? currentYear + 1 : currentYear;
            currentMonth = (currentMonth === 12) ? 1 : currentMonth + 1;
            $('span.previous').attr('year', currentYear).attr('month', currentMonth);
            $('span.next').attr('year', currentYear).attr('month', currentMonth);
            showCalendar(currentMonth, currentYear, calendarTableBody);
        }

        /**
         * When clicking on previous button set the right month and year in calender
         * @param currentYear
         * @param currentMonth
         * @param calendarTableBody
         */
        function previous(currentYear, currentMonth, calendarTableBody) {
            currentMonth = parseInt(currentMonth);
            currentYear = parseInt(currentYear);
            currentYear = (currentMonth === 1) ? currentYear - 1 : currentYear;
            currentMonth = (currentMonth === 1) ? 12 : currentMonth - 1;
            $('span.previous').attr('year', currentYear).attr('month', currentMonth);
            $('span.next').attr('year', currentYear).attr('month', currentMonth);
            showCalendar(currentMonth, currentYear, calendarTableBody);
        }

        return this.each(function (index, object) {

            /*
             * Check if is a input field. When false create one
             */
            if (!$(object).is('input')) {
                object = '<input id="' + settings.elementId + '" type="text" name="' + settings.elementName + '"/>';
            }

            $(object).attr('autocomplete', 'off');

            /**
             * Create a calendar placeholder element
             * @type {jQuery}
             */
            var elementHeight = $(object).height();
            var calendarElement = $('<div class="container calendar-placeholder">');
            calendarElement.css('top', elementHeight + 14);
            var calendarRow = $('<div class="row">');
            var calendarCol = $('<div class="col">');
            /**
             * Create tabele element
             * @type {*|jQuery|HTMLElement}
             */
            var calendarTable = $('<table class="table table-bordered table-responsive-sm table-sm calendar">');
            /**
             * Create table head element
             * @type {*|jQuery|HTMLElement}
             */
            var calendarTableThead = $('<thead>');
            /**
             * Add a row to table head
             * @type {*|jQuery|HTMLElement}
             */
            var row = $('<tr>');
            if (settings.showWeekNumbers) {
                row.append($('<th>').html('WK'));
            }
            $.each(dayLabels, function (index, label) {
                row.append($('<th>').html(label));
            });
            calendarTableThead.append(row);
            calendarTable.append(calendarTableThead);

            /**
             * Create calendar table body and add current calendar month
             * @type {*|jQuery|HTMLElement}
             */
            var calendarTableBody = $('<tbody>');
            calendarTableBody = showCalendar(currentMonth, currentYear, calendarTableBody)
            calendarTable.append(calendarTableBody);
            calendarCol.append(calendarTable);
            calendarRow.append(calendarCol)
            /**
             * Create month and year header
             * @type {*|jQuery|HTMLElement}
             */
            var monthYearHeader = $('<h3 class="col text-center monthAndYear">' + months[currentMonth] + ' ' + currentYear + '</h3>');
            /**
             * Create previous button
             * @type {*|jQuery|HTMLElement}
             */
            var previousButton = $('<div class="col-md-auto text-left">' +
                '<span class="btn btn-dark previous" year="' + currentYear + '" month="' + currentMonth + '">' +
                settings.previousButtonText +
                '</span>' +
                '</div>');

            /**
             * Create next button
             * @type {*|jQuery|HTMLElement}
             */
            var nextButton = $(
                '<div class="col-md-auto text-right">' +
                '<span class="btn btn-dark next" year="' + currentYear + '" month="' + currentMonth + '">' +
                settings.nextButtonText +
                '</span>' +
                '</div>'
            );
            /**
             * Bind next button to function
             * @type {jQuery}
             */
            var $nexButton = $(nextButton).bind("click", function (event) {
                var nextObject = $(object).parent('div').siblings('div.calendar-placeholder').children('div').children('div').children('span.next');
                var previousObject = $(object).parent('div').siblings('div.calendar-placeholder').children('div').children('div').children('span.previous');
                next(nextObject.attr("year"), nextObject.attr("month"), calendarTableBody, nextObject, previousObject);
            });

            /**
             * Bind previous button to function
             * @type {jQuery}
             */
            var $prevButton = $(previousButton).bind("click", function (event) {
                var nextObject = $(object).parent('div').siblings('div.calendar-placeholder').children('div').children('div').children('span.next');
                var previousObject = $(object).parent('div').siblings('div.calendar-placeholder').children('div').children('div').children('span.previous');

                previous(previousObject.attr("year"), previousObject.attr("month"), calendarTableBody, nextObject, previousObject);
            });

            /**
             * Create today button
             * When clikcing on button the calendar goes to the current year and month
             * @type {*|jQuery|HTMLElement}
             */
            var todayButton = $(
                '<div class="col text-center">' +
                '<span class="btn btn-dark" id="today">' +
                'today' +
                '</span>' +
                '</div>'
            );
            /**
             * Bind function to today button
             */
            var $tdButton = $(todayButton).bind("click", function (event) {
                showCalendar(currentMonth, currentYear, calendarTableBody);
            });

            /**
             * Add different elemets to calendar elemen
             * @type {*|jQuery|HTMLElement}
             */
            var rowHeader = $('<div class="row bg-dark mb-2 calendarHeader">');
            rowHeader.append($prevButton);
            rowHeader.append(monthYearHeader);
            rowHeader.append($nexButton);
            var rowFooter = $('<div class="row bg-dark mt-2 calendarFooter">');
            rowFooter.append($tdButton);
            calendarElement.append(rowHeader);
            calendarElement.append(calendarRow);
            calendarElement.append(rowFooter);
            calendarElement.hide();
            /*
             * Wrap the element with a input-group
             */
            var dateShiftPlaceholder = $('<div class="dateshift-placeholder">' +
                '<div class="input-group">');
            dateShiftPlaceholder.css('position', 'relative')
            $(object).wrap(dateShiftPlaceholder);

            $(object).parent('div').before(calendarElement);

            /*
             * Check if the input group has to prepend or append
             */
            var preappElement = 'app';
            if (settings.preapp === 'pre') {
                preappElement = 'prep';
            }
            var prependObject = '<div class="input-group-' + preappElement + 'end">\n' +
                '          <div class="input-group-text">' + settings.preappelement + '</div>\n' +
                '        </div>';

            /*
             * Pre- or append the input group element
             */
            if (settings.preapp === 'pre') {
                $(object).before(prependObject);
            } else {
                $(object).after(prependObject);
            }

            /**
             * Create hover function on input element to show/hide calendar
             */
            $(object).focus(function () {
                $(object).parent('div').siblings('div.calendar-placeholder').show();

                /**
                 * After a hover on input filed init a hover on calendar div
                 */
                $(object).parent('div').siblings('div.calendar-placeholder').hover(function () {
                    $(object).parent('div').siblings('div.calendar-placeholder').show();
                });
            });

            /**
             * On mouse out calendar hide calendar
             */
            $(object).parent('div').mouseout(function () {
                $(object).parent('div').siblings('div.calendar-placeholder').hide();

            });


        });
    };
}(jQuery));