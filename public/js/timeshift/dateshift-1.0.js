(function ($) {
    $.fn.dateshift = function (options) {
        var defaults = {
            elementId: 'dateshift',
            elementName: 'dateshift',
            height: 35,
            width: 200,
            preapp: 'app',
            preappelement: '@',
            nextButtonText: 'Volgend',
            previousButtonText: 'Vorige',
            dateFormat: 'yyyy-mm-dd'
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

        /*
         * Check if is a input field. When false create one
         */
        if (!this.is('input')) {
            var replaceInput = $('<input id="'+settings.elementId+'" type="text" name="'+settings.elementName+'"/>');
        }

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
        $.each(formats, function( index, format ) {
            if(format.exec(settings.dateFormat)!=null) {
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
            if(datePart > 9) {
                return datePart;
            } else {
                return '0' + datePart;
            }
        }

        function getWeekNumber(d) {
            // Copy date so don't modify original
            d = new Date(Date.UTC(d.getFullYear(), d.getMonth(), d.getDate()));
            // Set to nearest Thursday: current date + 4 - current day number
            // Make Sunday's day number 7
            d.setUTCDate(d.getUTCDate() + 4 - (d.getUTCDay()||7));
            // Get first day of year
            var yearStart = new Date(Date.UTC(d.getUTCFullYear(),0,1));
            // Calculate full weeks to nearest Thursday
            var weekNo = Math.ceil(( ( (d - yearStart) / 86400000) + 1)/7);
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
        function getDateByFormat(day, month, year)
        {
            switch(setFormat) {
                case 'yyyymmdd':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return year + month + day;
                case 'yyyy-mm-dd':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return year + '-' + month  + '-' + day;
                case 'yyyy/mm/dd':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return year + '/' + month  + '/' + day;
                case 'yy/m/d':
                    year = year.slice(-2);
                    return year + '/' + month  + '/' + day;
                case 'dd-mm-yyyy':
                    month = checkDateLenth(month);
                    day = checkDateLenth(day);
                    return day + '-' + month  + '-' + year;
                default:
                    return year + month + day;
            }
        }

        var today = new Date();
        var currentMonth = today.getMonth() + 1;
        var currentYear = today.getFullYear();

        /**
         * Calculate days in the goven month and year
         * @param iMonth
         * @param iYear
         * @returns {number}
         */
        function daysInMonth(month, year) {
            return 32 - new Date(year, month, 32).getDate();
        }


        /**
         * Get the Dutch number day. Because monday is 0
         * @param numberDay
         * @returns {number}
         */
        function getDutchNumberDay(numberDay)
        {
            switch(numberDay) {
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
         * Create the calendar
         * @param month
         * @param year
         * @param calendarBody
         * @returns {*}
         */
        function showCalendar(month, year, calendarBody) {

            //Create date object of the first day of the month
            var startDate = new Date(year + '-' + month + '-1');
            //Get the number of the specific day: 0:Sunday, 1:Monday, 2 Tuesday etc.
            var startNumberDay = startDate.getDay();
            //Get the first weeknumber
            var firstWeeknumber = getWeekNumber(startDate);
            var lastDayOfTheMonth = new Date(year, month + 1, 0);
            console.log('lastDayOfTheMonth: ', lastDayOfTheMonth);

            var lastWeekNumber = getWeekNumber(lastDayOfTheMonth);
            startNumberDay = getDutchNumberDay(startNumberDay);

            console.log('firstWeeknumber: ', firstWeeknumber);
            console.log('lastWeekNumber: ', lastWeekNumber);

            // clearing all previous cells
            $(calendarBody).empty();
            $('h3#monthAndYear').html(months[month] + ' ' + year);

            // creating all cells
           var date = 1;
            for (var i = 0; i < 6; i++) {
                // creates a table row
                var row = $('<tr>');
                //creating individual cells, filing them up with data.
                for (var j = 0; j < 7; j++) {
                    if(i === 0 && j < startNumberDay) {

                        var cell = $('<td>');
                        cell.append('&nbsp;');
                        row.append(cell);
                    }
                    else if (date > daysInMonth(month, year)) {
                        var cell = $('<td>');
                        cell.append('&nbsp;');
                        row.append(cell);
                    }
                    else {
                        var cell = $('<td class="selectDate" date="'+getDateByFormat(date, month, year)+'">');
                        if (date === today.getDate() && year === today.getFullYear() && month === (today.getMonth()+1)) {
                            cell.addClass("bg-warning");
                        } // color today's date
                        cell.append(date);

                        var clickableCell = $(cell).bind("click", function(event){
                            calendarBody.parent('table').parent('div').siblings('div.input-group').children('input').val($(this).attr('date'));
                            calendarBody.parent('table').parent('div').hide();
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
            $('span#previous').attr('year', currentYear).attr('month', currentMonth);
            $('span#next').attr('year', currentYear).attr('month', currentMonth);
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
            $('span#previous').attr('year', currentYear).attr('month', currentMonth);
            $('span#next').attr('year', currentYear).attr('month', currentMonth);
            showCalendar(currentMonth, currentYear, calendarTableBody);
        }

        /*
         * Create a calendar placeholder element
         */

        var elementHeight = this.height();
        var calendarElement = $('<div class="container calendar-placeholder">');
        calendarElement.css('top', elementHeight+14);


        var calendarTable = $('<table class="table table-bordered table-responsive-sm" id="calendar">');
        var calendarTableThead = $('<thead>');
        var row = $('<tr>');

        $.each(dayLabels, function(index, label){
            row.append($('<th>').html(label));
        });
        calendarTableThead.append(row);
        calendarTable.append(calendarTableThead);


        var calendarTableBody = $('<tbody id="calendar-body">');
        calendarTableBody = showCalendar(currentMonth, currentYear, calendarTableBody)
        calendarTable.append(calendarTableBody);

        /*
         * Create month Year header
         */
        var monthYearHeader = $('<h3 class="col text-center" id="monthAndYear">'+months[currentMonth]+ ' ' + currentYear +'</h3>');
        /*
         * Create previous button
         */
        var previousButton = $('<div class="col-md-auto text-left"><span class="btn btn-dark" year="'+currentYear+'" month="'+currentMonth+'" id="previous">'+settings.previousButtonText+'</span></div>');
        var $prevButton = $(previousButton).bind("click", function(event){
            previous($(this).children('span').attr("year"), $(this).children('span').attr("month"), calendarTableBody);
        });
        /*
         * Create next button
         */
        var nextButton = $(
            '<div class="col-md-auto text-right">' +
            '<span class="btn btn-dark" year="'+currentYear+'" month="'+currentMonth+'" id="next">' +
            settings.nextButtonText +
            '</span>' +
            '</div>'
        );
        var $nexButton = $(nextButton).bind("click", function(event){
            next($(this).children('span').attr("year"), $(this).children('span').attr("month"), calendarTableBody);
        });
        /**
         * Create today button
         * @type {*|jQuery|HTMLElement}
         */
        var todayButton = $(
            '<div class="col text-center">' +
            '<span class="btn btn-dark" year="'+currentYear+'" month="'+currentMonth+'" id="next">' +
            'today' +
            '</span>' +
            '</div>'
        );
        var $tdButton = $(todayButton).bind("click", function(event){
            showCalendar(currentMonth, currentYear, calendarTableBody)
        });

        var rowHeader = $('<div class="row bg-dark mb-2">');
        rowHeader.append($prevButton);
        rowHeader.append(monthYearHeader);
        rowHeader.append($nexButton);
        var rowFooter = $('<div class="row bg-dark mt-2">');
        rowFooter.append($tdButton);
        calendarElement.append(rowHeader);
        calendarElement.append(calendarTable);
        calendarElement.append(rowFooter);
        calendarElement.hide();
        /*
         * Wrap the element with a input-group
         */
        var dateShiftPlaceholder = $('<div class="dateshift-placeholder">' +
            '<div class="input-group">');
        dateShiftPlaceholder.css('position', 'relative')
        this.wrap(dateShiftPlaceholder);

        this.parent('div').before(calendarElement);

        /*
         * Check if the input group has to prepend or append
         */
        var preappElement = 'app';
        if (settings.preapp === 'pre') {
            preappElement = 'prep';
        }
        var prependObject = '<div class="input-group-'+preappElement+'end">\n' +
            '          <div class="input-group-text">'+settings.preappelement+'</div>\n' +
            '        </div>';

        /*
         * Pre- or append the input group element
         */
        if (settings.preapp === 'pre') {
            this.before(prependObject);
        } else {
            this.after(prependObject);
        }


        $(this).parent('div').parent('div').mouseover(function() {
            $(this).children('div.calendar-placeholder').show();
        });

        $(this).children('div.calendar-placeholder').hover(function() {
            $(this).children('div.calendar-placeholder').show();
        });

        $(this).parent('div').parent('div').mouseout(function() {
            $(this).children('div.calendar-placeholder').hide();
        });


        return this;


    };
}(jQuery));