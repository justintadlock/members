<?php

namespace Members\Fields;

class Checkbox {

	public function label() {
		return $this->label;
	}

	public function name() {
		return $this->name;
	}

	public function value() {
		return $this->value;
	}

	public function render() {

		$control = sprintf(
			'<input type="checkbox" name="%s" value="true" %s />',
			esc_attr( $this->name() ),
			checked( $this->value, true, false )
		);

		return sprintf( '<label>%s</label>', $control );
	}
}
