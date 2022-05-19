<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Datatables Helper
 *
 * @package    CodeIgniter
 * @subpackage helpers
 * @category   helper
 * @version    1.2.3
 * @author     ZettaSys <info@zettasys.com.ar>
 *
 */
if (!function_exists('buildJS'))
{

	function buildJS($tableData)
	{
		$CI = & get_instance();

		$columns = 'columns: [';
		$columnDefs = 'columnDefs: [';
		$columnCount = 0;
		$filters = '';
		if (isset($tableData['columns']))
		{
			foreach ($tableData['columns'] as $Property)
			{
				$className = isset($Property['class']) ? ', "className": "' . $Property['class'] . '"' : '';
				if (!isset($Property['render']))
				{
					$render = '';
				}
				elseif ($Property['render'] === 'date')
				{
					$render = ', "render": function (data, type, full, meta) { if(type === "display"){if(data){var mDate = moment(data);data = (mDate && mDate.isValid()) ? mDate.format("DD/MM/YYYY") : "";}}return data;}';
				}
				elseif ($Property['render'] === 'datetime')
				{
					$render = ', "render": function (data, type, full, meta) { if(type === "display"){if(data){var mDate = moment(data);data = (mDate && mDate.isValid()) ? mDate.format("DD/MM/YYYY HH:mm") : "";}}return data;}';
				}
				elseif ($Property['render'] === 'money')
				{
					$render = ', "render": $.fn.dataTable.render.number(\'.\', \',\', 2, \'$\')';
				}
				elseif ($Property['render'] === 'numeric')
				{
					$render = ', "render": $.fn.dataTable.render.number(\'.\', \',\', 2)';
				}
				else
				{
					$render = ', "render": ' . $Property['render'];
				}
				$visible = isset($Property['visible']) ? ', "visible": ' . $Property['visible'] : '';
				$searchable = isset($Property['searchable']) ? ', "searchable": ' . $Property['searchable'] : '';
				$sortable = isset($Property['sortable']) ? ', "sortable": ' . $Property['sortable'] : '';
				$orderData = isset($Property['orderData']) ? ', "orderData": ' . json_encode($Property['orderData']) : '';
				$columns .= '{"data": "' . $Property['data'] . '"},';
				$columnDefs .= '{'
						. '"targets": ' . $columnCount . ', '
						. '"width": "' . $Property['width'] . '%"'
						. $className . $render . $visible . $searchable . $sortable . $orderData
						. '}, ';
				$filters .= isset($Property['filter_name']) ? '$("#' . $Property['filter_name'] . '").change(function() {var key = $(this).find(\'option:selected\').val(); var val = this.options[this.selectedIndex].text; ' . $tableData['table_id'] . '.column(' . $columnCount . ').search(key !== "Todos" ? val : "").draw(); });' . "\n" : '';
				$columnCount++;
			}
		}
		$columns .= '],';
		$columnDefs .= '],';

		$tableJS = '<script type="text/javascript">';
		$tableJS .= '$(document).ready(function() {' . "\n";
		$tableJS .= '$.fn.dataTable.moment("DD/MM/YYYY");';
		$tableJS .= (isset($tableData['reuse_var']) && $tableData['reuse_var']) ? '' : 'var ';
		$tableJS .= $tableData['table_id'] . ' = $("#' . $tableData['table_id'] . '").DataTable({';
		if (isset($tableData['paging']))
		{
			$tableJS .= 'paging: ' . $tableData['paging'] . ', ';
		}
		if (isset($tableData['scrollY']))
		{
			$tableJS .= 'scrollY: ' . $tableData['scrollY'] . ', ';
		}
		if (isset($tableData['scrollCollapse']))
		{
			$tableJS .= 'scrollCollapse: \'' . $tableData['scrollCollapse'] . 'px\', ';
		}
		if (isset($tableData['order']))
		{
			$tableJS .= 'order: ' . json_encode($tableData['order']) . ', ';
		}
		if (isset($tableData['fnHeaderCallback']))
		{
			$tableJS .= 'fnHeaderCallback: ' . str_replace('"', '', json_encode($tableData['fnHeaderCallback'], JSON_UNESCAPED_SLASHES)) . ',';
		}
		if (isset($tableData['fnRowCallback']))
		{
			$tableJS .= 'fnRowCallback: ' . str_replace('"', '', json_encode($tableData['fnRowCallback'], JSON_UNESCAPED_SLASHES)) . ',';
		}
		if (isset($tableData['initComplete']))
		{
			$tableJS .= 'initComplete: ' . str_replace('"', '', json_encode($tableData['initComplete'], JSON_UNESCAPED_SLASHES)) . ',';
		}
		if (isset($tableData['fnDrawCallback']))
		{
			$tableJS .= 'fnDrawCallback: ' . str_replace('"', '', json_encode($tableData['fnDrawCallback'], JSON_UNESCAPED_SLASHES)) . ',';
		}
		if (isset($tableData['disableLengthChange']) && $tableData['disableLengthChange'])
		{
			$tableJS .= 'lengthChange: false,';
		}
		if (isset($tableData['disableSearching']) && $tableData['disableSearching'])
		{
			$tableJS .= 'searching: false,';
		}
		if (isset($tableData['disablePagination']) && $tableData['disablePagination'])
		{
			$tableJS .= 'bPaginate: false, ';
		}
		if (isset($tableData['dom']))
		{
			$tableJS .= 'dom: \'' . $tableData['dom'] . '\', ';
		}
		if (isset($tableData['buttons']))
		{
			$tableJS .= 'buttons: ' . json_encode($tableData['buttons']) . ', ';
		}
		if (isset($tableData['pageLength']))
		{
			$tableJS .= 'pageLength: ' . $tableData['pageLength'] . ', ';
		}
		else
		{
			$tableJS .= 'pageLength: 25, ';
		}
		$tableJS .= 'processing: true, '
				. 'stateSave: true, '
				. 'autoWidth: false, '
				. 'pagingType: "full_numbers", '
				. 'language: {"url": "vendor/datatables/i18n/Spanish.json"}, ';

		if (isset($tableData['extraData']))
		{
			$data = 'data: function (d) {' . $tableData['extraData'] . 'd.' . $CI->security->get_csrf_token_name() . '= "' . $CI->security->get_csrf_hash() . '";}';
		}
		else
		{
			$data = 'data: {' . $CI->security->get_csrf_token_name() . ':"' . $CI->security->get_csrf_hash() . '"}';
		}

		if (!isset($tableData['rows']))
		{
			if (!isset($tableData['server_side']) || $tableData['server_side'])
			{
				$tableJS .= 'serverSide: true, ';
				$tableJS .= 'ajax: {'
						. 'url: "' . $tableData['source_url'] . '", '
						. 'type: "POST", '
						. $data . '}, ';
			}
			else
			{
				$tableJS .= 'ajax: "' . $tableData['source_url'] . '", ';
			}
		}

		$tableJS .= $columns;
		$tableJS .= $columnDefs;
		$tableJS .= 'colReorder: true';
		$tableJS .= '});' . "\n";
		$tableJS .= $filters;
		if (isset($tableData['footer']) && $tableData['footer'])
		{
			$tableJS .= "
						$('#{$tableData['table_id']} tfoot th').each(function() {
							var title = $(this).text();
							if(title!=='')
								$(this).html('<input style=\"width: 100%;\" type=\"text\" placeholder=\"'+title+'\" />');
						});
						{$tableData['table_id']}.columns().every(function() {
							var that = this;
							$('input', {$tableData['table_id']}.table().footer().children[0].children[this[0][0]]).on('change', function() {
								if (that.search() !== this.value) {
									that.search(this.value).draw();
								}
							});
						});";
		}
		$tableJS .= '});';
		$tableJS .= '</script>';
		return $tableJS;
	}
}

if (!function_exists('buildHTML'))
{

	function buildHTML($tableData)
	{
		if (empty($tableData['columns_exclude']))
		{
			$tableData['columns_exclude'] = array();
		}
		$tableHTML = '<table id="' . $tableData['table_id'] . '" class="table table-hover table-condensed dt-responsive">'; // nowrap
		$tableHTML .= '<thead>';
		$tableHTML .= '<tr>';
		if (isset($tableData['columns']))
		{
			foreach ($tableData['columns'] as $Column)
			{
				$class = empty($Column['responsive_class']) ? '' : ' class="' . $Column['responsive_class'] . '"';
				$priority = empty($Column['priority']) ? '' : ' data-priority="' . $Column['priority'] . '"';
				$tableHTML .= "<th$class$priority>";
				$tableHTML .= $Column['label'];
				$tableHTML .= "</th>";
			}
		}
		$tableHTML .= '</tr>';
		$tableHTML .= '</thead>';
		if (isset($tableData['rows']))
		{
			$tableHTML .= '<tbody>';
			foreach ($tableData['rows'] as $Row)
			{
				$tableHTML .= '<tr>';
				foreach ($Row as $key => $Cell)
				{
					if (!in_array($key, $tableData['columns_exclude']))
					{
						$tableHTML .= '<td>' . $Cell . '</td>';
					}
				}
				if (!empty($tableData['rows_extra']))
				{
					foreach ($tableData['rows_extra'] as $Extra)
					{
						foreach ($Extra['replacement'] as $key => $val)
						{
							$sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));
							if (preg_match('/(\w+::\w+|\w+)\((.*)\)/i', $val, $matches) && is_callable($matches[1]))
							{
								$func = $matches[1];
								$args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
								foreach ($args as $args_key => $args_val)
								{
									$args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
									$args[$args_key] = isset($Row->{$args_val}) ? $Row->{$args_val} : $args_val;
								}
								$replace_string = call_user_func_array($func, $args);
							}
							elseif (isset($Row->{$sval}))
							{
								$replace_string = $Row->{$sval};
							}
							else
							{
								$replace_string = $sval;
							}
							$tableHTML .= '<td>' . str_ireplace('$' . ($key + 1), $replace_string, $Extra['content']) . '</td>';
						}
					}
				}
				$tableHTML .= '</tr>';
			}
			$tableHTML .= '</tbody>';
		}
		if (isset($tableData['footer']) && $tableData['footer'])
		{
			$tableHTML .= '<tfoot>';
			$tableHTML .= '<tr>';
			if (isset($tableData['columns']))
			{
				foreach ($tableData['columns'] as $Column)
				{
					$tableHTML .= "<th>";
					$tableHTML .= $Column['label'];
					$tableHTML .= "</th>";
				}
			}
			$tableHTML .= '</tr>';
			$tableHTML .= '</tfoot>';
		}
		$tableHTML .= '</table>';

		return $tableHTML;
	}
}