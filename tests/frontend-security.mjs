import { chromium } from 'playwright';
import { fileURLToPath } from 'node:url';
import fs from 'node:fs';
import path from 'node:path';
import assert from 'node:assert/strict';

// Run npm ci, then PLAYWRIGHT_CHROMIUM_EXECUTABLE=/path/to/chrome npm run test:frontend.
// Without an executable override, install Chromium with npx playwright install chromium.
const root = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '..');
const source = path.join(root, 'public');
const deps = path.join(root, 'node_modules');
const browser = await chromium.launch({executablePath: process.env.PLAYWRIGHT_CHROMIUM_EXECUTABLE || undefined, headless: true});
const page = await browser.newPage({acceptDownloads: true});
const errors = [];
page.on('pageerror', error => errors.push(error.message));
await page.route('**/*', route => route.abort());
const script = async name => page.addScriptTag({path: path.join(deps, name)});
const app = async name => page.addScriptTag({path: path.join(source, name)});
try {
  await page.setContent('<html><body><button id="editor">Edit</button><div id="chart"></div><input id="t_date_from" value="2026-10-01 00:00"><input id="t_date_to" value="2026-10-31 23:59"></body></html>');
  await script('jquery/dist/jquery.min.js');
  await script('bootstrap/dist/js/bootstrap.min.js');
  await script('dompurify/dist/purify.min.js');
  await app('js/bootstrap_security.js');
  await script('bs-confirmation/bootstrap-confirmation.js');
  await script('moment/min/moment.min.js');
  await app('js/lang_de.js');
  await page.evaluate(() => { LANG.LANG_CURRENT = 'en'; window.Production_Mode = true; window.V_RESULTS_PAGE = 'RESULTS'; window.V_DASHBOARD = []; window.V_ADMIN = true; window.V_CATEGORIES_FORMS_OPTIONS = ''; window.V_FORMS_TEMPLATES_OPTIONS = ''; window.V_RESULTS_TEMPLATES_OPTIONS = ''; window.V_IS_ATHLETE = false; window.V_GROUP_TRAINER = false; window.V_GROUP_ADMIN = true; window.V_IS_ADMIN = true; window.V_LOCATION_ADMIN = true; window.V_GROUP_ADMIN_ = true; });
  const dash = fs.readFileSync(path.join(source, 'index/js/dashboard.js'), 'utf8');
  await page.addScriptTag({content: dash.slice(dash.indexOf('function get_dash_content('), dash.indexOf('function initDashboard('))});
  const popover = await page.evaluate(() => {
    $('#editor').popover({html: true, animation: false, template: get_popover_template(true), content: get_dash_content([1, 'Test', 'calendar', 'month__1', 1, '#aaa'])}).popover('show');
    const result = {input: $('#dash_name').val(), selects: $('.popover select').length, save: $('#dashboard_save').length, style: $('#dash_name').attr('style')};
    $('#editor').popover('destroy');
    return result;
  });
  assert.equal(popover.input, 'Test'); assert.ok(popover.selects > 0); assert.equal(popover.save, 1); assert.ok(popover.style);
  await page.evaluate(() => {
    $('#editor').popover({html:true,animation:false,title:'<img src=x onerror="window.pwned=1">',content:'<form id="form_popup"><label>Pflichtfeld</label><input type="checkbox" data-regmon-sync-next-value="1"><input type="hidden" value="0"><select><option value="1">One</option></select><textarea name="text">A</textarea><button type="button">Save</button><script>window.pwned=1</script><a href="javascript:window.pwned=1">bad</a><img src=x onerror="window.pwned=1"></form>'}).popover('show');
  });
  await page.locator('.popover input[type=checkbox]').check();
  assert.equal(await page.locator('.popover input[type=hidden]').inputValue(), '1');
  assert.equal(await page.locator('.popover form select option').textContent(), 'One');
  assert.equal(await page.locator('.popover [onerror], .popover script, .popover a[href^="javascript:"]').count(), 0);
  assert.equal(await page.evaluate(() => window.pwned), undefined);
  await page.evaluate(() => { $('#editor').popover('destroy'); $('#editor').confirmation({title:'Delete?',animation:false,onConfirm:() => window.confirmed=true}).confirmation('show'); });
  await page.locator('[data-apply=confirmation]').click();
  assert.equal(await page.evaluate(() => window.confirmed), true);
  await script('bootstrap-multiselect/dist/js/bootstrap-multiselect.js');
  await script('eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js');
  await page.evaluate(() => {
    $('body').append('<select id="multi" multiple><option value="a">A</option><option value="b">B</option></select><input id="date">');
    $('#multi').multiselect({includeSelectAllOption:true,enableFiltering:true}).multiselect('select','b').multiselect('rebuild');
    $('#date').datetimepicker({format:'DD.MM.YYYY HH:mm'}).data('DateTimePicker').date(moment('2026-10-27T12:30'));
  });
  assert.deepEqual(await page.locator('#multi').evaluate(e => Array.from(e.selectedOptions, o=>o.value)), ['b']);
  assert.equal(await page.locator('#date').inputValue(), '27.10.2026 12:30');
  await script('highcharts/highcharts.js');
  await script('highcharts/modules/exporting.js');
  await script('highcharts/modules/offline-exporting.js');
  await script('highcharts/modules/no-data-to-display.js');
  await script('jspdf/dist/jspdf.umd.min.js');
  await script('svg2pdf.js/dist/svg2pdf.umd.min.js');
  await app('results/js/diagram_functions.js');
  await app('js/plugins/export/export-csv.js');
  const chart = await page.evaluate(() => {
    $('#chart').highcharts({chart:{animation:false},yAxis:[{title:{text:'A'}},{title:{text:'B'},opposite:true}],series:[{name:'Wert A',data:[[Date.UTC(2026,9,20),12.5],[Date.UTC(2026,9,21),15]]},{name:'Wert B',type:'scatter',yAxis:1,data:[[Date.UTC(2026,9,20),4]]},{name:'Outside',data:[[Date.UTC(2026,8,1),99]]}]});
    window.chart = $('#chart').highcharts();
    return {version:Highcharts.version,dateFrom:$('#t_date_from').val(),moment:moment($('#t_date_from').val()+':00','YYYY-MM-DD HH:mm:ss').format(),points:chart.series[0].points.map(p=>p.y),axes:chart.yAxis.length,rows:chart.getDataRows(),svg:chart.getSVG().includes('<svg'),csv:chart.getCSV(),table:chart.getTable()};
  });
  assert.equal(chart.version,'11.4.9'); assert.deepEqual(chart.points,[12.5,15]); assert.equal(chart.axes,2); assert.equal(chart.rows[0][0],'DateTime'); assert.equal(chart.rows.length,4); assert.match(chart.rows[1][0], /20\.10\.2026/); assert.equal(chart.svg,true); assert.match(chart.csv,/12\.5/);
  await page.evaluate(() => chart.series[0].update({name:'<img src=x onerror="window.pwned=1">'}));
  await page.evaluate(() => chart.viewData());
  assert.equal(await page.locator('#highcharts-data-table img').count(),0);
  await app('js/plugins/export/xlsx.core.min.js');
  await app('js/plugins/export/Blob.js');
  await app('js/plugins/export/FileSaver.js');
  await app('js/plugins/export/Export2Excel.js');
  const xlsx = page.waitForEvent('download');
  await page.evaluate(() => chart.downloadXLS());
  assert.match((await xlsx).suggestedFilename(),/\.xlsx$/);
  const download = page.waitForEvent('download');
  await page.evaluate(() => chart.downloadCSV());
  assert.match((await download).suggestedFilename(),/\.csv$/);
  const svg = page.waitForEvent('download');
  await page.evaluate(() => chart.exportChartLocal({type:'image/svg+xml'}));
  assert.match((await svg).suggestedFilename(),/\.svg$/);
  const png = page.waitForEvent('download');
  await page.evaluate(() => chart.exportChartLocal({type:'image/png'}));
  assert.match((await png).suggestedFilename(),/\.png$/);
  const pdf = page.waitForEvent('download', {timeout:30000});
  await page.evaluate(() => chart.exportChartLocal({type:'application/pdf',error:(_o,e) => {window.pdfError=String(e);}}));
  assert.match((await pdf).suggestedFilename(),/\.pdf$/);
  assert.deepEqual(errors, []);
  console.log(JSON.stringify({status:'passed',popover,chart:{version:chart.version,axes:chart.axes,rows:chart.rows.length},checks:['dashboard editor','form controls and checkbox sync','XSS rejection','confirmation','multiselect','datetimepicker','datetime multi-axis scatter chart','custom period-filtered CSV/table','escaped export HTML','CSV/XLSX/SVG/PNG/PDF downloads without CDN access']}, null, 2));
} finally { await browser.close(); }
