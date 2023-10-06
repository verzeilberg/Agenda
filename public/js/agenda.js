/**
 * Get the right date time format based on given format
 * @param format
 * @param dateString
 * @returns {string}
 */
function getTimeFormat(format, dateString) {
    if (typeof (format) === 'undefined' || typeof (dateString) === 'undefined') {
        throw Error("Format or date is missing");

    }
    var d = new Date(dateString);
    var hour = null;
    var minutes = null;
    var seconds = null;
    var formatParts = format.split(":");
    $.each(formatParts, function (index, value) {
        switch (value) {
            case 'h':
                hour = d.getHours();
                break;
            case 'hh':
                hour = (d.getHours() < 10 ? '0' + d.getHours() : d.getHours());
                break;
            case 'm':
                minutes = d.getMinutes();
                break;
            case 'mm':
                minutes = (d.getMinutes() < 10 ? '0' + d.getMinutes() : d.getMinutes());
                break;
            case 's':
                seconds = d.getSeconds();
                break;
            case 'ss':
                seconds = (d.getSeconds() < 10 ? '0' + d.getSeconds() : d.getSeconds());
                break;
            default:
                throw Error("Wrong format try h:m:s");
        }
    });


    var formattedDate = hour + ':' + minutes + ':' + seconds;
    return formattedDate;
}

function shortenTitle(title) {
    if (title.length > 30) {
        title = title.substring(0, 30) + '&hellip;';
    }
    return title;
}

function setAgendaLayout() {
    var buttonText = $('input[name=layout]').val();
    $('button#agendaLayout').html(buttonText);
}


$('.dropdown-menu > a').on("click", function () {
    var agendaLayout = $(this).data('layout');
    $('button#agendaLayout').html(agendaLayout);
    //window.location.href = link;
});

setAgendaLayout();

$(document).ready(function () {


    /**
     * Submit agenda item
     */
    $("button#submitAgendaItem").on("click", function (event) {
        event.preventDefault();
        var form = $(this).parent('div').parent('form');
        var data = form.serializeArray();
        //Validate form
        var valide = validateForm(data, form);
        if (valide) {
            $.ajax({
                type: 'POST',
                data: {
                    data: data
                },
                url: "/agenda-ajax/addAgendaItem",
                url: "/agenda-ajax/addAgendaItem",
                async: true,
                success: function (data) {
                    if (data['success'] === true) {
                        $('#addingAgendaItem').modal('hide');
                    } else {
                        alert(data['errorMessage']);
                    }
                }
            });
        }

    });

    /**
     * Function to validate form
     */
    function validateForm(data, form) {
        var result = true;
        $.each(data, function (index, object) {
            if (typeof ($('[name="' + object.name + '"]').attr('required')) !== 'undefined') {
                result = (object.value == '' ? false : true);
                if (!result) {
                    $('[name="' + object.name + '"]').css('border', '2px solid #ffc107');
                }
            }
        });
        return result;
    }

    /**
     * Init timeshift
     */
    $("#startTime, #endTime").timeshift({
        hourClock: 24
    });

    /**
     * Init dateshift
     */
    $("input[name=startDate], input[name=endDate]").dateshift({
        preappelement: '<i class="far fa-calendar-alt"></i>',
        preapp: 'app',
        nextButtonText: '<i class="far fa-caret-square-right"></i>',
        previousButtonText: '<i class="far fa-caret-square-left"></i>',
        dateFormat: 'dd-mm-yyyy'
    });

    /**
     * jQuery function to go to link when selected
     */
    $("span.dateLink").on("click", function () {
        var link = $(this).data('link');
        var date = $(this).data('date');
        //If element has class selected go to that day
        if ($(this).hasClass('selected')) {
            window.location.href = link;
        }
        $('span.dateLink').removeClass('selected');
        $(this).addClass('selected');
        getDateData(link);
        $('input[name=startDate]').val(date);
        $('input[name=endDate]').val(date);
    });
    /**
     * jQuery function to redirect on click weekLink
     */
    $("span.weekLink").on("click", function () {
        var link = $(this).data('link');
        window.location.href = link;
    });

    /**
     * Get date data for clicked day
     * @param date
     */
    function getDateData(date) {
        $.ajax({
            type: 'POST',
            data: {
                date: date
            },
            url: "/agenda-ajax/getDateData",
            async: true,
            success: function (data) {
                if (data.success === true) {
                    var day = data.dateData.day;
                    var dayName = data.dateData.dayName;
                    var month = data.dateData.month;
                    var year = data.dateData.year;


                    $('#date').html(day);
                    $('#day').html(dayName);
                    $('#month').html(month + '/' + year);
                    $('table#agendaItemsForDay tbody').empty();
                    $.each(data.agendaItems, function (index, item) {
                        var startTime = getTimeFormat('hh:mm:ss', item.startTime.date);
                        var endTime = getTimeFormat('hh:mm:ss', item.endTime.date);
                        var row = $('<tr>');
                        row.append($('<td>').html(shortenTitle(item.title)));
                        row.append($('<td class="text-right">').html(startTime + ' - ' + endTime));
                        $('table#agendaItemsForDay tbody').append(row);
                    });


                } else {
                    alert(data.errorMessage);
                }
            }
        });
    }

});

$(document).ready(function () {
    if ($('input[name="wholeDay"]').is(":checked")) {
        $('#startEndTime').hide();
    } else {
        $('#startEndTime').show();
    }

    $('input[name="wholeDay"]').change(function () {
        if ($(this).is(":checked")) {
            $('#startEndTime').hide();
        } else {
            $('#startEndTime').show();
        }
    });
});