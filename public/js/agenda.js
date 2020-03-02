/**
 * Get the right date time format based on given format
 * @param format
 * @param dateString
 * @returns {string}
 */
function getTimeFormat(format, dateString) {
    if(typeof(format) === 'undefined' || typeof(dateString) === 'undefined') {
        throw Error("Format or date is missing");

    }
    var d = new Date(dateString);
    var hour = null;
    var minutes = null;
    var seconds = null;
    var formatParts = format.split(":");
    $.each(formatParts, function( index, value ) {
        switch(value) {
            case 'h':
                hour = d.getHours();
                break;
            case 'hh':
                hour = (d.getHours() < 10? '0' + d.getHours():d.getHours());
                break;
            case 'm':
                minutes = d.getMinutes();
                break;
            case 'mm':
                minutes = (d.getMinutes() < 10? '0' + d.getMinutes():d.getMinutes());
                break;
            case 's':
                seconds = d.getSeconds();
                break;
            case 'ss':
                seconds = (d.getSeconds() < 10? '0' + d.getSeconds():d.getSeconds());
                break;
            default:
                throw Error("Wrong format try h:m:s");
        }
    });


    var formattedDate = hour + ':' + minutes + ':' + seconds;
    return formattedDate;
}

function shortenTitle(title)
{
    if(title.length > 30) {
        title = title.substring(0, 30) + '&hellip;';
    }
    return title;
}