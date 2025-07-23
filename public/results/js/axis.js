if (!Production_Mode) "use strict"; //remove on production so "const" works on iOS Safari

var V_Chart_Axis;
var loading;
var V_Axis__Num = 0;


jQuery(function () 
{
	
loading = parent.$("#loading");
$(document).ajaxStart(function () {
	loading.show();
});
$(document).ajaxStop(function () {
	loading.hide();
});
	

$(".coolfieldset").collapsible();

$(".help_colors, .help_lines, .help_formula").tooltip({html:true, animated:'fade', placement:'left'});



color_field('.cpA'); //first axis
color_remove('.color_remove.cpAx'); //first axis


//Button__Chart__Update
$('#Button__Chart__Update').on('click', function() 
{	
	V_Chart_Axis.destroy();

	$('#cont_graph').highcharts({});
	V_Chart_Axis = $('#cont_graph').highcharts();

	//Axis
	const axis_options = {
		id: $('#axis_id').val(),
		title: {
			text: $('#axis_name').val(),
			style: {
				color: $('#axis_color').val()
			}
		},
		labels: {
			style: {
				color: $('#axis_color').val()
			}
		},
		min: ($('#axis_min').val() != '' ? $('#axis_min').val() : null),
		max: ($('#axis_max').val() != '' ? $('#axis_max').val() : null),
		gridLineWidth: parseInt($('#axis_grid_sel').val()),
		opposite: ($('#axis_pos_sel').val() == 'true' ? true : false)
	};

	if ($('#axis_color').val() != '') {
		axis_options.gridLineColor = $('#axis_color').val();
	}

	V_Chart_Axis.yAxis[0].update(axis_options);
	//V_Chart_Axis.addAxis(axis_options);
	//console.log(axis_options);
	

	//Data
	const data_name = $("input[name='data_name[]']");
	const data_val  = $("input[name='data_val[]']");
	const data_num = data_name.length;

	for (let i = 0; i < data_num; ++i) {
		V_Chart_Axis.addSeries({
			name: $(data_name[i]).val(),
			data: [parseInt($(data_val[i]).val())]
		});
	}
});


//Load selected graph data
$('#load_saved').on('click', function() 
{
	const id = $("#saved_select").val();
	
	if (!id) {
		return;
	}
	
	//put loaded name in input field
	$("input[name='save_name']").val( $("#saved_select option:selected").text() );
	
	V_Axis__Num = 0;
	
	//Axis fields
	$('#axis_id').val(axis_saved_data[id].axis.id);
	$('#axis_name').val(axis_saved_data[id].axis.name);

	$('.color_remove.cpAx').next().val('').css('background', '');

	if (axis_saved_data[id].axis.color != '') {
		$('#axis_color').colorpicker('setValue', axis_saved_data[id].axis.color);
	}

	$('#axis_min').val(axis_saved_data[id].axis.min);
	$('#axis_max').val(axis_saved_data[id].axis.max);
	$('#axis_pos_sel').val(axis_saved_data[id].axis.pos);
	$('#axis_grid_sel').val(axis_saved_data[id].axis.grid);
});


//Save selected graph data
$('#save_selected').on('click', function() 
{
	//Axis
	const axis = {
		id		: $('#axis_id').val(),
		name	: $('#axis_name').val(),
		color	: $('#axis_color').val(),
		min		: $('#axis_min').val(),
		max		: $('#axis_max').val(),
		pos		: $('#axis_pos_sel').val(),
		grid	: $('#axis_grid_sel').val()
	};
	
	const axis_data = {axis: axis};
	//console.log(axis_data, JSON.stringify(axis_data));

	const save_names = [];
	$("#saved_select option:not(:selected)").each(function(i, el) {
		save_names.push($(this).text());
	});


	//validation
	jQuery.validator.addMethod("notExist", function(value, element, param) {
		return this.optional(element) || ($.inArray(value, save_names) == -1);
	}, LANG.RESULTS.TEMPLATE_NAME_EXISTS); //Name exists

		
	jQuery.validator.addMethod("noName", function(value, element, param) {
		return this.optional(element) || ($('#axis_name').val != '');
	}, LANG.RESULTS.NO_AXIS_LABEL); //No Axis Label


	//validate
	$("form #save_form").validate({
		rules: {
			save_name: { required: true, notExist: '', noName: '' }
		}
	});
	
	//valid
	if ($('#save_name').valid() && $('#axis_name').valid()) {
		const title = $("input[name='save_name']").val();
		//if it is the selected and have the same name get this id else get id=0 for new
		const id = ($("#saved_select option:selected").text() == title ? $("#saved_select").val() : 0);
		const post_data = {
			group_id		: V_Group_id,
			athlete_id		: V_Athlete_id,
			id				: id,
			title			: title,
			template_type	: 'axis',
			data			: JSON.stringify(axis_data)
		};
		$.post('ajax.template_save.php', post_data, function(data, result) {
			if (data != 'ERROR') {
				//update axis_saved_data json
				$("#saved_select_container").html(data);

				//update parent
				parent.Axis__After_Action__Update((id != 0 ? 'update' : 'new'), axis_data, id, title);
			}
		});
	}
});


//Delete selected graph data
$('#delete_saved').off('click').confirmation({
	href: 'javascript:void(0)',
	title: LANG.ARE_YOU_SURE,
	placement: 'top',
	btnOkLabel: LANG.YES, btnOkClass: 'btn btn-sm btn-success mr10',
	btnCancelLabel: LANG.NO, btnCancelClass: 'btn btn-sm btn-danger',
	onConfirm: function(e, button) {
		const id = $("#saved_select").val();
		if (!id) {
			return;
		}

		const post_data = {
			group_id	: V_Group_id,
			athlete_id	: V_Athlete_id,
			id			: id,
			template_type: 'axis'
		};
		$.post('results/ajax.template_delete.php', post_data, function(data, result) {
			if (data.indexOf('.....') != -1) {
				alert(data);
			}
			else if (data != 'ERROR') {
				$("#saved_select_container").html(data);
				//update parent
				parent.Axis__After_Action__Update('delete', '', id, '');
			}
		});
	}
});



$('#cont_graph').highcharts({});
V_Chart_Axis = $('#cont_graph').highcharts();

});
