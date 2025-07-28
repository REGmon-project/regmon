/**
 * A Highcharts plugin for exporting data from a rendered chart as CSV, XLS or HTML table
 *
 * Author:   Torstein Honsi
 * Licence:  MIT
 * Version:  1.4.2
 */
/*global Highcharts, window, document, Blob */

//MAD@@@@@@@@ have some changes here

(function (factory) {
    if (typeof module === 'object' && module.exports) {
        module.exports = factory;
    } else {
        factory(Highcharts);
    }
})(function (Highcharts) {

    'use strict';

    var each = Highcharts.each,
        pick = Highcharts.pick,
        seriesTypes = Highcharts.seriesTypes,
        downloadAttrSupported = document.createElement('a').download !== undefined;

    Highcharts.setOptions({
        lang: {
            downloadCSV: 'Download CSV',
            downloadXLS: 'Download XLS',
            viewData: 'View data table'
        }
    });


    /**
     * Get the data rows as a two dimensional array
     */
    Highcharts.Chart.prototype.getDataRows = function () {
        var options = (this.options.exporting || {}).csv || {},
            xAxis = this.xAxis[0],
            rows = {},
            rowArr = [],
            dataRows,
            names = [],
            i,
            x,
            xTitle = xAxis.options.title && xAxis.options.title.text,

            // Options
            dateFormat = options.dateFormat || '%Y-%m-%d %H:%M:%S',
            columnHeaderFormatter = options.columnHeaderFormatter || function (series, key, keyLength) {
                return series.name + (keyLength > 1 ? ' ('+ key + ')' : '');
            };

        // Loop the series and index values
        i = 0;
        each(this.series, function (series) {
            var keys = series.options.keys,
                pointArrayMap = keys || series.pointArrayMap || ['y'],
                valueCount = pointArrayMap.length,
                requireSorting = series.requireSorting,
                categoryMap = {},
                j;

			// Map the categories for value axes
            each(pointArrayMap, function (prop) {
                categoryMap[prop] = (series[prop + 'Axis'] && series[prop + 'Axis'].categories) || [];
            });

            if (series.options.includeInCSVExport !== false && series.visible !== false) { // #55
                j = 0;
                while (j < valueCount) {
                    names.push(columnHeaderFormatter(series, pointArrayMap[j], pointArrayMap.length));
                    j = j + 1;
                }

                each(series.points, function (point, pIdx) {
                    var key = requireSorting ? point.x : pIdx,
                        prop,
                        val;

                    j = 0;

                    if (!rows[key]) {
                        rows[key] = [];
                    }
                    rows[key].x = point.x;

                    // Pies, funnels, geo maps etc. use point name in X row
                    if (!series.xAxis || series.exportKey === 'name') {
                        rows[key].name = point.name;
                    }

                    while (j < valueCount) {
                        prop = pointArrayMap[j]; // y, z etc
                        val = point[prop];
                        rows[key][i + j] = pick(categoryMap[prop][val], val); // Pick a Y axis category if present
                        j = j + 1;
                    }

                });
                i = i + j;
            }
        });

        // Make a sortable array
        for (x in rows) {
            if (rows.hasOwnProperty(x)) {
                rowArr.push(rows[x]);
            }
        }
        // Sort it by X values
        rowArr.sort(function (a, b) {
            return a.x - b.x;
        });

        // Add header row
        if (!xTitle) {
            xTitle = xAxis.isDatetimeAxis ? 'DateTime' : 'Category';
        }
        dataRows = [[xTitle].concat(names)];

        // Add the category column
        each(rowArr, function (row) {

            var category = row.name;
            if (!category) {
                if (xAxis.isDatetimeAxis) {
                    if (row.x instanceof Date) {
                        row.x = row.x.getTime();
                    }
                    category = Highcharts.dateFormat(dateFormat, row.x);
                } else if (xAxis.categories) {
                    category = pick(xAxis.names[row.x], xAxis.categories[row.x], row.x)
                } else {
                    category = row.x;
                }
            }

            // Add the X/date/category
            row.unshift(category);
            dataRows.push(row);
        });
		
        //@@@@@MAD
        //return dataRows;

		var stt = moment($('#t_date_from').val()+':00', 'YYYY-MM-DD HH:mm:ss').format("YYYY MM DD HH mm ss").split(' ');
		var edt = moment($('#t_date_to').val()+':59', 'YYYY-MM-DD HH:mm:ss').format("YYYY MM DD HH mm ss").split(' ');
		var st = Date.UTC(stt[0],(stt[1]-1),stt[2], stt[3],stt[4],stt[5]);
		var ed = Date.UTC(edt[0],(edt[1]-1),edt[2], edt[3],edt[4],edt[5]);

        //filter rows by date
		var new_dataRows = [];
		var ii = 0;
        each(dataRows, function (row) {
            //export only the selected period
			if (ii == 0 || (st <= row.x && ed >= row.x)) {
				new_dataRows.push(row);
			}
			ii++;
        });
        
        return new_dataRows;
		//@@@@@MAD
    };

    /**
     * Get a CSV string
     */
    Highcharts.Chart.prototype.getCSV = function (useLocalDecimalPoint) {
        var csv = '',
            rows = this.getDataRows(),
            options = (this.options.exporting || {}).csv || {},
            //itemDelimiter = options.itemDelimiter || ',', // use ',' for direct import to Excel //@@@@@MAD
            itemDelimiter = options.itemDelimiter || ';', // use ';' for direct import to Excel  //@@@@@MAD
            lineDelimiter = options.lineDelimiter || '\n'; // '\n' isn't working with the js csv data extraction

        // Transform the rows to CSV
        each(rows, function (row, i) {
            var val = '',
                j = row.length,
                n = useLocalDecimalPoint ? (1.1).toLocaleString()[1] : '.';
            while (j--) {
                val = row[j];
                if (typeof val === "string") {
                    val = '"' + val + '"';
                }
                if (typeof val === 'number') {
                    if (n === ',') {
                        val = val.toString().replace(".", ",");
                    }
                }
                row[j] = val;
            }
            // Add the values
            csv += row.join(itemDelimiter);

            // Add the line delimiter
            if (i < rows.length - 1) {
                csv += lineDelimiter;
            }
        });
        return csv;
    };

    /**
     * Build a HTML table with the data
     */
    Highcharts.Chart.prototype.getTable = function (useLocalDecimalPoint) {
        var html = '<table>',
            rows = this.getDataRows();

        // Transform the rows to HTML
        each(rows, function (row, i) {
            var tag = i ? 'td' : 'th',
                val,
                j,
                n = useLocalDecimalPoint ? (1.1).toLocaleString()[1] : '.';

            html += '<tr>';
            for (j = 0; j < row.length; j = j + 1) {
                val = row[j];
                // Add the cell
                if (typeof val === 'number') {
                    val = val.toString();
                    if (n === ',') {
                        val = val.replace('.', n);
                    }
                    html += '<' + tag + ' class="number">' + val + '</' + tag + '>';

                } else {
                    html += '<' + tag + '>' + (val === undefined ? '' : val) + '</' + tag + '>';
                }
            }

            html += '</tr>';
        });
        html += '</table>';
        return html;
    };

    function getContent(chart, href, extension, content, MIME) {
        var a,
            blobObject,
            name,
            options = (chart.options.exporting || {}).csv || {},
            //url = options.url || 'http://www.highcharts.com/studies/csv-export/download.php';
            url = options.url || '';

        if (chart.options.exporting.filename) {
            name = chart.options.exporting.filename;
        } else if (chart.title) {
            name = chart.title.textStr.replace(/ /g, '-').toLowerCase();
        } else {
            name = 'chart';
        }

        // MS specific. Check this first because of bug with Edge (#76)
        if (window.Blob && window.navigator.msSaveOrOpenBlob) {
            // Falls to msSaveOrOpenBlob if download attribute is not supported
            blobObject = new Blob([content]);
            window.navigator.msSaveOrOpenBlob(blobObject, name + '.' + extension);

        // Download attribute supported
        } else if (downloadAttrSupported) {
            a = document.createElement('a');
            a.href = href;
            a.target = '_blank';
            a.download = name + '.' + extension;
            document.body.appendChild(a);
            a.trigger("click");
            a.remove();

        } 
		else console.log('Error! Fall back to server side handling');
		/*else {
            // Fall back to server side handling
            Highcharts.post(url, {
                data: content,
                type: MIME,
                extension: extension
            });
        }*/
    }

    /**
     * Call this on click of 'Download CSV' button
     */
    Highcharts.Chart.prototype.downloadCSV = function () {
        var csv = this.getCSV(true);
        getContent(
            this,
            'data:text/csv,\uFEFF' + csv.replace(/\n/g, '%0A'),
            'csv',
            csv,
            'text/csv'
        );
    };

    /**
     * Call this on click of 'Download XLS' button
     */
    /*Highcharts.Chart.prototype.downloadXLS = function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">' +
                '<head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>' +
                '<x:Name>Ark1</x:Name>' +
                '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->' +
                '<style>td{border:none;font-family: Calibri, sans-serif;} .number{mso-number-format:"0.00";}</style>' +
                '<meta name=ProgId content=Excel.Sheet>' +
                '<meta charset=UTF-8>' +
                '</head><body>' +
                this.getTable(true) +
                '</body></html>',
            base64 = function (s) { 
                return window.btoa(unescape(encodeURIComponent(s))); // #50
            };
        getContent(
            this,
            uri + base64(template),
            'xls',
            template,
            'application/vnd.ms-excel'
        );
    };
    */

    /**
     * Call this on click of 'Download XLSX' button  by @@@@@MAD 
     */
    Highcharts.Chart.prototype.downloadXLS = function () {
		this.viewData();
        let chart = this;
        let name = 'chart';
        if (chart.options.exporting.filename) {
            name = chart.options.exporting.filename;
        } else if (chart.title) {
            name = chart.title.textStr.replace(/ /g, '-').toLowerCase();
        }
		export_table_to_excel('highcharts-data-table', name + '.xlsx');
    };

    /**
     * View the data in a table below the chart
     */
    /*
    Highcharts.Chart.prototype.viewData = function () {
        if (!this.insertedTable) {
            var div = document.createElement('div');
            div.className = 'highcharts-data-table';
            // Insert after the chart container
            this.renderTo.parentNode.insertBefore(div, this.renderTo.nextSibling);
            div.innerHTML = this.getTable();
            this.insertedTable = true;
        }
    };
    */

    /**
     * View the data in a table below the chart  by @@@@@MAD 
     */
    Highcharts.Chart.prototype.viewData = function () {
        if (!this.insertedTable) {
			var table = document.getElementById('highcharts-data-table');
			if (table == null) {
				var div = document.createElement('div');
				div.id = 'highcharts-data-table';
				div.className = 'highcharts-data-table';
				div.style = 'display:none;';
				// Insert after the chart container
				this.renderTo.parentNode.insertBefore(div, this.renderTo.nextSibling);
				div.innerHTML = this.getTable();
				this.insertedTable = true;
			}
			else { //table exist
				table.innerHTML = this.getTable();
				this.insertedTable = true;
			}
        }
    };


    // Add "Download CSV" to the exporting menu. Use download attribute if supported, else
    // run a simple PHP script that returns a file. The source code for the PHP script can be viewed at
    // https://raw.github.com/highslide-software/highcharts.com/master/studies/csv-export/csv.php
    if (Highcharts.getOptions().exporting) {
        Highcharts.getOptions().exporting.buttons.contextButton.menuItems.push({
            textKey: 'downloadCSV',
            onclick: function () { this.downloadCSV(); }
        }, {
            textKey: 'downloadXLS',
            onclick: function () { this.downloadXLS(); }
        // }, {  //@@@@@MAD 
        //     textKey: 'viewData',
        //     onclick: function () { this.viewData(); }
        });
    }

    // Series specific
    if (seriesTypes.map) {
        seriesTypes.map.prototype.exportKey = 'name';
    }

});
