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
            previousButtonText: 'Vorige'
        };


        /*
         * Filter options. Compare them with the default ones and only use the defailt settings
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

        var dayLabels = [
            "Zon",
            "Maa",
            "Din",
            "Woe",
            "Don",
            "Vrij",
            "Zat"
        ];

        function isValidDate(dateString)
        {
            var regexs = [
                /^(y{4}m{2}d{2})\b/gm,
                /^(y{4}-m{2}-d{2})\b/gm
            ];

            $.each(regexs, function( index, regex ) {

                console.log(regex.exec(dateString));

                //console.log(regex.exec(dateString));
            });



            return true;
            // Parse the date parts to integers
            var parts = dateString.split("/");
            var day = parseInt(parts[1], 10);
            var month = parseInt(parts[0], 10);
            var year = parseInt(parts[2], 10);

            // Check the ranges of month and year
            if(year < 1000 || year > 3000 || month == 0 || month > 12)
                return false;

            var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

            // Adjust for leap years
            if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
                monthLength[1] = 29;

            // Check the range of the day
            return day > 0 && day <= monthLength[month - 1];
        }

        isValidDate('yyyymmdd');

        var today = new Date();
        var currentMonth = today.getMonth() + 1;
        var currentYear = today.getFullYear();

        function daysInMonth(iMonth, iYear) {
            return 32 - new Date(iYear, iMonth, 32).getDate();
        }



        function showCalendar(month, year, calendarBody) {

            var firstDay = (new Date(year, month)).getDay();

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
                    if (i === 0 && j < firstDay) {
                        var cell = $('<td>');
                        cell.append('&nbsp;');
                        row.append(cell);
                    }
                    else if (date > daysInMonth(month, year)) {
                        break;
                    }
                    else {
                        var cell = $('<td class="selectDate" date="'+date+'-'+month+'-'+year+'">');
                        if (date === today.getDate() && year === today.getFullYear() && month === today.getMonth()) {
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

        function next(currentYear, currentMonth, calendarTableBody) {
            currentMonth = parseInt(currentMonth);
            currentYear = parseInt(currentYear);
            currentYear = (currentMonth === 12) ? currentYear + 1 : currentYear;
            currentMonth = (currentMonth === 12) ? 1 : currentMonth + 1;
            $('span#previous').attr('year', currentYear).attr('month', currentMonth);
            $('span#next').attr('year', currentYear).attr('month', currentMonth);
            showCalendar(currentMonth, currentYear, calendarTableBody);
        }

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

        var rowHeader = $('<div class="row bg-dark mb-2">');
        rowHeader.append($prevButton);
        rowHeader.append(monthYearHeader);
        rowHeader.append($nexButton);
        calendarElement.append(rowHeader);
        calendarElement.append(calendarTable);
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