<?php

namespace Members\Widgets;

use Members\Contracts\Widgets\Widget;

abstract class Base extends WP_Widget implements Widget {

	protected $defaults = [];

	public function render() {

		$html  = $this->beforeWidget();
		$html .= $this->title();
		$html .= $this->afterWidget();

		return $html;
	}

	public function display() {
		echo $this->render();
	}

	public function widget() {
		$this->display();
	}

	protected function beforeWidget() {
		return $this->sidebar['before_widget'];
	}

	protected function afterWidget() {
		return $this->sidebar['after_widget'];
	}

	protected function title() {
		$html = '';

		if ( $this->instance['title'] ) {
			$html .= $this->sidebar['before_title'];
			$html .= apply_filters( 'widget_title', $this->instance['title'], $this->instance, $this->id_base );
			$html .= $this->sidebar['after_title'];
		}

		return $html;
	}

	public function form() {

		foreach ( $this->controls as $control ) {
			$control->display();
		}
	}

	protected function fieldName( $name ) {}

	protected function fieldId( $name ) {}

}
