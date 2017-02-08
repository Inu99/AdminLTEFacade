<?php
namespace exface\AdminLteTemplate\Template\Elements;
class lteInputDate extends lteInput {
	
	protected function init(){
		parent::init();
		$this->set_element_type('datepicker');
	}
	
	function generate_html(){
		$output = '
						<label for="' . $this->get_id() . '">' . $this->get_widget()->get_caption() . '</label>
						<div class="form-group input-group date">
							<div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</div>
							<input class="form-control pull-right"
									type="text"
									name="' . $this->get_widget()->get_attribute_alias() . '"
									value="' . $this->escape_string($this->get_value_with_defaults()) . '" 
									id="' . $this->get_id() . '"/>
						</div>
					';
		return $this->build_html_wrapper($output);
	}
	
	function generate_js(){
		$output = '
				$(\'#' . $this->get_id() . '\').' . $this->get_element_type() . '({
					autoclose: true,
					format: {
						toDisplay: function (date, format, language) {
							//date is a date-object and is parsed to a string
							//date is returned as yyyy-MM-dd
							var yyyy = date.getFullYear();
							var MM = (date.getMonth() < 9 ? \'0\' : \'\') + (date.getMonth() + 1); //pad with \'0\' if needed, months are zero based
							var dd = (date.getDate() < 10 ? \'0\' : \'\') + date.getDate(); //pad with \'0\' if needed
							return yyyy + "-" + MM + "-" + dd;
						},
						toValue: function (date, format, language) {
							//date is the input-string which is parsed to a date-object
							//date can be passed as yyyy-MM-dd and yyyy-MM-dd HH:mm:ss.S
							var match = /(\d{4})-(\d{2})-(\d{2})/.exec(date);
							var yyyy = Number(match[1]);
							var MM = Number(match[2]) - 1;
							var dd = Number(match[3]);
							return new Date(Date.UTC(yyyy, MM, dd));
						}
					},
					todayHighlight: true
				});';
		
		if ($this->get_widget()->is_required()) {
			$output .= $this->build_js_required();
		}
		
		return $output;
	}
	
	public function generate_headers(){
		$headers = parent::generate_headers();
		$headers[] = '<script type="text/javascript" src="exface/vendor/bower-asset/bootstrap-datepicker/dist/js/bootstrap-datepicker.js"></script>';
		$headers[] = '<link rel="stylesheet" href="exface/vendor/bower-asset/bootstrap-datepicker/dist/css/bootstrap-datepicker3.css">';
		return $headers;
	}
}