( function($, $C, $M, Handsontable) {
    'use strict';

    $( document ).ready(function() {
        var data = [
  ["", "Ford", "Volvo", "Toyota", "Honda"],
  ["2014", 10, 11, 12, 13],
  ["2015", 20, 11, 14, 13],
  ["2016", 30, 15, 12, 13]
];
        var container = document.getElementById('grade_table');

        var hot = new Handsontable(container, {
          data: data,
          minSpareRows: 1,
          rowHeaders: true,
          colHeaders: true,
          contextMenu: true
        });
    });

}(jQuery, COMMON, Mousetrap, Handsontable) );