//import React from 'react';
module.exports = function (elms) {
    "use strict";
    var monthLength, mm, yyyy, $day, $month, $year, date;

    $day = $(elms[0]);
    $month = $(elms[1]);
    $year = $(elms[2]);
    date = null;
    monthLength = 31;
    yyyy = new Date().getFullYear();
    mm = 0; //January is 0!
    $year.data('name', 'year');
    $month.data('name', 'month');

    $day[0].oninput = function () {
        var that = this;
        if (that.value.length > 2) {
            that.value = that.value.slice(0, 2);
        }
    };

    //append years (-80)
 /*   for (var i = 0, val; i < 80; i++) {
        val = yyyy - i;
        $year.append($('<option/>', {
            text: val,
            val: val
        }));
    }*/

    [$month, $year].forEach(function (e) {
        e.one('change', function () {
            $(this).children().eq(0).remove();
        });
        e.on('change', function () {
            var that = $(this);
            var name = that.data('name');
            if (name === 'year') {
                yyyy = that.val();
            } else if (name === 'month') {
                mm = that.val();
            }
            monthLength = new Date(Number(yyyy), Number(mm) + 1, 0).getDate();
        });
    });
    this.isDay = function (el) {
        return +$(el).val() <= monthLength && 0 < +$(el).val() && +$(el).val().length > 0;
    };
    return this;
};


